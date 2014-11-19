<aside id="left-panel" class="collapsed">
    <nav>
        <a href="#expand"><span class="navbar-toggle"><span class="icon-bar"></span><span class="icon-bar"></span>
            <span class="icon-bar"></span></span></a>

        <ul id="course1-menu" class="main-menu accordion">
            <li><a href="#collapse">Hide</a><h3>Course</h3></li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#level1" data-parent="#course1-menu"><span>1</span>Level 1</a>
                </div>
                <ul id="level1" class="accordion-body collapse <?php print (empty($course) || $course->getLevel() < 8 ? 'in' : ''); ?>">
                    <li><a href="<?php print $view['router']->generate('course1_introduction', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li><a href="<?php print $view['router']->generate('course1_setting_goals', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li><a href="<?php print $view['router']->generate('course1_distractions', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                    <li><a href="<?php print $view['router']->generate('course1_procrastination', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li><a href="<?php print $view['router']->generate('course1_environment', ['_step' => 0]); ?>"><span>&bullet;</span>Study environment</a></li>
                    <li><a href="<?php print $view['router']->generate('course1_partners', ['_step' => 0]); ?>"><span>&bullet;</span>Partners</a></li>
                    <li><a href="<?php print $view['router']->generate('course1_upgrade', ['_step' => 0]); ?>"><span>&bullet;</span>Upgrade</a></li>
                </ul>
            </li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#level2" data-parent="#course1-menu"><span>2</span>Level 2</a>
                </div>
                <ul id="level2" class="accordion-body collapse">
                    <li class="coming">Coming soon</li>
                </ul>
            </li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#level3" data-parent="#course1-menu"><span>3</span>Level 3</a>
                </div>
                <ul id="level3" class="accordion-body collapse">
                    <li class="coming">Coming soon</li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
