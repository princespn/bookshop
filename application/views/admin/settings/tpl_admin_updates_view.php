<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang('system_version'), 'fa-key', '', FALSE) ?>
	</div>
	<div class="col-md-8 text-right"></div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<h3><?= lang('system_updates') ?></h3>
			<span><?= lang('system_updates_description') ?></span>
			<hr/>
			<div class="form-group">
				<label class="col-md-3 control-label"><?= lang('your_system_version') ?></label>

				<div class="col-md-1">
					<p class="form-control-static"><?= APP_VERSION ?></p>
				</div>
				<label class="col-md-2 control-label"><?= lang('latest_version') ?></label>

				<div class="col-md-3">
					<?php if (config_item('system_updates_page')): ?>
					<p class="form-control-static"><span class="label label-danger"><span id="current_version_number"></span></span>
						<a href="<?=config_item('system_updates_page')?>" target="_blank"><span class="label label-primary"><?=i('fa fa-download')?> <?=lang('get_updates_manually')?></span></a></p>
				    <?php endif; ?>
                </div>
			</div>
            <hr/>
			<div id="update_use_server_path_box">
				<div class="form-group">
					<?= lang('use_server_file_path_to_update', 'sts_update_use_server_path', array('class' => 'col-md-3 control-label')) ?>
					<div class="col-md-5">
						<?= form_dropdown('sts_update_use_server_path', options('yes_no'),'1', 'id="sts_update_use_server_path" class="form-control"') ?>
					</div>
				</div>
				<hr/>
			</div>
			<div id="upload-file-box">
				<div class="form-group">
					<?= lang('update_path', 'sts_update_file_path', array('class' => 'col-md-3 control-label')) ?>
					<div class="col-md-5">
						<input type="text" name="sts_update_file_path" value="<?=DEFAULT_FILE_UPDATES_UPLOAD_PATH?>"
						       value="<?= config_item('sts_update_file_path') ?>" id="sts_update_file_path"
						       class="form-control"><br />
                        <div class="alert alert-warning">
                            <small><?=i('fa fa-info-circle')?> <?=lang('change_update_file_path')?></small>
                        </div>
					</div>
				</div>
				<hr/>
			</div>

			<div id="upload-box">
				<div class="form-group">
					<?= lang('upload_update', 'upload', array('class' => 'col-md-3 control-label')) ?>
					<div class="col-md-5">
						<div class="input-group">
							<button type="button" id="button-upload" class="btn btn-info btn-block">
								<span id="wait"><?= i('fa fa-upload') ?> <?= lang('file_upload') ?></span>
							</button>
							<small class="text-muted">
								* <?= lang('allowed_file_types') ?>
								: <span class="text-danger">.zip</span>
								|
								<?= lang('maximum_upload_file_size') ?>: <span class="text-danger">
										<?php if (!empty($sts_site_max_upload_size)): ?>
											<?= $sts_site_max_upload_size ?>
										<?php else: ?>
											<?php if (ini_get('post_max_size') < ini_get('upload_max_filesize')): ?>
												<?= ini_get('post_max_size') ?>
											<?php else: ?>
												<?= ini_get('upload_max_filesize') ?>
											<?php endif; ?>
										<?php endif; ?>
									</span>
							</small>
						</div>
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
				<a href="<?=admin_url('backup/view')?>" class="btn btn-danger navbar-btn block-phone"
				   id="update-button" <?= is_disabled('update', TRUE) ?>><?= i('fa fa-undo') ?> <?= lang('backup_system') ?></a>
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= lang('proceed') ?> <?= i('fa fa-caret-right') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<br/>
<script>
	$("#upload-file-box").show(100);
	$("#upload-box").hide(100);

	$("#sts_update_use_server_path").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "1") {
				$("#upload-file-box").show(100);
				$("#upload-box").hide(100);
                $("#sts_update_file_path").addClass('required');
			}
			else {
				$("#upload-file-box").hide(100);
				$("#upload-box").show(100);
				$("#sts_update_file_path").removeClass('required');
			}
		}
	);
	<?php if (!is_disabled('update', TRUE)): ?>
	$('#button-upload').on('click', function () {
		var node = this;
		$('#form-upload').remove();
		$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="files" /><input type="hidden" name="<?=$csrf_token?>" value="<?=$csrf_value?>" /></form>');
		$('#form-upload input[name=\'files\']').trigger('click');

		timer = setInterval(function () {
			if ($('#form-upload input[name=\'files\']').val() != '') {
				clearInterval(timer);
				$.ajax({
					url: '<?=admin_url(CONTROLLER_CLASS . '/upload/')?>',
					type: 'post',
					dataType: 'json',
					data: new FormData($('#form-upload')[0]),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function () {
						$('#wait').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('uploading_please_wait')?>');
						$("#button-upload").attr("disabled", 'disabled');
					},
					success: function (data) {
						if (data['type'] == 'error') {
							$('#response').html('<?=alert('error')?>');
						}
						else if (data['type'] == 'success') {
							$('#sts_update_file_path').attr('value', data['file_name']);
							$('#response').html('<?=alert('success')?>');
						}
						$('#msg-details').html(data['msg']);
					},
					complete: function () {
						$('#wait').html('<?=i('fa fa-upload')?> <?=lang('file_upload')?>');
						$("#button-upload").removeAttr("disabled", 'disabled');
					},
					error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	});
    <?php endif; ?>
	$("#current_version_number").load("<?=admin_url(CONTROLLER_CLASS . '/check_version/')?>");

	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=admin_url(CONTROLLER_CLASS . '/check/')?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
				beforeSend: function () {
					$('#update-button i ').addClass('fa-spin');
					$('#update-button span').html('<?=lang('please_wait')?>');
				},
				complete: function () {
					$('#update-button i ').removeClass('fa-spin');
					$('#update-button span').html('<?=lang('backup_database_now')?>');
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

