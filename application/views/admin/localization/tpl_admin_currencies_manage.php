<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
				<a data-href="<?= admin_url(TBL_CURRENCIES . '/delete/' . $row['currency_id']) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>

		<a href="<?= admin_url(TBL_CURRENCIES . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_currencies') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<div class="hidden-xs">
				<h3 class="text-capitalize">
					<?= lang('manage_currency') ?>
				</h3>
				<span><?=lang('manage_currency_details')?></span>
			</div>
			<hr/>
			<div class="form-group">
				<label for="name" class="col-md-3 control-label"><?= lang('currency_name') ?></label>

				<div class="col-md-2">
					<?= form_input('name', set_value('name', $row['name']), 'class="' . css_error('name') . ' form-control required"') ?>
				</div>
				<label for="value" class="col-md-1 control-label"><?= lang('value') ?></label>

				<div class="col-md-2">
					<?= form_input('value', set_value('value', $row['value']), 'class="' . css_error('value') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="code" class="col-md-3 control-label"><?= lang('code') ?></label>
				<div class="col-md-2">
					<?= form_input('code', set_value('code', $row['code'], FALSE), 'class="' . css_error('code') . ' form-control required" maxlength="3"') ?>
				</div>
				<label for="symbol_left" class="col-md-1 control-label"><?= lang('symbol_left') ?></label>

				<div class="col-md-2">
					<?= form_input('symbol_left', set_value('symbol_left', $row['symbol_left'], FALSE), 'class="' . css_error('symbol_left') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="symbol_right" class="col-md-3 control-label"><?= lang('symbol_right') ?></label>

				<div class="col-md-2">
					<?= form_input('symbol_right', set_value('symbol_right', $row['symbol_right'], FALSE), 'class="' . css_error('symbol_right') . ' form-control"') ?>
				</div>
				<label for="decimal_point" class="col-md-1 control-label"><?= lang('decimal_point') ?></label>

				<div class="col-md-2">
					<?= form_input('decimal_point', set_value('decimal_point', $row['decimal_point']), 'class="' . css_error('decimal_point') . ' form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="thousands_point" class="col-md-3 control-label"><?= lang('thousands_point') ?></label>

				<div class="col-md-2">
					<?= form_input('thousands_point', set_value('thousands_point', $row['thousands_point']), 'class="' . css_error('thousands_point') . ' form-control"') ?>
				</div>
				<label for="decimal_places" class="col-md-1 control-label"><?= lang('decimal_places') ?></label>

				<div class="col-md-2">
					<?= form_input('decimal_places', set_value('decimal_places', $row['decimal_places']), 'class="' . css_error('decimal_places') . ' form-control"') ?>
				</div>
			</div>
			<hr/>
		</div>
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('country_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
					       class="btn btn-success navbar-btn block-phone"/>
				<?php endif; ?>
				<button class="btn btn-info btn-submit navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= i('fa fa-refresh') ?> <span><?= lang('save_changes') ?></span></button>
			</div>
		</div>
	</div>
</nav>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('currency_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
            ajax_it('<?=current_url()?>', 'form');
		}
	});
</script>