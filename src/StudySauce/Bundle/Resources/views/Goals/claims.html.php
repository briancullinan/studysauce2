<?php
use StudySauce\Bundle\Entity\Claim;

foreach ($claims as $c)
{
    /** @var Claim $c */
    ?>
    <div class="grid_<?php print (empty($c->getMessage()) || empty($c->getPhoto()) ? 3 : 6); ?>">
        <strong><?php print $c->getCreated()->format('F jS, Y'); ?></strong>
        <?php if (!empty($c->getPhoto())) { ?>
            <img src="<?php print $c->getPhoto()->getUrl(); ?>">
        <?php }
        if (!empty($c->getMessage())) { ?>
            <p><?php print $c->getMessage(); ?></p>
        <?php } ?>
    </div>
<?php }


