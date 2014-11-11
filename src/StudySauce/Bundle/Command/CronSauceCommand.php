<?php

namespace StudySauce\Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
            ->setHelp(<<<EOF
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
        //$output->writeln(sprintf('Hello <comment>%s</comment>!', $input->getArgument('who')));

        // TODO: send reminders


        // TODO: clear mail spool
        $container = $this->getContainer();

        $transport = $container->get('mailer')->getTransport();
        $spool = $transport->getSpool();

        $spool->flushQueue($container->get('swiftmailer.transport.smtp'));
    }
}
