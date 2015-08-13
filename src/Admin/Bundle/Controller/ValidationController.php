<?php

namespace Admin\Bundle\Controller;

use Codeception\Configuration;
use Codeception\Event\FailEvent;
use Codeception\Event\StepEvent;
use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Module\Doctrine2;
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
use PHP_Timer;
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

    private static function setupThis()
    {

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

    }

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

        self::setupThis();

        return $this->render('AdminBundle:Validation:tab.html.php', self::$config);
    }

    /**
     * @param $allTests
     * @param $tests
     * @param int $level
     * @return array
     */
    private static function getTestDependencies($allTests, $tests, $level = 1)
    {
        $dependencies = [];
        if($level <= 0)
            return $dependencies;
        foreach($allTests as $i => $t) {
            /** @var Cest $t */
            if (in_array($t->getName(), $tests)) {
                // automatically include dependencies
                $depends = array_map(function ($d) {
                    $test = explode('::', $d);
                    return count($test) == 1 ? $test[0] : $test[1];
                }, PHPUnit_Util_Test::getDependencies(get_class($t->getTestClass()), $t->getName()));
                $dependencies = array_merge(
                    array_merge($dependencies, $depends),
                    self::getTestDependencies($allTests, self::getIncludedTests($t), $level-1));
            }
        }
        return $dependencies;
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

        self::setupThis();

        $steps = [];
        if(!empty($settings = self::$config[$suite = $request->get('suite')])) {
            // get the path of the test
            $options = ['verbosity' => 3, 'colors' => false];
            if(!empty($request->get('test'))) {
                $tests = explode('|', $request->get('test'));
                $depends = self::getTestDependencies(self::$config['tests'][$suite], $tests);
                $tests = array_merge($tests, $depends);
                $options['filter'] = implode('|', array_unique($tests));
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

            $features = [];
            $screenDir = $this->container->getParameter('kernel.root_dir') . '/../web/bundles/admin/results/';
            self::$dispatcher->addListener(Events::STEP_BEFORE, function (StepEvent $x) use (&$steps, &$features) {
                    /** @var Scenario $scenario */
                    if (($scenario = $x->getTest()->getScenario()) && $scenario->getFeature() != end($features)) {
                        $steps[$x->getTest()->getName()] .= '<h3>I want to ' . $scenario->getFeature() . '</h3>';
                        array_push($features, $scenario->getFeature());
                    }
                });
            self::$dispatcher->addListener(Events::TEST_BEFORE, function (TestEvent $x) use (&$steps, &$features) {
                    if (!isset($steps[$x->getTest()->getName()])) {
                        $steps[$x->getTest()->getName()] = '';
                    }
                    array_push($features, end($features));
                });
            self::$dispatcher->addListener(Events::TEST_AFTER, function (TestEvent $x) use (&$features) {
                    array_pop($features);
                });
            self::$dispatcher->addListener(Events::STEP_AFTER, function (StepEvent $x) use (&$steps) {
                    // look for javascript errors
                    if(isset(SuiteManager::$modules['WebDriver'])) {
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        $jsErrors = $driver->executeJS('return (function () {var tmpErrors = window.jsErrors; window.jsErrors = []; return tmpErrors || [];})();');
                        try {
                            $x->getTest()->assertEmpty($jsErrors, 'Javascript errors: ' . (is_array($jsErrors) ? implode($jsErrors) : $jsErrors));
                        }
                        catch(\PHPUnit_Framework_AssertionFailedError $e) {
                            $x->getTest()->getTestResultObject()->addFailure($x->getTest(), $e, PHP_Timer::stop());
                        }
                   }

                    // check for failures
                    //$x->getTest()->getTestResultObject()->failures()
                    if($x->getStep()->getAction() == 'wait')
                        $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep()->getAction() . '</strong> ' . str_replace('"', '', $x->getStep()->getArguments(true)) . ' seconds</span>';
                    else
                        $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep()->getAction() . '</strong> ' . str_replace('"', '', $x->getStep()->getArguments(true)) . '</span>';
                });
            self::$dispatcher->addListener(Events::TEST_ERROR, function (FailEvent $x, $y, $z) use (&$steps, $screenDir) {
                    $ss = 'TestFailure' . substr(md5(microtime()), -5);
                    $steps[$x->getTest()->getName()] .= '<pre class="error">' . htmlspecialchars($x->getFail()->getMessage(), ENT_QUOTES);
                    // try to get a screenshot to show in the browser
                    if(isset(SuiteManager::$modules['WebDriver'])) {
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        $driver->makeScreenshot($ss);
                        rename(
                            codecept_log_dir() . 'debug' . DIRECTORY_SEPARATOR . $ss . '.png',
                            $screenDir . $ss . '.png'
                        );
                        $steps[$x->getTest()->getName()] .= '<br /><a target="_blank" href="/bundles/admin/results/' .
                            $ss . '.png"><img width="200" src="/bundles/admin/results/' . $ss . '.png" /></a>';
                    }
                    $steps[$x->getTest()->getName()] .= '</pre>';
                });
            self::$dispatcher->addListener(Events::TEST_FAIL, function (FailEvent $x, $y, $z) use (&$steps, $screenDir) {
                    $ss = 'TestFailure' . substr(md5(microtime()), -5);
                    $steps[$x->getTest()->getName()] .= '<pre class="failure">' . htmlspecialchars($x->getFail()->getMessage(), ENT_QUOTES);
                    // try to get a screenshot to show in the browser
                    if(isset(SuiteManager::$modules['WebDriver'])) {
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        $driver->makeScreenshot($ss);
                        rename(
                            codecept_log_dir() . 'debug' . DIRECTORY_SEPARATOR . $ss . '.png',
                            $screenDir . $ss . '.png'
                        );
                        $steps[$x->getTest()->getName()] .= '<br /><a target="_blank" href="/bundles/admin/results/' .
                            $ss . '.png"><img width="200" src="/bundles/admin/results/' . $ss . '.png" /></a>';
                    }
                    $steps[$x->getTest()->getName()] .= '</pre>';
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
            $suiteManager->loadTests(null);
            Doctrine2::$em = $this->get('doctrine')->getManager();
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
        // get a list of all tests
        $allTests = '';
        foreach(self::$config['tests'] as $suite)
        {
            $allTests .= (!empty($allTests) ? '|' : '') . implode('|', array_map(function (Cest $t) { return $t->getName(); }, $suite));
        }

        $tests = [];
        // get function code
        $reflector = new \ReflectionClass(get_class($test->getTestClass()));
        if($reflector->hasMethod($test->getName())) {
            $method = $reflector->getMethod($test->getName());
            $line_start     = $method->getStartLine() - 1;
            $line_end       = $method->getEndLine();
            $line_count     = $line_end - $line_start;
            $line_array     = file($method->getFileName());
            $text = implode("", array_slice($line_array,$line_start,$line_count));

            // find calls to other functions in the class in this test
            preg_match_all('/' . $allTests . '/i', $text, $matches);
            foreach($matches[0] as $i => $m) {
                if($m == $test->getName())
                    continue;
                $tests[] = $m;
            }
        }
        return array_unique($tests);
    }

}


