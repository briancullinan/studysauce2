<?php

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/import.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/import.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="import">
    <div class="pane-content">
        <h2>Invite students to Study Sauce</h2>
        <h3>1) Enter their first name, last name, and email below to invite them to Study Sauce.</h3>
        <h3>2) Your students will receive an invitation with a link that will finish setting up their account.</h3>
        <h3>3) Voila, you are connected.</h3>
        <hr />
        <div class="import-row edit invalid">
            <label class="first-name">
                <span>First name</span>
                <input type="text" placeholder="First name" />
            </label>
            <label class="last-name">
                <span>Last name</span>
                <input type="text" placeholder="Last name" />
            </label>
            <label class="email">
                <span>Email</span>
                <input type="text" placeholder="Email" />
            </label>
        </div>
        <div class="highlighted-link form-actions invalid">
            <a href="#add-user" class="big-add">Add <span>+</span> user</a>
            <a href="#save-group" class="more">Import</a>
        </div>
        <hr />
        <h2>Use our batch importer</h2>
        <h3>1) Paste your comma separated list, one invite per line.</h3>
        <h3>2) Confirm the import worked in the list above and click import above.</h3>
        <label class="import-users">
            <textarea rows="4" placeholder="first,last,email&para;first,last,email"></textarea>
        </label>
        <fieldset id="user-preview">
            <legend>Preview</legend>
        </fieldset>
        <div class="highlighted-link invalid">
            <a href="#import-group" class="more">Import batch</a>
        </div>
    </div>
</div>
<?php $view['slots']->stop();
