<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
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
				class="hidden-xs"><?= lang('view_rewards') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="text-capitalize"><?= lang('manage_reward_details') ?></h3>
	<hr/>
	<div class="form-group">
		<?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
		<div class="r col-md-2">
			<?= form_dropdown('status', options('active'), set_value('status', $row['status']), 'id="status" class="form-control"'); ?>
		</div>
		<?= lang('points', 'points', array('class' => 'col-md-1 control-label')) ?>
		<div class="r col-md-2">
			<?= form_input('points', set_value('points', $row['points']), 'class="' . css_error('points') . ' form-control required"') ?>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<?= lang('reward_type', 'rule', array('class' => 'col-md-3 control-label')) ?>
		<div class="r col-md-5">
			<?= form_dropdown('rule', options('reward_types'), set_value('rule', $row['rule']), 'id="rule" class="form-control"'); ?>
		</div>
	</div>
	<hr/>
	<div id="dates">
		<div class="form-group">
			<?= lang('start_date', 'start_date', array('class' => 'col-md-3 control-label')) ?>
			<div class="r col-md-2">
				<div class="input-group">
					<input type="text" name="start_date"
					       value="<?= set_value('start_date', $row['start_date_formatted']) ?>"
					       class="form-control datepicker-input required"/>
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>
			<?= lang('expires_on', 'end_date', array('class' => 'col-md-1 control-label')) ?>
			<div class="r col-md-2">
				<div class="input-group">
					<input type="text" name="end_date"
					       value="<?= set_value('end_date', $row['end_date_formatted']) ?>"
					       class="form-control datepicker-input required"/>
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>
		</div>
		<hr/>
	</div>
		<div class="form-group">
			<label class="col-md-3 control-label"><?= lang('description') ?></label>
			<div class="col-sm-5">
				<?= form_input('description', set_value('description', $row['description'], FALSE), 'class="form-control required"') ?>
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
	<?= form_hidden('rule_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>

<script>

	$("#rule").change(function () {
			var selectedValue = $(this).val();

			if (selectedValue == "reward_user_birthday") {
				$("#dates").hide();
			}
			else {
				$("#dates").show();
			}
		}
	);

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
