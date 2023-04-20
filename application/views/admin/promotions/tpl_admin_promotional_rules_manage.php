<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
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
				<?= lang('start_date', 'start_date', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-2">
					<div class="input-group">
						<input type="text" name="start_date"
						       value="<?= set_value('start_date', $row['start_date_formatted']) ?>"
						       class="form-control datepicker-input required"/>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<?= lang('expires_on', 'end_date', array('class' => 'col-md-1 control-label')) ?>
				<div class="r col-md-2">
					<div class="input-group">
						<input type="text" name="end_date"
						       value="<?= set_value('end_date', $row['end_date_formatted']) ?>"
						       class="form-control datepicker-input required"/>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('rule', 'rule', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-2">
					<?= form_dropdown('rule', options('promo_rules'), set_value('rule', $row['rule']), 'id="status" class="form-control"'); ?>
				</div>
				<?= lang('if_item_in_cart_is', 'item_id', array('class' => 'col-lg-1 control-label')) ?>
				<div class="col-lg-2">
					<select id="item_id" class="form-control select2 required" name="item_id">
						<?php if (!empty($row['item_id'])): ?>
							<option value="<?= $row['item_id'] ?>"
							        selected><?= $row['item_name'] ?></option>
						<?php endif; ?>
					</select>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-3 control-label"><?= lang('if') ?></label>
				<div class="r col-md-2">
					<?= form_dropdown('type', options('promo_rule_types'), set_value('type', $row['type']), 'id="type" class="form-control"'); ?>
				</div>
				<label class="col-md-1 control-label"><?= lang('is') ?></label>
				<div class="r col-md-2">
					<?= form_dropdown('operator', options('rule_operator'), set_value('operator', $row['operator']), 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-3 control-label"><?= lang('amount') ?></label>
				<div class="r col-md-2">
					<?= form_input('amount', set_value('amount', input_amount($row['amount'])), 'class="' . css_error('sale_amount') . ' form-control required number" placeholder="0.00"') ?>
				</div>
				<label class="col-md-1 control-label"><?= lang('then_issue') ?></label>
				<div class="r col-md-2">
					<?= form_dropdown('action', options('promo_rule_actions'), set_value('action', $row['action']), 'id="action" class="form-control"') ?>
				</div>
			</div>
			<hr/>

			<div class="form-group">
				<label class="col-md-3 control-label"><?= lang('promo_amount') ?></label>
				<div class="r col-md-2">
					<?= form_input('promo_amount', set_value('promo_amount', $row['promo_amount']), 'class="form-control required number" placeholder="0"') ?>
				</div>
				<div id="special_offer">
					<?= lang('free_product', 'product', array('class' => 'col-lg-1 control-label')) ?>
					<div class="col-lg-2">
						<select id="product_id" class="form-control select2" name="product_id">
							<?php if (!empty($row['product_id'])): ?>
								<option value="<?= $row['product_id'] ?>"
								        selected><?= $row['product_name'] ?></option>
							<?php endif; ?>
						</select>
					</div>
				</div>
				<div id="quantity_discount">
					<?= lang('discount_type', 'discount_type', array('class' => 'col-lg-1 control-label')) ?>
					<div class="col-lg-2">
						<?= form_dropdown('discount_type', options('flat_percent'), set_value('discount_type', $row['discount_type']), 'id="discount_type" class="form-control"') ?>
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
	<?= form_hidden('rule_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>

<script>

	$("#change-status").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "issue_bonus_commission") {
				$("#bonus_amount").show();
				$("#group_id").hide();
			}
			if (selectedValue == "assign_affiliate_group") {
				$("#group_id").show();
				$("#bonus_amount").hide();
			}
		}
	);

	<?php if ($row['action'] == 'quantity_discount'): ?>
	$("#quantity_discount").show();
	$("#special_offer").hide();
	$('#discount_type').addClass('required');
	$('#product_id').removeClass('required');
	<?php else: ?>
	$("#quantity_discount").hide();
	$("#special_offer").show();
	$('#discount_type').removeClass('required');
	$('#product_id').addClass('required');
	<?php endif; ?>

	$("#action").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "special_offer") {
				$("#quantity_discount").hide();
				$("#special_offer").show();
				$('#discount_type').removeClass('required');
				$('#product_id').addClass('required');
			}
			else {
				$("#quantity_discount").show();
				$("#special_offer").hide();
				$('#discount_type').addClass('required');
				$('#product_id').removeClass('required');
			}
		}
	);

	$("#product_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_PRODUCTS . '/search/ajax/?product_type=general')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					product_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.product_id,
							text: item.product_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 1
	});

	$("#item_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_PRODUCTS . '/search/ajax/?product_type=general')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					product_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.product_id,
							text: item.product_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 1
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
