<?php


namespace TorchAndLaurel\Bundle\Controller;

use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\StudentInvite;
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
     * @param StudentInvite $student
     * @return Response
     */
    public function studentInviteAction(User $user = null, StudentInvite $student = null)
    {
        /** @var $user User */
        if (empty($user)) {
            $user = $this->getUser();
        }

        $codeUrl = $this->generateUrl('torchandlaurel_welcome',['_code' => $student->getCode()],UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your parent') . ' has asked for you to join Study Sauce')
            ->setFrom($user->getEmail())
            ->setTo($student->getEmail())
            ->setBody(
                $this->renderView(
                    'StudySauceBundle:Emails:student-invite.html.php',
                    [
                        'user' => $user,
                        'greeting' => 'Hello ' . $student->getFirst() . ' ' . $student->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]
                ),
                'text/html'
            );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader(
            'X-SMTPAPI',
            preg_replace(
                '/(.{1,72})(\s)/i',
                "\1\n   ",
                json_encode(
                    [
                        'category' => ['student-invite']
                    ]
                )
            )
        );
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
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('torchandlaurelparents_welcome', ['_code' => $parent->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

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
                        'category' => ['parent-invite']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param null $studentEmail
     * @param null $studentFirst
     * @param null $studentLast
     * @param $_code
     * @return Response
     */
    public function parentPrepayAction(User $user = null, $studentEmail = null, $studentFirst = null, $studentLast = null, $_code)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('torchandlaurel_register', ['_code' => $_code], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your parent') . ' has prepaid for your study plan')
            ->setFrom($user->getEmail())
            ->setTo($studentEmail)
            ->setBody($this->renderView('StudySauceBundle:Emails:prepay.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $studentFirst . ' ' . $studentLast . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['parent-prepay']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }
}