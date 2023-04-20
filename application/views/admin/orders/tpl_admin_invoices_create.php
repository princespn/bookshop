<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="invoice-form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_invoices') ?></span></a>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="hidden-xs hidden-sm text-capitalize"><?= i('fa fa-file-text-o') ?> <?= lang('new_invoice_for') ?> <?= $row['fname'] ?> <?= $row['lname'] ?></h3>
	<hr class="hidden-xs"/>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="billing_address_id" class="col-md-3 control-label">
					<?= i('fa fa-user') ?> <?= lang('bill_to') ?>
				</label>
				<div class="col-md-9">
					<?= form_dropdown('billing_address_id', address_array($row['addresses'], 'none', TRUE), '', 'id="load_address" class="form-control required"') ?>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label for="shipping_address_id" class="col-md-3 control-label">
					<?= i('fa fa-truck') ?> <?= lang('ship_to') ?>
				</label>
				<div class="col-md-9">
					<?= form_dropdown('shipping_address_id', address_array($row['addresses'], 'none', TRUE), '', 'id="load_address" class="form-control"') ?>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<?= lang('invoice_date', 'date_purchased', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-9">
					<div class="input-group">
						<input type="text" name="date_purchased" value="<?= set_value('date_purchased', $date) ?>"
						       class="form-control datepicker-input required"/>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="customer_telephone" class="col-md-3 control-label">
					<?= i('fa fa-phone') ?> <?= lang('phone') ?>
				</label>
				<div class="col-md-9">
					<input type="text" name="customers_telephone" value="" class="form-control"/>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label for="customer_fax" class="col-md-3 control-label">
					<?= i('fa fa-fax') ?> <?= lang('fax') ?>
				</label>
				<div class="col-md-9">
					<input type="text" name="customer_fax" value="" class="form-control"/>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<?= lang('due_date', 'due_date', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-9">
					<div class="input-group">
						<input type="text" name="due_date" value="<?= set_value('due_date', $due_date) ?>"
						       class="form-control datepicker-input required"/>
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>

				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<?= lang(i('fa fa-envelope') . ' email', 'customers_primary_email', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-9">
					<input type="text" name="customers_primary_email"
					       value="<?= set_value('primary_email', $row['primary_email']) ?>"
					       class="form-control required email"/>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<?= lang('referred_by', 'affiliate_id', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-9">
					<select id="affiliate_id" class="form-control select2" name="affiliate_id">
						<option value="0" selected><?= lang('type_in_username') ?></option>
					</select>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<?= lang('payment_status', 'payment_status_id', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-9">
					<?= form_dropdown('payment_status_id', options('payment_statuses'), '', 'class="form-control"') ?>
				</div>
			</div>
		</div>
	</div>
	<hr/>

	<div class="panel panel-default">
		<div class="panel-heading text-capitalize">
			<div class="row text-capitalize">
				<div class="col-md-9"><?= tb_header('item_description', '', FALSE, '', 'fa fa-shopping-cart') ?></div>
				<div class="col-md-1 text-center visible-lg"><?= tb_header('quantity', '', FALSE) ?></div>
				<div class="col-md-1 text-center visible-lg"><?= tb_header('price', '', FALSE) ?></div>
				<div class="col-md-1"></div>
			</div>
		</div>
		<div class="panel-body">
			<div id="item_rows">
				<div class="row">
					<div class="r col-md-9">
						<input type="text" name="items[1][invoice_item_name]" value=""
						       placeholder="<?= lang('item_description') ?>"
						       class="form-control required"/>
					</div>
					<div class="r col-md-1 text-center">
						<input type="text" name="items[1][quantity]" value="1" placeholder="1"
						       class="calc qty form-control digits required"/>
					</div>
					<div class="r col-md-1 text-center">
						<input type="text" name="items[1][unit_price]" value="0.00"
						       class="calc form-control number required"/>
					</div>
					<div class="col-md-1 text-right">
						<a class="tip btn btn-default block-phone" title="<?= lang('add_item_notes') ?>"
						   data-toggle="collapse"
						   href="#item-notes-1" aria-expanded="false"
						   aria-controls="item-notes-1"><?= i('fa fa-list') ?></a>
					</div>
				</div>
				<div id="item-notes-1" class="collapse">
					<hr/>
					<div class="row">
						<div class="col-md-1">
							<small><?= lang('item_specific_notes') ?></small>
						</div>
						<div class="col-md-8">
							<textarea name="items[1][item_notes]" class="form-control" rows="3"></textarea>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="row text-right">
				<div class="col-md-12">
					<a href="javascript:add_item('2')"
					   class="btn btn-default block-phone"><?= i('fa fa-plus') ?> <?= lang('add_more_items') ?></a>
				</div>
			</div>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row text-capitalize">
						<div class="col-md-12"><?= tb_header('invoice_notes', '', FALSE, '', 'fa fa-list') ?></div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<textarea NAME="invoice_notes" class="form-control" rows="5"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div
					class="panel-heading text-capitalize"><?= tb_header('taxes_and_shipping', '', FALSE, '', 'fa fa-anchor') ?></div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-md-7 control-label"><strong><?= lang('sub_total') ?></strong></label>

						<div class="col-md-5">

							<label class="form-control"><span class="sub-total">0</span></label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-7 control-label"><strong><?= lang('shipping') ?></strong></label>

						<div class="col-md-5">
							<input id="shipping" name="shipping_amount" type="text" value="0.00" class="calc number form-control"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-7 control-label"><strong><?= lang('taxes') ?></strong></label>

						<div class="col-md-5">
							<input id="taxes" name="tax_amount" type="text" value="0.00" class="calc number form-control"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-7 control-label"><strong><?= lang('total') ?></strong></label>

						<div class="col-md-5">
							<label class="form-control"><span class="total">0</span></label>
						</div>
					</div>
					<button
						class="btn btn-info btn-lg btn-block"><?= i('fa fa-refresh') ?> <?= lang('create_invoice') ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<?= form_hidden('member_id', $id) ?>
<?= form_close() ?>
<script>

	var next_item = 2;

	$(document).ready(function () {
		$('.calc').bind('keyup', function () {
			updateTotals();
		});
	});

	function updateTotals() {
		$.ajax({
			url: '<?=admin_url('invoices/invoice_totals/')?>',
			type: 'POST',
			dataType: 'json',
			data: $('#invoice-form').serialize(),
			success: function (data) {
				var subTotal = data.toFixed(2);
				$("span.sub-total").html(subTotal);
				var Ship = parseFloat($('#shipping').val()) || 0;
				var Tax = parseFloat($('#taxes').val()) || 0;
				var Total = parseFloat(data) + Ship + Tax;
				$('.total').text(Total.toFixed(2));
			},
			error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}

	function remove_item(id) {
		$(id).addClass('animated fadeOutDown');
		$(id).fadeOut(300, function () {
			$(this).remove();
			updateTotals();
		});
	}

	function add_item(item) {

		var html = '<div id="itemdiv-' + next_item + '" class="animated fadeIn">';
		html += '<hr /><div class="row">';
		html += '    <div class="r col-md-9">';
		html += '   <input type="text" name="items[' + next_item + '][invoice_item_name]" value="" placeholder="<?=lang('item_description')?>" class="form-control required" />';
		html += '    </div>';
		html += '    <div class="r col-md-1 text-center">';
		html += '        <input type="text" name="items[' + next_item + '][quantity]" value="1" placeholder="1" class="calc qty form-control digits required" />';
		html += '        </div>';
		html += '    <div class="r col-md-1 text-center">';
		html += '        <input type="text" name="items[' + next_item + '][unit_price]" value="0.00" class="calc form-control number required" />';
		html += '        </div>';
		html += '    <div class="r col-md-1 text-right">';
		html += '   <a href="javascript:remove_item(\'#itemdiv-' + next_item + '\')" class="btn btn-danger block-phone <?=is_disabled('delete')?>"><?=i('fa fa-minus')?></a>';
		html += '        <a class="tip btn btn-default block-phone" title="<?=lang('add_item_notes')?>"  data-toggle="collapse" href="#item-notes-' + next_item + '" aria-expanded="false" aria-controls="item-notes-' + next_item + '"><?=i('fa fa-list')?></a>';
		html += '    </div>';
		html += '    </div>';
		html += '    <div id="item-notes-' + next_item + '" class="collapse">';
		html += '        <hr />';
		html += '        <div class="row">';
		html += '        <div class="r col-md-1"><small><?=lang('item_specific_notes')?></small></div>';
		html += '    <div class="col-md-8"><textarea name="items[' + next_item + '][item_notes]" class="form-control" rows="3"></textarea></div>';
		html += '    </div>';
		html += '    </div>';
		html += '    </div>';

		$('#item_rows').append(html);
		$('.calc').bind('keyup', function () {
			updateTotals();
		});
		next_item++;
	}

	$("#affiliate_id").select2({
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

	$("#invoice-form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=current_url()?>',
				type: 'POST',
				dataType: 'json',
				data: $('#invoice-form').serialize(),
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