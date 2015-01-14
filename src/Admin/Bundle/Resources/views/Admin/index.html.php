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
            <div class="search">
                <label class="input"><input name="search" type="text" value="" placeholder="Search" /></label>
            </div>
            <div class="paginate">
                <a href="?page=first">&lt;&lt;</a> <a href="?page=prev">&lt;</a>
                <label class="input"><input name="page" type="text" value="1" /> / <span id="page-total"><?php print ceil($total / 25); ?></span></label>
                <a href="?page=next">&gt;</a> <a href="?page=last">&gt;&gt;</a>
            </div>
            <table class="<?php print ($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count(
            ) > 1 ? 'master' : ''); ?>">
                <thead>
                <tr>
                    <th><label><span>Visitors: <?php print $visitors; ?></span><br />
                        <select name="lastLogin">
                            <option value="">Date</option>
                            <option value="_ascending">Ascending (Then-Now)</option>
                            <option value="_descending">Descending (Now-Then)</option>
                        </select></label></th>
                    <th><label><span>Parents: <?php print $parents; ?></span><br />
                            <span>Partners: <?php print $partners; ?></span><br />
                            <span>Students: <?php print $students; ?></span><br />
                            <span>Advisers: <?php print $advisers; ?></span><br />
                        <select name="role">
                            <option value="">Role</option>
                            <option value="_ascending">Ascending (A-Z)</option>
                            <option value="_descending">Descending (Z-A)</option>
                            <option value="ROLE_PAID">PAID</option>
                            <option value="ROLE_ADMIN">ADMIN</option>
                            <option value="ROLE_PARENT">PARENT</option>
                            <option value="ROLE_PARTNER">PARTNER</option>
                            <option value="ROLE_ADVISER">ADVISER</option>
                            <option value="ROLE_STUDENT">STUDENT</option>
                            <option value="ROLE_MASTER_ADVISER">MASTER_ADVISER</option>
                        </select></label></th>
                    <th><label><span>TAL: <?php print count($users); ?></span><br />
                            <span>CSA: <?php print count($users); ?></span><br />
                        <select name="group">
                            <option value="">Group</option>
                            <option value="_ascending">Ascending (A-Z)</option>
                            <option value="_descending">Descending (Z-A)</option>
                            <?php foreach($groups as $i => $g) {
                                /** @var Group $g */
                                ?><option value="<?php print $g->getId(); ?>"><?php print $g->getName(); ?></option><?php
                            } ?>
                        </select></label></th>
                    <th><label><span>Total: <?php print $total; ?></span><br />
                        <select name="last">
                            <option value="">Student</option>
                            <option value="_ascending">Ascending (A-Z)</option>
                            <option value="_descending">Descending (Z-A)</option>
                            <option value="A%">A</option>
                            <option value="B%">B</option>
                            <option value="C%">C</option>
                            <option value="D%">D</option>
                            <option value="E%">E</option>
                            <option value="F%">F</option>
                            <option value="G%">G</option>
                            <option value="H%">H</option>
                            <option value="I%">I</option>
                            <option value="J%">J</option>
                            <option value="K%">K</option>
                            <option value="L%">L</option>
                            <option value="M%">M</option>
                            <option value="N%">N</option>
                            <option value="O%">O</option>
                            <option value="P%">P</option>
                            <option value="Q%">Q</option>
                            <option value="R%">R</option>
                            <option value="S%">S</option>
                            <option value="T%">T</option>
                            <option value="U%">U</option>
                            <option value="V%">V</option>
                            <option value="W%">W</option>
                            <option value="X%">X</option>
                            <option value="Y%">Y</option>
                            <option value="Z%">Z</option>
                        </select></label></th>
                    <th><label><span>Finished: <?php print $completed; ?></span><br />
                        <select name="completed">
                            <option value="">Completed</option>
                            <option value="_ascending">Ascending (0-100)</option>
                            <option value="_descending">Descending (100-0)</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="1,2">1 &amp; 2</option>
                            <option value="1,3">1 &amp; 3</option>
                            <option value="2,3">2 &amp; 3</option>
                            <option value="1,2,3">Completed</option>
                        </select></label></th>
                    <th><label><span>Sign Ups: <?php print $signups; ?></span><br />
                        <select name="created">
                            <option value="">Sign Up Date</option>
                            <option value="_ascending">Ascending (Then-Now)</option>
                            <option value="_descending">Descending (Now-Then)</option>
                        </select></label></th>
                    <th><label><span>Paid: <?php print $paid; ?></span><br />
                        <select name="hasPaid">
                            <option value="">Paid</option>
                            <option value="yes">Y</option>
                            <option value="no">N</option>
                        </select></label></th>
                    <th><label><span>Goals: <?php print $goals; ?></span><br />
                        <select name="hasGoals">
                            <option value="">Goals</option>
                            <option value="yes">Y</option>
                            <option value="no">N</option>
                        </select></label></th>
                    <th><label><span>Deadlines: <?php print $deadlines; ?></span><br />
                        <select name="hasDeadlines">
                            <option value="">Deadlines</option>
                            <option value="yes">Y</option>
                            <option value="no">N</option>
                        </select></label></th>
                    <th><label><span>Plans: <?php print $plans; ?></span><br />
                        <select name="hasPlans">
                            <option value="">Study Plan</option>
                            <option value="yes">Y</option>
                            <option value="no">N</option>
                        </select></label></th>
                    <th><label><span>Partner: <?php print $partnerTotal; ?></span><br />
                        <select name="hasPartners">
                            <option value="">Partner</option>
                            <option value="yes">Y</option>
                            <option value="no">N</option>
                        </select></label></th>
                    <th><label><span>Actions</span><br />
                            <select name="actions">
                                <option value="">Select All</option>
                                <option value="delete">Delete All</option>
                                <option value="cancel">Cancel All</option>
                                <option value="email">Email All</option>
                                <option value="export">Export All</option>
                                <option value="export">Clear All</option>
                            </select></label></th>
                </tr>
                </thead>
            </table>
            <div class="scroller">
                <table>
                    <tbody>
                    <?php foreach ($users as $i => $u) {
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
                        <td><?php print preg_replace('/^USER(,<br \/>)*|(,<br \/>)*USER|^PAID(,<br \/>)*|(,<br \/>)*PAID/i', '', implode(',<br />', array_map(function ($r) {return substr($r, 5);}, $u->getRoles()))); ?></td>
                        <td><?php print implode(',<br />', $u->getGroups()->map(function (Group $g) {return $g->getName();})->toArray()); ?></td>
                        <td><a href="<?php print $view['router']->generate('adviser',['_user' => $u->getId(), '_tab' => 'home']); ?>"><?php print $u->getFirst() . ' ' . $u->getLast(); ?></a><small><a href="mailto:<?php print $u->getEmailCanonical(); ?>"><?php print $u->getEmail(); ?></a></small></td>
                        <td><?php print $u->getCompleted(); ?>%</td>
                        <td data-timestamp="<?php print $u->getCreated()->getTimestamp(); ?>"><?php print $u->getCreated()->format('j M'); ?></td>
                        <td><?php print ($u->hasRole('ROLE_PAID') ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getGoals()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getDeadlines()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getSchedules()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><?php print ($u->getPartnerInvites()->count() > 0 ? 'Y' : 'N'); ?></td>
                        <td><a href="#edit-user"></a><a href="#confirm-remove-user" data-toggle="modal"></a> <label class="checkbox"><input type="checkbox" name="selected" /><i></i></label></td>
                        </tr><?php
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('AdminBundle:Dialogs:confirm-remove-user.html.php', ['id' => 'confirm-remove-user']);
$view['slots']->stop();
