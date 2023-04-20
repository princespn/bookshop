<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang($row['module']['module_name']), 'fa-upload', '', FALSE) ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_import_options') ?></span></a>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#config" role="tab" data-toggle="tab"><?= lang('configuration') ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="config">
					<h3 class="text-capitalize">
						<?= lang($row['module']['module_name']) ?>
					</h3>
					<span><?= lang($row['module']['module_description']) ?></span>
					<hr/>
					<?php if (!empty($row['values'])): ?>
						<?php foreach ($row['values'] as $v): ?>
							<div id="<?= $v['key'] ?>_box">
								<div class="form-group">
									<?= lang(format_settings_label($v['key'], CONTROLLER_CLASS, $row['module']['module_folder']), $v['key'], array('class' => 'col-md-3 control-label')) ?>
									<div class="col-md-5">
										<?= generate_settings_field($v, $v['value']) ?>
									</div>
								</div>
								<hr/>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
					<div id="upload-box">
						<div class="form-group">
							<?= lang('upload', 'upload', array('class' => 'col-md-3 control-label')) ?>
							<div class="col-md-5">
								<div class="input-group">
									<button type="button" id="button-upload" class="btn btn-info btn-block">
										<span id="wait"><?= i('fa fa-upload') ?> <?= lang('file_upload') ?></span>
									</button>
									<small class="text-muted">
										* <?= lang('allowed_file_types') ?>
										: <span class="text-danger">
									<?= str_replace('|', ',', $sts_data_import_allowed_file_types) ?>
									</span>
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
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('module_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= lang('proceed') ?> <?= i('fa fa-caret-right') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<script>
	<?php if ($module_data_import_product_downloads_use_server_path == '1'): ?>
	$("#module_data_import_product_downloads_server_file_path_box").show();
	$("#upload-box").hide(100);
	<?php else: ?>
	$("#module_data_import_product_downloads_server_file_path_box").hide();
	$("#upload-box").show(100);
	<?php endif; ?>

	$("#module_data_import_product_downloads_use_server_path").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "1") {
				$("#module_data_import_product_downloads_server_file_path_box").show(100);
				$("#upload-box").hide(100);
			}
			else {
				$("#module_data_import_product_downloads_server_file_path_box").hide(100);
				$("#upload-box").show(100);
			}
		}
	);

	$('#button-upload').on('click', function () {
		var node = this;
		$('#form-upload').remove();
		$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="files" /><input type="hidden" name="<?=$csrf_token?>" value="<?=$csrf_value?>" /></form>');
		$('#form-upload input[name=\'files\']').trigger('click');

		timer = setInterval(function () {
			if ($('#form-upload input[name=\'files\']').val() != '') {
				clearInterval(timer);
				$.ajax({
					url: '<?=admin_url(CONTROLLER_CLASS . '/upload/' . $id)?>',
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
							$('#module_data_import_product_downloads_server_file_path').attr('value', data['file_name']);
							$('#response').html('<?=alert('success')?>');
						}
						$('#msg-details').html(data['msg']);
					},
					complete: function () {
						$('#wait').html('<?=i('fa fa-upload')?> <?=lang('file_upload')?>');
						$("#button-upload").removeAttr("disabled", 'disabled');
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	});

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
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
</script>