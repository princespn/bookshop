<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-5">
		<?= generate_sub_headline('modules', 'fa-cogs', count($rows['values'])) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-7 text-right">
		<?php if (config_item('modules_marketplace')): ?>
		<a href="<?=config_item('modules_marketplace')?>" class="btn btn-primary" target="_blank">
			<?=i('fa fa-search')?> <span class="hidden-xs"><?=lang('get_more_modules')?></span></a>
		<?php endif; ?>
		<a data-toggle="modal" data-target="#upload-module" href="#"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-upload') ?> <span
				class="hidden-xs"><?= lang('upload_module') ?></span></a>
	</div>
</div>
<hr/>
<div role="tabpanel">
	<div class="col-lg-3">
		<div class="text-capitalize">
			<div class="list-group-item">
				<a class="pull-right additional-icon" href="#" data-toggle="collapse" data-target="#box-1"><i
						class="fa fa-chevron-down"></i></a>
				<strong><?= i('fa fa-cog') ?> <?= lang('modules') ?></strong>
			</div>
			<div id="box-1" class="collapse in">
				<?php foreach ($mod_folders as $m => $n): ?>
					<a href="<?= admin_url(CONTROLLER_CLASS . '/view/?module_type=' . $m) ?>"
					   class="list-group-item <?php if ($m == $module_type): ?> active <?php endif; ?> "><span
							class="badge"><?= $n['total'] ?></span><h4
							class="list-group-item-heading"><?= lang($m) ?></h4>

						<p class="list-group-item-text"><?= lang($m . '_module_description') ?></p>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div class="col-lg-9">
		<div class="box-info">
			<?php if (empty($rows['values'])): ?>
				<?= tpl_no_values() ?>
			<?php else: ?>
				<div>
					<h3 class="text-capitalize"><?= lang($module_type) ?></h3>
					<hr/>
					<?php foreach ($rows['values'] as $k => $v): ?>
						<?php if (!empty($v['install'])): ?>
							<div class="row animated fadeInUp">
								<div class="col-lg-9">
									<h5><?= check_desc($v['install']['module_name']) ?></h5>

									<p><?= check_desc($v['install']['module_description']) ?></p>
								</div>
								<div class="col-lg-3 text-right">
									<a
										href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/module_status/key/module_id/id/' . $v['install']['module_id']) ?>"
										class="tip btn btn-default block-phone<?= is_disabled('update', TRUE) ?>"
										data-toggle="tooltip"
										data-placement="bottom"
										title="<?= set_status($v['install']['module_status'], TRUE, FALSE) ?>"><?= set_status($v['install']['module_status']) ?></a>
									<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['install']['module_id']) ?>"
									   class="btn btn-default block-phone"
									   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
									<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['install']['module_id']) ?>"
									   data-toggle="modal" data-target="#confirm-delete" href="#"
									   class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
								</div>
							</div>
						<?php else: ?>
							<div class="row">
								<div class="col-lg-9">
									<?php if (!empty($v['info']['module_name'])): ?>
										<h5><?= $v['info']['module_name'] ?></h5>
										<p><?= $v['info']['module_description'] ?></p>
									<?php else: ?>
										<h5 class="text-capitalize"><?= lang($k) ?></h5>
									<?php endif; ?>
								</div>
								<div class="col-lg-3 text-right">
									<a
										href="<?= admin_url(CONTROLLER_CLASS . '/install/' . $module_type . '/' . $k) ?>"
										class="tip btn btn-primary block-phone<?= is_disabled('create', TRUE) ?>"
										data-toggle="tooltip"
										data-placement="bottom"
										title="<?= lang('install') ?>"><?= i('fa fa-upload') ?></a>

								</div>
							</div>
						<?php endif; ?>
						<hr/>
					<?php endforeach; ?>

				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="modal fade" id="upload-module" tabindex="-1" role="dialog" aria-labelledby="modal-title"
     aria-hidden="true">
	<div class="modal-dialog" id="modal-title">
		<div class="modal-content">
			<?= form_open_multipart(admin_url(CONTROLLER_CLASS . '/unzip'), 'role="form" class="form-horizontal"') ?>
			<div class="col-lg-12">
				<br/>
				<div class="panel panel-default">
					<div class="panel-body">
						<h4 class="text-capitalize"><?= lang('upload_module') ?> - <?= lang($module_type) ?></h4>
						<div class="alert alert-warning text-warning">
							<?= i('fa fa-info-circle') ?> <?= lang('zip_not_allowed') ?>
							<?= lang('upload_your_module_folder_to') ?>
							<strong><?= APPPATH . 'modules/' . $module_type ?></strong>
						</div>
						<hr/>
						<div class="row">
							<div class="col-md-8">
								<button type="button" id="button-upload" class="btn btn-default btn-block">
									<span id="wait"><?= i('fa fa-upload') ?> <?= lang('file_upload') ?></span>
								</button>
								<small class="text-muted">
									* <?= lang('allowed_file_types') ?>
									: <span class="text-danger">zip</span>
								</small>
							</div>
							<div class="col-md-2">
								<input type="hidden" name="zip_file" id="zip_file">
								<button class="btn btn-primary"
								        type="submit"><?= i('fa fa-caret-right') ?> <?= lang('proceed') ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?= form_hidden('module_type', $module_type) ?>
			<?= form_close() ?>
		</div>
	</div>
</div>
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
</script>