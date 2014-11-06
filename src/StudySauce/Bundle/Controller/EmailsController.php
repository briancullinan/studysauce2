<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\User;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class EmailsController
 * @package StudySauce\Bundle\Controller
 */
class EmailsController extends Controller
{
    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomepartnerAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-partner.html.php', [
                        'name' => $user,
                        'greeting' => (empty($user->getFirst()) ? 'Howdy partner' : ('Dear ' . $user->getFirst())) . ','
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode(['category' => ['welcome-partner']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomeStudentAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-student.html.php', [
                        'name' => $user,
                        'greeting' => 'Dear ' . ($user->getFirst() ?: 'student') . ','
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['welcome-student']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Partner $partner
     * @return Response
     */
    public function partnerInviteAction(User $user = null, Partner $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if($partner == null)
            $partner = $user->getPartners()->filter(function (Partner $p) {return $p->getActivated();})->first();

        if(empty($partner)) {
            $logger = $this->get('logger');
            $logger->error('Achievement called with no partner.');
            return new Response();
        }
        $codeUrl = $this->generateUrl('partner_welcome', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your student') . ' needs your help with school.')
            ->setFrom($user->getEmail())
            ->setTo($partner->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:partner-invite.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $partner->getFirst() . ' ' . $partner->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">If you are prepared to help ' . $user->getFirst() . ', click here to join Study Sauce and learn more about how we help students achieve their academic goals.</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['partner-invite']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Partner $partner
     * @return Response
     */
    public function achievementAction(User $user = null, Partner $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if($partner == null)
            $partner = $user->getPartners()->filter(function (Partner $p) {return $p->getActivated();})->first();

        if(empty($partner)) {
            $logger = $this->get('logger');
            $logger->error('Achievement called with no partner.');
            return new Response();
        }
        $codeUrl = $this->generateUrl('goals', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your student') . ' has a study achievement and wanted you to know.')
            ->setFrom($user->getEmail())
            ->setTo($partner->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:achievement.html.php', [
                        'user' => $user,
                        'greeting' => 'Dear ' . $partner->getFirst() . ' ' . $partner->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['achievement']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param ParentInvite $parent
     * @return Response
     */
    public function parentPayAction(User $user = null, ParentInvite $parent = null)
    {
        $codeUrl = $this->generateUrl('parent_welcome', ['_code' => $parent->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your student') . ' has asked for your help with school.')
            ->setFrom($user->getEmail())
            ->setTo($parent->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:parent-invite.html.php', [
                        'user' => $user,
                        'greeting' => 'Dear ' . $parent->getFirst() . ' ' . $parent->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['sponsor-invite']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param $properties
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function administratorAction(User $user, $properties)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        if(is_object($properties))
        {
            $fields = $orm->getClassMetadata('StudySauce:Product')->getFieldNames();
            $associations = $orm->getClassMetadata('StudySauce:Product')->getAssociationNames();
            $class_vars = get_class_vars(get_class($properties));
            foreach ($class_vars as $name => $value)
            {

            }
        }

        return new Response();
    }
}