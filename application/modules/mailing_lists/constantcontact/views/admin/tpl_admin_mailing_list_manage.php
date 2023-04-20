<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?=form_open('', 'role="form" id="form" class="form-horizontal"')?>
<div class="row">
	<div class="col-md-4">
		<?=generate_sub_headline('mailing_list_modules' . ' - '. $row['module']['module_name'], 'fa-envelope', '')?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?=admin_url('email_mailing_lists/view')?>" class="btn btn-primary">
			<?=i('fa fa-search')?> <span class="hidden-xs"><?=lang('view_mailing_list_modules')?></span></a>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="nav nav-tabs text-capitalize">
		<li class="active"><a href="#main" data-toggle="tab"><?= lang('module_description') ?></a></li>
	</ul>
	<div class="tab-content">
		<div id="main" class="tab-pane fade in active">
			<hr/>
			<div class="form-group">
				<?= lang('module_status', 'module_status', array('class' => 'col-sm-3 control-label')) ?>
				<div class="col-lg-5">
					<?= form_dropdown('module_status', options('active'), set_value('module_status', $row['module']['module_status']), 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<?php if (!empty($row['values'])): ?>
				<?php foreach ($row['values'] as $v): ?>
					<?php if ($v['type'] != 'hidden'): ?>
						<div class="form-group">
							<?= lang(format_settings_label($v['key'], $row['module']['module_type'], $row['module']['module_folder']), $v['key'], array('class' => 'col-md-3 control-label')) ?>

							<div class="col-lg-5">
                                <?php if ($v['key'] == 'module_mailing_lists_constantcontact_list_id'): ?>
	                                <?= form_dropdown('module_mailing_lists_constantcontact_list_id', options('', config_item('module_mailing_lists_constantcontact_list_id'), unserialize(config_item('module_mailing_lists_constantcontact_lists'))), '', 'class="form-control" id="module_mailing_lists_constantcontact_list_id"') ?>
                                <?php else: ?>
								<?= generate_settings_field($v, set_value($v['key'], $v['value'])) ?>
                                <?php endif; ?>
							</div>
						</div>
						<hr/>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="form-group">
				<?= lang('module_sort_order', 'module_sort_order', array('class' => 'col-sm-3 control-label')) ?>
				<div class="col-lg-5">
					<?= form_input('module_sort_order', set_value('module_sort_order', $row['module']['module_sort_order']), 'class="' . css_error('module_sort_order') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
		</div>

	</div>
</div>
<nav class="navbar navbar-fixed-bottom  save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-lg-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
					       class="btn btn-success navbar-btn block-phone"/>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('module_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
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