<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('manage_supplier', 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<?php if ($id != $default_supplier_id): ?>
				<a data-href="<?= admin_url(TBL_SUPPLIERS . '/delete/' . $row[ 'supplier_id' ]) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>
		
		<a href="<?= admin_url(TBL_SUPPLIERS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_suppliers') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-2">
		<div class="thumbnail">
			<div class="photo-panel">
				<a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
				   class="iframe cboxElement">
					<?= photo(CONTROLLER_METHOD, $row, 'img-responsive img-rounded', TRUE, 'image-0') ?></a>
			</div>
		</div>
	</div>
	<div class="col-md-10">
		<div class="box-info">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#info" role="tab" data-toggle="tab"><?= lang('supplier_info') ?></a></li>
				<li><a href="#image" role="tab" data-toggle="tab"><?= lang('image') ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="info">
					<div class="hidden-xs">
						<h3 class="text-capitalize"><?= $row[ 'supplier_name' ] ?></h3>
						<span><?= lang('configure_supplier_data_contact_information') ?></span>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('send_email_alert', 'supplier_send_alert', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_dropdown('supplier_send_alert', options('yes_no'), $row[ 'supplier_send_alert' ], 'class="form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('supplier_name', 'supplier_name', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_input('supplier_name', set_value('supplier_name', $row[ 'supplier_name' ]), 'class="' . css_error('supplier_name') . ' form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('email_address', 'supplier_email', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_input('supplier_email', set_value('supplier_email', $row[ 'supplier_email' ]), 'class="' . css_error('supplier_email') . ' form-control required email"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('phone', 'supplier_phone', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_input('supplier_phone', set_value('supplier_phone', $row[ 'supplier_phone' ]), 'class="' . css_error('supplier_phone') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('address', 'supplier_address', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_input('supplier_address', set_value('supplier_address', $row[ 'supplier_address' ]), 'class="' . css_error('supplier_address') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('city', 'supplier_city', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_input('supplier_city', set_value('supplier_city', $row[ 'supplier_city' ]), 'class="' . css_error('supplier_city') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('country', 'supplier_country', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_dropdown('supplier_country', options('countries'), $row[ 'supplier_country' ], 'id="country" class="s2 select2 form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('region', 'supplier_state', array( 'class' => 'col-md-3 control-label' )) ?>
						
						<div class="col-md-5">
							<div id="region_select">
								<?= form_dropdown('supplier_state', $regions, $row[ 'supplier_state' ], 'class="s2 select2 form-control "') ?>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('postal_code', 'supplier_zip', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<?= form_input('supplier_zip', set_value('supplier_zip', $row[ 'supplier_zip' ]), 'class="' . css_error('supplier_city') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
				</div>
				<div class="tab-pane" id="image">
					<hr/>
					<div class="form-group">
						<?= lang('supplier_image', 'image', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="col-md-5">
							<div class="input-group">
								<input type="text" name="image" value="<?= $row[ 'image' ] ?>" id="0"
								       class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('notes', 'supplier_notes', array( 'class' => 'col-md-3 control-label' )) ?>
						
						<div class="col-md-5">
							<?= form_textarea('supplier_notes', set_value('supplier_notes', $row[ 'supplier_notes' ]), 'class="' . css_error('supplier_notes') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('supplier_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<button class="btn btn-success navbar-btn block-phone" name="redir_button" value="1"
					id="update-button" <?= is_disabled('update', TRUE) ?>
					type="submit"><?= i('fa fa-plus') ?> <?= lang('save_add_another') ?></button>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=current_url()?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
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
	
	$('#country').change(function (event) {
		$.get('<?=admin_url('regions/load_regions/vendor_state')?>', {country_id: $('#country').val()},
			function (data) {
				$('#region_select').html(data);
				$(".s2").select2();
			}
		);
	});

</script>