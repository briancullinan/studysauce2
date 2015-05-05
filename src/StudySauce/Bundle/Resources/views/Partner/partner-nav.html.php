<ul class="nav nav-tabs">
    <li><?php print $user->getFirst() . ' ' . $user->getLast(); ?>
    <a href="mailto:<?php print $user->getEmail(); ?>">Email</a></li>
    <li><a href="#metrics">Metrics</a></li>
    <li><a href="#goals">Goals</a></li>
    <li><a href="#deadlines">Deadlines</a></li>
    <li><a href="#plan">Plan</a></li>
    <li><a href="#results">Results</a></li>
</ul>