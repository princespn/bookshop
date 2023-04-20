<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= $row[ 'module' ][ 'module_name' ] ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_payment_options') ?></span></a>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="text-capitalize"><?= lang('update_options') ?></h3>
	<hr/>
	<?php if (!empty($row[ 'values' ])): ?>
		<?php foreach ($row[ 'values' ] as $v): ?>
			<div id="<?= $v['module_alias'] ?>">
				<div class="form-group">
					<?= lang($v['module_alias'], $v[ 'key' ], array( 'class' => 'col-md-3 control-label' )) ?>
					<div class="r col-md-5">
						<?= generate_settings_field($v, $v[ 'value' ], $v['module_alias'] . ' required ') ?>
					</div>
				</div>
				<hr/>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

</div>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= i('fa fa-caret-right') ?> <?= lang('proceed') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_hidden('module_id', $id) ?>
<?= form_close() ?>
<script>
	$("select#module_<?=$row[ 'module' ][ 'module_type' ] . '_' . $row[ 'module' ][ 'module_folder' ]?>_use_date_range").change(function () {
		$("select#module_<?=$row[ 'module' ][ 'module_type' ] . '_' . $row[ 'module' ][ 'module_folder' ]?>_use_date_range option:selected").each(function () {
			if ($(this).attr("value") == "1") {
				$("#start_date").show(100);
				$("#end_date").show(100);
			}
			else {
				$("#start_date").hide(100);
				$("#end_date").hide(100);
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
