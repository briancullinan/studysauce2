<?php
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
/** @var GlobalVariables $app */
$view->extend('Course1Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course1 step1" id="course1_introduction-step1">

    <div class="pane-content">

        <div class="player-wrapper">
            <?php /* &origin=http%3A%2F%2Flocalhost */ ?>
            <iframe id="course1_introduction-player" src="https://www.youtube.com/embed/KgCJ5yDISNs?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1"></iframe>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course1_introduction', ['_step' => 2]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>