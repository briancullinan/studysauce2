<?php
use StudySauce\Bundle\Entity\Group;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Send emails to selected users
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<label class="input"><span>Template</span>
    <select name="template">
        <option value="">Select email template</option>
        <?php foreach ($emails as $i => $email) { ?>
            <option value="<?php print $email['id']; ?>"><?php print $email['id']; ?></option>
        <?php } ?>
</select></label>
<table class="variables">
    <tbody>
    <tr>
        <td><label class="input"><span>User: First</span><input type="text" /></label></td>
        <td><label class="input"><span>User: Last</span><input type="text" /></label></td>
        <td><label class="input"><span>User: Email</span><input type="text" /></label></td>
    </tr>
    </tbody>
</table>
<div class="highlighted-link">
    <a href="#add-line" class="big-add">Add <span>+</span> line</a>
    <a href="#close" class="more" data-dismiss="modal">Send now</a>
</div>
<label class="input"><span>Subject</span><input type="text" name="subject" /></label>
<label class="input">
    <span>Preview <a href="#toggle-source">Source</a></span>
    <div class="preview"></div>
</label>
<?php $view['slots']->stop();
