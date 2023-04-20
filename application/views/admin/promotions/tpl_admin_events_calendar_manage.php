<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
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
				class="hidden-xs"><?= lang('view_calendar') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="text-capitalize"><?= lang('manage_event_details') ?></h3>
	<hr/>
	<div class="form-group">
		<?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
		<div class="r col-md-2">
			<?= form_dropdown('status', options('active'), set_value('status', $row['status']), 'id="status" class="form-control"'); ?>
		</div>
		<?= lang('date', 'date', array('class' => 'col-md-1 control-label')) ?>
		<div class="r col-md-2">
			<div class="input-group">
				<input type="text" name="date"
				       value="<?= set_value('date', $row['date']) ?>"
				       class="form-control datepicker-input required"/>
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			</div>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('start_time', 'start_time', array('class' => 'col-md-3 control-label')) ?>
		<div class="col-md-2">
			<?= form_dropdown('start_hour', options('hours'), set_value('start_hour', $row['start_hour']), 'class="form-control time"'); ?>
			: <?= form_dropdown('start_min', options('minutes'), set_value('start_min', $row['start_min']), 'class="form-control time"'); ?>
		</div>
		<?= lang('end_time', 'end_time', array('class' => 'col-md-1 control-label')) ?>
		<div class="col-md-3">
			<?= form_dropdown('end_hour', options('hours'), set_value('end_hour', $row['end_hour']), 'class="form-control time"'); ?>
			: <?= form_dropdown('end_min', options('minutes'), set_value('end_min', $row['end_min']), 'class="form-control time"'); ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('title') ?></label>
		<div class="r col-md-5">
			<?= form_input('title', set_value('title', $row['title']), 'class="form-control required"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('location') ?></label>
		<div class="r col-md-5">
			<?= form_input('location', set_value('location', $row['location']), 'class="form-control"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('description') ?></label>
		<div class="r col-md-5">
			<?= form_textarea('description', set_value('description', $row['description']), 'class="form-control"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('event_photo') ?></label>
		<div class="r col-md-5">
			<div class="input-group">
				<input type="text" name="event_photo" value="<?= $row[ 'event_photo' ] ?>" id="0"
				       class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
			</div>
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
	<?= form_hidden('id', $id) ?>
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
