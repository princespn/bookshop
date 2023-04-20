<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="text-center load-time">
    <small><?= lang('load_time') ?>: <?= $this->benchmark->elapsed_time() ?>
        | <?= display_date(get_time(), TRUE, 3, TRUE) ?> | <?= APP_REVISION_NUMBER?></small>
</div>
</div>
</div> <!-- END CONTENT -->
</div> <!-- END CONTENT-PAGE -->
</div> <!-- END CONTAINER -->

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body capitalize">
                <h3><?=i('fa fa-trash-o')?> <?= lang('confirm_deletion') ?></h3>
                <?= confirm_deletion(CONTROLLER_METHOD) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('cancel') ?></button>
                <a href="#" class="btn btn-danger danger"><?= lang('delete') ?></a>
            </div>
        </div>
    </div>
</div>
<?php if (empty($disable_ajax_loader)): ?>
    <div id="loading" class="spinner"><?=i('fa fa-spinner fa-pulse')?></div>
<?php endif; ?>
