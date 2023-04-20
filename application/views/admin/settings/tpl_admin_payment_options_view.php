<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-6">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('payment_options', 'fa-credit-card', $rows['total']) ?>
		</div>
	</div>
	<div class="col-md-6 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url('status/update') ?>"
		   class="btn btn-info"><?= i('fa fa-cog') ?> <span
				class="hidden-xs"><?= lang('payment_statuses') ?></span></a>
		<a href="<?= admin_url('modules/view?module_type=payment_gateways') ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_modules') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<?php if (empty($rows['values'])): ?>
		<?= tpl_no_values() ?>
	<?php else: ?>
		<div role="tabpanel" class="tab-pane active" id="admin">
			<hr/>
			<div id="sortable">
			<?php foreach ($rows['values'] as $v): ?>
			<div class="ui-state-default" id="moduleid-<?= $v['module_id'] ?>">
				<div class="row text-capitalize">
					<div class="col-sm-8 r">
						<h5>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['module_id']) ?>">
								<?= $v['module_name'] ?></a>
						</h5>
						<small><?= check_desc($v['module_description']) ?></small>
					</div>
					<div class="col-sm-2 r">
						<a href="<?= admin_url(TBL_MODULES . '/external/' . $v['module_type'] . '/' . $v['module_folder']) ?>" target="_blank">
						<?php if (file_exists(PUBPATH . '/images/modules/module_payment_gateways_' . $v['module_folder'] . '_logo.png')):   ?>
							<img
								src="<?= base_url('/images/modules/module_payment_gateways_' . $v['module_folder'] . '_logo.png') ?>"
								alt="preview" class="img-responsive"/>
							<?php elseif (file_exists(PUBPATH . '/images/modules/module_payment_gateways_' . $v['module_folder'] . '.png')): ?>
								<img
									src="<?= base_url('/images/modules/module_payment_gateways_' . $v['module_folder'] . '.png') ?>"
									alt="preview" class=" img-responsive"/>
						<?php endif; ?>
						</a>
					</div>
					<div class="col-sm-2 r text-right">
						<span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>">
							<i class="fa fa-sort"></i></span>
						<a href="<?= admin_url('update_status/table/' . TBL_MODULES . '/type/module_status/key/module_id/id/' . $v['module_id']) ?>"
						   class="btn btn-default <?= is_disabled('update', TRUE) ?> "><?= set_status($v['module_status']) ?></a>
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['module_id']) ?>"
						   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i class="fa fa-pencil"></i>
							<span class="visible-xs"><?= lang('edit') ?></span> </a>
					</div>
				</div>
				<hr/>
			</div>
			<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
<div id="update"></div>
<script>
	$(function () {
		$('#sortable').sortable({
			handle: '.handle',
			placeholder: "ui-state-highlight",
			update: function () {
				var order = $('#sortable').sortable('serialize');
				console.log(order);
				$("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/')?>?" + order);
			}
		});
	});

</script>