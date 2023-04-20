<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($id > 257): ?>
				<!<a data-href="<?= admin_url(TBL_COUNTRIES . '/delete/' . $row['country_id']) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>

		<a href="<?= admin_url(TBL_COUNTRIES . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_countries') ?></span></a>
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
				<h3 class="text-capitalize">
					<?php if (CONTROLLER_FUNCTION == 'update'): ?>
						<?= i('flag-' . strtolower($row['country_iso_code_2'])) ?> <?= $row['country_name'] ?>
					<?php else: ?>
						<?= lang('add_country') ?>
					<?php endif; ?>
				</h3>
				<span><?=lang('manage_country_description')?></span>
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
				<label for="country_name" class="col-md-3 control-label"><?= lang('country_name') ?></label>

				<div class="col-md-5">
					<?= form_input('country_name', set_value('country_name', $row['country_name']), 'class="' . css_error('country_name') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="country_iso_code_2" class="col-md-3 control-label"><?= lang('country_iso_code_2') ?></label>

				<div class="col-md-5">
					<?= form_input('country_iso_code_2', set_value('country_iso_code_2', $row['country_iso_code_2']), 'class="' . css_error('country_iso_code_2') . ' form-control required" maxlength="2"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="country_iso_code_3" class="col-md-3 control-label"><?= lang('country_iso_code_3') ?></label>

				<div class="col-md-5">

					<?= form_input('country_iso_code_3', set_value('country_iso_code_3', $row['country_iso_code_3']), 'class="' . css_error('country_iso_code_3') . ' form-control required" maxlength="3"') ?>
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