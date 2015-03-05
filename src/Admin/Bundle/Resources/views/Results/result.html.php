<?php
use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz1;
use Course1\Bundle\Entity\Quiz2;
use Course1\Bundle\Entity\Quiz3;
use Course1\Bundle\Entity\Quiz4;
use Course1\Bundle\Entity\Quiz5;
use Course1\Bundle\Entity\Quiz6;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Entity\Course3;

/** @var Course1 $course1 */
/** @var Course2 $course2 */
/** @var Course3 $course3 */

?>
<table>
    <tr>
        <td>Course 1</td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td>Introduction</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course1Bundle:Introduction:quiz.html.php', ['quiz' => $course1->getQuiz1s()->first() ?: new Quiz1(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Settings goals</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course1Bundle:SettingGoals:quiz.html.php', ['quiz' => $course1->getQuiz2s()->first() ?: new Quiz2(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Distractions</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course1Bundle:Distractions:quiz.html.php', ['quiz' => $course1->getQuiz4s()->first() ?: new Quiz4(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Procrastination</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course1Bundle:Procrastination:quiz.html.php', ['quiz' => $course1->getQuiz3s()->first() ?: new Quiz3(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Study environment</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course1Bundle:Environment:quiz.html.php', ['quiz' => $course1->getQuiz5s()->first() ?: new Quiz5(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Partners</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course1Bundle:Partners:quiz.html.php', ['quiz' => $course1->getQuiz6s()->first() ?: new Quiz6(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
            </table>
        </td></tr>
    <tr>
        <td>Course 2</td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td>Study metrics</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course2Bundle:StudyMetrics:quiz.html.php', ['quiz' => $course2->getStudyMetrics()->first() ?: new \Course2\Bundle\Entity\StudyMetrics(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Study plans</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course2Bundle:StudyPlan:quiz.html.php', ['quiz' => $course2->getStudyPlan()->first() ?: new \Course2\Bundle\Entity\StudyPlan(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Interleaving</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course2Bundle:Interleaving:quiz.html.php', ['quiz' => $course2->getInterleaving()->first() ?: new \Course2\Bundle\Entity\Interleaving(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Studying for tests</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course2Bundle:StudyTests:quiz.html.php', ['quiz' => $course2->getStudyTests()->first() ?: new \Course2\Bundle\Entity\StudyTests(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Test taking</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course2Bundle:TestTaking:quiz.html.php', ['quiz' => $course2->getTestTaking()->first() ?: new \Course2\Bundle\Entity\TestTaking(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
            </table>
        </td></tr>
    <tr>
        <td>Course 3</td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td>Intro to strategies</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course3Bundle:Strategies:quiz.html.php', ['quiz' => $course3->getStrategies()->first() ?: new \Course3\Bundle\Entity\Strategies(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Group study</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course3Bundle:GroupStudy:quiz.html.php', ['quiz' => $course3->getGroupStudy()->first() ?: new \Course3\Bundle\Entity\GroupStudy(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Teach to learn</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course3Bundle:Teaching:quiz.html.php', ['quiz' => $course3->getTeaching()->first() ?: new \Course3\Bundle\Entity\Teaching(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Active reading</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course3Bundle:ActiveReading:quiz.html.php', ['quiz' => $course3->getActiveReading()->first() ?: new \Course3\Bundle\Entity\ActiveReading(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Spaced repetition</td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php $view->render('Course3Bundle:SpacedRepetition:quiz.html.php', ['quiz' => $course3->getSpacedRepetition()->first() ?: new \Course3\Bundle\Entity\SpacedRepetition(), 'csrf_token' => '']); ?>
                        <?php $view['slots']->output('body'); ?>
                    </td>
                </tr>
            </table>
        </td></tr>
</table>