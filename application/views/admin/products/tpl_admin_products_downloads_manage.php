<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class=" form-horizontal"') ?>
<div class="row">
	<div class="col-md-7">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-5 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_downloads') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<div class="hidden-xs">
		<h3 class="text-capitalize"><?= lang('download_details') ?></h3>
		<span><?= lang('setup_your_digital_download_file_information') ?></span>
	</div>
	<hr/>
	<ul class="nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#file" data-toggle="tab"><?= lang('upload_file') ?></a></li>
		<?php foreach ($row['lang'] as $v): ?>
			<li>
				<a href="#<?= $v['image'] ?>" data-toggle="tab"><?= i('flag-' . $v['image']) ?>
					<span class="visible-lg"><?= $v['name'] ?></span></a>
			</li>
		<?php endforeach; ?>
	</ul>
	<br/>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="file">
			<hr/>
			<div class="form-group">
				<?= lang('file_name', 'file_name', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_input('file_name', set_value('file_name', $row['file_name']), 'id="file_name" class="' . css_error('file_name') . ' form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('upload_folder', 'upload_folder', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<div class="input-group">
						<span class="form-control"><?=$sts_site_download_file_path?></span>
						<span class="input-group-addon">
							<a href="<?=admin_url('settings/#media-tab')?>"><?=i('fa fa-refresh')?> <?=lang('change')?></a></span>
					</div>
					<small class="text-muted">
						 <span class="text-danger">
								* <?= lang('upload_folder_must_be_writeable') ?>
							</span>
					</small>
				</div>
			</div>
			<hr />
			<div class="form-group">
				<?= lang('upload', 'upload', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<div class="input-group">
						<button type="button" id="button-upload" class="btn btn-info btn-block">
							<span id="wait"><?= i('fa fa-upload') ?> <?= lang('file_upload') ?></span></button>
						<small class="text-muted">
							<?= lang('allowed_file_types') ?>
							: <span class="text-danger">
								<?= str_replace('|', ',', $sts_site_download_allowed_file_types) ?>
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
		<?php foreach ($row['lang'] as $v): ?>
			<div class="tab-pane fade in" id="<?= $v['image'] ?>">
				<hr/>
				<div class="form-group">
					<label for="download_name"
					       class="col-sm-3 control-label"><?= lang('download_name') ?></label>

					<div class="col-lg-5">
						<?= form_input('lang[' . $v['language_id'] . '][download_name]', set_value('download_name', $v['download_name']), 'class="' . css_error('download_name') . ' form-control"') ?>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label for="description"
					       class="col-sm-3 control-label"><?= lang('description') ?></label>

					<div class="col-lg-5">
						<?= form_input('lang[' . $v['language_id'] . '][description]', set_value('description', $v['description']), 'class="' . css_error('description') . ' form-control"') ?>
					</div>
				</div>
				<hr/>
			</div>
			<?= form_hidden('lang[' . $v['language_id'] . '][language]', $v['name']) ?>
		<?php endforeach; ?>
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
	<?= form_hidden('download_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>

<script>
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
					url: '<?=admin_url(CONTROLLER_CLASS . '/upload')?>',
					type: 'post',
					dataType: 'json',
					data: new FormData($('#form-upload')[0]),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function () {
						$('#wait').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('uploading_please_wait')?>');
						$("#button-upload").attr("disabled",'disabled');
					},
					success: function (data) {
						if (data['type'] == 'error') {
							$('#response').html('<?=alert('error')?>');
						}
						else if (data['type'] == 'success') {
							$('#file_name').attr('value', data['file_name']);
							
							$('#response').html('<?=alert('success')?>');
						}
						$('#msg-details').html(data['msg']);
					},
					complete: function() {
						$('#wait').html('<?=i('fa fa-upload')?> <?=lang('file_upload')?>');
						$("#button-upload").removeAttr("disabled",'disabled');
					},
					error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	});
    <?php endif; ?>
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

						$('#response').html('<?=alert('success')?>');

						setTimeout(function () {
							$('.alert-msg').fadeOut('slow');
						}, 5000);
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
