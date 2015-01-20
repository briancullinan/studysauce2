<?php

namespace Admin\Bundle\Controller;

use Codeception\Codecept;
use Codeception\Configuration;
use Codeception\Event\StepEvent;
use Codeception\Event\SuiteEvent;
use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\PHPUnit\Listener;
use Codeception\PHPUnit\ResultPrinter\HTML;
use Codeception\PHPUnit\ResultPrinter\UI;
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
use Doctrine\Common\Annotations\AnnotationRegistry;
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

        $suites = Configuration::suites();
        $tests = [];
        foreach ($suites as $suite) {
            $settings = Configuration::suiteSettings($suite, Configuration::config());
            $testLoader = new TestLoader($settings['path']);
            $testLoader->loadTests();
            $tests[$suite] = $testLoader->getTests();
        }

        return $this->render('AdminBundle:Validation:index.html.php', [
                'suites' => $suites,
                'tests' => $tests
            ]);
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
            $test = $request->get('test');
            foreach($testLoader->getTests() as $i => $t) {
                /** @var Cest $t */
                if($t->getName() == $test) {
                    $options = ['filter' => $test, 'verbosity' => 3, 'colors' => false];

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

                    $suiteManager = new SuiteManager($dispatcher, $suite, $settings);
                    $suiteManager->initialize();
                    $suiteManager->getSuite()->setBackupGlobals(false);
                    $suiteManager->getSuite()->setBackupStaticAttributes(false);
                    $suiteManager->loadTests($t->getFileName());
                    $suiteManager->run($runner, $result, $options);
                    $dispatcher->dispatch(Events::TEST_AFTER, new TestEvent($t));
                    $dispatcher->dispatch(Events::SUITE_AFTER, new SuiteEvent($suiteManager->getSuite()));
                    break;
                }

            }

        }

        return new JsonResponse(isset($result) && isset($printer)
                ? ['result' => $output, 'errors' => $result->errorCount()
                    ? $result->errors()[0]->thrownException() . ''
                    : null]
                : true);
    }

}


