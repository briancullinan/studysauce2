
<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('body'); ?>

<div class="panel-pane" id="premium">

    <div class="pane-content">

        <h2>Welcome back!</h2>

        <input type="hidden" name="_csrf_token" value="{{ csrf_token }}" />

        <label for="username">{{ 'security.login.username'|trans }}</label>
        <input type="text" id="username" name="_username" value="{{ last_username }}" required="required" />

        <label for="password">{{ 'security.login.password'|trans }}</label>
        <input type="password" id="password" name="_password" required="required" />

        <input type="checkbox" id="remember_me" name="_remember_me" value="on" />
        <label for="remember_me">{{ 'security.login.remember_me'|trans }}</label>

        <input type="submit" id="_submit" name="_submit" value="{{ 'security.login.submit'|trans }}" />

    </div>

</div>

<?php $view['slots']->stop(); ?>
