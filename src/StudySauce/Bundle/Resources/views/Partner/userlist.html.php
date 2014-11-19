<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\Schedule;
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
                    </select></th>
                <th><select>
                        <option>Student</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                    </select></th>
                <th><select>
                        <option>School</option>
                        <option>Ascending (A-Z)</option>
                        <option>Descending (Z-A)</option>
                    </select></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($users as $i => $u)
            {
                /** @var User $u */
                if($u->hasRole('ROLE_ADVISER'))
                    continue;

                /** @var Schedule $schedule */
                $schedule = $u->getSchedules()->first();
                ?>
                <tr class="user-id-<?php print $u->getId(); ?> status_<?php print ($u->getProperty('adviser_status') ?: 'green'); ?>">
                    <td><a href="#change-status"><span>&nbsp;</span></a></td>
                    <td data-timestamp="<?php print (empty($u->getLastLogin()) ? $u->getCreated()->getTimestamp() : $u->getLastLogin()->getTimestamp()); ?>"><?php print (empty($u->getLastLogin()) ? $u->getCreated()->format('j M y') : $u->getLastLogin()->format('j M y')); ?></td>
                    <td><a href="<?php print $view['router']->generate('adviser', ['_user' => $u->getId(), '_tab' => 'metrics']); ?>"><?php print $u->getFirst() . ' ' . $u->getLast(); ?></a></td>
                    <td><?php print (!empty($schedule) ? $schedule->getUniversity() : 'Not set'); ?></td>
                </tr><?php
            } ?>
            </tbody>
        </table>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice1'));
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice2'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice3'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice4'),['strategy' => 'sinclude']);
$view['slots']->stop();
