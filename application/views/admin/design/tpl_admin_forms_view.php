<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('manage_forms', 'fa-list') ?>
		</div>
	</div>
	<div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('add_form') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<?php if (!empty($rows)): ?>
		<div role="tabpanel" class="tab-pane active" id="admin">
			<hr/>
			<?php foreach ($rows as $v): ?>
				<div class="row text-capitalize">
					<div class="col-sm-10">
						<h5>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['form_id']) ?>"><?= $v['form_name'] ?></a>
						</h5>
						<small><?= check_desc($v['form_description']) ?></small>
					</div>
					<div class="col-sm-2 text-right">
						<a href="<?= generate_form_link($v['form_id']) ?>" target="_blank"
						   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
						   title="<?= lang('view_form') ?>"><?= i('fa fa-search') ?></a>
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update_fields/' . $v['form_id']) ?>"
						   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
						   title="<?= lang('manage_form_fields') ?>"><?= i('fa fa-list') ?></a>
						<?php if ($v['form_type'] == 'custom'): ?>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['form_id']) ?>"
							   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['form_id']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						<?php endif; ?>
					</div>
				</div>
				<hr/>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
