<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('run_updates', 'fa-refresh', '') ?>
	</div>
	<div class="col-md-8 text-right"></div>
</div>
<hr/>

<?php if (!empty($results)): ?>
	<div class="row">
		<div class="col-md-12">
			<div class="box-info">
				<h3><?=i('fa fa-database')?> <?=lang('update_results') ?></h3>
				<hr/>
				<?php if (!empty($results)): ?>
					<div class="alert alert-success text-success">
						<?=i('fa fa-info-circle')?> <?= lang('system_updated_successfully') ?>
					</div>
					<hr/>
                    <nav class="navbar navbar-fixed-bottom save-changes">
                        <div class="container text-right">
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="<?=admin_url()?>"
                                        class="btn btn-success"><?=i('fa fa-undo')?> <?=lang('finish')?></a>
                                </div>
                            </div>
                        </div>
                    </nav>
					<?php if (!empty($results['error'])): ?>
						<?= $results['error'] ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php else: ?>
<div class="row" id="fields">
	<div class="col-md-12">
		<div class="alert alert-danger">
			<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
			<h4 class="text-danger"><?=i('fa fa-exclamation-circle')?> <?= lang('warning') ?></h4>
			<div>
				<p><?=lang('system_update_backup_warning_description')?></p>
                <p><?=lang('any_customizations_will_be_overwritten')?></p>
                <p><strong> <?=lang('backup_now')?></strong></p>
                <p><strong><?=lang('update_path')?>: <?= config_item('sts_update_file_path')?></strong></p>
            </div>
			<nav class="navbar navbar-fixed-bottom save-changes">
				<div class="container text-right">
					<div class="row">
						<div class="col-md-12">
							<a href="<?=admin_url('backup/view')?>" class="btn btn-danger navbar-btn block-phone"
							   id="update-button" <?= is_disabled('update', TRUE) ?>><?= i('fa fa-undo') ?> <?= lang('backup_system') ?></a>
							<button class="btn btn-info navbar-btn block-phone"
							        id="update-button" <?= is_disabled('update', TRUE) ?>
							        type="submit"><?= lang('proceed_with_updates') ?> <?= i('fa fa-caret-right') ?></button>
						</div>
					</div>
				</div>
			</nav>
			<?=form_hidden('path', config_item('sts_update_file_path'))?>
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
					$('#wait-text').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('updates_running_please_wait')?>');
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
						$('#fields').show();
						$('#wait').hide();
					}

					$('#msg-details').html(response.msg);
				},
				complete: function () {
					$("#update-button").removeAttr("disabled", 'disabled');
				},
				error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					$('#fields').show(400);
					$('#wait').hide(400);
					$("#update-button").removeAttr("disabled", 'disabled');

				}
			});
		}
	});
</script>
<?php endif; ?>