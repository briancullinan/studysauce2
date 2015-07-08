<?php

namespace StudySauce\Bundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use EDAM\Error\EDAMSystemException;
use EDAM\Types\Notebook;
use EDAM\Types\Tag;
use Evernote\Client as EvernoteClient;
use StudySauce\Bundle\Controller\EmailsController;
use StudySauce\Bundle\Controller\NotesController;
use StudySauce\Bundle\Controller\PlanController;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\StudyNote;
use StudySauce\Bundle\Entity\User;
use Swift_Mailer;
use Swift_Transport;
use Swift_Transport_SpoolTransport;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WhiteOctober\SwiftMailerDBBundle\Spool\DatabaseSpool;

/**
 * Hello World command for demo purposes.
 *
 * You could also extend from Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
 * to get access to the container via $this->getContainer().
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class CronSauceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // TODO: use studysauce:cron to send reminder emails
        $this
            ->setName('sauce:cron')
            ->setDescription('Run all the periodic things Study Sauce needs to do.')
            //->addArgument('who', InputArgument::OPTIONAL, 'Who to greet.', 'World')
            ->setHelp(
                <<<EOF
                The <info>%command.name%</info> command performs the following tasks:
* Send reminder e-mails
* Clear the mail queue
<info>php %command.full_name%</info>

EOF
            );
    }

    private function sendReminders()
    {

        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        // send reminders
        $notActivated = Criteria::create()->where(Criteria::expr()->isNull('activated'))->orWhere(
            Criteria::expr()->eq('activated', false)
        );
        $partners = $orm->getRepository('StudySauceBundle:PartnerInvite')->matching($notActivated)->toArray();
        foreach ($partners as $i => $p) {
            /** @var PartnerInvite $p */
            // send for 4 weeks
            if (!$p->getActivated() &&
                (($p->getCreated()->getTimestamp() < time() - 86400 * 3 && $p->getCreated()->getTimestamp() > time(
                        ) - 86400 * 4) ||
                    ($p->getCreated()->getTimestamp() < time() - 86400 * 10 && $p->getCreated()->getTimestamp() > time(
                        ) - 86400 * 11) ||
                    ($p->getCreated()->getTimestamp() < time() - 86400 * 17 && $p->getCreated()->getTimestamp() > time(
                        ) - 86400 * 18) ||
                    ($p->getCreated()->getTimestamp() < time() - 86400 * 24 && $p->getCreated()->getTimestamp() > time(
                        ) - 86400 * 25)) &&
                (empty($p->getReminder()) || $p->getReminder()->getTimestamp() < time() - 86400 * 7)
            ) {
                $emails->partnerReminderAction($p->getUser(), $p);
                $p->setReminder(new \DateTime());
                $orm->merge($p);
                $orm->flush();
            }
        }
    }

    private function send3DayMarketing()
    {
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();

        // send 3 day signup reminder
        $users = $orm->getRepository('StudySauceBundle:User');
        /** @var QueryBuilder $qb */
        $qb = $users->createQueryBuilder('u')
            ->andWhere('u.created > \'' . date_timestamp_set(new \DateTime(), time() - 86400 * 4)->format('Y-m-d 00:00:00') . '\'')
            ->andWhere('u.properties NOT LIKE \'%s:16:"welcome_reminder";b:1;%\' OR u.properties IS NULL')
            ->andWhere('u.roles NOT LIKE \'%GUEST%\' AND u.roles NOT LIKE \'%DEMO%\'');
        $users = $qb->getQuery()->execute();
        foreach ($users as $i => $u) {
            /** @var User $u */
            // TODO: skip advised users
            if ($u->getCreated()->getTimestamp() < time() - 86400 * 3 && $u->getCreated()->getTimestamp() > time() - 86400 * 4) {
                $u->setProperty('welcome_reminder', true);
                //$emails->marketingReminderAction($u);
                $orm->merge($u);
                $orm->flush();
            }
        }

    }

    private function sendDeadlines()
    {
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();
        $emails = new EmailsController();
        $emails->setContainer($this->getContainer());

        // send deadline reminders
        $reminders = new ArrayCollection($orm->getRepository('StudySauceBundle:Deadline')->createQueryBuilder('d')
            ->select('d')
            ->andWhere('d.user IS NOT NULL AND d.deleted != 1')
            ->getQuery()
            ->getResult());
        $deadlines = [];

        // create a list of adviser deadlines
        $reminderRecipients = [];
        $adviser = $reminders->filter(function (Deadline $d) {
            return $d->getUser()->hasRole('ROLE_ADVISER') || $d->getUser()->hasRole('ROLE_MASTER_ADVISER');});
        foreach ($adviser->toArray() as $i => $d) {
            /** @var Deadline $d */

            /** @var Deadline $adviserCompletion */
            $adviserCompletion = $adviser->filter(function (Deadline $r) use ($d) {return $r->getAssignment() == 'Adviser completion' && $d->getUser() == $r->getUser();})->first();

            if($d->shouldSend() && $d->getAssignment() == 'Course completion')
            {
                // get a list of all users in the group
                $addresses = [];
                $incomplete = [];
                $complete = [];
                foreach($d->getUser()->getGroups()->toArray() as $g)
                {
                    /** @var Group $g */
                    foreach($g->getUsers()->toArray() as $u)
                    {
                        /** @var User $u */
                        $addresses[] = $u->getEmail();

                        if($u->hasRole('ROLE_ADVISER') || $u->hasRole('ROLE_MASTER_ADVISER') ||
                            $u->hasRole('ROLE_DEMO') || $u->hasRole('ROLE_ADMIN') ||
                            $u->hasRole('ROLE_PARTNER') || $u->hasRole('ROLE_PARENT'))
                            continue;

                        if($u->getCompleted() < 100) {
                            $incomplete[$u->getId()] = $u;
                            $deadlines[$u->getId()][] = $d;
                            $reminderRecipients[$u->getId()] = $u;
                        }
                        else {
                            $complete[$u->getId()] = $u;
                        }
                    }
                }

                // also send reminder to users that haven't even signed up
                $nosignup = [];
                foreach($d->getUser()->getGroupInvites() as $gi)
                {
                    /** @var GroupInvite $gi */
                    if(array_search($gi->getEmail(), $addresses) === false)
                    {
                        $r = md5($gi->getEmail());
                        $reminderRecipients[$r] = $gi;
                        $deadlines[$r][] = $d;
                        $nosignup[] = $gi;
                    }
                }

                // send adviser updates
                usort($incomplete, function (User $a, User $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                usort($nosignup, function (GroupInvite $a, GroupInvite $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                usort($complete, function (User $a, User $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                if(!empty($adviserCompletion) && $adviserCompletion->shouldSend() &&
                    !empty($incomplete)) {
                    $emails->adviserCompletionAction($d->getUser(), $d, $incomplete, $nosignup, $complete);
                    $adviserCompletion->markSent();
                    $orm->merge($adviserCompletion);
                }

                $d->markSent();
                $orm->merge($d);
                $orm->flush();
            }
        }

        // user deadlines
        foreach ($reminders as $i => $d) {
            /** @var Deadline $d */
            // don't send advisers their own reminders, only send them to students above
            if($d->getUser()->hasRole('ROLE_ADVISER') || $d->getUser()->hasRole('ROLE_MASTER_ADVISER') ||
                $d->getUser()->hasRole('ROLE_DEMO') || $d->getUser()->hasRole('ROLE_ADMIN') ||
                $d->getUser()->hasRole('ROLE_GUEST'))
                continue;
            // due tomorrow
            if ($d->shouldSend()) {
                $deadlines[$d->getUser()->getId()][] = $d;
                $reminderRecipients[$d->getUser()->getId()] = $d->getUser();
                $d->markSent();
                $orm->merge($d);
                $orm->flush();
            }
        }

        // send aggregate emails
        foreach ($deadlines as $i => $all) {
            $user = $reminderRecipients[$i];
            $emails->deadlineReminderAction($user, $all);
        }

    }

    private function sendSpool()
    {
        // clear mail spool
        /** @var Swift_Mailer $mailer */
        $mailer = $this->getContainer()->get('mailer');
        /** @var Swift_Transport_SpoolTransport $transport */
        $transport = $mailer->getTransport();
        /** @var DatabaseSpool $spool */
        $spool = $transport->getSpool();
        $spool->setTimeLimit(60*4.5);
        /** @var Swift_Transport $queue */
        $queue = $this->getContainer()->get('swiftmailer.transport.real');
        $spool->flushQueue($queue);

    }

    private function syncNotes()
    {

        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();

        // sync user notes
        // list all users with an evernote access token
        $users = $orm->getRepository('StudySauceBundle:User');
        /** @var QueryBuilder $qb */
        $qb = $users->createQueryBuilder('u')
            ->andWhere('u.evernote_access_token IS NOT NULL');
        $users = $qb->getQuery()->execute();
        foreach($users as $u) {
            try {
                NotesController::syncNotes($u, $this->getContainer());
            }
            catch (\Exception $e) {
                print $e;
            }
        }

    }

    private function syncEvents()
    {
        /** @var $orm EntityManager */
        $orm = $this->getContainer()->get('doctrine')->getManager();


        // sync calendar
        $users = $orm->getRepository('StudySauceBundle:User');
        /** @var QueryBuilder $qb */
        $qb = $users->createQueryBuilder('u')
            ->andWhere('u.gcal_access_token IS NOT NULL AND u.gcal_access_token != \'\'');
        $users = $qb->getQuery()->execute();
        foreach($users as $u) {
            try {
                PlanController::syncEvents($u, $this->getContainer());
            }
            catch (\Exception $e) {
                print $e;
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // set the timeout to 4 and a half minutes
        set_time_limit(60*4.5);
        $this->sendReminders();
        $this->send3DayMarketing();
        $this->sendDeadlines();
        $this->sendSpool();
        $this->syncNotes();
        $this->syncEvents();
    }
}
