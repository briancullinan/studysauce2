<aside id="left-panel">
    <nav>
        <a href="#expand"><span class="navbar-toggle"><span class="icon-bar"></span><span class="icon-bar"></span>
            <span class="icon-bar"></span></span></a>
        <a href="#collapse">Hide</a>
        <ul id="course1-menu" class="main-menu accordion">
            <li><h3><span>&nbsp;</span>Course</h3></li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#lesson1" data-parent="#course1-menu"><span>1</span>Level 1</a>
                </div>
                <ul id="lesson1" class="accordion-body collapse">
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson2', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson3', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                </ul>
            </li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#lesson2" data-parent="#course1-menu"><span>2</span>Level 2</a>
                </div>
                <ul id="lesson2" class="accordion-body collapse">
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson2', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson3', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                </ul>
            </li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#lesson3" data-parent="#course1-menu"><span>3</span>Level 3</a>
                </div>
                <ul id="lesson3" class="accordion-body collapse">
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson2', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson3', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
