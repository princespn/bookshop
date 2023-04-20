<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> Create Pincode</h2>
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
				class="hidden-xs">View Pincode</span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="nav nav-tabs text-capitalize">
		<li class="active"><a href="#main" data-toggle="tab"><?= lang('details') ?></a></li>
		<li><a href="#options" data-toggle="tab"><?= lang('options') ?></a></li>
	</ul>
	<div class="tab-content">
		<div id="main" class="tab-pane fade in active">
			<h3 class="text-capitalize">
				<?php if (CONTROLLER_FUNCTION == 'update'): ?>
					<?= lang('manage_coupon_details') ?>
				<?php else: ?>
					<?= lang('create_coupon') ?>
				<?php endif; ?>
			</h3>
			<hr/>
			<div class="form-group">
				<?= lang('coupon_code', 'coupon_code', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-2">
					<div class="input-group">
						<?= form_input('coupon_code', set_value('coupon_code', $row['coupon_code']), ' id="coupon_code" class="' . css_error('coupon_code') . ' form-control"') ?>
						<span id="generate_code" class="cursor input-group-addon">
						<i class="fa fa-refresh"></i></span>
					</div>
				</div>
				<?= lang('status', 'status', array('class' => 'col-md-1 control-label')) ?>
				<div class="r col-md-2">
					<?= form_dropdown('status', options('active'), set_value('status', $row['status']), 'id="status" class="form-control required"'); ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('coupon_amount', 'coupon_amount', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-2">
					<?= form_input('coupon_amount', set_value('coupon_amount', input_amount($row['coupon_amount'])), 'class="' . css_error('coupon_amount') . ' form-control number"') ?>
				</div>
				<?= lang('coupon_type', 'coupon_type', array('class' => 'col-md-1 control-label')) ?>
				<div class="r col-md-2">
					<?= form_dropdown('coupon_type', options('flat_percent'), set_value('coupon_type', $row['coupon_type']), 'id="coupon_type" class="form-control" required'); ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('start_date', 'start_date', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-2">
					<div class="input-group">
						<input type="text" name="start_date"
						       value="<?= set_value('start_date', $row['start_date']) ?>"
						       class="form-control datepicker-input required"/>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
				<?= lang('expires_on', 'end_date', array('class' => 'col-md-1 control-label')) ?>
				<div class="r col-md-2">
					<div class="input-group">
						<input type="text" name="end_date"
						       value="<?= set_value('end_date', $row['end_date']) ?>"
						       class="form-control datepicker-input required"/>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('restrict_to_products', 'restrict_products', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-5">
					<?= form_dropdown('restrict_products', options('yes_no'), set_value('restrict_products', $row['restrict_products']), 'id="restrict_products" class="form-control" required'); ?>
				</div>
			</div>
			<div id="show_products">
				<hr/>
				<div class="form-group">
					<?= lang('select_products', 'select_products', array('class' => 'col-md-3 control-label')) ?>
					<div class="r col-md-5">
						<select multiple id="select_products" class="form-control select2"
						        name="select_products[]">
							<?php if (!empty($row['select_products'])): ?>
								<?php foreach ($row['select_products'] as $v): ?>
									<option value="<?= $v['product_id'] ?>"
									        selected><?= $v['product_name'] ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div id="options" class="tab-pane fade in">
			<h3 class="text-capitalize"><?= lang('options') ?></h3>
			<hr/>
			<div class="form-group">
				<?= lang('minimum_order_amount', 'minimum_order', array('class' => 'col-md-3 control-label')) ?>
				<div class="r col-md-2">
					<?= form_input('minimum_order', set_value('minimum_order', $row['minimum_order']), 'class="' . css_error('minimum_order') . ' form-control number"') ?>
				</div>
				<?= lang('free_shipping', 'free_shipping', array('class' => 'col-md-1 control-label')) ?>
				<div class="r col-md-2">
					<?= form_dropdown('free_shipping', options('yes_no'), set_value('free_shipping', $row['free_shipping']), 'id="free_shipping" class="form-control"'); ?>
				</div>
			</div>
			<hr/>
            <div class="form-group">
	            <?= lang('number_of_uses_allowed', 'uses_per_coupon', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-2">
                    <div class="<?php if (CONTROLLER_FUNCTION == 'update'): ?>input-group<?php endif; ?>">
			            <?= form_input('uses_per_coupon', set_value('uses_per_coupon', $row['uses_per_coupon']), 'class="' . css_error('uses_per_coupon') . ' form-control digits"') ?>
			            <?php if (CONTROLLER_FUNCTION == 'update'): ?>
                            <span class="input-group-addon"><?= $row['coupon_uses'] ?> <?= $row['coupon_uses'] == 1 ? lang('redemption') : lang('redemptions') ?></span>
			            <?php endif; ?>
                    </div>
                </div>
	            <?= lang('affiliate', 'member_id', array('class' => 'col-md-1 control-label')) ?>
                <div class="r col-md-2">
                    <select id="member_id" class="form-control select2" name="member_id">
			            <?php if (!empty($row['member_id'])): ?>
                            <option value="<?= $row['member_id'] ?>"
                                    selected><?= $row['username'] ?></option>
			            <?php endif; ?>
                    </select>
                </div>
            </div>
            <hr/>
			<div class="form-group">
				<?= lang('notes', 'notes', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-sm-5">
					<?= form_textarea('notes', set_value('notes', $row['notes']), 'class="form-control"') ?>
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
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
</div>
<?= form_hidden('enable_recurring_coupon', '1') ?>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('coupon_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>

<script>

	<?php if ($row['restrict_products'] == '1'): ?>
	$("#show_products").show();
	<?php else: ?>
	$("#show_products").hide();
	<?php endif; ?>

	$("#restrict_products").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "1") {
				$("#show_products").show();
			}
			else {
				$("#show_products").hide();
			}
		}
	);

	$('#generate_code').click(function () {
		$.ajax({
			url: '<?=admin_url(CONTROLLER_CLASS . '/generate_coupon/')?>',
			type: 'GET',
			dataType: 'json',
			data: {coupon_code: $('#coupon_code').val()},
			success: function (response) {
				if (response.type == 'success') {
					$('#coupon_code').val(response.coupon_code);
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

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

	$("#product_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_PRODUCTS . '/search/ajax/product_name/1')?>',
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

	$("#select_products").select2({
		ajax: {
			url: '<?=admin_url(TBL_PRODUCTS . '/search/ajax/')?>',
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
