<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-6">
		<?= generate_sub_headline(lang(CONTROLLER_CLASS) . ' - ' . $row['module']['module_name'], 'fa-pencil', '',  FALSE) ?>
	</div>
	<div class="col-md-6 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_payment_gateways') ?></span></a>
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
					<span><?= $row['module']['module_name'] ?></span>
					<hr/>
					<div class="form-group">
						<?= lang('enabled', 'module_status', array('class' => 'col-md-3 control-label')) ?>
						<div class="col-md-5">
							<?= form_dropdown('module_status', options('yes_no'), $row['module']['module_status'], 'class="form-control"') ?>
						</div>
					</div>
					<hr/>
					<?php if (!empty($row['values'])): ?>
						<?php foreach ($row['values'] as $v): ?>
							<?php if ($v['key'] == 'module_payment_gateways_paypal_standard_api_username'): ?>
							<div class="form-group">
								<div class="col-md-5 col-md-offset-3">
									<div class="alert alert-warning">
										<?=i('fa fa-info-circle')?> <?=lang('api_details_only_required_for_refunds')?>
									</div>
								</div>
							</div>
								<?php endif; ?>
							<div class="form-group">
								<?= lang(format_settings_label($v['key'], CONTROLLER_CLASS, $row['module']['module_folder']), $v['key'], array('class' => 'col-md-3 control-label')) ?>
								<div class="col-md-5">
									<?= generate_settings_field($v, $v['value']) ?>
								</div>
							</div>
							<hr/>
						<?php endforeach; ?>
					<?php endif; ?>
					<div class="form-group">
						<?= lang('sort_order', 'module_sort_order', array('class' => 'col-md-3 control-label')) ?>
						<div class="col-md-5">
							<input type="number" name="module_sort_order"
							       value="<?= set_value('module_sort_order', $row['module']['module_sort_order']) ?>"
							       class="form-control number required">
						</div>
					</div>
					<hr/>
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