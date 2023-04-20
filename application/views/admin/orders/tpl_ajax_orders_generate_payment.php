<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <h3 class="text-capitalize"><?= lang('generate_order_invoice_and_confirmation') ?></h3>
    <hr/>
<?= form_open(admin_url('orders/create_order'), 'role="form" id="create-order-form" class="form-horizontal"') ?>
	<div class="form-group">
		<label class="col-md-4 control-label">
			<?= lang('generate_invoice_with_order') ?>
		</label>

		<div class="col-md-6">
			<select id="generate_invoice" class="form-control" name="generate_invoice">
				<option value="1"><?= lang('yes') ?></option>
				<option value="2"><?= lang('yes_and_send_invoice_email') ?></option>
				<option value="0"><?= lang('no_create_order_only') ?></option>
			</select>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-4 control-label">
			<?= lang('send_order_confirmation') ?>
		</label>

		<div class="col-md-6">
			<select id="send_email" class="form-control" name="send_email">
				<option value="1"><?= lang('yes') ?></option>
				<option value="0"><?= lang('no') ?></option>
			</select>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
                                        <textarea name="order_notes" class="form-control" rows="5"
                                                  placeholder="<?= lang('enter_notes_for_order_here') ?>"></textarea>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-sm-12 text-right">
			<button type="submit" class="btn btn-lg btn-info">
				<?= i('fa fa-refresh') ?> <?= lang('save_and_create_order') ?>
			</button>
		</div>
	</div>
<?= form_close() ?>