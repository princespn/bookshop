<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" target="_top" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-12">
		<h3 class="text-capitalize"> <?= lang('restore_database') ?></h3>
		<span><?= lang('database_name') ?> - <?= $id ?></span>
		<hr/>
		<div class="box-info">
			<div id="backup" class="alert alert-warning">
				<h5 class="text-warning"><?= i('fa fa-exclamation-circle') ?> <?= lang('warning') ?></h5>
				<p><?= lang('restore_database_warning') ?></p>
				<p><?= lang('restore_logout_warning') ?></p>
			</div>
			<div id="wait" style="display: none;">
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-warning">
							<h3 id="wait-text" class="text-warning"></h3></div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="text-right">
				<button id="update-button" type="submit"
				        class="btn btn-primary"><?= lang('begin_restore') ?> <?= i('fa fa-caret-right') ?></button>
			</div>
		</div>
	</div>
</div>
<?= form_hidden('file', $id) ?>
<?= form_close(); ?>
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
					$('#backup').hide();
					$('#wait').show();
					$('#wait-text').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('database_restoring_please_wait')?>');
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
					$('#backup').show();
					$('#wait').hide();
					$("#update-button").removeAttr("disabled", 'disabled');
				},
				error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					$('#backup').show(400);
					$('#wait').hide(400);
					$("#update-button").removeAttr("disabled", 'disabled');

				}
			});
		}
	});
</script>