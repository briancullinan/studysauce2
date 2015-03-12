<?php

namespace StudySauce\Bundle\Command;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use StudySauce\Bundle\Controller\EmailsController;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Swift_Mailer;
use Swift_Transport;
use Swift_Transport_SpoolTransport;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);

        $container = $this->getContainer();
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        $emails = new EmailsController();
        $emails->setContainer($container);

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

        // send deadline reminders
        $futureReminders = Criteria::create()->where(Criteria::expr()->gt('dueDate', new \DateTime()));
        $reminders = $orm->getRepository('StudySauceBundle:Deadline')->matching($futureReminders)->toArray();
        $deadlines = [];

        // create a list of adviser deadlines
        $reminderRecipients = [];
        foreach ($reminders as $i => $d) {
            /** @var Deadline $d */
            if(($d->getUser()->hasRole('ROLE_ADVISER') || $d->getUser()->hasRole('ROLE_MASTER_ADVISER')) &&
                $d->shouldSend() && $d->getAssignment() == 'Course completion')
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
                            $u->hasRole('ROLE_ADMIN') || $u->hasRole('ROLE_PARTNER') || $u->hasRole('ROLE_PARENT'))
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
                usort($incomplete, function ($a, $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                usort($nosignup, function ($a, $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                usort($complete, function ($a, $b) {
                    return strcmp($a->getLast(), $b->getLast());
                });
                $emails->adviserCompletionAction($d->getUser(), $d, $incomplete, $nosignup, $complete);

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
                $d->getUser()->hasRole('ROLE_DEMO') || $d->getUser()->hasRole('ROLE_GUEST'))
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

        // clear mail spool
        /** @var Swift_Mailer $mailer */
        $mailer = $container->get('mailer');
        /** @var Swift_Transport_SpoolTransport $transport */
        $transport = $mailer->getTransport();
        /** @var  $spool */
        $spool = $transport->getSpool();
        /** @var Swift_Transport $queue */
        $queue = $container->get('swiftmailer.transport.real');
        $spool->flushQueue($queue);
    }
}
