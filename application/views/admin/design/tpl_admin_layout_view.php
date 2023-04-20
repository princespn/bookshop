<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title">
			<?= i('fa fa-pencil') ?> <?=lang('edit_home_page')?>
        </h2>
    </div>
    <div class="col-md-7 text-right">
        <a data-href="<?= admin_url('content_builder/reset_home') ?>" data-toggle="modal"
           data-target="#confirm-reset" href="#"
           class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-undo') ?> <span
                    class="hidden-xs"><?= lang('reset_to_default') ?></span></a>
        <a href="<?= admin_url('layout_manager/layout/' . $category . '/' . $template) ?>?full=1" target="_blank"
           class="btn btn-primary"><?= i('fa fa-external-link') ?>
            <span class="hidden-xs"><?= lang('launch_in_full_window') ?></span></a>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
        <div class="box-info">
            <iframe id="media" src="<?= admin_url('layout_manager/layout/' . $category . '/' . $template) ?>"
                    height="900"></iframe>
        </div>
    </div>
    <div class="modal fade" id="confirm-reset" tabindex="-1" role="dialog" aria-labelledby="modal-headline"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body capitalize">
                    <h3 id="modal-headline"><i class="fa fa-undo "></i> <?= lang('confirm_reset') ?></h3>
					<?= lang('this_will_reset_to_default') ?>. <?= lang('are_you_sure_you_want_to_do_this') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('cancel') ?></button>
                    <a href="<?= admin_url('content_builder/reset_home') ?>"
                       class="btn btn-danger danger"><?= lang('proceed') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>


