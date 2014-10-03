<aside id="left-panel">
    <nav>
        <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <ul class="main-menu">
            <li><h3><span>&nbsp;</span>Curriculum</h3></li>
            <li><a onclick="return false;" data-toggle="collapse" data-target="#lesson1"><span>1</span>Level 1</a>
                <ul id="lesson1" class="collapse">
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson2', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson3', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                </ul>
            </li>
            <li><a onclick="return false;" data-toggle="collapse" data-target="#lesson2"><span>2</span>Level 2</a>
                <ul id="lesson2" class="collapse">
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson2', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson3', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                </ul>
            </li>
            <li><a onclick="return false;" data-toggle="collapse" data-target="#lesson3"><span>3</span>Level 3</a>
                <ul id="lesson3" class="collapse">
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson1', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson2', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li><a href="<?php print $view['router']->generate('lesson3', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                </ul>
            </li>
            <li><a href="/premium"><span>&nbsp;</span>Premium</a></li>
        </ul>
    </nav>
</aside>
