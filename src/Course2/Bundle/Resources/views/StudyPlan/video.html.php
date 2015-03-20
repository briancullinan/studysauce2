<?php

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Course2\Bundle\Entity\Course2;
/** @var Course2 $course */
/** @var GlobalVariables $app */

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step1" id="course2_study_plan-step1">

    <div class="pane-content">

        <div class="player-wrapper">
            <iframe id="course2_study_plan-player" src="https://www.youtube.com/embed/oHTRbjxzF5o?rel=0&amp;autohide=0&amp;controls=<?php print ($course->getLesson2() > 1 ? 1 : 0); ?>&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost(); ?>"></iframe>
        </div>
        <div class="highlighted-link invalid">
            <a href="<?php print $view['router']->generate('course2_study_plan', ['_step' => 2]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>
