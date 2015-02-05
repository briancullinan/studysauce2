<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\ContactMessage;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Swift_Message;
use Swift_Mime_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
    public function welcomePartnerAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var Swift_Mime_Message $message */
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
        $this->send($message);

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

        /** @var Swift_Mime_Message $message */
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
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param PartnerInvite $partner
     * @return Response
     */
    public function partnerInviteAction(User $user = null, PartnerInvite $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if($partner == null)
            $partner = $user->getPartnerInvites()->filter(function (PartnerInvite $p) {return $p->getActivated();})->first();

        if(empty($partner)) {
            $logger = $this->get('logger');
            $logger->error('Achievement called with no partner.');
            return new Response();
        }
        $codeUrl = $this->generateUrl('partner_welcome', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
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
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param PartnerInvite $partner
     * @return Response
     */
    public function partnerReminderAction(User $user = null, PartnerInvite $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if($partner == null)
            $partner = $user->getPartnerInvites()->filter(function (PartnerInvite $p) {return $p->getActivated();})->first();

        if(empty($partner)) {
            $logger = $this->get('logger');
            $logger->error('Achievement called with no partner.');
            return new Response();
        }
        $codeUrl = $this->generateUrl('partner_welcome', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Your invitation' . (!empty($user->getFirst()) ? (' from ' . $user->getFirst()) : '') . ' to join Study Sauce is still pending')
            ->setFrom($user->getEmail())
            ->setTo($partner->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:partner-reminder.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $partner->getFirst() . ' ' . $partner->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">If you are prepared to help ' . $user->getFirst() . ', click here to join Study Sauce and learn more about how we help students achieve their academic goals.</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['partner-reminder']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param StudentInvite $student
     * @return Response
     */
    public function studentInviteAction(User $user = null, StudentInvite $student = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('student_welcome', ['_code' => $student->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your parent') . ' has asked for you to join Study Sauce')
            ->setFrom($user->getEmail())
            ->setTo($student->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:student-invite.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $student->getFirst() . ' ' . $student->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['student-invite']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param Payment $payment
     * @param $address
     * @return Response
     */
    public function invoiceAction(User $user = null, Payment $payment, $address)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Thank you for your purchase!')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:invoice.html.php', [
                        'user' => $user,
                        'address' => $address,
                        'payment' => $payment,
                        'greeting' => 'Hello ' . $user->getFirst() . ' ' . $user->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['invoice']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param $user
     * @return Response
     */
    public function marketingReminderAction(User $user)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Get the most out of your Study Sauce account')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:welcome-reminder.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $user->getFirst() . ' ' . $user->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['welcome-reminder']])));
        $this->send($message);

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

        $codeUrl = $this->generateUrl('student_welcome', ['_code' => $_code], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
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
                        'category' => ['prepay']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param $reminders
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deadlineReminderAction(User $user, $reminders)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];
        $reminderOutput = count($reminders) > 1 ? 'Below are your reminders.<br /><br />' : 'Below is your reminder.<br /><br />';
        $classes = [];
        if(is_array($reminders) && !empty($reminders)) {
            foreach ($reminders as $reminder) {
                /** @var Deadline $reminder */
                $classI = array_search($reminder->getCourse(), array_values($courses));

                if ($classI === false) {
                    $classI = -1;
                }

                if ($classI == 0) {
                    $color = '#FF0D00';
                } elseif ($classI == 1) {
                    $color = '#FF8900';
                } elseif ($classI == 2) {
                    $color = '#FFD700';
                } elseif ($classI == 3) {
                    $color = '#BAF300';
                } elseif ($classI == 4) {
                    $color = '#2DD700';
                } elseif ($classI == 5) {
                    $color = '#009999';
                } elseif ($classI == 6) {
                    $color = '#162EAE';
                } elseif ($classI == 7) {
                    $color = '#6A0AAB';
                } elseif ($classI == 8) {
                    $color = '#BE008A';
                } else {
                    $color = '#DDDDDD';
                }

                $className = !empty($reminder->getCourse()) ? $reminder->getCourse()->getName() : 'Nonacademic';
                $reminderOutput .= '<br /><strong>Subject:</strong><br /><span style="height:24px;width:24px;background-color:' . $color . ';display:inline-block;border-radius:100%;border: 3px solid #555555;vertical-align: middle;">&nbsp;</span> ' . $className . '<br /><br /><strong>Assignment:</strong><br />' . $reminder->getAssignment(
                    ) . '<br /><br /><strong>Days until due date:</strong><br />' . $reminder->getDaysUntilDue() . '<br /><br />';
                if (array_search($className, $classes) === false) {
                    $classes[] = $className;
                }

                // save the sent status of the reminder
                $timespan = floor(($reminder->getDueDate()->getTimestamp() - time()) / 86400);
                foreach ([1, 2, 4, 7, 14] as $i => $t) {
                    if ($timespan - $t <= 0) {
                        $sent = $reminder->getReminderSent();
                        $sent[] = $t * 86400;
                        $reminder->setReminderSent($sent);
                        $orm->merge($reminder);
                        $orm->flush();
                        break;
                    }
                }
            }
        }

        $codeUrl = $this->generateUrl('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('You have a notification for ' . implode(', ', $classes))
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:deadline-reminder.html.php', [
                        'user' => $user,
                        'reminders' => $reminderOutput,
                        'greeting' => 'Hi ' . $user->getFirst() . ',',
                        'link' => '<a href="' . $codeUrl . '">Click here to log in to Study Sauce and edit your deadlines</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['deadline-reminder']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param PartnerInvite $partner
     * @return Response
     */
    public function achievementAction(User $user = null, PartnerInvite $partner = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if($partner == null)
            $partner = $user->getPartnerInvites()->filter(function (PartnerInvite $p) {return $p->getActivated();})->first();

        if(empty($partner)) {
            $logger = $this->get('logger');
            $logger->error('Achievement called with no partner.');
            return new Response();
        }

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your student') . ' has a study achievement and wanted you to know.')
            ->setFrom($user->getEmail())
            ->setTo($partner->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:achievement.html.php', [
                        'user' => $user,
                        'greeting' => 'Dear ' . $partner->getFirst() . ' ' . $partner->getLast() . ',',
                        'link' => '<a href="' . $this->generateUrl('goals', ['_code' => $partner->getCode()], UrlGeneratorInterface::ABSOLUTE_URL) . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['achievement']])));
        $this->send($message);

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

        $codeUrl = $this->generateUrl('parent_welcome', ['_code' => $parent->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
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
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param GroupInvite $invite
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function groupInviteAction(User $user = null, GroupInvite $invite = null, Group $group = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        if(empty($group))
            $group = $invite->getGroup();

        /** @var Swift_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce!')
            ->setFrom($user->getEmail())
            ->setTo($invite->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:group-invite.html.php', [
                        'user' => $user,
                        'invite' => $invite,
                        'group' => $group,
                        'greeting' => 'Dear ' . $invite->getFirst() . ' ' . $invite->getLast() . ',',
                        'link' => '<a href="' . $this->generateUrl('student_welcome', ['_code' => $invite->getCode()], UrlGeneratorInterface::ABSOLUTE_URL) . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['group-invite']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('password_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        /** @var Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Your Study Sauce password has been reset.')
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:reset-password.html.php', [
                        'user' => $user,
                        'greeting' => 'Dear ' . $user->getFirst() . ' ' . $user->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Create a new password</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['reset-password']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param $contact
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactMessageAction(User $user = null, ContactMessage $contact)
    {
        if($user == null)
            $user = $this->getUser();

        /** @var \Swift_Mime_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Contact Us: From ' . $contact->getName())
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo('admin@studysauce.com')
            ->setBody($this->renderView('StudySauceBundle:Emails:contact-message.html.php', [
                        'link' => '&nbsp;',
                        'user' => $user,
                        'contact' => $contact
                    ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['contact-message']])));
        $this->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param $properties
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function administratorAction(User $user = null, $properties)
    {
        if($user == null)
            $user = $this->getUser();

        /** @var $orm EntityManager */
        //$orm = $this->get('doctrine')->getManager();
        //$fields = $orm->getClassMetadata(get_class($properties))->getFieldNames();
        //$associations = $orm->getClassMetadata(get_class($properties))->getAssociationNames();

        if(is_object($properties)) {
            if($properties instanceof HttpExceptionInterface) {
                $subject = 'HTTP Error: ' . $properties->getStatusCode();
            }
            elseif ($properties instanceof \Exception) {
                $subject = 'Error: ' . get_class($properties);
            }
            else {
                $subject = 'Message Type: ' . get_class($properties);
            }
        }
        elseif($this->get('request')->get('_controller') == 'StudySauceBundle:Plan:widget') {
            $subject = 'Study plan overlap: ' . $properties['student'];
        }
        else {
            $subject = 'Message from ' . $this->get('request')->get('_controller');
        }

        /** @var \Swift_Mime_SimpleMessage $message */
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo('admin@studysauce.com')
            ->setBody($this->renderView('StudySauceBundle:Emails:administrator.html.php', [
                        'link' => '&nbsp;',
                        'user' => $user,
                        'properties' => self::dump($properties, $properties instanceof \Exception ? 3 : 2)
                    ]), 'text/html' );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['administrator']])));
        $this->sendToAdmin($message);

        return new Response();
    }

    /**
     * @param Swift_Mime_Message $message
     */
    protected function send(\Swift_Mime_Message $message)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        if($this->container->getParameter('defer_all_emails') !== false) {
            $message->setTo($this->container->getParameter('defer_all_emails') ?: 'brian@studysauce.com');
        }

        // check to make sure the limit hasn't been reached
        $to = $message->getTo();
        reset($to);
        $count = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m')
            ->select('COUNT(DISTINCT m.id)')
            ->andWhere('m.message LIKE \'%s:' . (strlen(key($to))) . ':"' . key($to) . '"%\'')
            ->andWhere('m.message LIKE \'%s:' . strlen($message->getHeaders()->get('X-SMTPAPI')->getFieldBody()) . ':"' . $message->getHeaders()->get('X-SMTPAPI')->getFieldBody() . '"%\'')
            ->andWhere('m.created > :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();

        if($count >= 2)
        {
            $message->setSubject('CANCELLED: ' . $message->getSubject());
            $message->setTo($this->container->getParameter('defer_all_emails') ?: 'brian@studysauce.com');
        }

        /** @var \Swift_Mailer $mailer */
        $mailer = $this->get('mailer');
        $mailer->send($message);
    }

    /**
     * @param Swift_Mime_Message $message
     */
    protected function sendToAdmin(\Swift_Mime_Message $message)
    {
        if($this->container->getParameter('defer_all_emails') !== false) {
            $message->setTo($this->container->getParameter('defer_all_emails') ?: 'brian@studysauce.com');
        }
        /** @var \Swift_Transport_EsmtpTransport $transport */
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername('brian@studysauce.com')
            ->setPassword('Da1ddy23');
        /** @var \Swift_Mailer $mailer */
        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->send($message);
    }

    private static $_objects;
    private static $_output;
    private static $_depth;

    /**
     * Converts a variable into a string representation.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as PRADO controls.
     * @param mixed $var variable to be dumped
     * @param integer $depth maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param bool $highlight
     * @return string the string representation of the variable
     */
    public static function dump($var,$depth=10,$highlight=false)
    {
        self::$_output='';
        self::$_objects=[];
        self::$_depth=$depth;
        self::dumpInternal($var,0);
        if($highlight)
        {
            $result=highlight_string("<?php\n".self::$_output,true);
            return preg_replace('/&lt;\\?php<br \\/>/','',$result,1);
        }
        else
            return self::$_output;
    }

    /**
     * @param $var
     * @param $level
     */
    private static function dumpInternal($var,$level)
    {
        switch(gettype($var))
        {
            case 'boolean':
                self::$_output.=$var?'true':'false';
                break;
            case 'integer':
                self::$_output.="$var";
                break;
            case 'double':
                self::$_output.="$var";
                break;
            case 'string':
                self::$_output.="'$var'";
                break;
            case 'resource':
                self::$_output.='{resource}';
                break;
            case 'NULL':
                self::$_output.="null";
                break;
            case 'unknown type':
                self::$_output.='{unknown}';
                break;
            case 'array':
                if(self::$_depth<=$level)
                    self::$_output.='array(...)';
                else if(empty($var))
                    self::$_output.='array()';
                else
                {
                    $keys=array_keys($var);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="array\n".$spaces.'(';
                    foreach($keys as $key)
                    {
                        self::$_output.="\n".$spaces."    [$key] => ";
                        self::dumpInternal($var[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
            case 'object':
                if(($id=array_search($var,self::$_objects,true))!==false)
                    self::$_output.=get_class($var).'#'.($id+1).'(...)';
                else if(self::$_depth<=$level)
                    self::$_output.=get_class($var).'(...)';
                else
                {
                    $id=array_push(self::$_objects,$var);
                    $className=get_class($var);
                    $members=(array)$var;
                    $keys=array_keys($members);
                    $spaces=str_repeat(' ',$level*4);
                    self::$_output.="$className#$id\n".$spaces.'(';
                    foreach($keys as $key)
                    {
                        $keyDisplay=strtr(trim($key),["\0"=>':']);
                        self::$_output.="\n".$spaces."    [$keyDisplay] => ";
                        self::dumpInternal($members[$key],$level+1);
                    }
                    self::$_output.="\n".$spaces.')';
                }
                break;
        }
    }

}