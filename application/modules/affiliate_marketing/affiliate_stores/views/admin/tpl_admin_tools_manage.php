<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('manage_store', 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev'], $module_id) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_row/' . $row['id'] . '/' . $module_id) ?>"
			   data-toggle="modal"
			   data-target="#confirm-delete" href="#"
			   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>"
		   class="btn btn-primary"><?= i('fa fa-list') ?> <span
				class="hidden-xs"><?= lang('view_affiliate_tools') ?></span></a>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view_rows/0/?module_id=' . $module_id) ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_stores') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next'], $module_id) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#config" role="tab" data-toggle="tab"><?= lang('config') ?></a></li>
		<li><a href="#options" role="tab" data-toggle="tab"><?= lang('options') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="config">
			<h3 class="text-capitalize"><?= lang('store_details') ?></h3>
			<hr/>
			<div class="form-group">
				<?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-2">
					<?= form_dropdown('status', options('active'), $row['status'], 'class="form-control"') ?>
				</div>
				<?= lang('affiliate', 'member_id', array('class' => 'col-md-1 control-label')) ?>
				<div class="col-md-2">
					<?php if (CONTROLLER_FUNCTION == 'update'): ?>
					<p class="form-control"><?=$row['username']?></p>
					<?php else: ?>
					<select id="member_id" class="form-control select2" name="member_id">
						<?php if (!empty($row['username'])): ?>
							<option value="<?= set_value('member_id', $row['member_id']) ?>"
							        selected><?= set_value('username', $row['username']) ?></option>
						<?php endif; ?>
					</select>
					<?php endif; ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('store_name', 'store_name', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_input('name', set_value('name', $row['name'], FALSE), 'id="name" class="' . css_error('name') . ' form-control "') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('welcome_headline', 'welcome_headline', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_input('welcome_headline', set_value('welcome_headline', $row['welcome_headline'], FALSE), 'id="welcome_headline" class="' . css_error('welcome_headline') . ' form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('welcome_text', 'welcome_text', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_textarea('welcome_text', set_value('welcome_text', $row['welcome_text'], FALSE), 'id="welcome_text" class=" ' . css_error('welcome_text') . ' form-control"') ?>
				</div>
			</div>
			<hr/>
		</div>
		<div class="tab-pane" id="options">
			<h3><?= lang('options') ?></h3>
			<hr/>
			<div class="form-group">
				<?= lang('header_background', 'header_image', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<div class="input-group">
						<?= form_input('header_image', set_value('header_image', $row['header_image']), 'id="1" class="' . css_error('header_image') . ' form-control"') ?>
						<span class="input-group-addon"> <a class="iframe"
						                                    href="<?= base_url() ?>filemanager/dialog.php?fldr=backgrounds&type=1&akey=<?= $file_manager_key ?>&field_id=1"><?= i('fa fa-upload') ?> <?= lang('select_image') ?></a></span>
					</div>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('avatar', 'avatar_image', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<div class="input-group">
						<?= form_input('avatar_image', set_value('avatar_image', $row['avatar_image']), 'id="2" class="' . css_error('avatar_image') . ' form-control"') ?>
						<span class="input-group-addon"> <a class="iframe"
						                                    href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=2"><?= i('fa fa-upload') ?> <?= lang('select_image') ?></a></span>
					</div>
				</div>
			</div>
			<hr/>
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
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>

	<?=html_editor('init', 'html')?>

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
		minimumInputLength: 1
	});

	$("#coupon_id").select2({
		ajax: {
			url: '<?=admin_url('search/select/' . TBL_COUPONS . '/coupon_code/coupon_id/status')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					term: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.coupon_id,
							text: item.coupon_code
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
			tinyMCE.triggerSave();
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
							});
						}
					}

					$('#msg-details').html(response.msg);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});

</script>