<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-external-link') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_tracking') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="text-capitalize"><?= lang('manage_tracking_details') ?></h3>
	<span><?=lang('tracking_details_description')?></span>
	<hr/>
	<div class="form-group">
		<?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
		<div class="r col-md-2">
			<?= form_dropdown('status', options('active'), set_value('status', $row['status']), 'id="status" class="form-control"'); ?>
		</div>
		<?= lang('expires_on', 'end_date', array('class' => 'col-md-1 control-label')) ?>
		<div class="r col-md-2">
			<div class="input-group">
				<input type="text" name="end_date"
				       value="<?= set_value('end_date', $row['end_date']) ?>"
				       class="form-control datepicker-input required"/>
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			</div>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('tracker_name') ?></label>
		<div class="r col-md-2">
			<?= form_input('name', set_value('name', $row['name']), 'class="form-control required"') ?>
		</div>
		<label class="col-md-1 control-label"><?= lang('user') ?></label>
		<div class="col-md-2">
			<select id="member_id" class="form-control select2" name="member_id">
				<?php if (!empty($row['username'])): ?>
				<option value="<?= $row['member_id'] ?>"
				        selected><?= $row['username'] ?></option>
				<?php endif; ?>
			</select>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('url') ?></label>
		<div class="r col-md-5">
			<?= form_input('url', set_value('url', $row['url']), 'placeholder="' . site_url() . '" class="form-control required"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('expired_url') ?></label>
		<div class="r col-md-5">
			<?= form_input('expired_url', set_value('expired_url', $row['expired_url']), 'placeholder="' . site_url() . '" class="form-control"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('notes') ?></label>
		<div class="r col-md-5">
			<?= form_textarea('notes', set_value('notes', $row['notes']), 'class="form-control"') ?>
		</div>
	</div>
	<hr/>
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
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('tracking_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>

<script>

	$("#member_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_MEMBERS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					username: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.member_id,
							text: item.username
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

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
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});
</script>
