<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Payment;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function welcomePartnerAction(User $user = null)
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
        $mailer = $this->get('mailer');
        $mailer->send($message);

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
        $mailer = $this->get('mailer');
        $mailer->send($message);

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
                        'category' => ['invoice-receipt']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

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
                        'category' => ['marketing-reminder']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param User $student
     * @param $_code
     * @return Response
     */
    public function parentPrepayAction(User $user = null, User $student = null, $_code)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('student_welcome', ['_code' => $_code], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject(($user->getFirst() ?: 'Your parent') . ' has prepaid for your study plan')
            ->setFrom($user->getEmail())
            ->setTo($student->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:prepay.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $student->getFirst() . ' ' . $student->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['parent-prepay']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

        return new Response();
    }

    /**
     * @param User $user
     * @param StudentInvite $student
     * @return Response
     */
    public function adviserInviteAction(User $user = null, StudentInvite $student = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $codeUrl = $this->generateUrl('scholar_welcome', ['_code' => $student->getCode()], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject('Welcome to Study Sauce!')
            ->setFrom($user->getEmail())
            ->setTo($student->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:adviser-invite.html.php', [
                        'user' => $user,
                        'greeting' => 'Hello ' . $student->getFirst() . ' ' . $student->getLast() . ',',
                        'link' => '<a href="' . $codeUrl . '">Go to Study Sauce</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['adviser-invite']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

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
        foreach($reminders as $reminder)
        {
            /** @var Deadline $reminder */
            $classI = array_search($reminder->getCourse(), array_values($courses));

            if($classI === false)
                $classI = -1;

            if($classI == 0)
                $color = '#FF0D00';
            elseif($classI == 1)
                $color = '#FF8900';
            elseif($classI == 2)
                $color = '#FFD700';
            elseif($classI == 3)
                $color = '#BAF300';
            elseif($classI == 4)
                $color = '#2DD700';
            elseif($classI == 5)
                $color = '#009999';
            elseif($classI == 6)
                $color = '#162EAE';
            elseif($classI == 7)
                $color = '#6A0AAB';
            elseif($classI == 8)
                $color = '#BE008A';
            else
                $color = '#DDDDDD';

            $timespan = strtotime($reminder->getDueDate()) - time();
            if(floor($timespan / 86400) + 1 <= 0)
                $days = 'today';
            elseif(floor($timespan / 86400) + 1 > 1)
                $days = floor($timespan / 86400) + 1 . ' days';
            else
                $days = '1 day';

            $reminderOutput .= '<br /><strong>Subject:</strong><br /><span style="height:24px;width:24px;background-color:' . $color . ';display:inline-block;border-radius:100%;border: 3px solid #555555;">&nbsp;</span> ' . (!empty($reminder->getCourse()) ? $reminder->getCourse()->getName() : 'Nonacademic') . '<br /><br /><strong>Assignment:</strong><br />' . $reminder->getAssignment() . '<br /><br /><strong>Days until due date:</strong><br />' . $days . '<br /><br />';
            if(array_search($reminder->getCourse(), $classes) === false)
                $classes[] = $reminder->getCourse();

            // save the sent status of the reminder
            foreach([86400,172800,345600,604800,1209600] as $i => $t)
            {
                if($timespan - $t <= 0)
                {
                    $sent = $reminder->getReminderSent();
                    $sent[] = $t;
                    $reminder->setReminderSent($sent);
                    $orm->merge($reminder);
                    $orm->flush();
                    break;
                }
            }
        }

        $codeUrl = $this->generateUrl('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $message = Swift_Message::newInstance()
            ->setSubject('You have a notification for ' . implode(', ', $classes))
            ->setFrom('admin@studysauce.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderView('StudySauceBundle:Emails:deadline-reminder.html.php', [
                        'user' => $user,
                        'greeting' => 'Hi ' . $user->getFirst() . ',',
                        'link' => '<a href="' . $codeUrl . '">Click here to log in to Study Sauce and edit your deadlines</a>'
                    ]), 'text/html');
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['reminders']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

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
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

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
                        'category' => ['parent-invite']])));
        $mailer = $this->get('mailer');
        $mailer->send($message);

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

        if(is_object($properties))
            $from = get_class($properties);
        else
            $from = $this->get('request')->get('_controller');

        /** @var \Swift_Mime_SimpleMessage $message */
        $message = Swift_Message::newInstance()
            ->setSubject('Message from ' . $from)
            ->setFrom(!empty($user) ? $user->getEmail() : 'guest@studysauce.com')
            ->setTo('admin@studysauce.com')
            ->setBody(
                $this->renderView(
                    'StudySauceBundle:Emails:administrator.html.php',
                    [
                        'link' => '&nbsp;',
                        'user' => $user,
                        'properties' => self::dump($properties, $properties instanceof \Exception ? 3 : 2)
                    ]
                ),
                'text/html'
            );
        $headers = $message->getHeaders();
        $headers->addParameterizedHeader('X-SMTPAPI', preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode([
                        'category' => ['administrator']])));

        /** @var \Swift_Transport_EsmtpTransport $transport */
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername('brian@studysauce.com')
            ->setPassword('Da1ddy23');
        /** @var \Swift_Mailer $mailer */
        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->send($message);

        return new Response();
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