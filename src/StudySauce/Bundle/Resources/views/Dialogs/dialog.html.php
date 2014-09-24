<!-- Modal -->
<div class="modal fade" id="<?php print $id; ?>" tabindex="-1" role="dialog" aria-labelledby="contact-support" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?php $view['slots']->output('modal-header') ?>
            </div>
            <div class="modal-body">
                <?php $view['slots']->output('modal-body') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <?php $view['slots']->output('modal-footer') ?>
            </div>
        </div>
    </div>
</div>