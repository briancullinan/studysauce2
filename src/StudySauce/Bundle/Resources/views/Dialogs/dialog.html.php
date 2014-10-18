<!-- Modal -->
<div class="modal fade" id="<?php print $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?php $view['slots']->output('modal-header') ?>
            </div>
            <?php if($view['slots']->get('modal-body') != null): ?>
            <div class="modal-body">
                <?php $view['slots']->output('modal-body') ?>
            </div>
            <?php endif; ?>
            <?php if($view['slots']->get('modal-footer') != null): ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <?php $view['slots']->output('modal-footer') ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>