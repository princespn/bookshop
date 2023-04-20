<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="prod_form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-group', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'):?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($id != $sts_members_default_discount_group):  ?>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $row['group_id']) ?>"
				   data-toggle="modal" data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>

		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
			<span class="hidden-xs"><?= lang('view_discount_groups') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<h3 class="header"><?= lang('manage_group_details') ?></h3>
			<span class="text-muted">
				<?= lang('manage_group_details_description') ?>
				<?=lang('manage_discount_group_description')?>
			</span>
			<hr />
			<div class="form-group">
				<?= lang('group_name', 'group_name', 'class="col-md-3 control-label"') ?>

				<div class="col-lg-5">
					<?= form_input('group_name', set_value('group_name', $row['group_name']), 'class="' . css_error('group_name') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('group_description', 'group_description', 'class="col-md-3 control-label"') ?>

				<div class="col-lg-5">
					<?= form_textarea('group_description', set_value('group_description', $row['group_description']), 'class="' . css_error('group_description') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('discount_type', 'discount_type', 'class="col-md-3 control-label"') ?>

				<div class="col-lg-5">
					<?= form_dropdown('discount_type', options('flat_percent'), $row['discount_type'], 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('group_amount', 'group_amount', 'class="col-md-3 control-label"') ?>

				<div class="col-lg-5">
					<?= form_input('group_amount', set_value('group_amount', $row['group_amount']), 'class="' . css_error('group_amount') . ' form-control required number"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('sort_order', 'sort_order', 'class="col-md-3 control-label"') ?>

				<div class="col-lg-5">
					<?= form_input('sort_order', set_value('sort_order', $row['sort_order']), 'class="' . css_error('sort_order') . ' form-control required digits"') ?>
				</div>
			</div>
			<hr/>
		</div>
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('group_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-lg-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<button  name="redir_button" value="1" class="btn btn-success navbar-btn block-phone <?= is_disabled('update', true) ?>" id="update-button"
					         type="submit"><?=i('fa fa-plus')?> <?= lang('save_add_another') ?></button>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', true) ?>" id="update-button"
				        type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<br/>