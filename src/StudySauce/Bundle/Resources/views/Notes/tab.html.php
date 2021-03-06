<?php

use StudySauce\Bundle\Controller\NotesController;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\StudyNote;

/** @var \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables $app */
/** @var Course[] $classes */

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
    window.initialTags = JSON.parse('<?php
     $tags = [];
     foreach($allTags as $guid => $t) {
        if(is_numeric($guid))
        $tags[] = ['value' => $t, 'text' => $t];
        else
        $tags[] = ['value' => $guid, 'text' => $t];
     }
     print json_encode($tags); ?>');
</script>
<?php foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/ckeditor/ckeditor.js',],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/notes.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane <?php
        print (empty($services) ? 'connected' : 'not-connected'); ?>" id="notes">
        <div class="pane-content">
            <h2>Study notes</h2>
            <?php if(!empty($services)) {
                foreach($services as $o => $url) { ?>
                    <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('notes'); ?>" class="more">Connect Evernote</a>
                <?php }
            }
            else { ?>
                <a href="https://<?php print ($app->getEnvironment() == 'prod' ? 'www' : 'sandbox'); ?>.evernote.com/" target="evernote" class="more">Backed up with Evernote</a>
            <?php } ?>
            <div class="new-study-note highlighted-link">
                <form action="<?php print $view['router']->generate('notes_search'); ?>">
                    <a href="#add-note" class="big-add">Add <span>+</span> study note</a>
                    <label class="input">
                        <input type="text" name="search" placeholder="Search" />
                    </label>
                    <button type="submit" value="search" class="more">Search</button>
                </form>
            </div>
            <?php
            $first = true;
            $notesCount = 0;
            foreach ($schedules as $s) {
                /** @var Schedule $s */
                $classes = array_combine(
                    $s->getClasses()->map(function (Course $c) {return $c->getId();})->toArray(),
                    $s->getClasses()->toArray());
                ?>
                <div
                    class="term-row schedule-id-<?php print $s->getId(); ?> <?php print ($first ? 'selected' : ''); ?>">
                    <div class="term-name"><?php
                        foreach ([11 => 'Winter', 8 => 'Fall', 1 => 'Spring', 6 => 'Summer'] as $m => $t) {
                            // skip dates beyond the current term
                            if ($m > intval(date('n')) && (empty($s->getTerm()) || $m > $s->getTerm()->format('n'))) {
                                continue;
                            }

                            $label = $t . ' ' . (empty($s->getTerm()) ? date('Y') : $s->getTerm()->format('Y'));
                        }
                        print $label;
                        ?>
                    </div>
                    <div class="term-editor">
                        <?php
                        if(!isset($notes[$s->getId()]))
                            $notes[$s->getId()] = [];

                        $keys = array_map(function ($i) use ($classes, $notes, $s, $notebooks) {return is_numeric($i) && isset($classes[$i])
                            ? $classes[$i]->getIndex()
                            : (isset($notebooks[$i]) ? strtolower($notebooks[$i]) : 9999999999);}, array_keys($notes[$s->getId()]));
                        $orig = array_keys($notes[$s->getId()]);
                        array_multisort($keys, SORT_ASC, SORT_STRING, $notes[$s->getId()], $orig);
                        $notes[$s->getId()] = array_combine($orig, array_values($notes[$s->getId()]));
                        foreach ($notes[$s->getId()] as $id => $books) {
                            $c = NotesController::getCourseByName($id, new Doctrine\Common\Collections\ArrayCollection($schedules));
                            if(!empty($c)) {
                                $name = $c->getName();
                                $classI = $c->getIndex();
                                $id = '';
                            }
                            else {
                                $name = $notebooks[$id];
                                $classI = '';
                            }
                            ?>
                            <div data-notebook="<?php print $id; ?>" class="class-row course-id-<?php print (!empty($c) ? $c->getId() : '');
                                print ($first && !empty($books) ? ' selected' : ' '); ?>">
                                <div class="class-name read-only">
                                    <label class="input"><span>Class name</span>
                                        <?php if($classI !== '') { ?><i class="class<?php print $classI; ?>"></i><?php } ?>
                                        <input type="text" value="<?php print $name; ?>" placeholder="Class name">
                                    </label>
                                    <a href="#delete-notebook" data-toggle="modal">&nbsp;</a>
                                </div>
                            </div>
                            <div class="notes">
                                <?php
                                foreach ($books as $n) {
                                    /** @var StudyNote $n */
                                    $time = (!empty($n->getUpdated())
                                        ? $n->getUpdated()
                                        : (!empty($n->getRemoteUpdated())
                                            ? $n->getRemoteUpdated()
                                            : $n->getCreated()))
                                    ?>
                                    <div data-notebook="<?php print $id; ?>" data-timestamp="<?php print $time->getTimestamp(); ?>" class="note-row note-id-<?php print $n->getId();
                                    print ($notesCount < 10 ? ' loaded' : ' loading');
                                    print ' course-id-' . (!empty($c) ? $c->getId() : ''); ?>" data-tags="<?php
                                    $theseTags = $n->getProperty('tags') ?: [];
                                    $tags = [];
                                    foreach($theseTags as $guid => $t) {
                                        if(is_numeric($guid))
                                            $tags[$t] = $t;
                                        else
                                            $tags[$guid] = $t;
                                    }
                                    print htmlentities(json_encode(array_keys($tags))); ?>">
                                        <h4 class="note-name"><a href="#view-note"><?php print (empty($n->getTitle()) ? 'Untitled' : $n->getTitle()); ?></a></h4>
                                        <div class="summary">
                                            <small class="date"><?php print $time->format('j M'); ?></small>
                                            <?php if(!empty($n->getContent())) {
                                                $count = preg_match('/^[\s\S]{0,1000}([^\<]{0,500}?\>|$)/i', $n->getContent(), $matches);
                                                $summary = preg_replace('/<[^>]*>/i', '', $matches[0]);
                                                print $summary;
                                            }
                                            else { ?>
                                            <img src="<?php print $view['router']->generate('notes_thumb', ['note' => $n->getId()]); ?>" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php }
                        if(empty($notes[$s->getId()])) { ?>No notes for this term<?php }

                        if($first) { ?>
                            <div class="highlighted-link form-actions valid">
                                <a href="#add-notebook" class="big-add" data-toggle="modal">Add <span>+</span> notebook</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php $first = false;
            } ?>
            <h3 class="note-title">
                <label class="input books"><select name="notebook">
                    <option value="">Select a notebook</option><?php
                    $allNotebooks = [];
                    if(isset($schedules[0])) {
                        $s = $schedules[0];
                        $courses = $s->getClasses()->toArray();
                        foreach($courses as $i => $c)
                        {
                            /** @var Course $c */
                            // find notebook with matching name
                            $id = '';
                            foreach($notebooks as $guid => $notebookName) {
                                if($notebookName == $c->getName()) {
                                    $allNotebooks[] = $guid;
                                    break;
                                }
                            }
                            ?><option value="<?php print $c->getId(); ?>"><?php print $c->getName(); ?></option><?php
                        }
                    }
                    foreach($notebooks as $guid => $notebookName) {
                        if(!in_array($id = $guid, $allNotebooks)) {
                            ?><option value="<?php print $id; ?>"><?php print $notebookName; ?></option><?php
                        }
                    }
                    ?>
                        <option>Add notebook</option></select></label>
                <label class="input tags">
                    <input type="text" placeholder="Tags" data-data="" value="" autocomplete="off">
                </label>
                <label class="input title"><input type="text" placeholder="Title your note"/></label>
            </h3>
            <?php echo $view->render('StudySauceBundle:Checkin:mini-checkin.html.php'); ?>
            <div id="editor1" contenteditable="true">This is note content</div>
            <div class="highlighted-link"><a href="#delete-note">Delete note</a><a href="#save-note" class="more">Save</a></div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
if (!empty($services)) {
    print $this->render('StudySauceBundle:Dialogs:notes-connect.html.php',['id' => 'notes-connect', 'services' => $services]);
}
print $this->render('StudySauceBundle:Dialogs:notes-discard.html.php',['id' => 'notes-discard']);
print $this->render('StudySauceBundle:Dialogs:add-notebook.html.php',['id' => 'add-notebook']);
print $this->render('StudySauceBundle:Dialogs:delete-notebook.html.php',['id' => 'delete-notebook']);
$view['slots']->stop();
