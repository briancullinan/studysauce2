<?php

namespace Admin\Bundle\Controller;

use Codeception\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ValidationController
 * @package StudySauce\Bundle\Controller
 */
class EmailsController extends \StudySauce\Bundle\Controller\EmailsController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $emails = [];
        $templatesDir = new \DirectoryIterator($this->container->getParameter('kernel.root_dir') . '/../src/StudySauce/Bundle/Resources/views/Emails/');
        foreach($templatesDir as $f) {
            /** @var \DirectoryIterator $f */
            if($f->getFilename() == 'layout.html.php')
                continue;
            if(!$f->isDot()) {
                // get count for current email category
                $base = basename($f->getFilename(), '.html.' . $f->getExtension());
                $count = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m')
                    ->select('COUNT(DISTINCT m.id)')
                    ->andWhere('m.message LIKE \'%s:' . (17 + strlen($base)) . ':"{"category":["' . $base . '"]}"%\'')
                    ->getQuery()
                    ->getSingleScalarResult();
                $emails[] = [
                    'id' => $base,
                    'count' => $count
                ];
            }
        }

        $yesterday = new \DateTime('yesterday');
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m');
        $recent = $qb->select('COUNT(DISTINCT m.id)')
            ->andWhere('m.created > :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getSingleScalarResult();


        return $this->render('AdminBundle:Emails:tab.html.php', [
                'emails' => $emails,
                'total' => 0,
                'recent' => $recent
            ]);
    }

    public static $templateVars = [];
    public static $emails = [];
    private static $tables = [];

    /**
     * @param string $_email
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function templateAction($_email = '')
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        self::$tables = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        $fullName = 'StudySauceBundle:Emails:' . $_email . '.html.php';

        // get automatic variables requred for ever send
        $params = [];
        $objects = [];
        $subject = '';
        if($_email == '') {
            return new Response('');
        }

        // look up inputs
        // also check template file for usages
        $templateText = implode("", file($this->getPathFromName($fullName)));
        $reflector = new \ReflectionClass('\StudySauce\Bundle\Controller\EmailsController');
        foreach($reflector->getMethods() as $m)
        {
            $methodText = $this->_getMethodText($m);
            // check if current method has a reference to the template
            if(strpos($methodText, $fullName) !== false) {

                // setup method inputs from function parameters
                foreach($m->getParameters() as $p)
                {
                    $parameterName = $p->getName();
                    $className = !empty($p->getClass()) ? basename($p->getClass()->getFileName(), '.php') : $p->getName();
                    list($classParams, $classObjects) = $this->generateParams($className, $parameterName, $methodText . $templateText);
                    $params = array_merge($params, $classParams);
                    $objects = array_merge($objects, $classObjects);
                }

                if(preg_match('/setSubject\((([\'"]*).*?\2)\)\s*->/i', $methodText, $match)) {
                    $getSubject = function ($vars, $match) {
                        extract($vars);
                        $subject = eval('return ' . $match[1] . ';');
                        return $subject;
                    };
                    $subject = $getSubject($objects, $match);
                }

                // mock send the email
                call_user_func_array([$this, $m->getName()], $objects);
                $template = self::$emails[0];
                break;
            }
        }
        if(!isset($template)) {
            // derive variables from template alone without types
            preg_match_all('/\$([a-z0-9]*)/i', $templateText, $matches);
            foreach(array_unique($matches[1]) as $p) {
                list($classParams, $classObjects) = $this->generateParams($p, $p, $templateText, true);
                $params = array_merge($params, $classParams);
                $objects = array_merge($objects, $classObjects);
            }
            $template = $this->render($fullName, $objects)->getContent();
        }

        return $this->render('AdminBundle:Emails:template.html.php', [
                'template' => $template,
                'params' => $params,
                'objects' => $objects,
                'subject' => $subject
            ]);
    }

    /**
     * @param $template
     * @return mixed
     */
    private function getPathFromName($template)
    {
        $parser = $this->container->get('templating.name_parser');
        $locator = $this->container->get('templating.locator');

        $path = $locator->locate($parser->parse($template));
        return $path;
    }

    /**
     * @param Swift_Message $message
     */
    protected function send(\Swift_Message $message)
    {
        self::$emails[] = $message->getBody();
    }

    /**
     * @param Swift_Message $message
     */
    protected function sendToAdmin(\Swift_Message $message)
    {
        self::$emails[] = $message->getBody();
    }

    protected static $autoInclude = ['user' => ['Email']];
    /**
     * @param $className
     * @param $parameterName
     * @param $subject
     * @param bool $entitiesOnly
     * @return array
     */
    private function generateParams($className, $parameterName, $subject, $entitiesOnly = false)
    {
        $params = [];
        $objects = [];
        // if we are dealing with an entity class try to figure out which methods are used
        if(($classI = array_search(true, array_map(function ($t) use ($className) {
                        return strpos(strtolower($t), strtolower($className)) !== false;
                    }, self::$tables))) !== false) {
            $namespace = explode('\\', self::$tables[$classI]);
            $className = end($namespace);
            $mockName = 'Mock' . $className;
            $instance = 'class ' . $mockName . ' extends ' . self::$tables[$classI] . ' {
';
            preg_match_all('/' . $parameterName . '\s*->\s*get([a-z0-9_]*?)\s*\(/i', $subject, $properties);
            // use the entity for the field if no inputs are detected
            if(!count($properties[1])) {
                $params[$parameterName]['name'] = $className;
                $params[$parameterName]['prop'] = '';
            }
            if(isset(self::$autoInclude[$parameterName])) {
                $properties = array_unique(array_merge(self::$autoInclude[$parameterName], $properties[1]));
                self::$templateVars = array_merge(self::$templateVars, array_map(function ($c) use ($parameterName) {return $parameterName . $c; }, self::$autoInclude[$parameterName]));
            }
            else
                $properties = array_unique($properties[1]);
            foreach($properties as $c)
            {
                $params[$parameterName . $c]['name'] = $className;
                $params[$parameterName . $c]['prop'] = $c;
                if(strpos(strtolower($c), 'email') !== false) {
                    $instance .= 'public function get' . $c . '() { \Admin\Bundle\Controller\EmailsController::$templateVars[] = "' . $parameterName . $c . '"; return "' . $className . '_' . $c . '@mailinator.com"; }
';
                }
                else {
                    $instance .= 'public function get' . $c . '() { \Admin\Bundle\Controller\EmailsController::$templateVars[] = "' . $parameterName . $c . '"; return "{' . $className . ':' . $c . '}"; }
';
                }
            }
            $objects[$parameterName] = eval($instance . '
};
return new ' . $mockName . '();');
        }
        elseif(!$entitiesOnly)
        {
            self::$templateVars[] = $parameterName;
            $params[$parameterName]['name'] = $parameterName;
            $params[$parameterName]['prop'] = '';
            $objects[$parameterName] = '{' . $parameterName . '}';
        }
        return [$params, $objects];
    }

    /**
     * @param \ReflectionMethod $m
     * @return string
     */
    private function _getMethodText(\ReflectionMethod $m)
    {
        // check if current method has a reference to the template
        $line_start     = $m->getStartLine() - 1;
        $line_end       = $m->getEndLine();
        $line_count     = $line_end - $line_start;
        $line_array     = file($m->getFileName());
        $methodText = implode("", array_slice($line_array,$line_start,$line_count));
        return $methodText;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        /** @var EntityManager $orm */
        $orm = $this->getDoctrine()->getManager();
        self::$tables = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        // look up inputs
        // also check template file for usages
        foreach(self::$tables as $t)
        {
            $namespace = explode('\\', $t);
            $parameterName = strtolower(end($namespace));
            if(strpos(strtolower($request->get('field')), $parameterName) !== 0) {
                continue;
            }

            // find setter method that matches field name
            $getters = [];
            $default = '';
            $reflector = new \ReflectionClass($t);
            foreach($reflector->getMethods() as $c) {
                if(substr($c->getName(), 0, 3) == 'set')
                    continue;
                if($request->get('field') == strtolower(end($namespace)) . substr($c->getName(), 3)) {
                    $default = lcfirst(substr($c->getName(), 3));
                    $getters[] = lcfirst(substr($c->getName(), 3));
                }
                if(in_array(strtolower(end($namespace)) . substr($c->getName(), 3), explode(',', $request->get('alt')))) {

                    // search database
                    $getters[] = lcfirst(substr($c->getName(), 3));
                }
            }

            $getters = array_unique($getters);

            // do search
            $search = $orm->getRepository($t)->createQueryBuilder('m')
                ->select(array_map(function ($g) {return 'm.' . $g;}, $getters))
                ->andWhere('m.' . implode(' LIKE \'%' . $request->get('q') . '%\' OR m.', $getters) . ' LIKE \'%' . $request->get('q') . '%\'')
                ->getQuery()
                ->execute();

            return new JsonResponse(array_map(function ($x) use ($default) {return [
                        'text' => $x[$default],
                        'value' => $x[$default]
                    ] + array_values(array_diff_key($x, [$default => '']));
                    }, $search));
        }
        return new JsonResponse([]);
    }
}

