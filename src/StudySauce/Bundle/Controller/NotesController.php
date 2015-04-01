<?php

namespace StudySauce\Bundle\Controller;

use Evernote\Model\Notebook;
use Evernote\Model\SearchResult;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Evernote\Client as EvernoteClient;

/**
 * Class ScheduleController
 * @package StudySauce\Bundle\Controller
 */
class NotesController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $schedules = $user->getSchedules()->toArray();

        $services = [];
        $notebooks = [];
        $notes = [];
        if(!empty($user->getEvernoteAccessToken())) {
            $client = new EvernoteClient($user->getEvernoteAccessToken(), true);
            $notebooks = $client->listNotebooks();
            foreach($notebooks as $n) {
                /** @var Notebook $n */
                $results = $client->findNotesWithSearch(null, $n);
                foreach($results as $s) {
                    /** @var SearchResult $s */
                    if($s->type === 1) {
                        $notes[$n->getGuid()][] = $client->getNote($s->guid);
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
            'schedules' => $schedules,
            'services' => $services,
            'notebooks' => $notebooks,
            'notes' => $notes
        ]);
    }


}