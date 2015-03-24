<?php
use Course1\Bundle\Course1Bundle;
use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Course2Bundle;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Course3Bundle;
use Course3\Bundle\Entity\Course3;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var User $user */
$user = $app->getUser();
/** @var $partner PartnerInvite */
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
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/userlist.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/userlist.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
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
        <table class="<?php print ($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count() > 1 ? 'master' : ''); ?>">
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
                        <option>Completion</option>
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
                <?php if($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count() > 1) { ?>
                    <th><select>
                            <option>Adviser</option>
                            <option>Ascending (A-Z)</option>
                            <option>Descending (Z-A)</option>
                        </select></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($sessions as $i => $s)
            {
                /** @var Visit $s */
                $parts = explode('/', $s->getPath());
                $path = implode('', $parts);
                /** @var Schedule $schedule */
                $schedule = $s->getUser()->getSchedules()->first();
                /** @var User $adviser */
                $adviser = $s->getUser()->getGroups()->map(function (Group $g) { return $g->getUsers()->filter(function (User $u) {return $u->hasRole('ROLE_ADVISER');})->first();})->first();
                if(!empty($adviser))
                    $adviser = $s->getUser()->getGroups()->map(function (Group $g) { return $g->getUsers()->filter(function (User $u) {return $u->hasRole('ROLE_MASTER_ADVISER');})->first();})->first();
                ?><tr class="user-id-<?php print $s->getUser()->getId(); ?> status_<?php print ($s->getUser()->getProperty('adviser_status') ?: 'green'); ?>">
                <td><a href="#change-status"><span>&nbsp;</span></a></td>
                <td data-timestamp="<?php print $s->getCreated()->getTimestamp(); ?>"><?php print $s->getCreated()->format('j M'); ?></td>
                <td><?php print $s->getUser()->getCompleted(); ?>%</td>
                <td><a title="<?php print (!empty($parts[1]) ? ucfirst($parts[1]) : 'Home'); ?>" href="<?php print $view['router']->generate('adviser', ['_user' => $s->getUser()->getId(), '_tab' => $path == '' ? 'home' : $path]); ?>"><?php print $s->getUser()->getFirst() . ' ' . $s->getUser()->getLast(); ?></a></td>
                <td><?php print (empty($schedule) || empty($schedule->getUniversity()) ? 'Not set' : $schedule->getUniversity()); ?></td>
                <?php if($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count() > 1) { ?>
                <td><?php print (!empty($adviser) ? ($adviser->getFirst() . ' ' . $adviser->getLast()) : 'Not assigned'); ?></td>
                <?php } ?>
                </tr><?php
            } ?>
            </tbody>
        </table>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
if($showPartnerIntro) {
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice1'));
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice2'),['strategy' => 'sinclude']);
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice3'),['strategy' => 'sinclude']);
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:partnerAdvice4'),['strategy' => 'sinclude']);
}
$view['slots']->stop();