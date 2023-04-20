<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('dashboard_icons', 'fa-pencil') ?>
		</div>
	</div>
	<div class="col-md-4 text-right">
		<a href="<?= admin_url( 'layout_manager/config') ?>" class="btn btn-primary"><i
				class="fa fa-undo"></i> <span class="hidden-xs"><?= lang('back_to_site_layout') ?></span></a>
		<a href="<?= admin_url('dashboard/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i>
			<span class="hidden-xs"><?= lang('add_icon') ?></span></a>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="text-capitalize"><?= lang('members_dashboard_icons') ?></h3>
	<span class="text-capitalize"><?= lang('dashboard_icons_sort_order_description') ?></span>
	<hr/>
	<?= form_open('', 'id="form" class="form-horizontal"') ?>
	<div class="row" id="sortable">
		<?php if (!empty($icons)): ?>
			<?php foreach ($icons as $v): ?>
				<div class="ui-state-default col-lg-2 col-sm-6" id="id-<?= $v['dash_id'] ?>">
					<div class="thumbnail text-center icons handle cursor">
						<div class="gallery-item">
							<br />
							<p class="lead"><?= i('fa fa-3x ' . $v['icon']) ?></p>
							<h5 class="card-title"><?= lang($v['title']) ?></h5>
							<div class="caption">
								<a href="<?= admin_url('update_status/table/' . TBL_MEMBERS_DASHBOARD . '/type/status/key/dash_id/id/' . $v['dash_id']) ?>"
								   class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
								<a href="<?= admin_url('dashboard/update/' . $v['dash_id']) ?>"
								   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
								<?php if ($v['type'] == 'custom'): ?>
								<a data-href="<?= admin_url('dashboard/delete/' . $v['dash_id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg-inline <?= is_disabled('delete') ?>">
									<?= i('fa fa-trash-o') ?></a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?= form_close() ?>
</div>
<div id="update"></div>
<script>
	$(function () {
		$('#sortable').sortable({
			handle: '.handle',
			placeholder: "ui-state-highlight",
			update: function () {
				var order = $('#sortable').sortable('serialize');
				$("#update").load("<?=admin_url('dashboard/update_order')?>?" + order);
			}
		});
	});

</script>