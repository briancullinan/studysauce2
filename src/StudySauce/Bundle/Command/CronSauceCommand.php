<?php

namespace StudySauce\Bundle\Command;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use StudySauce\Bundle\Controller\EmailsController;
use StudySauce\Bundle\Entity\Deadline;
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
            if ((($p->getCreated()->getTimestamp() < time() - 86400 * 3 && $p->getCreated()->getTimestamp() > time(
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
            }
        }

        // send signup reminder
        $users = $orm->getRepository('StudySauceBundle:User');
        /** @var QueryBuilder $qb */
        $qb = $users->createQueryBuilder('p')
            ->where('p.properties NOT LIKE \'%s:16:"welcome_reminder";b:1;%\'')
            ->orWhere('p.properties IS NULL');
        $users = $qb->getQuery()->execute();
        foreach ($users as $i => $u) {
            /** @var User $u */
            if ($u->getCreated()->getTimestamp() < time() - 86400 * 3 && $u->getCreated()->getTimestamp() > time(
                ) - 86400 * 4
            ) {
                $u->setProperty('welcome_reminder', true);
                $emails->marketingReminderAction($u);
                $orm->merge($u);
                $orm->flush();
            }
        }

        // send deadline reminders
        $futureReminders = Criteria::create()->where(Criteria::expr()->gt('dueDate', new \DateTime()));
        $reminders = $orm->getRepository('StudySauceBundle:Deadline')->matching($futureReminders)->toArray();
        $deadlines = [];
        foreach ($reminders as $i => $d) {
            /** @var Deadline $d */
            // due tomorrow
            if ((in_array('86400', $d->getReminder()) && $d->getDueDate()->getTimestamp() > time() + 86400 && $d->getDueDate()->getTimestamp() < time() + 86400 * 2 &&
                    !in_array('86400', $d->getReminderSent())) ||
                // due in two days
                (in_array('172800', $d->getReminder()) && $d->getDueDate()->getTimestamp() > time() + 86400 * 2 && $d->getDueDate()->getTimestamp() < time() + 86400 * 3 &&
                    !in_array('86400', $d->getReminderSent()) &&
                    !in_array('172800', $d->getReminderSent())) ||
                // due in four days
                (in_array('345600', $d->getReminder()) && $d->getDueDate()->getTimestamp() > time() + 86400 * 4 && $d->getDueDate()->getTimestamp() < time() + 86400 * 5 &&
                    !in_array('86400',$d->getReminderSent()) &&
                    !in_array('172800', $d->getReminderSent()) &&
                    !in_array('345600', $d->getReminderSent())) ||
                // due in a week
                (in_array('604800', $d->getReminder()) && $d->getDueDate()->getTimestamp() > time() + 86400 * 7 && $d->getDueDate()->getTimestamp() < time() + 86400 * 8 &&
                    !in_array('86400', $d->getReminderSent()) &&
                    !in_array('172800', $d->getReminderSent()) &&
                    !in_array('345600', $d->getReminderSent()) &&
                    !in_array('604800', $d->getReminderSent())) ||
                // due in two weeks
                (in_array('1209600', $d->getReminder()) && $d->getDueDate()->getTimestamp() > time() + 86400 * 14 && $d->getDueDate()->getTimestamp() < time() + 86400 * 15 &&
                    !in_array('86400',$d->getReminderSent()) &&
                    !in_array('172800', $d->getReminderSent()) &&
                    !in_array('345600', $d->getReminderSent()) &&
                    !in_array('604800', $d->getReminderSent()) &&
                    !in_array('1209600', $d->getReminderSent()))
            ) {
                $deadlines[$d->getUser()->getId()][] = $d;
            }
        }

        // send aggregate emails
        foreach ($deadlines as $i => $all) {
            /** @var Deadline $d */
            $d = $all[0];
            $emails->deadlineReminderAction($d->getUser(), $all);
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
