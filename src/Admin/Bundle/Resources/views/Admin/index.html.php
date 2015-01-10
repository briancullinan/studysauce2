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
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/admin.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/admin.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="admin">
        <div class="pane-content">
            <table class="<?php print ($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count(
            ) > 1 ? 'master' : ''); ?>">
                <thead>
                <tr>
                    <th><label><span>Visitors: <?php print $visitors; ?></span><br />
                        <select>
                            <option>Date</option>
                            <option>Ascending (Now-Then)</option>
                            <option>Descending (Then-Now)</option>
                        </select></label></th>
                    <th><label><span>Parents: <?php print $parents; ?></span><br />
                            <span>Partners: <?php print $partners; ?></span><br />
                            <span>Students: <?php print $students; ?></span><br />
                            <span>Advisers: <?php print $advisers; ?></span><br />
                        <select>
                            <option>Role</option>
                            <option>Ascending (A-Z)</option>
                            <option>Descending (Z-A)</option>
                        </select></label></th>
                    <th><label><span>TAL: <?php print count($users); ?></span><br />
                            <span>CSA: <?php print count($users); ?></span><br />
                        <select>
                            <option>Group</option>
                            <option>Ascending (A-Z)</option>
                            <option>Descending (Z-A)</option>
                        </select></label></th>
                    <th><label><span>Total: <?php print count($users); ?></span><br />
                        <select>
                            <option>Student</option>
                            <option>Ascending (A-Z)</option>
                            <option>Descending (Z-A)</option>
                        </select></label></th>
                    <th><label><span>Finished: <?php print $completed; ?></span><br />
                        <select>
                            <option>Completed</option>
                            <option>Ascending (0-100)</option>
                            <option>Descending (100-0)</option>
                        </select></label></th>
                    <th><label><span>Sign Ups: <?php print $signups; ?></span><br />
                        <select>
                            <option>Sign Up Date</option>
                            <option>Ascending (Now-Then)</option>
                            <option>Descending (Then-Now)</option>
                        </select></label></th>
                    <th><label><span>Paid: <?php print $paid; ?></span><br />
                        <select>
                            <option>Paid</option>
                            <option>Ascending (Y-N)</option>
                            <option>Descending (N-Y)</option>
                        </select></label></th>
                    <th><label><span>Goals: <?php print $goals; ?></span><br />
                        <select>
                            <option>Goals</option>
                            <option>Ascending (Y-N)</option>
                            <option>Descending (N-Y)</option>
                        </select></label></th>
                    <th><label><span>Deadlines: <?php print $deadlines; ?></span><br />
                        <select>
                            <option>Deadlines</option>
                            <option>Ascending (Y-N)</option>
                            <option>Descending (N-Y)</option>
                        </select></label></th>
                    <th><label><span>Plans: <?php print $plans; ?></span><br />
                        <select>
                            <option>Study Plan</option>
                            <option>Ascending (Y-N)</option>
                            <option>Descending (N-Y)</option>
                        </select></label></th>
                    <th><label><span>Partner: <?php print $partnerTotal; ?></span><br />
                        <select>
                            <option>Partner</option>
                            <option>Ascending (Y-N)</option>
                            <option>Descending (N-Y)</option>
                        </select></label></th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
            <div class="scroller">
                <table>
                    <tbody>
                    <?php
                    foreach ($users as $i => $u) {
                        /** @var User $u */

                        ?>
                        <tr class="user-id-<?php print $u->getId(); ?> status_<?php print ($u->getProperty(
                            'adviser_status'
                        ) ?: 'green'); ?>">
                        <td data-timestamp="<?php print (empty($u->getLastLogin())
                            ? $u->getCreated()->getTimestamp()
                            : $u->getLastLogin()->getTimestamp()); ?>"><?php print (empty($u->getLastLogin())
                                ? $u->getCreated()->format('j M')
                                : $u->getLastLogin()->format('j M')); ?></td>
                        <td><?php print implode(', ', array_map(function ($r) {return substr($r, 5);}, $u->getRoles())); ?></td>
                        <td><?php print implode(', ', $u->getGroups()->map(function (Group $g) {return $g->getName();})->toArray()); ?></td>
                        <td><a href="<?php print $view['router']->generate(
                                'adviser',
                                ['_user' => $u->getId(), '_tab' => 'home']
                            ); ?>"><?php print $u->getFirst() . ' ' . $u->getLast(); ?></a></td>
                        <td><?php print $u->getCompleted(); ?>%</td>
                        <td data-timestamp="<?php print $u->getCreated()->getTimestamp(); ?>"><?php print $u->getCreated()->format('j M'); ?></td>
                        <td><?php print ($u->hasRole('ROLE_PAID') ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getGoals()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getDeadlines()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getSchedules()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getPartnerInvites()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><a href="#edit-user"></a><a href="#remove-user"></a></td>
                        </tr><?php
                    } ?>
                    </tbody>
                </table>
            </div>
            <div class="search">
                <label class="input"><input name="search" type="text" value="" placeholder="Search" /></label>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
