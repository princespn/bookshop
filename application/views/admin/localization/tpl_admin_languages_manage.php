<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('add_language', 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<button data-toggle="collapse" data-target="#add_block" class="btn btn-primary <?= is_disabled('create') ?>"><?=i('fa fa-upload')?> <?=lang('install_language_file')?></button>

		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_languages') ?></span></a>
	</div>
</div>
<hr/>
<div id="add_block" class="row capitalize collapse">
	<?= form_open_multipart(admin_url(CONTROLLER_CLASS . '/unzip'), 'class="form-horizontal"') ?>
	<div class="col-lg-12">
		<br />
		<div class="panel panel-default">
			<div class="panel-body">
				<h4 class="text-capitalize"><?=lang('language_zip_file')?></h4>
				<div class="alert alert-warning text-warning">
					<?=i('fa fa-info-circle')?> <?=lang('zip_not_allowed')?>
					<?=lang('upload_your_language_files_to')?> <strong><?=PUBPATH . '/application/language' ?></strong>
				</div>
				<hr />
				<div class="col-md-2">
					<div>
						<button type="button" id="button-upload" class="btn btn-default btn-block">
							<span id="wait"><?= i('fa fa-upload') ?> <?= lang('file_upload') ?></span>
						</button>
						<small class="text-muted">
							* <?= lang('allowed_file_types') ?>
							: <span class="text-danger">zip</span>
						</small>
					</div>
				</div>
				<input type="hidden" name="zip_file" id="zip_file">
				<button class="btn btn-primary" type="submit"><?=i('fa fa-caret-right')?> <?=lang('proceed')?></button>
			</div>
		</div>
	</div>
	</form>
</div>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<div class="hidden-xs">
				<h3 class="text-capitalize"><?= lang('add_language') ?></h3>
				<span><?=lang('manage_language_description')?></span>
			</div>
			<hr/>
			<?php if (empty($languages)): ?>
			<div class="alert alert-warning">
				<?=i('fa fa-info-circle')?> <?=lang('no_language_files_available')?>
				<?=lang('upload_your_language_files_to')?> <strong><?=PUBPATH . '/application/language' ?></strong>
			</div>
			<?php else: ?>
			<div class="form-group">
				<?= lang('language', 'status', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_dropdown('name', options('languages', '', $languages), $row['status'], 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="code" class="col-md-3 control-label"><?= lang('two_digit_code') ?></label>

				<div class="col-md-5">
					<?= form_input('code', set_value('code', $row['code']), 'class="' . css_error('code') . ' form-control required" maxlength="2"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="image" class="col-md-3 control-label"><?= lang('image') ?></label>

				<div class="col-md-5">
					<?= form_dropdown('image', options('images', '', $flags), '', 'id="flag-image" class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<?php endif; ?>
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
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	function formatState (state) {
		if (!state.id) { return state.text; }
		var $state = $(
			'<span><i class="flag-' + state.element.value.toLowerCase() + '"></i> ' + state.text + '</span>'
		);
		return $state;
    }
    $("#flag-image").select2({
		templateResult: formatState
	});
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
							$('#zip_file').attr('value', data['file_name']);
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


</script>