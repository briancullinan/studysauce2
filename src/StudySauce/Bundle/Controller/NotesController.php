<?php

namespace StudySauce\Bundle\Controller;

use Buzz\Browser;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use EDAM\Error\EDAMSystemException;
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
     * @param $name
     * @param Collection $schedules
     * @return Course|null
     */
    public static function getCourseByName($name, Collection $schedules)
    {
        /** @var Schedule $s */
        $s = $schedules->filter(function (Schedule $s) use ($name) {
            return $s->getClasses()->exists(function ($_, Course $c) use ($name) {
                return $c->getName() == $name || $c->getId() == $name;
            });
        })->first();
        if(!empty($s)) {
            /** @var Course $c */
            return $s->getClasses()->filter(function (Course $c) use ($name) {
                return $c->getName() == $name || $c->getId() == $name;
            })->first();
        }
        return null;
    }

    /**
     * @param ContainerInterface $container
     * @return array
     */
    public static function getNotebooksFromEvernote($container)
    {
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser())) {

            if(empty($user->getEvernoteAccessToken()))
                return [];

            $client = new EvernoteClient(
                $user->getEvernoteAccessToken(),
                $container->get('kernel')->getEnvironment() != 'prod'
            );
            try {
                $notebooks = $client->listPersonalNotebooks();
            } catch (EDAMSystemException $e) {
                if ($e->errorCode == 19) {
                    sleep(ceil($e->rateLimitDuration));
                    $notebooks = $client->listPersonalNotebooks();
                }
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

        }
        return [];
    }

    /**
     * @param ContainerInterface $container
     * @return array
     */
    public static function getTags($container)
    {
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser())) {
            // try to get notebooks from existing notes
            $notes = $user->getNotes()->toArray();
            $allTags = [];
            foreach($notes as $n) {
                /** @var StudyNote $n */
                $allTags = array_merge($allTags, $n->getProperty('tags') ?: []);

            }

            if(empty($allTags)) {
                return self::getNotebooksFromEvernote($container);
            }
            // TODO: add Google folders in here
            return $allTags;
        }
        return [];
    }

    /**
     * @param ContainerInterface $container
     * @return array
     */
    public static function getNotebooks($container)
    {
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser())) {
            // try to get notebooks from existing notes
            $notes = $user->getNotes()->toArray();
            $notebooks = [];
            foreach($notes as $n) {
                /** @var StudyNote $n */
                list($guid, $name) = !empty($n->getProperty('notebook')) ? $n->getProperty('notebook') : ['', ''];
                $notebooks[$guid] = $name;
            }

            if(empty($notebooks)) {
                return self::getNotebooksFromEvernote($container);
            }
            // TODO: add Google folders in here
            return $notebooks;
        }
        return [];
    }

    /**
     * @param ContainerInterface $container
     * @param array $folder
     * @return array
     */
    public static function getNotesFromEvernote($container, $folder)
    {
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        $nb = new Notebook();
        $nb->setGuid($folder[0]);
        $notes = [];
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser())) {

            if(empty($user->getEvernoteAccessToken()))
                return $notes;

            $client = new EvernoteClient(
                $user->getEvernoteAccessToken(),
                $container->get('kernel')->getEnvironment() != 'prod'
            );
            $bookTags = $client->getUserNotestore()
                ->listTagsByNotebook($user->getEvernoteAccessToken(), $folder[0]);
            $bookTags = array_combine(array_map(function (Tag $t) {return $t->guid;}, $bookTags), array_map(function (Tag $t) {return $t->name;}, $bookTags));

            $results = $client->findNotesWithSearch(null, $nb);

            /** @var $orm EntityManager */
            $orm = $container->get('doctrine')->getManager();

            // check our cache of notes, if it has been updated, remove it from the database to force it to redownload from evernote
            foreach ($results as $r) {
                /** @var SearchResult $r */
                if ($r->type === 1) {
                    $s = null;

                    if(!empty($r->tagGuids))
                        $tags = array_intersect_key($bookTags, array_combine($r->tagGuids, range(0, count($r->tagGuids)-1)));
                    else
                        $tags = [];

                    /** @var StudyNote[] $stored */
                    $stored = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
                        ->andWhere('n.id = :id')
                        ->setParameter('id', $r->guid)
                        ->getQuery()
                        ->getResult();
                    if (!empty($stored) && $stored[0]->getRemoteUpdated() != null &&
                        $stored[0]->getRemoteUpdated()->getTimestamp() < $r->updated / 1000
                    ) {
                        $stored[0]->setContent(null);
                    }

                    if(empty($stored)) {
                        $note = new StudyNote();
                        $user->addNote($note);
                        $note->setUser($user);
                        $note->setRemoteId($r->guid);
                        $note->setTitle($r->title);
                        $note->setProperty('notebook', $folder);
                        $note->setProperty('tags', $tags);
                        $note->setCreated(date_timestamp_set(new \DateTime(), $r->created / 1000));
                        $note->setRemoteUpdated(date_timestamp_set(new \DateTime(), $r->updated / 1000));
                        $orm->persist($note);
                        $orm->flush();
                        $notes[] = $note;
                    }
                    else
                    {
                        $stored[0]->setTitle($r->title);
                        $stored[0]->setProperty('notebook', $folder);
                        $stored[0]->setProperty('tags', $tags);
                        $stored[0]->setCreated(date_timestamp_set(new \DateTime(), $r->created / 1000));
                        $stored[0]->setRemoteUpdated(date_timestamp_set(new \DateTime(), $r->updated / 1000));
                        $orm->merge($stored[0]);
                        $orm->flush();
                        $notes[] = $stored[0];
                    }
                }
            }
        }
        return $notes;
    }

    /**
     * @param ContainerInterface $container
     * @param $folder
     * @return StudyNote[]
     */
    public static function getNotes($container, $folder) {
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser())) {

            $notes = $user->getNotes()->filter(function (StudyNote $n) use ($folder) {
                return !empty($n->getProperty('notebook'))
                && $n->getProperty('notebook')[0] == $folder[0];})->toArray();
            if(empty($notes)) {
                return self::getNotesFromEvernote($container, $folder);
            }

            return $notes;
        }
        return [];
    }

    /**
     * @return Response
     * @throws EDAMSystemException
     * @throws \Exception
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $schedules = $user->getSchedules();

        $services = [];
        $allNotes = [];
        $allTags = self::getTags($this->container);
        $notebooks = self::getNotebooks($this->container);
        foreach($notebooks as $guid => $notebookName) {
            // find all the notes in this notebook and put them in the right schedule
            $notes = self::getNotes($this->container, [$guid, $notebookName]);
            foreach($notes as $note) {
                /** @var StudyNote $note */
                // find course with matching name
                /** @var Course $c */
                $c = self::getCourseByName($notebookName, $schedules);
                if(empty($c)) {
                    foreach ($note->getProperty('tags') as $t) {
                        $c = self::getCourseByName($t, $schedules);
                        if (!empty($c)) {
                            $s = $c->getSchedule();
                            break;
                        }
                    }
                }
                else
                    $s = $c->getSchedule();

                // find first schedule that was set up before the note
                if(empty($s)) {
                    $s = $schedules->count() < 2
                        ? $schedules->first()
                        : $schedules->filter(function (Schedule $s) use ($note) {

                            // get earliest class time
                            $start = min(
                                array_map(
                                    function (Course $c) {
                                        return empty($c->getStartTime())
                                            ? 0
                                            : $c->getStartTime()->getTimestamp();
                                    },
                                    $s->getClasses()->toArray()
                                )
                            );

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
            'allTags' => $allTags,
            'summary' => function ($noteId) {
                return $this->noteSummaryAction($noteId);
            }
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
     * @param array $noteIds
     * @param Request $request
     * @return Response
     * @throws EDAMSystemException
     * @throws \Exception
     */
    public function noteSummaryAction($noteIds = null, Request $request = null)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $result = [];
        if(empty($noteIds) && !empty($request)) {
            $noteIds = $request->get('noteIds');
        }
        $i = 0;
        while($i < count($noteIds)) {
            try {
                /** @var StudyNote[] $stored */
                $stored = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
                    ->andWhere('n.remoteId = :id')
                    ->setParameter('id', $noteIds[$i])
                    ->getQuery()
                    ->getResult();
                $new = false;
                if(!empty($stored)) {
                    $stored = $stored[0];
                    //$content = $stored[0]->getContent();
                    //$cleaned = substr(trim(preg_replace('/\n+/i', "\n", preg_replace('/<[^>]*>/i', "\n", $content))), 0, 1000);
                }
                else {
                    /** @var StudyNote $stored */
                    $stored = new StudyNote();
                    $stored->setUser($user);
                    $user->addNote($stored);
                    $stored->setRemoteId($noteIds[$i]);
                    $new = true;
                }
                $thumb = $stored->getThumbnail();
                if(empty($thumb)) {
                    // get the thumbnail only
                    $shardId = self::getShardIdFromToken($user->getEvernoteAccessToken());
                    $src = 'https://' . ($this->get('kernel')->getEnvironment() != 'prod' ? 'sandbox' : 'www') . '.evernote.com/shard/' . $shardId . '/thm/note/' . $noteIds[$i] . '.jpg';
                    $content = http_build_query(['auth' => $user->getEvernoteAccessToken(), 'size' => 200]);
                    $browser = new Browser();
                    $browser->getClient()->setVerifyPeer(false);
                    $thumb = $browser->post($src, ['Content-Type' => 'application/x-www-form-urlencoded' . "\r\n"], $content);
                    $stored->setThumbnail($thumb->getContent());
                    if($new)
                        $orm->persist($stored);
                    else
                        $orm->merge($stored);
                    $orm->flush();
                }
                $cleaned = '<img src="' . $this->generateUrl('notes_thumb', ['id' => $noteIds[$i]]) . '" />';
                $result[$noteIds[$i]] = $cleaned;
                $i++;
            }
            catch (EDAMSystemException $e) {
                if($e->errorCode == 19) {
                    sleep(ceil($e->rateLimitDuration));
                }
                else throw $e;
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');
        $results = $client->findNotesWithSearch($request->get('search'));
        $result = [];
        foreach ($results as $r) {
            /** @var SearchResult $r */

            if ($r->type === 1) {
                $result[] = $r->guid;
            }
        }

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function thumbAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var StudyNote[] $stored */
        $stored = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
            ->andWhere('n.remoteId = :id')
            ->setParameter('id', $request->get('id'))
            ->getQuery()
            ->getResult();
        if(!empty($stored)) {
            $thumb = $stored[0]->getThumbnail();
            if(empty($thumb)) {
                $this->noteSummaryAction([$request->get('id')]);
                $stored = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
                    ->andWhere('n.id = :id')
                    ->setParameter('id', $request->get('id'))
                    ->getQuery()
                    ->getResult();
                $thumb = $stored[0]->getThumbnail();
            }
            return new Response($thumb, 200, ['Content-Type' => 'image/jpeg']);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $request
     * @return Response
     */
    public function noteAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var StudyNote[] $stored */
        $stored = $orm->getRepository('StudySauceBundle:StudyNote')->createQueryBuilder('n')
            ->andWhere('n.id = :id')
            ->setParameter('id', $request->get('noteId'))
            ->getQuery()
            ->getResult();
        if(!empty($stored)
            && $stored[0]->getContent() !== null
            && (
                empty($stored[0]->getUpdated())
                || empty($stored[0]->getRemoteUpdated())
                || $stored[0]->getRemoteUpdated() <= $stored[0]->getUpdated())) {
            return new Response($stored[0]->getContent());
        }
        else if(!empty($user->getEvernoteAccessToken())) {
            $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');

            /** @var \EDAM\Types\Note $n */
            $n = $client->getNote($request->get('noteId'))->getEdamNote();
            if(!empty($stored)) {
                $note = $stored[0];
            }
            else {
                $note = new StudyNote();
                $note->setRemoteId($n->guid);
                $note->setUser($user);
                $user->addNote($note);
                $note->setCreated(date_timestamp_set(new \DateTime(), $n->created / 1000));
                $orm->persist($note);
                $orm->flush();
            }
            $note->setTitle($n->title);
            $note->setContent($n->content);
            $note->setRemoteUpdated(date_timestamp_set(new \DateTime(), $n->updated / 1000));
            $orm->merge($note);
            $orm->flush();
            return new Response($note->getContent());
        }
        else
        {
            return new Response('');
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notebookAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
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

        $allTags = self::getTags($this->container);
        if(is_numeric($request->get('notebookId'))) {
            $c = self::getCourseByName($request->get('notebookId'), $user->getSchedules());
        }
        $notebooks = self::getNotebooks($this->container);
        $notebook = isset($notebooks[$request->get('notebookId')])
            ? [$request->get('notebookId'), $notebooks[$request->get('notebookId')]]
            : (!empty($c)
                ? array_search($c->getName(), $notebooks)
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
            $note->setCreated(new \DateTime());
        }
        else {
            $note = $stored[0];
        }
        $note->setThumbnail(null);
        $note->setContent('<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd"><en-note>' .
            (new HtmlNoteContent($request->get('body')))->toEnml() . '</en-note>');
        $note->setTitle($request->get('title'));
        $note->setProperty('notebook', $notebook);
        $note->setUpdated(new \DateTime());

        // update and create tags
        if(!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            $tags = array_combine($tags, range(0, count($tags)-1));
            $newTags = array_diff_key($allTags, $tags);
            $existing = array_intersect_key($allTags, $tags);
            $note->setProperty('tags', array_merge($existing, $newTags));
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');
        $store = $client->getUserNotestore();
        if($request->get('remove')) {
            $store->deleteNote($user->getEvernoteAccessToken(), $request->get('noteId'));
            $store->close();
        }

        return $this->forward('StudySauceBundle:Notes:index', ['_format' => 'tab']);
    }

}