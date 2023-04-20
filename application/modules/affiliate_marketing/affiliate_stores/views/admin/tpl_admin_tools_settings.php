<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS . ' - ' . $module_row['module']['module_name'], 'fa-pencil', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-list') ?> <span class="hidden-xs"><?= lang('view_affiliate_tools') ?></span></a>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view_rows/0/?module_id=' . $id) ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_stores') ?></span></a>
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
						<?= lang('module_configuration') ?>
					</h3>
					<span><?= $module_row['module']['module_name'] ?></span>
					<hr/>
					<?php if (!empty($module_row['values'])): ?>
						<?php foreach ($module_row['values'] as $v): ?>
							<?php if ($v['key'] == 'module_affiliate_marketing_affiliate_stores_redirect_affiliate_link'): ?>
								<?php if (config_option('sts_affiliate_link_type') != 'regular'): ?>
									<?php continue; ?>
								<?php endif; ?>
							<?php endif; ?>
							<div id="<?= $v['key'] ?>_box">
								<div class="form-group">
									<?= lang(format_settings_label($v['key'], CONTROLLER_CLASS, $module_row['module']['module_folder']), $v['key'], array('class' => 'col-md-3 control-label')) ?>
									<div class="col-md-5">
										<?= generate_settings_field($v, $v['value']) ?>
									</div>
								</div>
								<hr/>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if (!empty($id)): ?>
	<?= form_hidden('module_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
					       class="btn btn-success navbar-btn block-phone"/>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<br/>

<script>
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