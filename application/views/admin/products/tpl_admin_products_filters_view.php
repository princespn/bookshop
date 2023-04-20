<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-file-text-o') ?>
        <hr class="visible-xs"/>
    </div>
    <div class="col-md-4 text-right">
	    <a href="<?= admin_url('update_status/settings/sts_products_filters_enable/') ?>"
	       class="btn btn-info <?= is_disabled('create') ?>">
		    <?= i('fa fa-info-circle') ?>
		    <?php if (config_enabled('sts_products_filters_enable')): ?>
			    <span class="hidden-xs"><?= lang('deactivate_product_filters') ?></span>
		    <?php else: ?>
			    <span class="hidden-xs"><?= lang('activate_product_filters') ?></span>
		    <?php endif; ?>
	    </a>
    </div>
</div>
<hr/>
<?php if (empty($rows)): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
    <div class="box-info">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
        <div class="row text-capitalize hidden-xs hidden-sm">
            <div class="col-md-1 text-center"><h5><?= lang('status') ?></h5></div>
            <div class="col-md-8"><h5><?= lang('filter_name') ?></h5></div>
            <div class="col-md-3 text-right"></div>
        </div>
        <hr/>
        <div id="sortable">
			<?php foreach ($rows as $v): ?>
                <div class="ui-state-default" id="filterid-<?= $v['filter_id'] ?>">
                    <div class="row">
                        <div class="col-md-1 text-center hidden-xs hidden-sm">
                            <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/filter_id/id/' . $v['filter_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
                        </div>
                        <div class="col-md-8">
                            <h5>
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['filter_id']) ?>">
									<?= lang($v['filter_name']) ?></a>
                            </h5>
							<?= lang($v['filter_description']) ?>
                        </div>
                        <div class="col-md-3 text-right">
	                     <span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>">
		                     <i class="fa fa-sort"></i></span>
	                        <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'filter_id' ]) ?>"
	                           class="btn btn-default <?= is_disabled('update', TRUE) ?>" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> </a>
                        </div>
                    </div>
	                <hr/>
                </div>
			<?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-md-12"></div>
        </div>
		<?= form_close() ?>
    </div>
    <div id="update"></div>
<?php endif; ?>
<script>
    $(function () {
        $('#sortable').sortable({
            handle: '.handle',
            placeholder: "ui-state-highlight",
            update: function () {
                var order = $('#sortable').sortable('serialize');
                $("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/')?>?" + order);
            }
        });
    });
</script>
