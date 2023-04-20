<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-7">
	       <div class="sub-header block-title"><?=lang('site_builder')?> - <?= $row['title'] ?></div>
        </div>
        <div class="col-md-5 text-right">
            <a href="<?= base_url(SITE_BUILDER . '/' . $id . '?full_screen=1') ?>" class="btn btn-success"><?= i('fa fa-refresh') ?>
                <span class="hidden-xs"><?= lang('full_screen') ?></span></a>
            <a data-href="<?= admin_url('site_builder/reset/' . $row['page_id']) ?>" data-toggle="modal"
               data-target="#confirm-reset" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-undo') ?> <span
                        class="hidden-xs"><?= lang('reset_to_default') ?></span></a>
            <a href="<?= admin_url('site_pages/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
                <span class="hidden-xs"><?= lang('view_pages') ?></span></a>
        </div>
    </div>
</div>
<hr />
    <iframe id="media" src="<?= base_url( SITE_BUILDER . '/' . $id) ?>" height="800"></iframe>

    <div class="modal fade" id="confirm-reset" tabindex="-1" role="dialog" aria-labelledby="modal-headline"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body capitalize">
                    <h3 id="modal-headline"><i class="fa fa-undo "></i> <?= lang('confirm_reset') ?></h3>
					<?= lang('this_will_reset_to_default') ?>. <?= lang('are_you_sure_you_want_to_do_this') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?= lang('cancel') ?></button>
                    <a href="<?= admin_url('site_builder/reset/' . $row['page_id']) ?>"
                       class="btn btn-danger danger"><?= lang('proceed') ?></a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>