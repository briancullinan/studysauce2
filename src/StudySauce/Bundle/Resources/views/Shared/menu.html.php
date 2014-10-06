<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Course:menu')); ?>

<aside id="right-panel" class="collapsed">
    <nav>
        <a href="#expand"><span class="navbar-toggle">Study Tools</span></a>
        <a href="#collapse">Hide</a>
        <ul class="main-menu">
            <li><h3>Study Tools</h3></li>
            <li><a href="<?php print $view['router']->generate('goals'); ?>"><span>&nbsp;</span>Goals</a></li>
            <li><a href="<?php print $view['router']->generate('schedule'); ?>"><span>&nbsp;</span>Class schedule</a></li>
            <li><a href="<?php print $view['router']->generate('checkin'); ?>"><span>&nbsp;</span>Check in</a></li>
            <li><a href="<?php print $view['router']->generate('metrics'); ?>"><span>&nbsp;</span>Study metrics</a></li>
            <li><a href="<?php print $view['router']->generate('deadlines'); ?>"><span>&nbsp;</span>Deadlines</a></li>
            <li><a href="<?php print $view['router']->generate('partner'); ?>"><span>&nbsp;</span>Accountability partner</a></li>
            <li><a href="<?php print $view['router']->generate('plan'); ?>"><span>&nbsp;</span>Study plan <sup class="premium">Premium</sup></a></li>
            <li><a href="<?php print $view['router']->generate('profile'); ?>"><span>&nbsp;</span>Study profile <sup class="premium">Premium</sup></a></li>
            <li><a href="<?php print $view['router']->generate('premium'); ?>"><span>&nbsp;</span>Premium</a></li>
            <li><a href="<?php print $view['router']->generate('tips'); ?>"><span>&nbsp;</span>Tips</a></li>
            <li><a href="<?php print $view['router']->generate('account'); ?>"><span>&nbsp;</span>Account settings</a></li>
            <li><h3>Coming soon</h3></li>
            <li><a href=""><span>&nbsp;</span>Midterm/final planner</a></li>
            <li><a href=""><span>&nbsp;</span>Grade calculator</a></li>
            <li><a href="#quizlet"><span>&nbsp;</span>Quizlet</a></li>
            <li><a href="#drive"><span>&nbsp;</span>Google Drive</a></li>
            <li><a href="#evernote"><span>&nbsp;</span>Evernote</a></li>
            <li><a href="#blackboard"><span>&nbsp;</span>Blackboard</a></li>
        </ul>
    </nav>
</aside>