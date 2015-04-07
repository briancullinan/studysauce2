<?php

use Evernote\Model\Note;
use Evernote\Model\Notebook;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/notes.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/ionicons.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<script type="text/javascript">
    CKEDITOR_BASEPATH = '<?php print $view['router']->generate('_welcome'); ?>bundles/admin/js/ckeditor/';
    window.initialTags = JSON.parse('<?php print json_encode($allTags); ?>');
</script>
<?php foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/ckeditor/ckeditor.js',],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/notes.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="notes">
        <div class="pane-content">
            <h2>Study notes</h2>
            <div class="new-study-note">
                <a href="#add-note" class="big-add">Add <span>+</span> study note</a>
            </div>
            <?php
            $first = true;
            foreach ($schedules as $s) {
                /** @var Schedule $s */
                $classes = array_combine(
                    $s->getClasses()->map(function (Course $c) {return $c->getId();})->toArray(),
                    $s->getClasses()->toArray());
                ?>
                <div
                    class="term-row schedule-id-<?php print $s->getId(); ?> <?php print ($first ? 'selected' : ''); ?>">
                    <div class="term-name"><?php
                        for ($y = intval(date('Y')); $y > intval(date('Y')) - 20; $y--) {
                            foreach ([11 => 'Winter', 8 => 'Fall', 1 => 'Spring', 6 => 'Summer'] as $m => $t) {
                                // skip dates beyond the current term
                                if ($y == date('Y') && $m > intval(date('n')) && (
                                        empty($s->getTerm()) || $s->getTerm()->format('n/Y') != $m . '/' . $y)
                                ) {
                                    continue;
                                }

                                if (!empty($s->getTerm()) && $s->getTerm()->format('n/Y') == $m . '/' . $y) {
                                    $label = $t . ' ' . $y;
                                }
                            }
                        }
                        if(empty($label) || $first)
                            print 'Current term';
                        else
                            print $label;
                        ?>
                    </div>
                    <div class="term-editor">
                        <?php
                        if(!isset($notes[$s->getId()]))
                            $notes[$s->getId()] = [];

                        $keys = array_map(function ($i) use ($classes) {return is_numeric($i) ? $classes[$i]->getIndex() : false;}, array_keys($notes[$s->getId()]));
                        $orig = array_keys($notes[$s->getId()]);
                        array_multisort($keys, SORT_DESC, SORT_NUMERIC, $notes[$s->getId()], $orig);
                        $notes[$s->getId()] = array_combine($orig, array_values($notes[$s->getId()]));
                        foreach ($notes[$s->getId()] as $i => $books) {
                            /** @var Notebook $b */
                            if(is_numeric($i)) {
                                /** @var Course $c */
                                $c = $classes[$i];
                                $name = $c->getName();
                                $classI = $c->getIndex();
                            }
                            else {
                                /** @var Notebook $n */
                                $n = $notebooks[$i];
                                $name = $n->getName();
                                $classI = '';
                            }
                            ?>
                            <div class="class-row notebook-id-<?php print (is_numeric($i) ? '' : $i); ?>
                                course-id-<?php print (is_numeric($i) ? $i : '');
                                print ($first ? ' selected' : ' '); ?>">
                                <div class="class-name read-only">
                                    <label class="input"><span>Class name</span><i class="class<?php print $classI; ?>"></i>
                                        <input type="text" value="<?php print $name; ?>" placeholder="Class name">
                                    </label>
                                </div>
                            </div>
                            <div class="notes">
                                <?php
                                foreach ($books as $n) {
                                    /** @var Note $n */
                                    ?>
                                    <div class="note-row note-id-<?php print $n->getGuid(); ?>" data-tags="<?php print htmlentities(json_encode($n->getEdamNote()->tagGuids)); ?>">
                                        <h4 class="note-name">
                                            <a href="#view-note"><?php print $n->getTitle(); ?></a>
                                        </h4>
                                        <div class="summary">
                                            <small class="date"><?php print date_timestamp_set(new \DateTime(), $n->getEdamNote()->updated)->format('j M h:i'); ?></small>
                                            <?php print $n->getContent()->toEnml(); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php }
                        if(empty($notes[$s->getId()])) { ?>No notes for this term<?php } ?>
                    </div>
                </div>
                <?php $first = false;
            } ?>
            <h3 class="note-title">
                <label class="input books"><select name="notebook">
                    <option value="">Select a notebook</option><?php
                    $allnotebooks = [];
                    if(isset($schedules[0])) {
                        $s = $schedules[0];
                        $courses = $s->getClasses()->toArray();
                        foreach($courses as $i => $c)
                        {
                            /** @var Course $c */
                            // find notebook with matching name
                            $id = '';
                            foreach($notebooks as $b) {
                                if($b->getName() == $c->getName()) {
                                    $id = $b->getGuid();
                                    $allnotebooks[] = $id;
                                    break;
                                }
                            }
                            ?><option value="<?php print (!empty($id) ? $id : $c->getId()); ?>"><?php print $c->getName(); ?></option><?php
                        }
                    }
                    foreach($notebooks as $b) {
                        if(!in_array($id = $b->getGuid(), $allnotebooks)) {
                            ?><option value="<?php print $id; ?>"><?php print $b->getName(); ?></option><?php
                        }
                    }
                    ?></select></label>
                <label class="input tags">
                    <input type="text" placeholder="Tags" data-data="" value="" autocomplete="off">
                </label>
                <label class="input title"><input type="text" placeholder="Title your note"/></label></h3>
            <div id="editor1" contenteditable="true">This is note content</div>
            <div class="highlighted-link"><a href="#save-note" class="more">Save</a></div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
if (!empty($services)) {
    print $this->render(
        'StudySauceBundle:Dialogs:notes-connect.html.php',
        ['id' => 'notes-connect', 'services' => $services]
    );
}
$view['slots']->stop();
