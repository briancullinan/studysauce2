<?php

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Course1\Bundle\Entity\Course1;
/** @var Course1 $course */
/** @var GlobalVariables $app */

$view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step1" id="course1_partners-step1">

    <div class="pane-content">

        <div class="player-wrapper">
            <iframe id="course1_partners-player" src="https://www.youtube.com/embed/AyDKKFFyQuU?rel=0&amp;autohide=0&amp;controls=<?php print ($course->getLesson6() > 1 ? 1 : 0); ?>&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost(); ?>"></iframe>
        </div>
        <div class="highlighted-link invalid">
            <a href="<?php print $view['router']->generate('course1_partners', ['_step' => 2]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>
