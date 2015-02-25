<?php
/** @var Course1 $course1 */
use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz1;
use Course1\Bundle\Entity\Quiz2;
use Course1\Bundle\Entity\Quiz3;
use Course1\Bundle\Entity\Quiz4;
use Course1\Bundle\Entity\Quiz5;
use Course1\Bundle\Entity\Quiz6;

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
</table>