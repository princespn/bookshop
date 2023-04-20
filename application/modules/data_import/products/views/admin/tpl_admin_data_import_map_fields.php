<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('map_field_names', 'fa-upload', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_import_options') ?></span></a>
	</div>
</div>
<hr/>

<div class="row" id="fields">
	<div class="col-md-12">
		<div class="box-info">
			<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
			<h3><?= lang('map_import_fields') ?></h3>
			<span><?= lang('map_import_fields_description') ?></span>
			<hr/>
			<?php if (!empty($row['values'])): ?>
				<?php if (!empty($row['fields'])): ?>
					<div class="form-group visible-lg visible-md">
						<div class="col-md-2 col-md-offset-3">
							<h5><?= lang('import_fields') ?></h5>
						</div>
						<div class="col-md-2 col-md-offset-2">
							<h5><?= lang('database_fields') ?></h5>
						</div>
					</div>
					<hr/>
					<?php foreach ($row['values'] as $k => $v): ?>
						<?php if (config_option('module_data_import_products_generate_new_ids') == 1): ?>
							<?php if ($v == 'product_id'): ?>
								<?php continue; ?>
							<?php endif; ?>
						<?php endif; ?>
						<div class="form-group">
							<label class="col-md-2 control-label"><?= lang('field') ?></label>
							<div class="col-md-3">
								<span class="form-control"><?= $v ?></span>
							</div>
							<label class="col-md-1 control-label"><?= lang('maps_to') ?></label>
							<div class="col-md-3">
								<?= form_dropdown('fields[' . $k . ']', options('', '', $row['fields']), map_field($v, $row['fields']), ' class="form-control"') ?>
							</div>
						</div>
						<hr/>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php else: ?>
				<?= tpl_no_values('', '', 'no_data_found') ?>
			<?php endif; ?>
			<nav class="navbar navbar-fixed-bottom save-changes">
				<div class="container text-right">
					<div class="row">
						<div class="col-md-12">
							<button class="btn btn-info navbar-btn block-phone"
							        id="update-button" <?= is_disabled('update', TRUE) ?>
							        type="submit"><?= lang('do_import') ?> <?= i('fa fa-caret-right') ?></button>
						</div>
					</div>
				</div>
			</nav>
			<?= form_hidden('module_id', $id) ?>
			<?= form_close() ?>
		</div>
	</div>
</div>
<div id="wait" style="display: none;">
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-warning">
				<h3 id="wait-text" class="text-warning"></h3>
			</div>
		</div>
	</div>

</div>
<script>
	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=current_url()?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
				beforeSend: function () {
					$('#fields').hide();
					$('#wait').show();
					$('#wait-text').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('data_importing_please_wait')?>');
					$("#update-button").attr("disabled", 'disabled');
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
					}

					$('#msg-details').html(response.msg);
				},
				complete: function () {
					$("#update-button").removeAttr("disabled", 'disabled');
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					$('#fields').show(400);
					$('#wait').hide(400);
					$("#update-button").removeAttr("disabled", 'disabled');

				}
			});
		}
	});
</script>