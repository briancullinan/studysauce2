<?php
use Codeception\Configuration;
use Codeception\TestCase\Cest;
use Codeception\Util\Annotation;
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/menu.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/validation.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/validation.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="validation">
        <div class="pane-content">
            <h2>Settings</h2>
            <hr/>
            <label class="input host-setting"><span>Selenium Server</span>
                <input type="text"
                       value="<?php print $view->escape($acceptance['modules']['config']['WebDriver']['host']); ?>"/>
                <small>You must run <a href="http://www.seleniumhq.org/download/">Selenium Server</a> and <a
                        href="https://sites.google.com/a/chromium.org/chromedriver/downloads">ChromeDriver</a> with the
                    command
                    <code>java -jar selenium-server-standalone-2.44.0.jar -Dwebdriver.chrome.driver=.\chromedriver.exe -port
                        4444</code>
                </small>
            </label>
            <label class="input browser-setting"><span>Browser</span>
                <select>
                    <option
                        value="chrome" <?php print ($acceptance['modules']['config']['WebDriver']['browser'] == 'chrome' ? 'checked="checked"' : ''); ?>>
                        Chrome
                    </option>
                    <option
                        value="firefox" <?php print ($acceptance['modules']['config']['WebDriver']['browser'] == 'firefox' ? 'checked="checked"' : ''); ?>>
                        Firefox
                    </option>
                    <option
                        value="ie" <?php print ($acceptance['modules']['config']['WebDriver']['browser'] == 'ie' ? 'checked="checked"' : ''); ?>>
                        Internet Explorer
                    </option>
                </select>
                <small></small>
            </label>
            <label class="input wait-setting"><span>Wait</span>
                <input type="text"
                       value="<?php print $view->escape($acceptance['modules']['config']['WebDriver']['wait']); ?>"/>
                <small>Number of seconds between each step. Some steps require additional wait which will be shown in
                    the results.
                </small>
            </label>
            <label class="input url-setting"><span>StudySauce URL</span>
                <input type="text" value="https://<?php print $view->escape($_SERVER['HTTP_HOST']); ?>"/>
                <small>Path to StudySauce instance to test (e.g. https://staging.studysauce.com or
                    https://test.studysauce.com). WARNING: database changes will occur on the selected instance.
                </small>
            </label>
            <?php foreach ($suites as $i => $suite) { ?>
                <h2><?php print $suite; ?> <small>(<?php print count($tests[$suite]); ?>)</small></h2>
                <div class="suite-actions">
                    <a href="#run-all" class=" suite-<?php print $suite; ?> ">Run</a>
                    <label class="checkbox"><input type="checkbox"><i></i></label>
                </div>
                <hr/>
                <table>
                    <?php foreach ($tests[$suite] as $s => $t) {
                        /** @var Cest $t */
                        $depends = array_map(function ($d) {
                                $test = explode('::', $d);
                                return count($test) == 1 ? $test[0] : $test[1];
                            }, PHPUnit_Util_Test::getDependencies(get_class($t->getTestClass()), $t->getName()));
                        $includes = \Admin\Bundle\Controller\ValidationController::getIncludedTests($t);
                        ?>
                        <tr class=" test-id-<?php print $t->getName();
                        ?> depends-on-<?php print implode(' depends-on-', $depends);
                        ?> includes-<?php print implode(' includes-', $includes);
                        ?> suite-<?php print $suite; ?> ">
                            <td><?php print substr($t->getName(), 3); ?>
                                <?php if (!empty($depends)) { ?>
                                <br /><small>Depends on: <?php print implode(', ', $depends); ?></small>
                                <?php } ?>
                                <?php if (!empty($includes)) { ?>
                                    <br /><small>Includes: <?php print implode(', ', $includes); ?></small>
                                <?php } ?>
                            </td>
                            <td></td>
                            <td>
                                <a href="#run-test">Run</a>
                                <label class="checkbox"><input type="checkbox"><i></i></label>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
