<?php

use Course3\Bundle\Entity\Course3;
/** @var Course3 $course */


$view->extend('Course3Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step1" id="course3_teaching-step1">

    <div class="pane-content">

        <div class="player-wrapper">
            <iframe id="course3_teaching-player" src="https://www.youtube.com/embed/Tlh_Ft4KriQ?rel=0&amp;autohide=0&amp;controls=<?php print ($course->getLesson3() > 1 ? 1 : 0); ?>&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost(); ?>"></iframe>
            <a href="#yt-pause">&times;</a>
        </div>
        <div class="highlighted-link invalid">
            <a href="<?php print $view['router']->generate('course3_teaching', ['_step' => 2]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>
