<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
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
				class="hidden-xs"><?= lang('view_rules') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="nav nav-tabs text-capitalize">
		<li class="active"><a href="#main" data-toggle="tab"><?= lang('details') ?></a></li>
		<li><a href="#notes" data-toggle="tab"><?= lang('notes') ?></a></li>
	</ul>
	<div class="tab-content">
		<div id="main" class="tab-pane fade in active">
			<h3 class="text-capitalize">
				<?php if (CONTROLLER_FUNCTION == 'update'): ?>
					<?= lang('manage_rule_details') ?>
				<?php else: ?>
					<?= lang('create_rule') ?>
				<?php endif; ?>
			</h3>
			<hr/>
			<div class="form-group">
				<?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-2">
					<?= form_dropdown('status', options('active'), set_value('status', $row['status']), 'id="status" class="form-control"'); ?>
				</div>
				<?= lang('expires_on', 'end_date', array('class' => 'col-md-1 control-label')) ?>
				<div class="r col-md-2">
					<div class="input-group">
                         <span class="input-group-addon">
                                            <?= form_checkbox('enable_end_date', '1', $row['enable_end_date']); ?>
                                            </span>
						<input type="text" name="end_date"
						       value="<?= set_value('end_date', $row['end_date_formatted']) ?>"
						       class="form-control datepicker-input required"/>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-3 control-label"><?= lang('if') ?></label>
				<div class="r col-md-2">
					<?= form_dropdown('sale_type', options('rule_sale_types'), set_value('sale_type', $row['sale_type']), 'id="sale_type" class="form-control"'); ?>
				</div>
				<div id="time_limit"><label class="col-md-1 control-label"><?= lang('for') ?></label>
					<div class="r col-md-2">
						<?= form_dropdown('time_limit', options('rule_time_limit'), set_value('time_limit', $row['time_limit']), 'class="form-control"') ?>
					</div>
				</div>
				<?php if ($sts_affiliate_commission_levels > 1): ?>
					<div id="levels"><label class="col-md-1 control-label"><?= lang('for_level') ?></label>
						<div class="r col-md-2">
							<?= form_dropdown('level', options('commission_levels_any'), set_value('level', $row['level']), 'class="form-control"') ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-3 control-label"><?= lang('is') ?></label>
				<div class="r col-md-2">
					<?= form_dropdown('operator', options('rule_operator'), set_value('operator', $row['operator']), 'class="form-control"') ?>
				</div>
				<label class="col-md-1 control-label"><?= lang('amount') ?></label>
				<div class="r col-md-2">
					<?= form_input('sale_amount', set_value('sale_amount', input_amount($row['sale_amount'])), 'class="' . css_error('sale_amount') . ' form-control required number" placeholder="0.00"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-3 control-label"><?= lang('then') ?></label>
				<div class="r col-md-2">
					<?= form_dropdown('action', options('rule_actions'), set_value('action', $row['action']), 'id="change-status" class="form-control"') ?>
				</div>
				<label class="col-md-1 control-label"></label>
				<div class="r col-md-2">
					<div id="issue_bonus_commission">
						<input name="bonus_amount" type="text" id="bonus_amount" value="<?= $row['bonus_amount'] ?>"
						       class="form-control"/>
					</div>
					<div id="assign_affiliate_group">
						<?= form_dropdown('group_id', options('affiliate_groups'), set_value('group_id', $row['group_id']), 'id="group_id" class="form-control"') ?>
					</div>
				</div>
			</div>
			<hr/>
		</div>
		<div id="notes" class="tab-pane fade in">
			<h3 class="text-capitalize"><?= lang('notes') ?></h3>
			<hr/>
			<label class="col-md-3 control-label"><?= lang('note') ?></label>
			<div class="col-sm-5">
				<?= form_textarea('notes', set_value('notes', $row['notes']), 'class="form-control"') ?>
			</div>
		</div>
	</div>
	<nav class="navbar navbar-fixed-bottom save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('id', $id) ?>
<?php endif; ?>
<?= form_close() ?>

<script>

	<?php if ($row['action'] == 'assign_affiliate_group'): ?>
	$("#group_id").show();
	$("#bonus_amount").hide();
	<?php else: ?>
	$("#bonus_amount").show();
	$("#group_id").hide();
	<?php endif; ?>

	$("#change-status").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "assign_affiliate_group") {
				$("#group_id").show();
				$("#bonus_amount").hide();
			}
			else {
				$("#bonus_amount").show();
				$("#group_id").hide();
			}
		}
	);

	<?php if ($row['sale_type'] == 'total_amount_of_commissions' || $row['sale_type'] == 'total_amount_of_sales' || $row['sale_type'] == 'total_amount_of_referrals'  || $row['sale_type'] == 'total_amount_of_clicks'): ?>
	$("#time_limit").show();
	$("#levels").hide();
	<?php else: ?>
	$("#time_limit").hide();
	$("#levels").show();
	<?php endif; ?>

	$("#sale_type").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "total_amount_of_commissions") {
				$("#levels").hide();
				$("#time_limit").show();
			}
			else if (selectedValue == "total_amount_of_sales") {
				$("#levels").hide();
				$("#time_limit").show();
			}
			else if (selectedValue == "total_amount_of_referrals") {
				$("#levels").hide();
				$("#time_limit").show();
			}
			else if (selectedValue == "total_amount_of_clicks") {
				$("#levels").hide();
				$("#time_limit").show();
			}
			else {
				$("#levels").show();
				$("#time_limit").hide();
			}
		}
	);

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
