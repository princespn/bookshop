<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update_address'): ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_address/' . $id . '/' . $row['member_id']) ?>"
			   data-toggle="modal" data-target="#confirm-delete" href="#" <?= is_disabled('delete') ?>
			   class="md-trigger btn btn-danger"><?= i('fa fa-trash-o') ?> <?=lang('delete')?></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $row['member_id']) ?>"
		   class="btn btn-primary"><?= i('fa fa-undo') ?> <span class="hidden-xs"><?= lang('go_back') ?></span></a>
	</div>
</div>
<hr/>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="box-info">
	<h3 class="text-capitalize"><?= lang('address_details') ?></h3>
	<hr/>
	<div class="form-group">
		<?= lang('default_billing_address', 'billing_default', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="r col-md-1">
			<?= form_dropdown('billing_default', options('yes_no'), $row[ 'billing_default' ], 'class="form-control"') ?>
		</div>
		<?= lang('default_shipping', 'shipping_default', array( 'class' => 'col-md-1 control-label' )) ?>
		<div class="r col-md-1">
			<?= form_dropdown('shipping_default', options('yes_no'), $row[ 'shipping_default' ], 'class="form-control"') ?>
		</div>
		<?= lang('default_payment', 'payment_default', array( 'class' => 'col-md-1 control-label' )) ?>
		<div class="r col-md-1">
			<?= form_dropdown('payment_default', options('yes_no'), $row[ 'payment_default' ], 'class="form-control"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('fname', 'fname', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="r col-md-5">
			<?= form_input('fname', set_value('fname', $row[ 'fname' ]), 'class="' . css_error('fname') . ' form-control required"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('lname', 'lname', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="r col-md-5">
			<?= form_input('lname', set_value('lname', $row[ 'lname' ]), 'class="' . css_error('lname') . ' form-control required"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('company', 'company', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="r col-md-5">
			<?= form_input('company', set_value('company', $row[ 'company' ]), 'class="' . css_error('company') . ' form-control"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('address_1', 'address_1', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="col-md-5">
			<?= form_input('address_1', set_value('address_1', $row[ 'address_1' ]), 'class="' . css_error('address_1') . ' form-control required"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('address_2', 'address_2', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="col-md-5">
			<?= form_input('address_2', set_value('address_2', $row[ 'address_2' ]), 'class="' . css_error('address_2') . ' form-control"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('city', 'city', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="col-md-5">
			<?= form_input('city', set_value('city', $row[ 'city' ]), 'class="' . css_error('city') . ' form-control required"') ?>
		</div>
	</div>
	<hr />
	<div class="form-group">
		<?= lang('state_province', 'state', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="col-md-5">
			<?= form_dropdown('state', options('regions', FALSE, $row[ 'regions_array' ]), $row[ 'state' ], 'id="state" class="form-control s2"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('country', 'country', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="col-md-5">
			<select id="country" class="country_id form-control select2" name="country"
			        onchange="updateregion('state')">
				<option value="<?= $row[ 'country' ] ?>" selected><?= $row[ 'country_name' ] ?></option>
			</select>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('postal_code', 'postal_code', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="col-md-5">
			<?= form_input('postal_code', set_value('postal_code', $row[ 'postal_code' ]), 'class="' . css_error('postal_code') . ' form-control required"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('phone', 'phone', array( 'class' => 'col-md-3 control-label' )) ?>
		<div class="col-md-5">
			<?= form_input('phone', set_value('phone', $row[ 'phone' ]), 'class="' . css_error('phone') . ' form-control"') ?>
		</div>
	</div>
	<hr/>
</div>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', true) ?>" id="update-button"
				        type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?php if (CONTROLLER_FUNCTION == 'update_address'):   ?>
<?=form_hidden('id', $id)?>
	<?=form_hidden('member_id', $row['member_id'])?>
<?php else: ?>
	<?=form_hidden('member_id', $id)?>
<?php endif; ?>
<?= form_close() ?>
<script>
	$("#form").validate();
	//search countries
	$(".country_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_COUNTRIES . '/search_countries/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					country_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.country_id,
							text: item.country_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	function updateregion(select) {
		$.get('<?=admin_url('regions/load_regions/state')?>', {country_id: $('#country').val()},
			function (data) {
				$('#state').html(data);
				$(".s2").select2();
			}
		);
	}
</script>