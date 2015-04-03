<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EDAM\Types\Tag;
use Evernote\Model\Note;
use Evernote\Model\Notebook;
use Evernote\Model\SearchResult;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Evernote\Client as EvernoteClient;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ScheduleController
 * @package StudySauce\Bundle\Controller
 */
class NotesController extends Controller
{
    /**
     * @param $name
     * @param Collection $schedules
     * @return mixed|null
     */
    private static function getCourseByName($name, Collection $schedules)
    {
        /** @var Schedule $s */
        $s = $schedules->filter(function (Schedule $s) use ($name) {
            return $s->getClasses()->exists(function ($_, Course $c) use ($name) {
                return $c->getName() == $name;
            });
        })->first();
        if(!empty($s)) {
            /** @var Course $c */
            return $s->getClasses()->first(
                function (Course $c) use ($name) {
                    return $c->getName() == $name;
                }
            );
        }
        return null;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $schedules = $user->getSchedules();

        $services = [];
        $notebooks = [];
        $notes = [];
        if(!empty($user->getEvernoteAccessToken())) {
            $client = new EvernoteClient($user->getEvernoteAccessToken(), true);
            $notebooks = $client->listNotebooks();
            foreach($notebooks as $b) {
                /** @var Notebook $b */
                $allTags = $client->getUserNotestore()
                    ->listTagsByNotebook($user->getEvernoteAccessToken(), $b->getGuid());
                $allTags = array_combine(array_map(function (Tag $t) {return $t->guid;}, $allTags), $allTags);
                // find course with matching name
                $c = self::getCourseByName($b->getName(), $schedules);

                // find all the notes in this notebook and put them in the right schedule
                $results = $client->findNotesWithSearch(null, $b);
                foreach($results as $r) {
                    /** @var SearchResult $r */
                    if($r->type === 1) {
                        // find first schedule that was set up before the note
                        $s = $schedules->count() < 2
                            ? $schedules->first()
                            : $schedules->filter(function (Schedule $s) use ($r) {

                                // get earliest class time
                                $start = min(array_map(function (Course $c) {
                                    return empty($c->getStartTime())
                                        ? 0
                                        : $c->getStartTime()->getTimestamp();
                                }, $s->getClasses()->toArray()));

                                // candidate schedule if note was created and modified after the start of the schedule
                                return $r->updated > min($s->getCreated()->getTimestamp(), $start) ||
                                    $r->created > min($s->getCreated()->getTimestamp(), $start);
                            })->first();
                        /** @var Note $n */
                        $n = $client->getNote($r->guid);
                        $tags = array_map(function ($t) use ($allTags) {
                            return $allTags[$t];}, $n->getEdamNote()->tagGuids ?: []);
                        foreach($tags as $t) {
                            /** @var Tag $t */
                            $c = self::getCourseByName($t->name, $schedules);
                        }

                        $notes[$s->getId()][!empty($c) ? $c->getId() : $b->getGuid()][] = $n;
                    }
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
            'schedules' => $schedules->toArray(),
            'services' => $services,
            'notebooks' => array_combine(
                array_map(function (Notebook $b) {return $b->getGuid();}, $notebooks),
                $notebooks),
            'notes' => $notes
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = new EvernoteClient($user->getEvernoteAccessToken(), true);
        /** @var Notebook $notebook */
        if(!empty($request->get('notebookId'))) {
            $notebooks = $client->listNotebooks();
            foreach($notebooks as $n) {
                /** @var Notebook $n */
                if($n->getGuid() == $request->get('notebookId')) {
                    $notebook = $n;
                    break;
                }
            }
        }
        else {
            $notebook = new Notebook();
        }

        /** @var Note $note */
        if(empty($request->get('noteId'))) {
            $note = new Note();
        }
        else {
            $note = $client->getNote($request->get('noteId'));
        }
        $note->setContent($request->get('body'));
        $note->setTitle($request->get('title'));
        if(empty($request->get('noteId'))) {
            $client->uploadNote($note, $notebook);
        }
        else {
            $client->replaceNote($note, $note);
        }
        return $this->forward('StudySauceBundle:Notes:index', ['_format' => 'tab']);
    }

}