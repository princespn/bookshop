<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('dashboard_icons', 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php if ($row['dash_id'] > 14): ?>
			<a data-href="<?= admin_url('dashboard/delete/' . $row['dash_id']) ?>" data-toggle="modal"
			     data-target="#confirm-delete" href="#"
			     class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
				<?php endif; ?>
		<?php endif; ?>

		<a href="<?= admin_url('dashboard/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_icons') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-2">
		<div class="thumbnail hidden-sm text-center">
			<p class="lead">
				<br />
				<i class="fa <?=$row['icon']?> fa-5x" id="picker-target"></i>
			</p>
			</div>
	</div>
	<div class="col-md-10">
		<div class="box-info">
			<h3 id="title" class="header capitalize"><?=lang($row['title'])?></h3>
			<hr />
			<div class="form-group">
				<label for="icon" class="col-md-3 control-label"><?= lang('icon') ?></label>
				<div class="col-md-5">
					<div class="input-group">
						<?= form_input('icon', set_value('icon', $row['icon']), 'data-placement="bottomRight" class="icp form-control required"') ?>
						<span class="input-group-addon"></span>
					</div>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="title" class="col-md-3 control-label"><?= lang('title') ?></label>

				<div class="col-md-5">
					<?= form_input('title', set_value('title', $row['title']), 'class="' . css_error('title') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="description" class="col-md-3 control-label"><?= lang('description') ?></label>

				<div class="col-md-5">
					<?= form_input('description', set_value('description', $row['description']), 'class="' . css_error('description') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="url" class="col-md-3 control-label"><?= lang('url') ?></label>

				<div class="col-md-5">
					<?= form_input('url', set_value('url', $row['url']), 'class="' . css_error('url') . ' form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label for="sort_order" class="col-md-3 control-label"><?= lang('sort_order') ?></label>

				<div class="col-md-5">
					<?= form_input('sort_order', set_value('sort_order', $row['sort_order']), 'class="' . css_error('sort_order') . ' form-control required digits"') ?>
				</div>
			</div>
			<hr/>
		</div>
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('dash_id', $id) ?>
<?php endif; ?>
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
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	$('.icp').iconpicker();

	$('.icp').on('iconpickerSelected', function(e) {
		$("#picker-target").attr('class', '');
		$('#picker-target').addClass('fa-5x fa fa-' + e.iconpickerValue);
	});

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
						else if (response['data']) {
							$('#response').html('<?=alert('success')?>');
							$.each(response['data'], function (key, value) {
								$('#' + key).html(value);
								$('#update_' + key).val(value);
							});

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