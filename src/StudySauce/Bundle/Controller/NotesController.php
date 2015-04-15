<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use EDAM\Error\EDAMSystemException;
use EDAM\Types\Tag;
use Evernote\Model\Note;
use Evernote\Model\Notebook;
use Evernote\Model\PlainTextNoteContent;
use Evernote\Model\SearchResult;
use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Evernote\Client as EvernoteClient;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
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
        $notebooks = [];
        $notes = [];
        $allTags = [];
        if(!empty($user->getEvernoteAccessToken())) {
            $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');
            try {
                $notebooks = $client->listPersonalNotebooks();
            }
            catch(EDAMSystemException $e) {
                if($e->errorCode == 19) {
                    sleep(ceil($e->rateLimitDuration / 1000));
                    $notebooks = $client->listPersonalNotebooks();
                }
                else throw $e;
            }
            $notebooks = array_map(function (\EDAM\Types\Notebook $book) { return new Notebook($book);}, $notebooks);
            foreach($notebooks as $b) {
                /** @var Notebook $b */
                $bookTags = $client->getUserNotestore()
                    ->listTagsByNotebook($user->getEvernoteAccessToken(), $b->getGuid());
                $allTags = array_merge($allTags, array_combine(array_map(function (Tag $t) {return $t->guid;}, $bookTags), $bookTags));

                // find all the notes in this notebook and put them in the right schedule
                $results = $client->findNotesWithSearch(null, $b);
                foreach($results as $r) {
                    /** @var SearchResult $r */

                    if($r->type === 1) {
                        $s = null;

                        $tags = array_map(function ($t) use ($allTags) {
                            return $allTags[$t];}, $r->tagGuids ?: []);

                        // find course with matching name
                        /** @var Course $c */
                        $c = self::getCourseByName($b->getName(), $schedules);
                        if(empty($c)) {
                            foreach ($tags as $t) {
                                /** @var Tag $t */
                                $c = self::getCourseByName($t->name, $schedules);
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
                                : $schedules->filter(function (Schedule $s) use ($r) {

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
                                    return $r->updated > min($s->getCreated()->getTimestamp(), $start) ||
                                    $r->created > min($s->getCreated()->getTimestamp(), $start);
                                })->first();
                        }

                        $notes[empty($s) ? '' : $s->getId()][!empty($c) ? $c->getId() : $b->getGuid()][] = $r;
                    }

                }

                // show empty notebooks in current term
                if(empty($results)) {
                    $notes[empty($schedules->first())?'':$schedules->first()->getId()][$b->getGuid()] = [];
                }
            }
        }
        else {

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
            'notebooks' => array_combine(
                array_map(function (Notebook $b) {return $b->getGuid();}, $notebooks),
                $notebooks),
            'notes' => $notes,
            'allTags' => array_values(array_map(function (Tag $t) {
                return ['value' => $t->guid, 'text' => $t->name];}, $allTags)),
            'summary' => function ($noteId) {
                return $this->noteSummaryAction($noteId);
            }
        ]);
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
        /** @var User $user */
        $user = $this->getUser();
        $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');
        $result = [];
        if(empty($noteIds) && !empty($request)) {
            $noteIds = $request->get('noteIds');
        }
        $i = 0;
        while($i < count($noteIds)) {
            try {
                $n = $client->getNote($noteIds[$i]);
                $content = $n->getContent()->toEnml();
                $cleaned = substr(trim(preg_replace('/\n+/i', "\n", preg_replace('/<[^>]*>/i', "\n", $content))), 0, 1000);
                $result[$noteIds[$i]] = $cleaned;
                $i++;
            }
            catch (EDAMSystemException $e) {
                if($e->errorCode == 19) {
                    sleep(ceil($e->rateLimitDuration / 1000));
                }
                else throw $e;
            }
        }
        /** @var Note $n */
        return new JsonResponse($result);
    }

    /**
     * @param $request
     * @return Response
     */
    public function noteAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');
        /** @var Note $n */
        $n = $client->getNote($request->get('noteId'));
        return new Response($n->getContent()->toEnml());
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

        $nb = new \EDAM\Types\Notebook(['name' => $request->get('name')]);
        $store->createNotebook($user->getEvernoteAccessToken(), $nb);

        $store->close();

        return $this->forward('StudySauceBundle:Notes:index', ['_format' => 'tab']);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = new EvernoteClient($user->getEvernoteAccessToken(), $this->get('kernel')->getEnvironment() != 'prod');
        $store = $client->getUserNotestore();

        $allTags = [];
        /** @var \EDAM\Types\Notebook $notebook */
        if(!empty($request->get('notebookId'))) {
            $notebooks = $client->listNotebooks();
            foreach($notebooks as $b) {
                /** @var Notebook $b */
                $bookTags = $store->listTagsByNotebook($user->getEvernoteAccessToken(), $b->getGuid());
                $allTags = array_merge($allTags, array_combine(array_map(function (Tag $t) {return $t->guid;}, $bookTags), $bookTags));
                if($b->getGuid() == $request->get('notebookId')) {
                    $notebook = $b->getEdamNotebook();
                    break;
                }
            }
        }

        if(empty($notebook) && !empty($request->get('notebookId'))) {
            // get class name
            /** @var Course $c */
            $c = self::getCourseByName($request->get('notebookId'), $user->getSchedules());
            $nb = new \EDAM\Types\Notebook(['name' => $c->getName()]);
            $notebook = $store->createNotebook($user->getEvernoteAccessToken(), $nb);
        }

        if(empty($notebook)) {
            $notebook = $store->getDefaultNotebook($user->getEvernoteAccessToken());
        }

        /** @var \EDAM\Types\Note $note */
        if(empty($request->get('noteId'))) {
            $note = new \EDAM\Types\Note();
        }
        else {
            $note = $client->getNote($request->get('noteId'))->getEdamNote();
        }
        $note->content = (new PlainTextNoteContent($request->get('body')))->toEnml();
        $note->title = $request->get('title');
        $moved = false;
        if($note->notebookGuid != $notebook->guid) {
            $note->notebookGuid = $notebook->guid;
            $moved = true;
        }

        // update and create tags
        if(!empty($request->get('tags'))) {
            $tags = explode(',', $request->get('tags'));
            $newTags = array_diff($tags, array_keys($allTags));
            $existing = array_intersect($tags, array_keys($allTags));
            foreach($newTags as $t) {
                $tag = new Tag();
                $tag->name = $t;
                /** @var Tag $t */
                $t = $store->createTag($user->getEvernoteAccessToken(), $tag);
                $existing[] = $t->guid;
                $allTags[$t->guid] = $t;
            }
            $note->tagGuids = $existing;
            $note->tagNames = array_values(array_map(function (Tag $t) {
                return $t->name;}, array_intersect_key($allTags, array_flip($existing))));
        }


        if(empty($request->get('noteId')) || $moved) {
            $oldGuid = $note->guid;
            $store->createNote($user->getEvernoteAccessToken(), $note);
            if($moved && !empty($request->get('noteId'))) {
                // delete the old note an it will be recreated below
                $store->deleteNote($user->getEvernoteAccessToken(), $oldGuid);
            }
        }
        else {
            $store->updateNote($user->getEvernoteAccessToken(), $note);
        }
        $store->close();

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