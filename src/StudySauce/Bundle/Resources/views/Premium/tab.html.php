<?php

use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/premium.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/premium.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('check');
foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/check.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
    <img width="16" height="16" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="premium">
    <div class="pane-content">
        <h2>Choose your plan: 30 day money back guarantee on all accounts</h2>
        <div class="plans-chart">
            <table cellpadding="0" cellspacing="0" width="100%" class="plans">
                <tbody><tr>
                    <td class="blank" width="150">&nbsp;</td>
                    <td class="blank" width="25%">&nbsp;</td>
                    <td class="best highlighted-link"></td>
                    <td class="blank" width="25%">&nbsp;</td>
                </tr>
                <tr>
                    <th class="pick-text">&nbsp;</th>
                    <th><h2>Basic</h2>
                        <p class="highlighted-link"><a href="<?php print $view['router']->generate('home'); ?>" class="more">Go home</a><br>
                            <span class="price">Free</span></p>
                    </th>
                    <th class="highlight"><h2>Premium</h2>
                        <p class="highlighted-link"><a href="<?php print $view['router']->generate('checkout'); ?>" class="more">Choose plan</a><br>
                            <span class="price">$9.99/month</span></p>
                    </th>
                    <th><h2>Enterprise</h2>
                        <p class="highlighted-link"><a href="#schedule-demo" class="more" data-toggle="modal">Schedule a demo</a><br>
                            <span class="price">Contact us</span></p>
                    </th>
                </tr>
                <tr>
                    <td class="feature">Basic study course</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Streaming classical music</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Deadline reminders</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Study metrics</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Study goals</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Accountability partner</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Advanced study course</td>
                    <td>&nbsp;</td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Customized study plan</td>
                    <td>&nbsp;</td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Personal study profile</td>
                    <td>&nbsp;</td>
                    <td class="highlight"><?php $view['slots']->output('check'); ?></td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Advise multiple students</td>
                    <td>&nbsp;</td>
                    <td class="highlight">&nbsp;</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Custom student messaging</td>
                    <td>&nbsp;</td>
                    <td class="highlight">&nbsp;</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                <tr>
                    <td class="feature">Supplementary programs</td>
                    <td>&nbsp;</td>
                    <td class="highlight">&nbsp;</td>
                    <td><?php $view['slots']->output('check'); ?></td>
                </tr>
                </tbody>
            </table>
            <div class="plans">
                <div class="basic">
                    <h2>Basic</h2>
                    <p class="highlighted-link"><a href="#home" class="more">Go home</a><br>
                        <span class="price">Free</span></p>

                    <p>Deadline reminders <?php $view['slots']->output('check'); ?></p>
                    <p>Streaming classical music <?php $view['slots']->output('check'); ?></p>
                    <p>Personalized study tips <?php $view['slots']->output('check'); ?></p>
                    <p>Study metrics <?php $view['slots']->output('check'); ?></p>
                    <p>Study goals <?php $view['slots']->output('check'); ?></p>
                    <p>Accountability partner <?php $view['slots']->output('check'); ?></p>
                </div>
                <div class="premium">
                    <h2>Premium</h2>
                    <p class="highlighted-link"><a href="<?php print $view['router']->generate('checkout'); ?>" class="more">Choose plan</a><br>
                        <span class="price">$9.99/month</span></p>

                    <p>All of the above plus: <?php $view['slots']->output('check'); ?></p>
                    <p>Customized study plan <?php $view['slots']->output('check'); ?></p>
                    <p>Guided study sessions <?php $view['slots']->output('check'); ?></p>
                    <p>Personal study profile <?php $view['slots']->output('check'); ?></p>
                </div>
                <div class="enterprise">
                    <h2>Enterprise</h2>
                    <p class="highlighted-link"><a href="#schedule-demo" class="more" data-toggle="modal">Schedule a demo</a><br>
                        <span class="price">Contact us</span></p>

                    <p>All of the above plus: <?php $view['slots']->output('check'); ?></p>
                    <p>Advise multiple students <?php $view['slots']->output('check'); ?></p>
                    <p>Custom student messaging <?php $view['slots']->output('check'); ?></p>
                    <p>Supplementary programs <?php $view['slots']->output('check'); ?></p>
                </div>
            </div>
        </div>
        <div class="did-you-know">
            <div class="did-you-know-container">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/did_you_know_620x310.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="165" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?>
                <h1>Your parents can sponsor you.</h1>
                <div class="one highlighted-link"><a class="more parents" href="#bill-parents" data-toggle="modal">Bill my parents</a></div>
            </div>
        </div>
        <h2>Give it a shot, Study Sauce will help you...</h2>
        <div class="three-step-process">
            <ol>
                <li>
                    <h3>Learn how to learn</h3>
                    <strong>1</strong>
                    <p>Chances are no one ever taught you how to study.  Take our study profile assessment to discover your unique study style.</p>
                </li>
                <li>
                    <h3>Stop procrastinating</h3>
                    <strong>2</strong>
                    <p>Get organized and stop stressing out before midterms.  Let us build your personalized study plan, so you know what to study and when.</p>
                </li>
                <li>
                    <h3>Be more effective</h3>
                    <strong>3</strong>
                    <p>Use the best study strategy every time you study.  We tell you how to study based on your unique profile and the specific requirements of the class.</p>
                </li>
            </ol>
        </div>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:scheduleDemo'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents1'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents2'), ['strategy' => 'sinclude']);
$view['slots']->stop();
