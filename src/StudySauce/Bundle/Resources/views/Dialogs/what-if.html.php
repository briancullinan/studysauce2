<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
What if:
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#class-grade" data-target="#class-grade" data-toggle="tab">Class grade</a></li>
    <li><a href="#term-gpa" data-target="#term-gpa" data-toggle="tab">Term GPA</a></li>
    <li><a href="#overall-gpa" data-target="#overall-gpa" data-toggle="tab">Overall GPA</a></li>
</ul>
<div class="tab-content">
    <div id="class-grade" class="tab-pane active">
        To finish with a grade of
        <label class="input"><select>
                <option value="A+">A+</option>
                <option value="A">A</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B">B</option>
                <option value="B-">B-</option>
                <option value="C+">C+</option>
                <option value="C">C</option>
                <option value="C-">C-</option>
                <option value="D+">D+</option>
                <option value="D">D</option>
                <option value="D-">D-</option>
                <option value="F">F</option>
            </select></label>
        in <label class="input">
            <select class="class-name"></select>
        </label>
        I need to average <span class="result">98%</span> on my remaining assignments.
    </div>
    <div id="term-gpa" class="tab-pane">
        If I make these grades,
        <div class="class-row">
            <div class="class-name"></div>
            <label class="input"><select>
                    <option value="A+">A+</option>
                    <option value="A">A</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B">B</option>
                    <option value="B-">B-</option>
                    <option value="C+">C+</option>
                    <option value="C">C</option>
                    <option value="C-">C-</option>
                    <option value="D+">D+</option>
                    <option value="D">D</option>
                    <option value="D-">D-</option>
                    <option value="F">F</option>
                </select></label>
            <div class="hours"></div>
        </div>
        my GPA will be <span class="result">3.0</span>.
    </div>
    <div id="overall-gpa" class="tab-pane">
        To improve my overall GPA to
        <label class="input"><select class="overall-gpa">
                <?php for($i = 40; $i >= 0; $i--) { ?>
                    <option value="<?php print round($i / 10, 1); ?>"><?php print number_format($i / 10, 1); ?></option>
                <?php } ?>
            </select></label>
        I need this term's GPA to be <span class="result">4.0</span>.
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#save-scale" data-dismiss="modal" class="btn btn-primary">Done</a>
<?php $view['slots']->stop() ?>

