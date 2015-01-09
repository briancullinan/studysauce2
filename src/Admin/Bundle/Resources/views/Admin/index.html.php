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
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/admin.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/userlist.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/admin.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="admin">
        <div class="pane-content">
            <div id="userlist">
            <div class="search">
                <label class="input"><input name="search" type="text" value="" placeholder="Search" /></label>
            </div>
            <div class="headers">
                <div class="count-bubble">
                    <small>sign ups</small>
                    <i><?php print $signups; ?></i>
                </div>
                <div class="count-bubble">
                    <small>visitors</small>
                    <i><?php print $visitors; ?></i>
                </div>
                <div class="count-bubble">
                    <small>total</small>
                    <i><?php print count($users); ?></i>
                </div>
            </div>
            <div id="select-status" style="display: none;">
                    <a href="#green"><span>&nbsp;</span></a>
                    <a href="#yellow"><span>&nbsp;</span></a>
                    <a href="#red"><span>&nbsp;</span></a></div>
                <table class="<?php print ($user->hasRole('ROLE_MASTER_ADVISER') && $user->getGroups()->count(
                ) > 1 ? 'master' : ''); ?>">
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
                        <th><select>
                                <option>Adviser</option>
                                <option>Ascending (A-Z)</option>
                                <option>Descending (Z-A)</option>
                            </select></th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
                <div class="scroller">
                    <table>
                        <tbody>
                        <?php
                        foreach ($sessions as $i => $s) {
                            /** @var Visit $s */
                            $first = strpos($s->getPath(), '/', 1);
                            $length = ($first ? $first : strlen($s->getPath())) - 1;
                            $path = empty($length) ? '' : substr($s->getPath(), 1, $length);
                            /** @var Schedule $schedule */
                            $schedule = $s->getUser()->getSchedules()->first();
                            /** @var Course1 $course1 */
                            $course1 = $s->getUser()->getCourse1s()->first();
                            /** @var Course2 $course2 */
                            $course2 = $s->getUser()->getCourse2s()->first();
                            /** @var Course3 $course3 */
                            $course3 = $s->getUser()->getCourse3s()->first();
                            $completed = 0;
                            if (!empty($course1)) {
                                $completed += ($course1->getLesson1() === 4 ? 1 : 0) + ($course1->getLesson2(
                                    ) === 4 ? 1 : 0) +
                                    ($course1->getLesson3() === 4 ? 1 : 0) + ($course1->getLesson4() === 4 ? 1 : 0) +
                                    ($course1->getLesson5() === 4 ? 1 : 0) + ($course1->getLesson6() === 4 ? 1 : 0) +
                                    ($course1->getLesson7() === 4 ? 1 : 0);
                            }
                            if (!empty($course2)) {
                                $completed += ($course2->getLesson1() === 4 ? 1 : 0) + ($course2->getLesson2(
                                    ) === 4 ? 1 : 0) +
                                    ($course2->getLesson3() === 4 ? 1 : 0) + ($course2->getLesson4() === 4 ? 1 : 0) +
                                    ($course2->getLesson5() === 4 ? 1 : 0);
                            }
                            if (!empty($course3)) {
                                $completed += ($course3->getLesson1() === 4 ? 1 : 0) + ($course3->getLesson2(
                                    ) === 4 ? 1 : 0) +
                                    ($course3->getLesson3() === 4 ? 1 : 0) + ($course3->getLesson4() === 4 ? 1 : 0) +
                                    ($course3->getLesson5() === 4 ? 1 : 0);
                            }
                            $overall = round(
                                $completed * 100.0 / (Course1Bundle::COUNT_LEVEL
                                    - ($s->getUser()->hasRole('ROLE_PAID') ? 1 : 0)
                                    + Course2Bundle::COUNT_LEVEL + Course3Bundle::COUNT_LEVEL)
                            );
                            /** @var User $adviser */
                            $adviser = $s->getUser()->getGroups()->map(
                                function (Group $g) {
                                    return $g->getUsers()->filter(
                                        function (User $u) {
                                            return $u->hasRole('ROLE_ADVISER');
                                        }
                                    )->first();
                                }
                            )->first();
                            if (!empty($adviser)) {
                                $adviser = $s->getUser()->getGroups()->map(
                                    function (Group $g) {
                                        return $g->getUsers()->filter(
                                            function (User $u) {
                                                return $u->hasRole('ROLE_MASTER_ADVISER');
                                            }
                                        )->first();
                                    }
                                )->first();
                            }
                            ?>
                            <tr class="user-id-<?php print $s->getUser()->getId(); ?> status_<?php print ($s->getUser(
                            )->getProperty('adviser_status') ?: 'green'); ?>">
                            <td><a href="#change-status"><span>&nbsp;</span></a></td>
                            <td data-timestamp="<?php print $s->getCreated()->getTimestamp(
                            ); ?>"><?php print $s->getCreated()->format('j M'); ?></td>
                            <td><?php print $overall; ?>%</td>
                            <td><a href="<?php print $view['router']->generate(
                                    'adviser',
                                    ['_user' => $s->getUser()->getId(), '_tab' => $path == '' ? 'home' : $path]
                                ); ?>"><?php print $s->getUser()->getFirst() . ' ' . $s->getUser()->getLast(); ?></a>
                            </td>
                            <td><?php print (!empty($schedule) ? $schedule->getUniversity() : 'Not set'); ?></td>
                            <td><?php print (!empty($adviser) ? ($adviser->getFirst() . ' ' . $adviser->getLast(
                                    )) : 'Not assigned'); ?></td>
                            <td><a href="#edit-user"></a><a href="#remove-user"></a></td>
                            </tr><?php
                        } ?>
                        <?php
                        foreach ($users as $i => $u) {
                            /** @var User $u */
                            if ($u->hasRole('ROLE_ADVISER') || $u->hasRole('ROLE_MASTER_ADVISER')) {
                                continue;
                            }

                            /** @var Schedule $schedule */
                            $schedule = $u->getSchedules()->first();
                            /** @var Course1 $course1 */
                            $course1 = $s->getUser()->getCourse1s()->first();
                            /** @var Course2 $course2 */
                            $course2 = $s->getUser()->getCourse2s()->first();
                            /** @var Course3 $course3 */
                            $course3 = $s->getUser()->getCourse3s()->first();
                            $completed = 0;
                            if (!empty($course1)) {
                                $completed += ($course1->getLesson1() === 4 ? 1 : 0) + ($course1->getLesson2(
                                    ) === 4 ? 1 : 0) +
                                    ($course1->getLesson3() === 4 ? 1 : 0) + ($course1->getLesson4() === 4 ? 1 : 0) +
                                    ($course1->getLesson5() === 4 ? 1 : 0) + ($course1->getLesson6() === 4 ? 1 : 0) +
                                    ($course1->getLesson7() === 4 ? 1 : 0);
                            }
                            if (!empty($course2)) {
                                $completed += ($course2->getLesson1() === 4 ? 1 : 0) + ($course2->getLesson2(
                                    ) === 4 ? 1 : 0) +
                                    ($course2->getLesson3() === 4 ? 1 : 0) + ($course2->getLesson4() === 4 ? 1 : 0) +
                                    ($course2->getLesson5() === 4 ? 1 : 0);
                            }
                            if (!empty($course3)) {
                                $completed += ($course3->getLesson1() === 4 ? 1 : 0) + ($course3->getLesson2(
                                    ) === 4 ? 1 : 0) +
                                    ($course3->getLesson3() === 4 ? 1 : 0) + ($course3->getLesson4() === 4 ? 1 : 0) +
                                    ($course3->getLesson5() === 4 ? 1 : 0);
                            }
                            $overall = round(
                                $completed * 100.0 / (Course1Bundle::COUNT_LEVEL
                                    - ($u->hasRole('ROLE_PAID') ? 1 : 0)
                                    + Course2Bundle::COUNT_LEVEL + Course3Bundle::COUNT_LEVEL)
                            );
                            /** @var User $adviser */
                            $adviser = $u->getGroups()->map(
                                function (Group $g) {
                                    return $g->getUsers()->filter(
                                        function (User $u) {
                                            return $u->hasRole('ROLE_ADVISER');
                                        }
                                    )->first();
                                }
                            )->first();
                            ?>
                            <tr class="user-id-<?php print $u->getId(); ?> status_<?php print ($u->getProperty(
                                'adviser_status'
                            ) ?: 'green'); ?>">
                            <td><a href="#change-status"><span>&nbsp;</span></a></td>
                            <td data-timestamp="<?php print (empty($u->getLastLogin()) ? $u->getCreated()->getTimestamp(
                            ) : $u->getLastLogin()->getTimestamp()); ?>"><?php print (empty($u->getLastLogin(
                                )) ? $u->getCreated()->format('j M') : $u->getLastLogin()->format('j M')); ?></td>
                            <td><?php print $overall; ?>%</td>
                            <td><a href="<?php print $view['router']->generate(
                                    'adviser',
                                    ['_user' => $u->getId(), '_tab' => 'home']
                                ); ?>"><?php print $u->getFirst() . ' ' . $u->getLast(); ?></a></td>
                            <td><?php print (!empty($schedule) ? $schedule->getUniversity() : 'Not set'); ?></td>
                            <td><?php print (!empty($adviser) ? ($adviser->getFirst() . ' ' . $adviser->getLast(
                                    )) : 'Not assigned'); ?></td>
                            <td><a href="#edit-user"></a><a href="#remove-user"></a></td>
                            </tr><?php
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
