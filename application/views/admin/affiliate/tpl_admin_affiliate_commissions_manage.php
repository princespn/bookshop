<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_commissions') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#details" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
		<li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="details">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="text-capitalize"><?= lang('commission_details') ?></h3>
                </div>
                <div class="col-md-4">
	                <?php if ($row['comm_status'] == 'paid'): ?>
                        <div class="alert alert-success">
                            <strong><?=i('fa fa-info-circle')?> <?=lang('paid')?></strong><br />
                            <small><?=lang('commission_already_marked_paid')?></small>
                        </div>
	                <?php endif; ?>
                </div>
            </div>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('status', 'comm_status', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_dropdown('comm_status', options('comm_statuses'), $row[ 'comm_status' ], 'class="form-control required" id="comm_status"') ?>
						</div>
						<?= lang('approved', 'approved', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_dropdown('approved', options('yes_no'), $row[ 'approved' ], 'class="form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('date_generated', 'date', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<div class="input-group">
								<input type="text" name="date"
								       value="<?= set_value('date', $row[ 'date_formatted' ]) ?>"
								       class="form-control datepicker-input required"/>
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<?= lang('referred_by', 'member_id', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<select id="member_id" class="form-control select2"
							        name="member_id">
								<option value="<?= set_value('member_id', $row[ 'member_id' ]) ?>"
								        selected><?= set_value('username', $row[ 'username' ]) ?></option>
							</select>
						</div>
					</div>
					<hr/>
					<div id="payment-div">
						<div class="form-group">
							<?= lang('date_paid', 'date_paid', array( 'class' => 'col-md-3 control-label' )) ?>
							<div class="r col-md-2">
								<div class="input-group">
									<input type="text" name="date_paid"
									       value="<?= set_value('date_paid', $row[ 'date_paid_formatted' ]) ?>"
									       class="form-control datepicker-input required"/>
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div>
							<?= lang('payment_id', 'payment_id', array( 'class' => 'col-md-1 control-label' )) ?>
							<div class="r col-md-2">
								<?= form_input('payment_id', set_value('payment_id', $row[ 'payment_id' ]), 'class="' . css_error('payment_id') . ' form-control number" placeholder="0"') ?>
							</div>
						</div>
						<hr/>
					</div>
					<?php if (CONTROLLER_FUNCTION == 'create'): ?>
						<div class="form-group">
							<?= lang('use_group_amounts', 'use_group_amounts', array( 'class' => 'col-md-3 control-label' )) ?>
							<div class="r col-md-5">
								<?= form_dropdown('use_group_amounts', options('yes_no'), '', 'id="use_group_amounts" class="form-control"') ?>
							</div>
						</div>
						<hr/>
						<?php if (check_upline_config()): ?>
							<div class="group" id="upline-div">
								<div class="form-group">
									<?= lang('generate_upline_commission', 'generate_upline_commission', array( 'class' => 'col-md-3 control-label' )) ?>
									<div class="r col-md-5">
										<?= form_dropdown('generate_upline', options('yes_no'), '', 'id="generate_upline_commission" class="form-control"') ?>
									</div>
								</div>
								<hr/>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<div class="form-group">
						<?= lang('sale_amount', 'sale_amount', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<?= form_input('sale_amount', set_value('sale_amount', input_amount($row[ 'sale_amount' ])), 'class="' . css_error('sale_amount') . ' form-control required number" placeholder="0.00"') ?>
						</div>
					</div>
					<hr/>
					<div class="group" id="comm-div">
						<div class="form-group">
							<?= lang('commission_amount', 'commission_amount', array( 'class' => 'col-md-3 control-label' )) ?>
							<div class="r col-md-5">
								<?= form_input('commission_amount', set_value('commission_amount', input_amount($row[ 'commission_amount' ])), 'class="' . css_error('commission_amount') . ' form-control number" placeholder="0.00"') ?>
							</div>
						</div>
						<hr/>
					</div>
					<?php if (check_upline_config()): ?>
						<div class="group" id="level-div">
							<div class="form-group">
								<?= lang('commission_level', 'commission_level', array( 'class' => 'col-md-3 control-label' )) ?>
								<div class="r col-md-5">
									<?= form_dropdown('commission_level', options('commission_levels'), $row[ 'commission_level' ], 'class="form-control"') ?>
								</div>
							</div>
							<hr/>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<?= lang('transaction_id', 'trans_id', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<?= form_input('trans_id', set_value('trans_id', $row[ 'trans_id' ]), 'class="' . css_error('trans_id') . ' form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('invoice_id', 'invoice_id', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<select id="invoice_id" class="form-control select2"
							        name="invoice_id">
								<option value="<?= $row[ 'invoice_id' ] ?>"
								        selected><?= $row[ 'invoice_number' ] ?></option>
							</select>
						</div>
						<?= lang('fee', 'fee', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_input('fee', set_value('fee', input_amount($row[ 'fee' ])), 'class="' . css_error('fee') . ' form-control number" placeholder="0.00"') ?>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
		<div class="tab-pane fade in" id="notes">
			<h3 class="text-capitalize"><?= lang('commission_notes') ?></h3>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('notes', 'commission_notes', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<textarea name="commission_notes" class="form-control"
							          rows="10"><?= $row[ 'commission_notes' ] ?></textarea>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('referring_web_page', 'referrer', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<?= form_input('referrer', set_value('referrer', $row[ 'referrer' ]), 'class="' . css_error('referrer') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
	</div>
	<nav class="navbar navbar-fixed-bottom save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('comm_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<script>
	$("#member_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_MEMBERS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					username: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.member_id,
							text: item.username
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

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

	$("select#comm_status").change(function () {
		$("select#comm_status option:selected").each(function () {
			if ($(this).attr("value") == "paid") {
				$("#payment-div").show(100);
			}
			else {
				$("#payment-div").hide(100);
			}
		});
	}).change();

	$("select#use_group_amounts").change(function () {
		$("select#use_group_amounts option:selected").each(function () {
			if ($(this).attr("value") == "0") {
				$("#comm-div").show(100);
				$("#level-div").show(100);
				$("#upline-div").hide(100);
			}
			else {
				$("#comm-div").hide(100);
				$("#level-div").hide(100);
				$("#upline-div").show(100);
			}
		});
	}).change();


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
</script>