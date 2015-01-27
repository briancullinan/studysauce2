<?php

namespace Admin\Bundle\Controller;

use Codeception\Configuration;
use Codeception\Event\FailEvent;
use Codeception\Event\StepEvent;
use Codeception\Events;
use Codeception\Module\Symfony2;
use Codeception\Module\WebDriver;
use Codeception\PHPUnit\Listener;
use Codeception\PHPUnit\ResultPrinter\UI;
use Codeception\PHPUnit\Runner;
use Codeception\Scenario;
use Codeception\Subscriber\AutoRebuild;
use Codeception\Subscriber\BeforeAfterTest;
use Codeception\Subscriber\Bootstrap;
use Codeception\Subscriber\ErrorHandler;
use Codeception\Subscriber\Module;
use Codeception\SuiteManager;
use Codeception\TestCase;
use Codeception\TestCase\Cest;
use Codeception\TestLoader;
use Doctrine\ORM\Query;
use PHPUnit_Framework_TestFailure;
use PHPUnit_Util_Test;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ValidationController
 * @package StudySauce\Bundle\Controller
 */
class ValidationController extends Controller
{

    public static $dispatcher;
    private static $config = [];

    /**
     * @return Response
     */
    public function indexAction()
    {

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        Configuration::config(__DIR__ . '/../');

        self::$config = [
            'suites' => Configuration::suites(),
            'tests' => []
        ];
        foreach (self::$config['suites'] as $suite) {
            self::$config[$suite] = Configuration::suiteSettings($suite, Configuration::config());
            $testLoader = new TestLoader(self::$config[$suite]['path']);
            $testLoader->loadTests();
            self::$config['tests'][$suite] = $testLoader->getTests();
        }

        return $this->render('AdminBundle:Validation:index.html.php', self::$config);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function testAction(Request $request) {
        set_time_limit(0);
        if(!defined('PHPUNIT_TESTSUITE'))
        {
            define('PHPUNIT_TESTSUITE', true);
        }

        require_once(__DIR__ . '/../../../../vendor/codeception/codeception/autoload.php');
        Configuration::config(__DIR__ . '/../');

        $suites = Configuration::suites();
        $steps = [];
        if(in_array($suite = $request->get('suite'), $suites)) {
            $settings = Configuration::suiteSettings($suite, Configuration::config());
            $testLoader = new TestLoader($settings['path']);
            $testLoader->loadTests();
            // get the path of the test
            $options = ['verbosity' => 3, 'colors' => false];
            if(!empty($request->get('test'))) {
                $tests = explode('|', $request->get('test'));
                foreach($testLoader->getTests() as $i => $t) {
                    /** @var Cest $t */
                    if (in_array($t->getName(), $tests)) {
                        // automatically include dependencies
                        $depends = PHPUnit_Util_Test::getDependencies(get_class($t->getTestClass()), $t->getName());
                        $options['filter'] = $request->get('test') . (count($depends) ? ('|' . implode('|', $depends)) : '');
                        break;
                    }
                }
                if(!isset($options['filter']))
                    return new JsonResponse(true);
            }

            // set customized settings
            if(!empty($request->get('host'))) {
                $settings['modules']['config']['WebDriver']['host'] = $request->get('host');
            }
            if(!empty($request->get('browser'))) {
                $settings['modules']['config']['WebDriver']['browser'] = $request->get('browser');
            }
            if(!empty($request->get('wait'))) {
                $settings['modules']['config']['WebDriver']['wait'] = $request->get('wait');
            }
            if(!empty($request->get('url'))) {
                $settings['modules']['config']['WebDriver']['url'] = $request->get('url');
            }


            /** @var EventDispatcher self::$dispatcher */
            self::$dispatcher = new EventDispatcher();
            // required
            self::$dispatcher->addSubscriber(new ErrorHandler());
            self::$dispatcher->addSubscriber(new Bootstrap());
            self::$dispatcher->addSubscriber(new Module());
            self::$dispatcher->addSubscriber(new BeforeAfterTest());
            self::$dispatcher->addSubscriber(new AutoRebuild());

            $scenario = null;
            $feature = '';
            $screenDir = $this->container->getParameter('kernel.root_dir') . '/../web/bundles/admin/results/';
            self::$dispatcher->addListener(Events::STEP_BEFORE, function (StepEvent $x) use (&$steps, &$scenario, &$feature) {
                    if (!isset($steps[$x->getTest()->getName()])) {
                        $steps[$x->getTest()->getName()] = '';
                    }
                    /** @var Scenario $scenario */
                    if (($scenario = $x->getTest()->getScenario()) && $scenario->getFeature() != $feature) {
                        $feature = $scenario->getFeature();
                        $steps[$x->getTest()->getName()] .= '<h3>I want to ' . $feature . '</h3>';
                    }
                });
            self::$dispatcher->addListener(Events::STEP_AFTER, function (StepEvent $x) use (&$steps, &$scenario, &$feature) {
                    // check for failures
                    //$x->getTest()->getTestResultObject()->failures()
                    if($x->getStep()->getAction() == 'wait')
                        $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep()->getAction() . '</strong> ' . $x->getStep()->getArguments(true) . ' seconds</span>';
                    else
                        $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep()->getAction() . '</strong> ' . $x->getStep()->getArguments(true) . '</span>';
                });
            self::$dispatcher->addListener(Events::TEST_ERROR, function (FailEvent $x, $y, $z) use (&$steps, $screenDir) {
                    $ss = 'TestFailure' . substr(md5(microtime()), -5);
                    $steps[$x->getTest()->getName()] .= '<pre class="error">' . $x->getFail()->getMessage() . '<br />
                    <a href="/bundles/admin/results/' . $ss . '.png"><img width="200" src="/bundles/admin/results/' . $ss . '.png" /></a></pre>';
                    // try to get a screenshot to show in the browser
                    /** @var WebDriver $driver */
                    $driver = SuiteManager::$modules['WebDriver'];
                    $driver->makeScreenshot($ss);
                    rename(codecept_log_dir() . 'debug' . DIRECTORY_SEPARATOR . $ss . '.png', $screenDir . $ss . '.png');
                });
            self::$dispatcher->addListener(Events::TEST_FAIL, function (FailEvent $x, $y, $z) use (&$steps, $screenDir) {
                    $ss = 'TestFailure' . substr(md5(microtime()), -5);
                    $steps[$x->getTest()->getName()] .= '<pre class="failure">' . $x->getFail()->getMessage() . '<br />
                    <a href="/bundles/admin/results/' . $ss . '.png"><img width="200" src="/bundles/admin/results/' . $ss . '.png" /></a></pre>';
                    // try to get a screenshot to show in the browser
                    /** @var WebDriver $driver */
                    $driver = SuiteManager::$modules['WebDriver'];
                    $driver->makeScreenshot($ss);
                    rename(codecept_log_dir() . 'debug' . DIRECTORY_SEPARATOR . $ss . '.png', $screenDir . $ss . '.png');
                });

            $result = new \PHPUnit_Framework_TestResult;
            $result->addListener(new Listener(self::$dispatcher));
            $runner = new Runner($options);
            $printer = new UI(self::$dispatcher, $options);
            $runner->setPrinter($printer);

            // don't initialize Symfony2 module because we are already running and will feed it the right parameters
            if(($i = array_search('Symfony2', $settings['modules']['enabled'])) !== false)
                unset($settings['modules']['enabled'][$i]);

            $suiteManager = new SuiteManager(self::$dispatcher, $suite, $settings);
            $suiteManager->initialize();
            // add Symfony2 module back in without initializing, setting the correct kernel for the current instance
            $settings['modules']['enabled'] = ['Symfony2'];
            /** @var Symfony2 $symfony */
            $symfony = Configuration::modules($settings)['Symfony2'];
            SuiteManager::$modules['Symfony2'] = $symfony;
            $symfony->kernel = $this->container->get( 'kernel' );
            $suiteManager->getSuite()->setBackupGlobals(false);
            $suiteManager->getSuite()->setBackupStaticAttributes(false);
            $suiteManager->loadTests(isset($t) ? $t->getFileName() : null);
            $suiteManager->run($runner, $result, $options);
        }
        if(isset($result) && isset($runner)) {
            $result->flushListeners();
            $printer = $runner->getPrinter();
            $errors = [];
            foreach($result->errors() as $e)
            {
                /** @var PHPUnit_Framework_TestFailure $e */
                $errors[] = $e->thrownException();
            }
            return $this->render('AdminBundle:Validation:results.html.php', [
                    'printer' => $printer,
                    'result' => $result,
                    'errors' => $errors,
                    'steps' => $steps
                ]);
        }

        return new JsonResponse(true);
    }

    /**
     * @param Cest $test
     * @return array
     */
    public static function getIncludedTests(Cest $test)
    {
        $tests = [];
        // get function code
        $reflector = new \ReflectionClass(get_class($test->getTestClass()));
        if($reflector->hasMethod($test->getName())) {
            $method = $reflector->getMethod($test->getName());
            $line_start     =$method->getStartLine() - 1;
            $line_end       =$method->getEndLine();
            $line_count     =$line_end - $line_start;
            $line_array     =file($method->getFileName());
            $text = implode("", array_slice($line_array,$line_start,$line_count));

            // find calls to other functions in the class in this test
            $allTests = '';
            foreach(self::$config['tests'] as $suite)
            {
                $allTests .= implode('|', array_map(function (Cest $t) { return $t->getName(); }, $suite));
            }
            preg_match_all('/' . $allTests . '/i', $text, $matches);
            foreach($matches[0] as $i => $m) {
                if($m == $test->getName())
                    continue;
                $tests[] = $m;
            }
        }
        return $tests;
    }

}


