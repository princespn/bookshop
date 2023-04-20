<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_payments') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="box-info">
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#details" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
        <li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?php if (config_enabled('sts_invoice_show_debug_info') && !empty($row['debug_info'])): ?>
				<li><a href="#debug" role="tab" data-toggle="tab"><?= lang('debug_info') ?></a></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="details">
			<h3 class="text-capitalize">
				<?php if (CONTROLLER_FUNCTION == 'update'): ?>
					<?= lang('invoice_id') ?> #<?= $row['invoice_number'] ?>
				<?php else: ?>
					<?= lang('create_payment') ?>
				<?php endif; ?>
			</h3>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('method', 'method', array('class' => 'col-md-3 control-label')) ?>
						<div class="r col-md-2">
							<?= form_dropdown('method', options('payment_methods'), $row['method'], 'class="form-control required"') ?>
						</div>
						<?= lang('date', 'date', array('class' => 'col-md-1 control-label')) ?>
						<div class="r col-md-2">
							<div class="input-group">
								<input type="text" name="date"
								       value="<?= set_value('date', $row['date_formatted']) ?>"
								       class="form-control datepicker-input required"/>
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('transaction_id', 'transaction_id', array('class' => 'col-md-3 control-label')) ?>
						<div class="r col-md-2">
							<?= form_input('transaction_id', set_value('transaction_id', $row['transaction_id']), 'class="' . css_error('transaction_id') . ' form-control required"') ?>
						</div>
						<?= lang('invoice_id', 'invoice_id', array('class' => 'col-md-1 control-label')) ?>
						<div class="r col-md-2">
							<select id="invoice_id" class="form-control select2 required"
							        name="invoice_id">
								<option value="<?= $row['invoice_id'] ?>"
								        selected><?= $row['invoice_number'] ?></option>
							</select>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('amount', 'amount', array('class' => 'col-md-3 control-label')) ?>
						<div class="r col-md-2">
							<?php if (empty($row['amount']) && !empty($row['total'])): ?>
								<?= form_input('amount', set_value('amount', input_amount($row['total'])), 'class="' . css_error('amount') . ' form-control required number" placeholder="0.00"') ?>
							<?php else: ?>
								<?= form_input('amount', set_value('amount', input_amount($row['amount'])), 'class="' . css_error('amount') . ' form-control required number" placeholder="0.00"') ?>
							<?php endif; ?>
						</div>
						<?= lang('fee', 'fee', array('class' => 'col-md-1 control-label')) ?>
						<div class="r col-md-2">
							<?= form_input('fee', set_value('fee', input_amount($row['fee'])), 'class="' . css_error('fee') . ' form-control number" placeholder="0.00"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('description', 'description', array('class' => 'col-md-3 control-label')) ?>
						<div class="col-md-5">
							<?= form_input('description', set_value('description', $row['description']), 'class="' . css_error('description') . ' form-control required"') ?>
						</div>
					</div>
					<hr/>
					<?php if (config_enabled('affiliate_marketing') && !empty($row['affiliate_id'])): ?>
						<?php if (CONTROLLER_FUNCTION == 'create'): ?>
							<div class="form-group">
								<?= lang('process_commissions', 'process_commissions', array('class' => 'col-md-3 control-label')) ?>
								<div class="col-md-5">
									<?= form_dropdown('process_commissions', options('process_commissions'), '', 'class="form-control required"') ?>
								</div>
							</div>
							<hr/>
						<?php endif; ?>
					<?php endif; ?>
					<div class="form-group">
						<?= lang('cc_type', 'cc_type', array('class' => 'col-md-3 control-label')) ?>
						<div class="r col-md-2">
							<?= form_dropdown('cc_type', options('cc_type'), set_value('cc_type', $row['cc_type']), 'class="form-control"') ?>
						</div>
						<?= lang('cc_last_four', 'cc_last_four', array('class' => 'col-md-1 control-label')) ?>

						<div class="r col-md-2">
							<?= form_input('cc_last_four', set_value('cc_last_four', $row['cc_last_four']), 'class="' . css_error('cc_last_four') . ' form-control digits" maxlength="4"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('cc_month', 'cc_month', array('class' => 'col-md-3 control-label')) ?>

						<div class="r col-md-2">
							<?= form_dropdown('cc_month', options('cc_months'), set_value('cc_month', $row['cc_month']), 'id="card-expiry-month" class="form-control required"') ?>
						</div>
						<?= lang('cc_year', 'cc_year', array('class' => 'col-md-1 control-label')) ?>

						<div class="r col-md-2">
							<?= form_dropdown('cc_year', options('cc_years'), set_value('cc_year', $row['cc_year']), 'id="card-expiry-year" class="form-control required"') ?>
						</div>
					</div>
					<hr/>
				</div>
			</div>
			<nav class="navbar navbar-fixed-bottom save-changes">
				<div class="container text-right">
					<div class="row">
						<div class="col-md-12">
							<?php if (CONTROLLER_FUNCTION == 'update' && $row['amount'] > 0): ?>
								<a data-toggle="modal" data-target="#confirm-refund" href="#"
								   class="md-trigger btn btn-danger visible-lg-inline <?= is_disabled('delete') ?>">
									<?= i('fa fa-cog') ?><?= lang('process_refund') ?></a>
							<?php endif; ?>

							<button class="btn btn-info navbar-btn block-phone"
							        id="update-button" <?= is_disabled('update', TRUE) ?>
							        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
						</div>
					</div>
				</div>
			</nav>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= form_hidden('invoice_payment_id', $row['invoice_payment_id']) ?>
			<?php endif; ?>
		</div>
        <div class="tab-pane fade in" id="notes">
            <h3 class="text-capitalize"><?= lang('notes') ?></h3>
            <hr/>
            <div class="form-group">
		        <?= lang('notes', 'notes', array('class' => 'col-md-3 control-label')) ?>

                <div class="col-md-5">
			        <?= form_textarea('notes', set_value('notes', $row['notes']), 'class="' . css_error('notes') . ' form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?php if (config_enabled('sts_invoice_show_debug_info') && !empty($row['debug_info'])): ?>
				<div class="tab-pane fade in" id="debug">
					<h3 class="text-capitalize"><?= lang('debug_information') ?></h3>
					<hr/>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<?= lang('debug', 'debug', array('class' => 'col-md-3 control-label')) ?>
								<div class="col-md-8">
									<textarea class="form-control" rows="10"
									          disabled><?= show_debug($row['debug_info']) ?></textarea>
								</div>
							</div>
							<hr/>
						</div>
					</div>
					<hr/>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<?= form_close() ?>
<div class="modal fade" id="confirm-refund" tabindex="-1" role="dialog" aria-labelledby="modal-title"
     aria-hidden="true">
	<div class="modal-dialog" id="modal-title">
		<div class="modal-content">
			<?= form_open(admin_url(CONTROLLER_CLASS . '/refund/' . $row['invoice_payment_id']), 'role="form" id="refund-form"') ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?= i('fa fa-cog') ?> <?= lang('confirm_refund') ?></h4>
			</div>
			<div class="modal-body capitalize">
				<div class="row">
					<div class="col-md-12">
						<strong><?=lang('are_you_sure_you_want_to_refund')?></strong>
					</div>
				</div>
				<hr />
				<div class="row">
					<?= lang('method', 'refund_method', array('class' => 'col-md-2 col-md-offset-1 control-label')) ?>
					<div class="r col-md-8">
						<select name="refund_method" class="form-control">
							<option value="manual"><?= lang('process_refund_manually') ?></option>
							<?php if (!empty($refund_module)): ?>
								<option value="<?= $row['method'] ?>"><?= lang('process_refund_through') . ' ' . lang($row['method']) ?></option>
							<?php endif; ?>
						</select>
					</div>
				</div>
				<hr />
				<div class="row">
					<?= lang('amount', 'refund_amount', array('class' => 'col-md-2 col-md-offset-1 control-label')) ?>

					<div class="r col-md-8">
						<?= form_input('refund_amount', input_amount($row['amount']), 'class="form-control numeric"') ?>

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('cancel') ?></button>
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= i('fa fa-cog') ?> <?= lang('process_refund') ?></button>
			</div>
			<?php form_close() ?>
		</div>
	</div>
</div>
<script>

	$("#invoice_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_INVOICES . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					invoice_number: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.invoice_id,
							text: item.invoice_number
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=current_url()?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
				beforeSend: function () {
					$('#update-button').button('loading');
				},
				complete: function () {
					$('#update-button').button('reset');
				},
				success: function (response) {
					if (response.type == 'success') {
						$('.alert-danger').remove();
						$('.form-control').removeClass('error');

						if (response.redirect) {
							location.href = response.redirect;
						}
						else {
							$('#response').html('<?=alert('success')?>');

							setTimeout(function () {
								$('.alert-msg').fadeOut('slow');
							}, 5000);
						}
					}
					else {
						$('#response').html('<?=alert('error')?>');
						if (response['error_fields']) {
							$.each(response['error_fields'], function (key, val) {
								$('#' + key).addClass('error');
								$('#' + key).focus();
							});
						}
					}

					$('#msg-details').html(response.msg);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});

	$('#confirm-refund').on('show.bs.modal', function (e) {
		$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
	});

	$("#refund-form").validate();
</script>