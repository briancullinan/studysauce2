<?php
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var $partner Partner */
$permissions = !empty($partner) ? $partner->getPermissions() : [
    'goals',
    'metrics',
    'deadlines',
    'uploads',
    'plan',
    'profile'
];

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/userlist.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/userlist.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="userlist">
    <div class="pane-content">
        <div id="select-status" style="display: none;">
            <a href="#green"><span>&nbsp;</span></a>
            <a href="#yellow"><span>&nbsp;</span></a>
            <a href="#red"><span>&nbsp;</span></a></div>
        <table>
            <thead>
            <tr>
                <th><select>
                        <option>Status</option>
                        <option>Ascending</option>
                        <option>Descending</option>
                        <option>Red</option>
                        <option>Yellow</option>
                        <option>Green</option>
                    </select></th>
                <th><select>
                        <option>Date</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                        <option>28-Oct</option>
                        <option>26-Oct</option>
                        <option>25-Oct</option>
                        <option>21-Oct</option>
                        <option>18-Oct</option>
                        <option>15-Oct</option>
                        <option>14-Oct</option>
                        <option>13-Oct</option>
                        <option>11-Oct</option>
                        <option>09-Oct</option>
                        <option>08-Oct</option>
                        <option>07-Oct</option>
                    </select></th>
                <th><select>
                        <option>Student</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                        <option>Denice Carpenter</option>
                        <option>Rahim Karim</option>
                        <option>Uyen Tran</option>
                    </select></th>
                <th><select>
                        <option>School</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                        <option>The University of Texas at Austin</option>
                        <option>University of Texas at Austin</option>
                    </select></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($users as $i => $u)
            {
                /** @var User $u */
                ?>
                <tr class="uid<?php print $u->getId(); ?> status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1414504085">28-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Rahim Karim</a></td>
                <td>University of Texas at Austin</td>
                </tr>
            <?php } ?>

            <tr class="uid2288 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1414385389">26-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Rahim Karim</a></td>
                <td>University of Texas at Austin</td>
            </tr>
            <tr class="uid2288 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1414295574">25-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Rahim Karim</a></td>
                <td>University of Texas at Austin</td>
            </tr>
            <tr class="uid2288 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1413938606">21-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Rahim Karim</a></td>
                <td>University of Texas at Austin</td>
            </tr>
            <tr class="uid2289 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1413651602">18-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Denice Carpenter</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2289 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1413419023">15-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Denice Carpenter</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2289 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1413415251">15-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Denice Carpenter</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2290 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1413319254">14-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Uyen Tran</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2289 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1413251544">13-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Denice Carpenter</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2290 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1413094198">11-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Uyen Tran</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2290 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1412914709">09-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Uyen Tran</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2290 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1412816367">08-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Uyen Tran</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2290 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1412794154">08-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Uyen Tran</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2289 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1412726785">07-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Denice Carpenter</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2290 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1412694965">07-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Uyen Tran</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2289 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1412694955">07-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Denice Carpenter</a></td>
                <td>The University of Texas at Austin</td>
            </tr>
            <tr class="uid2288 status_green">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-actual="1412694862">07-Oct</td>
                <td><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => 2]); ?>">Rahim Karim</a></td>
                <td>University of Texas at Austin</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice1'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice2'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice3'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice4'),['strategy' => 'sinclude']);
$view['slots']->stop();
