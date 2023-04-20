<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($id > 1): ?>
				<a data-href="<?= admin_url(TBL_REGIONS . '/delete/' . $row['region_id']) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>

		<a href="<?= admin_url(TBL_REGIONS . '/view/?country_id=' . $row['region_country_id']) ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_regions') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<div class="hidden-xs">
				<h4 class="text-capitalize">
					<?php if (CONTROLLER_FUNCTION == 'update'): ?>
						<?= i('flag-' . strtolower($row['country_iso_code_2'])) ?> <?= $row['country_name'] ?>
					<?php else: ?>
						<?= i('fa fa-cog') ?> <?= lang(CONTROLLER_FUNCTION) ?>
					<?php endif; ?>
				</h4>
			</div>
			<br/>

			<div class="form-group">
				<label for="region_name" class="col-md-3 control-label"><?= lang('country') ?></label>

				<div class="col-md-5">
					<div class="form-control-static"><?= $country['country_name'] ?></div>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('show_on_forms', 'status', array('class' => 'col-md-3 control-label')) ?>
				<div class="col-md-5">
					<?= form_dropdown('status', options('yes_no'), $row['status'], 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="region_name" class="col-md-3 control-label"><?= lang('region_name') ?></label>

				<div class="col-md-5">
					<?= form_input('region_name', set_value('region_name', $row['region_name']), 'class="' . css_error('region_name') . ' form-control required" maxlength="10"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="region_code" class="col-md-3 control-label"><?= lang('region_code') ?></label>

				<div class="col-md-5">
					<?= form_input('region_code', set_value('region_code', $row['region_code']), 'class="' . css_error('region_code') . ' form-control required" maxlength="10"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="sort_order" class="col-md-3 control-label"><?= lang('sort_order') ?></label>

				<div class="col-md-5">
					<input type="number" name="sort_order" value="<?= set_value('sort_order', $row['sort_order']) ?>"
					       class="form-control digits required">
				</div>
			</div>
			<hr/>
		</div>
	</div>
</div>
<?= form_hidden('region_country_id', $row['region_country_id']) ?>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('region_id', $row['region_id']) ?>
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
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('country_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
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
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
</script>