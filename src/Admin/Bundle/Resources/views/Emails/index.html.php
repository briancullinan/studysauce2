<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/admin.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/emails.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/emails.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="emails">
    <div class="pane-content">
    <div class="search">
        <label class="input"><input name="search" type="text" value="" placeholder="Search" /></label>
    </div>
    <table class="templates">
    <thead>
    <tr>
        <th><label><span>Category: </span><br />
                <select name="status">
                    <option value="">Status</option>
                    <option value="_ascending">Ascending (A-Z)</option>
                    <option value="_descending">Descending (Z-A)</option>
                    <option value="FAILED">FAILED</option>
                    <option value="READY">READY</option>
                    <option value="PROCESSING">PROCESSING</option>
                    <option value="COMPLETE">COMPLETE</option>
                </select></label></th>
        <th><label class="input">
                <span>Recent: <?php print $recent; ?></span><br />
                <input type="text" name="created" value="" placeholder="All Emails" />
            </label><div></div></th>
        <th><label><span>Actions</span><br />
                <select name="actions">
                    <option value="">Select All</option>
                    <option value="delete">Delete All</option>
                    <option value="cancel">Cancel All</option>
                    <option value="email">Email All</option>
                    <option value="export">Export All</option>
                    <option value="export">Clear All</option>
                </select></label></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($emails as $i => $email) {
        ?>
        <tr class="email-id-<?php print $email['id']; ?> read-only ">
            <td><?php print $email['id']; ?></td>
            <td><?php print $email['count']; ?></td>
            <td>
                <a href="#edit-email" data-toggle="modal"></a>
                <a href="#send-email" data-toggle="modal"></a>
                <label class="checkbox"><input type="checkbox" name="selected" /><i></i></label>
            </td>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('AdminBundle:Dialogs:send-email.html.php', ['id' => 'send-email', 'emails' => $emails, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('AdminBundle:Dialogs:edit-email.html.php', ['id' => 'edit-email', 'emails' => $emails, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
$view['slots']->stop();
