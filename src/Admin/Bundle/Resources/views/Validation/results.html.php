<pre><?php $printer->printResult($result); ?></pre>
<?php foreach($steps as $test => $output) { ?>
<div class="test-id-<?php print $test; ?>">
    <?php print $output; ?>
</div>
<?php } ?>
<?php
foreach($errors as $e) {
    print $e;
}
