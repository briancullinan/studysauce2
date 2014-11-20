<!-- Modal -->
<div class="modal fade" id="<?php print $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php if($view['slots']->get('modal-header') != null): ?>
            <div class="modal-header">
                <a href="#close-dialog" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></a>
                <?php $view['slots']->output('modal-header') ?>
            </div>
            <?php endif; ?>
            <?php if($view['slots']->get('modal-body') != null): ?>
            <div class="modal-body">
                <?php $view['slots']->output('modal-body') ?>
            </div>
            <?php endif; ?>
            <?php if($view['slots']->get('modal-footer') != null): ?>
            <div class="modal-footer">
                <?php $view['slots']->output('modal-footer') ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>