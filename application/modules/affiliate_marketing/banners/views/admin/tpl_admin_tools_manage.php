<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('manage_banner', 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev'], $module_id) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_row/' . $row['id'] . '/' . $module_id) ?>"
			   data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>"
		   class="btn btn-primary"><?= i('fa fa-list') ?> <span
				class="hidden-xs"><?= lang('view_affiliate_tools') ?></span></a>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view_rows/0/?module_id=' . $module_id) ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_banners') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next'], $module_id) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#config" role="tab" data-toggle="tab"><?= lang('config') ?></a></li>
		<li><a href="#preview" role="tab" data-toggle="tab"><?= lang('preview') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="config">
			<h3 class="text-capitalize"><?= lang('banner_details') ?></h3>
			<hr/>
			<div class="form-group">
				<?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-2">
					<?= form_dropdown('status', options('active'), $row['status'], 'class="form-control"') ?>
				</div>
				<?= lang('sort_order', 'sort_order', array('class' => 'col-md-1 control-label')) ?>
				<div class="col-md-2">
					<input type="number" name="sort_order" value="<?= set_value('sort_order', $row['sort_order']) ?>"
					       class="form-control required">
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('name', 'name', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_input('name', set_value('name', $row['name']), 'class="' . css_error('name') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('banner_file_name', 'banner_file_name', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<div class="input-group">
						<?= form_input('banner_file_name', set_value('banner_file_name', $row['banner_file_name']), 'id="1" class="' . css_error('banner_file_name') . ' form-control required"') ?>
						<span class="input-group-addon">
							<a class="iframe" href="<?= base_url() ?>filemanager/dialog.php?type=1&fldr=banners&akey=<?= $file_manager_key ?>&field_id=1"><?= i('fa fa-upload') ?> <?= lang('update_banner') ?></a></span>
					</div>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('banner_width', 'banner_width', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-2">
					<input type="number" name="banner_width"
					       value="<?= set_value('banner_width', $row['banner_width']) ?>"
					       class="form-control digits required">
				</div>
				<?= lang('banner_height', 'banner_height', array('class' => 'col-md-1 control-label')) ?>
				<div class="col-md-2">
					<input type="number" name="banner_height"
					       value="<?= set_value('banner_height', $row['banner_height']) ?>"
					       class="form-control digits required">
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('enable_custom_url_redirect', 'enable_redirect', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_dropdown('enable_redirect', options('yes_no'), $row['enable_redirect'], 'id="enable_redirect" class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div id="redirect" <?php if ($row['enable_redirect'] == '0'): ?> class="display_none" <?php endif; ?>>
				<div class="form-group">
					<?= lang('url_to_redirect_to', 'redirect_custom_url', array('class' => 'col-md-3 control-label')) ?>
					<div class="col-md-5">
						<?= form_input('redirect_custom_url', set_value('redirect_custom_url', $row['redirect_custom_url'], FALSE), 'id="custom_url" class="' . css_error('redirect_custom_url') . ' form-control"') ?>
					</div>
				</div>
				<hr/>
			</div>
			<div class="form-group">
				<?= lang('restrict_to_affiliate_group', 'affiliate_group', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_dropdown('affiliate_group', options('affiliate_groups', 'none'), $row['affiliate_group'], 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('notes', 'notes', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_textarea('notes', set_value('notes', $row['notes']), 'class="' . css_error('notes') . ' form-control"') ?>
				</div>
			</div>
			<hr/>
		</div>
		<div class="tab-pane" id="preview">
			<h3 class="text-capitalize"><?= lang('preview') ?></h3>
			<hr/>
			<div class="form-group">
				<div class="col-md-6 col-md-offset-3">
					<?php if (!empty($row['banner_file_name'])): ?>
                        <img src="<?=$row['banner_file_name']?>" alt="banner_image" height="<?=$row['banner_height']?>"
                             width="<?=$row['banner_width']?>" id="image-1" />
					<?php else: ?>
						<?= img('//placehold.it/468x60', 'banner_image', 'id="image-1"') ?>
					<?php endif; ?>
				</div>
			</div>
			<hr/>
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
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
<?= form_hidden('id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	$("select#enable_redirect").change(function () {
		$("select#enable_redirect option:selected").each(function () {
			if ($(this).attr("value") == "1") {
				$("#redirect").show(100);
				$('#custom_url').addClass('required');
			}
			else {
				$("#redirect").hide(100);
				$('#custom_url').removeClass('required');
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