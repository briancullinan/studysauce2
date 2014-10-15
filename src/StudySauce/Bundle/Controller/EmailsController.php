<?php

namespace StudySauce\Bundle\Controller;

use StudySauce\Bundle\Entity\User;
use Swift_Message;
use Swift_Transport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\SwiftMailerDBBundle\Spool\DatabaseSpool;

/**
 * Class EmailsController
 * @package StudySauce\Bundle\Controller
 */
class EmailsController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomepartnerAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-partner.html.php', ['name' => $user]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode(['category' => ['welcome-partner']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomestudentAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-student.html.php', ['name' => $user]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode(['category' => ['welcome-student']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        $container = $this->getContainer();

        $transport = $container->get('mailer')->getTransport();
        $spool = $transport->getSpool();

        $spool->flushQueue($container->get('swiftmailer.transport.real'));

        return new Response();
    }

}