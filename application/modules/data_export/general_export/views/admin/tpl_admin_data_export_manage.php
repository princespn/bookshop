<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang($row['module']['module_name']), 'fa-upload', '', FALSE) ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_export_options') ?></span></a>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#config" role="tab" data-toggle="tab"><?= lang('configuration') ?></a></li>
				<li><a href="#history" role="tab" data-toggle="tab"><?= lang('file_archive') ?></a></li>
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
				</div>
				<div class="tab-pane" id="history">
					<h3 class="text-capitalize">
						<?= lang('import_export_files') ?>
					</h3>
					<span><?= lang('archived_files_description') ?></span>
					<hr/>
					<?php if (empty($archive)): ?>
						<div class="alert alert-warning text-warning">
							<?= i('fa fa-exclamation-circle') ?> <?= lang('no_files_found') ?>
						</div>
					<?php else: ?>
						<?php foreach ($archive as $v): ?>
							<?php if (check_file($v, 'export')): ?>
							<div class="row">
								<div class="col-md-8">
									<h5><?=$v?></h5>
								</div>
								<div class="col-md-4 text-right">
									<a href="<?= admin_url(CONTROLLER_CLASS . '/download/' . $v) ?>"
									   class="btn btn-default"><?= i('fa fa-download') ?></a>
									<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v) ?>"
									   data-toggle="modal" data-target="#confirm-delete" href="#"
									   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
								</div>
							</div>
							<hr />
							<?php endif; ?>
						<?php endforeach; ?>
						</ul>
					<?php endif; ?>
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
	$("select#module_data_export_general_export_table").change(function () {
		$("select#module_data_export_general_export_table option:selected").each(function () {
			if ($(this).attr("value") == "products_downloads") {
				$("#module_data_export_general_export_start_date_box").hide(100);
				$("#module_data_export_general_export_end_date_box").hide(100);
			}
			else {
				$("#module_data_export_general_export_start_date_box").show(100);
				$("#module_data_export_general_export_end_date_box").show(100);
			}
		});
	}).change();
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