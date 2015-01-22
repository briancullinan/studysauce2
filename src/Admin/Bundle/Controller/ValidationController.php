<?php

namespace Admin\Bundle\Controller;

use Codeception\Configuration;
use Codeception\Event\StepEvent;
use Codeception\Events;
use Codeception\Module\Symfony2;
use Codeception\PHPUnit\Listener;
use Codeception\PHPUnit\ResultPrinter\HTML;
use Codeception\PHPUnit\Runner;
use Codeception\Scenario;
use Codeception\Subscriber\AutoRebuild;
use Codeception\Subscriber\BeforeAfterTest;
use Codeception\Subscriber\Bootstrap;
use Codeception\Subscriber\ErrorHandler;
use Codeception\Subscriber\Module;
use Codeception\SuiteManager;
use Codeception\TestCase\Cest;
use Codeception\TestLoader;
use Doctrine\ORM\Query;
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

        $config = [
            'suites' => Configuration::suites(),
            'tests' => []
        ];
        foreach ($config['suites'] as $suite) {
            $config[$suite] = Configuration::suiteSettings($suite, Configuration::config());
            $testLoader = new TestLoader($config[$suite]['path']);
            $testLoader->loadTests();
            $config['tests'][$suite] = $testLoader->getTests();
        }

        return $this->render('AdminBundle:Validation:index.html.php', $config);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function testAction(Request $request) {
        set_time_limit(0);

        require_once(__DIR__ . '/../../../../vendor/codeception/codeception/autoload.php');
        Configuration::config(__DIR__ . '/../');

        $suites = Configuration::suites();
        $output = '';
        if(in_array($suite = $request->get('suite'), $suites)) {
            $settings = Configuration::suiteSettings($suite, Configuration::config());
            $testLoader = new TestLoader($settings['path']);
            $testLoader->loadTests();
            // get the path of the test
            $options = ['verbosity' => 3, 'colors' => false];
            foreach($testLoader->getTests() as $i => $t) {
                /** @var Cest $t */
                if ($t->getName() == $request->get('test')) {
                    $options['filter'] = $request->get('test');
                    break;
                }
            }


            /** @var EventDispatcher $dispatcher */
            $dispatcher = new EventDispatcher();
            // required
            $dispatcher->addSubscriber(new ErrorHandler());
            $dispatcher->addSubscriber(new Bootstrap());
            $dispatcher->addSubscriber(new Module());
            $dispatcher->addSubscriber(new BeforeAfterTest());
            $dispatcher->addSubscriber(new AutoRebuild());

            $scenario = null;
            $feature = '';
            $dispatcher->addListener(Events::STEP_BEFORE, function (StepEvent $x) use (&$output, &$scenario, &$feature) {
                    /** @var Scenario $scenario */
                    if(($scenario = $x->getTest()->getScenario()) && $scenario->getFeature() != $feature) {
                        $feature = $scenario->getFeature();
                        $output .= '<h3>I want to ' . $feature . '</h3>';
                    }
                    $output .= 'I <strong>' . $x->getStep()->getAction() . '</strong> ' . $x->getStep()->getArguments(true) . '<br />';
                });
            $dispatcher->addListener(Events::SUITE_AFTER, function ($x, $i, $j) {
                    $v = '';
                });
            $result = new \PHPUnit_Framework_TestResult;
            $result->addListener(new Listener($dispatcher));
            $runner = new Runner($options);
            $printer = new HTML($dispatcher, $options);
            $runner->setPrinter($printer);

            // don't initialize Symfony2 module because we are already running and will feed it the right parameters
            if(($i = array_search('Symfony2', $settings['modules']['enabled'])) !== false)
                unset($settings['modules']['enabled'][$i]);

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

            $suiteManager = new SuiteManager($dispatcher, $suite, $settings);
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

        return new JsonResponse(isset($result) && isset($printer)
                ? ['result' => $output, 'errors' => $result->errorCount()
                    ? $result->errors()[0]->thrownException() . ''
                    : null]
                : true);
    }

}


