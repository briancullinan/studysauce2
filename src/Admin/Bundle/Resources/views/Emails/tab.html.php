<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    ['@AdminBundle/Resources/public/css/admin.css'],
    [],
    ['output' => 'bundles/admin/css/*.css']
) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(
    ['@AdminBundle/Resources/public/css/emails.css'],
    [],
    ['output' => 'bundles/admin/css/*.css']
) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    ['@AdminBundle/Resources/public/js/emails.js'],
    [],
    ['output' => 'bundles/admin/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    window.entities = JSON.parse('<?php print json_encode($entities); ?>');
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="emails">
        <div class="pane-content">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#templates" data-target="#templates" data-toggle="tab">Templates</a></li>
                <li><a href="#send-email" data-target="#send-email" data-toggle="tab">Send emails</a></li>
            </ul>
            <div class="tab-content">
                <div id="templates" class="tab-pane active">
                    <div class="search">
                        <label class="input"><input name="search" type="text" value="" placeholder="Search"/></label>
                    </div>
                    <table class="templates">
                        <thead>
                        <tr>
                            <th><label><span>Category: </span><br/>
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
                                    <span>Recent: <?php print $recent; ?></span><br/>
                                    <input type="text" name="created" value="" placeholder="All Emails"/>
                                </label>

                                <div></div>
                            </th>
                            <th><label><span>Actions</span><br/>
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
                                    <a href="#send-email" data-toggle="tab"></a>
                                    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div id="send-email" class="tab-pane">
                    <label class="input"><span>Template</span>
                        <select name="template">
                            <option value="">Select email template</option>
                            <?php foreach ($emails as $i => $email) { ?>
                                <option value="<?php print $email['id']; ?>"><?php print $email['id']; ?></option>
                            <?php } ?>
                        </select></label>
                    <table class="variables">
                        <thead>
                        <tr>
                            <th><label>User</label></th>
                            <th><label></label> <a href="#remove-field"></a></th>
                            <th><label></label> <a href="#remove-field"></a></th>
                            <th><a href="#add-field">+</a></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><label class="input"><input name="userFirst" placeholder="First" type="text"/></label></td>
                            <td><label class="input"><input name="userLast" placeholder="Last" type="text"/></label></td>
                            <td><label class="input"><input name="userEmail" placeholder="Email" type="text"/></label></td>
                            <td><a href="#remove-line"></a></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="highlighted-link">
                        <a href="#add-line" class="big-add">Add <span>+</span> line</a>
                        <a href="#send-confirm" class="more" data-toggle="modal">Send now</a>
                    </div>
                    <label class="input"><span>Subject</span><input type="text" name="subject"/></label>
                    <label class="input">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#editor1">Preview</a></li>
                            <li><a href="#markdown">Source</a></li>
                            <li><a href="#headers">Headers</a></li>
                        </ul>
                        <div class="preview"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('AdminBundle:Dialogs:edit-email.html.php',['id' => 'edit-email', 'emails' => $emails, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('AdminBundle:Dialogs:send-confirm.html.php',['id' => 'send-confirm']);
$view['slots']->stop();
