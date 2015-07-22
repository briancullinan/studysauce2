<?php

namespace StudySauce\Bundle\Controller;

use Buzz\Browser;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use EDAM\Error\EDAMSystemException;
use EDAM\Types\Note;
use EDAM\Types\Tag;
use Evernote\Model\HtmlNoteContent;
use Evernote\Model\Notebook;
use Evernote\Model\SearchResult;
use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\StudyNote;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Evernote\Client as EvernoteClient;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class ScheduleController
 * @package StudySauce\Bundle\Controller
 */
class NotesController extends Controller
{
    /**
     * @param ContainerInterface $container
     */
    public static function getDemoNotes($container)
    {

        /** @var $userManager UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        /** @var User $guest */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')) {
            $guest = $userManager->findUserByUsername('guest');

            $user->setEvernoteId($guest->getEvernoteId());
            $user->setEvernoteAccessToken($guest->getEvernoteAccessToken());
            $userManager->updateUser($user);
        }

    }

    /**
     * @param $user
     * @param ContainerInterface $container
     * @throws EDAMSystemException
     * @throws \Exception
     */
    public static function syncNotes(User $user, ContainerInterface $container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var User $user */
        // list all notebooks from evernote and compare notes
        $notebooks = NotesController::getNotebooksFromEvernote($user, $container->get('kernel')->getEnvironment());
        $client = new EvernoteClient($user->getEvernoteAccessToken(), $container->get('kernel')->getEnvironment() != 'prod');
        $allTags = $client->getUserNotestore()->listTags($user->getEvernoteAccessToken());
        $allTags = array_combine(
            array_map(function (Tag $t) {
                return $t->guid;
            }, $allTags),
            array_map(function (Tag $t) {
                return $t->name;
            }, $allTags)
        );
        $existing = [];
        foreach ($notebooks as $guid => $notebookName) {
            // list all notes
            $notes = NotesController::getNotesFromEvernote(
                $user,
                [$guid, $notebookName],
                $container->get('kernel')->getEnvironment(),
                $orm,
                $allTags
            );

            // download note from Evernote if content is empty or note on server is newer
            foreach ($notes as $note) {
                /** @var StudyNote $note */
                $existing[] = $note->getRemoteId();
                if (empty($note->getContent()) || empty($note->getUpdated()) || $note->getRemoteUpdated(
                    ) > $note->getUpdated()
                ) {
                    // download the newer note content from Evernote
                    NotesController::getNoteFromEvernote(
                        $user,
                        $note,
                        $container->get('kernel')->getEnvironment(),
                        $orm
                    );
                }
            }
        }

        // loop through user notes and sync back to evernote
        $client = new EvernoteClient(
            $user->getEvernoteAccessToken(),
            $container->get('kernel')->getEnvironment() != 'prod'
        );
        $store = $client->getUserNotestore();

        foreach($user->getNotes()->toArray() as $note) {
            if(!empty($user->getEvernoteAccessToken()) && !empty($note->getRemoteId()) &&
                !in_array($note->getRemoteId(), $existing)) {
                // TODO: mark the note as deleted from our store
                $orm->remove($note);
                $user->removeNote($note);
                $orm->flush();
            }
            /** @var StudyNote $note */
            elseif(empty($note->getRemoteUpdated()) || $note->getUpdated() > $note->getRemoteUpdated()) {
                // figure out what has changed and update evernote

                // use default notebook if it isn't assigned to one already
                if(empty($note->getProperty('notebook'))) {
                    $notebook = $store->getDefaultNotebook($user->getEvernoteAccessToken())->guid;
                }
                else
                {
                    // must be a class name
                    if(is_numeric($note->getProperty('notebook')[0])) {
                        $c = NotesController::getCourseByName($note->getProperty('notebook')[0], $user->getSchedules());
                        $notebook = array_search(strtolower($c->getName()), array_map('strtolower', $notebooks));
                        if(empty($notebook)) {
                            // create a notebook based on class name
                            $nb = new \EDAM\Types\Notebook(['name' => $c->getName()]);
                            $notebook = $store->createNotebook($user->getEvernoteAccessToken(), $nb)->guid;
                            $notebooks[$notebook] = $c->getName();
                        }
                    }
                    elseif(isset($notebooks[$note->getProperty('notebook')[0]])) {
                        $notebook = $note->getProperty('notebook')[0];
                    }
                    elseif(!empty($notebook = array_search($note->getProperty('notebook')[1], $notebooks))) {
                        // look up notebook by name instead of id
                    }
                    else {
                        $nb = new \EDAM\Types\Notebook(['name' => $note->getProperty('notebook')[1]]);
                        $notebook = $store->createNotebook($user->getEvernoteAccessToken(), $nb)->guid;
                        $notebooks[$notebook] = $note->getProperty('notebook')[1];
                    }
                }

                $note->setProperty('notebook', [$notebook, $notebooks[$notebook]]);

                /** @var Note $evernote */
                if(empty($note->getRemoteId())) {
                    $evernote = new Note();
                }
                else {
                    $evernote = $client->getNote($note->getRemoteId())->getEdamNote();
                }
                if(empty($note->getContent()))
                    continue;
                $evernote->content = $note->getContent();
                if(empty($title = $note->getTitle()))
                    $title = 'Untitled-' . (new \DateTime())->format('Y-m-d');
                $evernote->title = $title;
                $evernote->notebookGuid = $notebook;

                // update and create tags
                if(!empty($tags = $note->getProperty('tags'))) {
                    $existingTags = array_merge(array_intersect($allTags, $tags), array_intersect_key($allTags, $tags));
                    $newTags  = array_diff($tags, $existingTags);
                    foreach($newTags as $k => $t) {
                        $tag = new Tag();
                        if(empty($t))
                            $t = $k;
                        $tag->name = $t;
                        /** @var Tag $t */
                        $t = $store->createTag($user->getEvernoteAccessToken(), $tag);
                        $existingTags[$t->guid] = $t->name;
                        $allTags[$t->guid] = $t->name;
                    }
                    $note->setProperty('tags', $existingTags);
                    $evernote->tagGuids = array_keys($existingTags);
                    $evernote->tagNames = array_values($existingTags);
                }

                if(empty($note->getRemoteId())) {
                    $evernote = $store->createNote($user->getEvernoteAccessToken(), $evernote);
                    $note->setRemoteId($evernote->guid);
                }
                else {
                    $store->updateNote($user->getEvernoteAccessToken(), $evernote);
                }

                $orm->merge($note);
                $orm->flush();
            }
        }
    }

    /**
     * @param $name
     * @param Collection $schedules
     * @return Course|null
     */
    public static function getCourseByName($name, Collection $schedules)
    {
        /** @var Schedule $s */
        return $schedules->map(function (Schedule $s) use ($name) {
            return $s->getClasses()->filter(function (Course $c) use ($name) {
                return $c->getName() == $name || $c->getId() == $name;
            })->first();
        })->filter(function ($c) {return !empty($c);})->first();
    }

    /**
     * @param User $user
     * @param string $env
     * @return array
     */
    public static function getNotebooksFromEvernote(User $user, $env)
    {
        if(empty($user->getEvernoteAccessToken()))
            return [];

        $client = new EvernoteClient(
            $user->getEvernoteAccessToken(),
            $env != 'prod'
        );
        try {
            $notebooks = $client->listPersonalNotebooks();
        } catch (EDAMSystemException $e) {
            return [];
        }
        if (isset($notebooks)) {
            $notebooks = array_combine(
                array_map(
                    function (\EDAM\Types\Notebook $book) {
                        return $book->guid;
                    },
                    $notebooks
                ),
                array_map(
                    function (\EDAM\Types\Notebook $book) {
                        return $book->name;
                    },
                    $notebooks
                )
            );
            return $notebooks;
        }
        return [];
    }

    /**
     * @param User $user
     * @param string $env
     * @return array
     */
    public static function getNotebooks(User $user, $env)
    {
        // try to get notebooks from existing notes
        $notes = $user->getNotes()->toArray();
        $notebooks = [];
        foreach($notes as $n) {
            /** @var StudyNote $n */
            list($guid, $name) = !empty($n->getProperty('notebook')) ? $n->getProperty('notebook') : ['', ''];
            $notebooks[$guid] = $name;
        }

        $added = $user->getProperty('addedNotebooks') ?: [];
        foreach($added as $nb) {
            if(array_search($nb, $notebooks) === false) {
                $notebooks[$nb] = $nb;
            }
        }

        if(empty($notebooks)) {
            return self::getNotebooksFromEvernote($user, $env);
        }

        $removed = $user->getProperty('removedNotebooks') ?: [];
        foreach($removed as $nb) {
            if(($rm = array_search($nb, $notebooks)) !== false) {
                unset($notebooks[$rm]);
            }
        }
        // TODO: add Google folders in here
        return $notebooks;
    }

    /**
     * @param User $user
     * @param array $folder
     * @param string $env
     * @param EntityManager $orm
     * @param $allTags
     * @return array
     */
    public static function getNotesFromEvernote(User $user, $folder, $env, EntityManager $orm, &$allTags)
    {
        if(empty($user->getEvernoteAccessToken()) || is_numeric($folder[0])
            || in_array($folder[0], $user->getProperty('addedNotebooks') ?: [])
            || in_array($folder[0], $user->getProperty('removedNotebooks') ?: []))
            return [];

        $nb = new Notebook();
        $nb->setGuid($folder[0]);
        $notes = [];

        $client = new EvernoteClient($user->getEvernoteAccessToken(), $env != 'prod');
        if(empty($allTags)) {
            $allTags = $client->getUserNotestore()->listTags($user->getEvernoteAccessToken());
            $allTags = array_combine(
                array_map(function (Tag $t) {
                        return $t->guid;
                    }, $allTags),
                array_map(function (Tag $t) {
                        return $t->name;
                    }, $allTags)
            );
        }
        $results = $client->findNotesWithSearch(null, $nb);

        // check our cache of notes, if it has been updated, remove it from the database to force it to redownload from evernote
        foreach ($results as $r) {
            /** @var SearchResult $r */
            if ($r->type === 1) {
                $s = null;

                if(!empty($r->tagGuids))
                    $tags = array_intersect_key($allTags, array_combine($r->tagGuids, range(0, count($r->tagGuids)-1)));
                else
                    $tags = [];

                /** @var StudyNote[] $stored */
                $stored = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
                    ->andWhere('n.remoteId = :id AND n.user = :uid')
                    ->setParameters(['id' => $r->guid, 'uid' => $user])
                    ->getQuery()
                    ->getResult();
                if (!empty($stored) && $stored[0]->getRemoteUpdated() != null &&
                    $stored[0]->getRemoteUpdated()->getTimestamp() < $r->updated / 1000
                ) {
                    $stored[0]->setContent(null);
                }

                if(empty($stored)) {
                    $note = new StudyNote();
                    $note->setUser($user);
                    $user->addNote($note);
                    $note->setRemoteId($r->guid);
                    $note->setTitle($r->title);
                    $note->setProperty('notebook', $folder);
                    $note->setProperty('tags', $tags);
                    $note->setCreated(date_timestamp_set(new \DateTime(), $r->created / 1000));
                    $note->setRemoteUpdated(date_timestamp_set(new \DateTime(), $r->updated / 1000));
                    $orm->persist($note);
                    $notes[] = $note;
                }
                elseif(date_timestamp_set(new \DateTime(), $r->updated / 1000) > $stored[0]->getRemoteUpdated())
                {
                    $stored[0]->setUser($user);
                    $user->addNote($stored[0]);
                    $stored[0]->setTitle($r->title);
                    $stored[0]->setProperty('notebook', $folder);
                    $stored[0]->setProperty('tags', $tags);
                    $stored[0]->setCreated(date_timestamp_set(new \DateTime(), $r->created / 1000));
                    $stored[0]->setRemoteUpdated(date_timestamp_set(new \DateTime(), $r->updated / 1000));
                    $orm->merge($stored[0]);
                    $notes[] = $stored[0];
                }
                else {
                    $notes[] = $stored[0];
                }
            }
        }

        $orm->flush();
        return $notes;
    }

    /**
     * @param User $user
     * @param array $folder
     * @param string $env
     * @param EntityManager $orm
     * @param $allTags
     * @return \StudySauce\Bundle\Entity\StudyNote[]
     */
    public static function getNotes($user, $folder, $env, $orm, &$allTags) {
        $notes = $user->getNotes()->filter(function (StudyNote $n) use ($folder) {
            return (empty($n->getProperty('notebook')) && empty($folder[0])) ||
            (!empty($n->getProperty('notebook')) && $n->getProperty('notebook')[0] == $folder[0]);})->toArray();

        if(empty($allTags)) {
            foreach($user->getNotes()->toArray() as $n) {
                /** @var StudyNote $n */
                $allTags = array_merge($allTags, $n->getProperty('tags') ?: []);
            }
        }

        // don't try to download notes from evernote that haven't been uploaded yet
        if(empty($notes) && !empty($folder[0])) {
            return self::getNotesFromEvernote($user, $folder, $env, $orm, $allTags);
        }

        return $notes;
    }

    /**
     * @return Response
     * @throws EDAMSystemException
     * @throws \Exception
     */
    public function indexAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $schedules = $user->getSchedules();

        $services = [];
        $allNotes = [];
        $allTags = [];
        $notebooks = self::getNotebooks($user, $this->get('kernel')->getEnvironment());
        foreach($notebooks as $guid => $notebookName) {
            // find all the notes in this notebook and put them in the right schedule
            $notes = self::getNotes($user, [$guid, $notebookName], $this->get('kernel')->getEnvironment(), $orm, $allTags);
            foreach($notes as $note) {
                /** @var StudyNote $note */
                // find course with matching name
                /** @var Course $c */
                $c = self::getCourseByName($notebookName, $schedules);
                if(empty($c) && !empty($note->getProperty('tags'))) {
                    foreach ($note->getProperty('tags') as $t) {
                        $c = self::getCourseByName($t, $schedules);
                        if (!empty($c)) {
                            $s = $c->getSchedule();
                            break;
                        }
                    }
                }
                elseif(!empty($c))
                    $s = $c->getSchedule();

                // find first schedule that was set up before the note
                if(empty($s)) {
                    $s = $schedules->filter(function (Schedule $s) {
                        return $s->getClasses()->count() > 0;})->count() < 2
                        ? $schedules->first()
                        : $schedules->filter(function (Schedule $s) use ($note) {

                            // get earliest class time
                            $start = empty($s->getClasses()->count())
                                ? $s->getCreated()->getTimestamp()
                                : min(array_map(function (Course $c) {
                                    return empty($c->getStartTime())
                                        ? 0
                                        : $c->getStartTime()->getTimestamp();
                                    }, $s->getClasses()->toArray()));

                            // candidate schedule if note was created and modified after the start of the schedule
                            return !empty($note->getRemoteUpdated()) && $note->getRemoteUpdated()->getTimestamp() > min($s->getCreated()->getTimestamp(), $start) ||
                                $note->getCreated()->getTimestamp() > min($s->getCreated()->getTimestamp(), $start);
                        })->first();
                }

                $allNotes[empty($s) ? '' : $s->getId()][!empty($c) ? $c->getId() : $guid][] = $note;
            }

            // show empty notebooks in current term
            if(empty($notes)) {
                $allNotes[empty($schedules->first())?'':$schedules->first()->getId()][$guid] = [];
            }
        }

        if(empty($user->getEvernoteAccessToken())) {

            // list oauth services
            /** @var OAuthHelper $oauth */
            $oauth = $this->get('hwi_oauth.templating.helper.oauth');
            foreach($oauth->getResourceOwners() as $o) {
                if($o != 'evernote')
                    continue;
                $services[$o] = $oauth->getLoginUrl($o);
            }

        }

        return $this->render('StudySauceBundle:Notes:tab.html.php', [
            'schedules' => $schedules->count() == 0 ? [new Schedule()] : $schedules->toArray(),
            'services' => $services,
            'notebooks' => $notebooks,
            'notes' => $allNotes,
            'allTags' => $allTags
        ]);
    }

    /**
     * @param $token
     * @return null
     */
    private static function getShardIdFromToken($token)
    {
        $result = preg_match('/:?S=(s[0-9]+):?/', $token, $matches);

        if ($result === 1 && array_key_exists(1, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if(!empty($user->getEvernoteAccessToken())) {
            $client = new EvernoteClient(
                $user->getEvernoteAccessToken(),
                $this->get('kernel')->getEnvironment() != 'prod'
            );
            $results = $client->findNotesWithSearch($request->get('search'));
            $result = [];
            foreach ($results as $r) {
                /** @var SearchResult $r */

                if ($r->type === 1) {
                    $result[] = $r->guid;
                }
            }
        }

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var Note[] $notes */
        $params = array_merge(['uid' => $user, 'search' => '%' . $request->get('search') . '%'], !empty($result) ? ['rids' => $result] : []);
        $notes = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
            ->andWhere('n.user = :uid')
            ->andWhere((!empty($result) ? 'n.remoteId IN (:rids) OR' : '') . ' n.title LIKE :search OR n.content LIKE :search')
            ->setParameters($params)
            ->getQuery()->execute();

        return new JsonResponse(array_map(function (StudyNote $n) {return $n->getId();}, $notes));
    }

    /**
     * @param StudyNote $note
     * @return Response
     */
    public function thumbAction(StudyNote $note)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $thumb = $note->getThumbnail();
        if(empty($thumb)) {
            // get the thumbnail only
            $shardId = self::getShardIdFromToken($user->getEvernoteAccessToken());
            $src = 'https://' . ($this->get('kernel')->getEnvironment() != 'prod' ? 'sandbox' : 'www') . '.evernote.com/shard/' . $shardId . '/thm/note/' . $note->getRemoteId() . '.jpg';
            $content = http_build_query(['auth' => $user->getEvernoteAccessToken(), 'size' => 200]);
            $browser = new Browser();
            $browser->getClient()->setVerifyPeer(false);
            $thumb = $browser->post($src, ['Content-Type' => 'application/x-www-form-urlencoded' . "\r\n"], $content);
            $note->setThumbnail($thumb->getContent());
            $orm->merge($note);
            $orm->flush();
            $thumb = $thumb->getContent();
        }
        return new Response($thumb, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * @param StudyNote $note
     * @return Response
     */
    public function noteAction(StudyNote $note)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        if(!empty($note->getContent())
            // if dates haven't been set, return saved content
            && (empty($note->getUpdated())
                || empty($note->getRemoteUpdated())
                || $note->getRemoteUpdated() <= $note->getUpdated()
                || empty($user->getEvernoteAccessToken()))) {
            return new Response($note->getContent());
        }
        elseif(!empty($user->getEvernoteAccessToken())) {
            $note = self::getNoteFromEvernote($user, $note, $this->get('kernel')->getEnvironment(), $orm);
            return new Response($note->getContent());
        }
        throw new NotFoundHttpException('Existing note must be specified');
    }

    /**
     * @param User $user
     * @param StudyNote $note
     * @param $env
     * @param EntityManager $orm
     * @return StudyNote
     */
    public static function getNoteFromEvernote(User $user, StudyNote $note, $env, EntityManager $orm)
    {
        $client = new EvernoteClient($user->getEvernoteAccessToken(), $env != 'prod');

        /** @var Note $n */
        $n = $client->getNote($note->getRemoteId())->getEdamNote();
        $note->setTitle($n->title);
        $note->setContent($n->content);
        $note->setRemoteUpdated(date_timestamp_set(new \DateTime(), $n->updated / 1000));
        if(empty($note->getUpdated()))
            $note->setUpdated($note->getRemoteUpdated());
        $orm->merge($note);
        $orm->flush();
        return $note;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notebookAction(Request $request)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var User $user */
        $user = $this->getUser();
        if(!empty($request->get('name'))) {
            $added = $user->getProperty('addedNotebooks');
            $added[] = $request->get('name');
            $user->setProperty('addedNotebooks', $added);
        }
        elseif(!empty($request->get('remove'))) {
            $removed = $user->getProperty('removedNotebooks');
            $removed[] = $request->get('remove');
            $user->setProperty('removedNotebooks', $removed);
        }
        $userManager->updateUser($user);

        /*if(!empty($user->getEvernoteAccessToken())) {
            $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');
            $store = $client->getUserNotestore();

            if(!empty($request->get('name'))) {
                $nb = new \EDAM\Types\Notebook(['name' => $request->get('name')]);
                $store->createNotebook($user->getEvernoteAccessToken(), $nb);
            }
            elseif(!empty($request->get('remove'))) {
                $store->expungeNotebook($user->getEvernoteAccessToken(), $request->get('remove'));
            }
            $store->close();
        }*/

        return $this->forward('StudySauceBundle:Notes:index', ['_format' => 'tab']);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $allTags = [];
        foreach($user->getNotes()->toArray() as $n) {
            /** @var StudyNote $n */
            $allTags = array_merge($allTags, $n->getProperty('tags') ?: []);

        }
        if(is_numeric($request->get('notebookId'))) {
            $c = self::getCourseByName($request->get('notebookId'), $user->getSchedules());
        }
        $notebooks = self::getNotebooks($user, $this->get('kernel')->getEnvironment());
        $notebook = isset($notebooks[$request->get('notebookId')])
            ? [$request->get('notebookId'), $notebooks[$request->get('notebookId')]]
            : (!empty($c)
                ? [$c->getId(), $c->getName()]
                : []);

        $stored = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
            ->andWhere('n.id = :id')
            ->setParameter('id', $request->get('noteId'))
            ->getQuery()
            ->getResult();
        /** @var StudyNote $note */
        if(empty($stored)) {
            $note = new StudyNote();
            $note->setUser($user);
            $user->addNote($note);
            $note->setCreated(new \DateTime());
        }
        else {
            $note = $stored[0];
        }
        $note->setThumbnail(null);
        $note->setContent('<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd"><en-note>' .
            (new HtmlNoteContent($request->get('body')))->toEnml() . '</en-note>');
        if(empty($title = $request->get('title'))) {
            $title = 'Untitled-' . (new \DateTime())->format('Y-m-d');
        }
        $note->setTitle($title);
        $note->setProperty('notebook', $notebook);
        $note->setUpdated(new \DateTime());

        // update and create tags
        if(!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            $tags = array_combine($tags, range(0, count($tags)-1));
            $existing = array_intersect_key($allTags, $tags);
            $newTags = array_diff_key($tags, $existing);
            $note->setProperty('tags', array_merge($existing, array_flip($newTags)));
        }
        if(empty($stored)) {
            $orm->persist($note);
        }
        else {
            $orm->merge($note);
        }
        $orm->flush();
        return $this->forward('StudySauceBundle:Notes:index', ['_format' => 'tab']);
    }

    /**
     * @param StudyNote $note
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(StudyNote $note)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        if(!empty($note->getRemoteId())) {
            try {
                $client = new EvernoteClient(
                    $user->getEvernoteAccessToken(),
                    $this->get('kernel')->getEnvironment() != 'prod'
                );
                $store = $client->getUserNotestore();
                $store->deleteNote($user->getEvernoteAccessToken(), $note->getRemoteId());
                $store->close();
            }
            catch(\Exception $e) {

            }
        }

        $user->removeNote($note);
        $orm->remove($note);
        $orm->flush();

        return $this->forward('StudySauceBundle:Notes:index', ['_format' => 'tab']);
    }

}