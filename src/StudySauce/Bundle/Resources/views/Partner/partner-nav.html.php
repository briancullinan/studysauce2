<ul class="nav nav-tabs">
    <li><a href="<?php print $view['router']->generate('metrics_partner', ['_user' => $user->getId()]); ?>">Metrics</a></li>
    <li><a href="<?php print $view['router']->generate('goals_partner', ['_user' => $user->getId()]); ?>">Goals</a></li>
    <li><a href="<?php print $view['router']->generate('deadlines_partner', ['_user' => $user->getId()]); ?>">Deadlines</a></li>
    <li><a href="<?php print $view['router']->generate('uploads_partner', ['_user' => $user->getId()]); ?>">Uploads</a></li>
    <li><a href="<?php print $view['router']->generate('plan_partner', ['_user' => $user->getId()]); ?>">Plan</a></li>
</ul>