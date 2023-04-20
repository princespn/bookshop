<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_certificates') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#details" role="tab" data-toggle="tab"><?= lang('details') ?></a></li>
		<li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?php if (!empty($row[ 'redemption' ])): ?>
				<li><a href="#history" role="tab" data-toggle="tab"><?= lang('redemption_history') ?></a></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="details">
			<h3 class="text-capitalize"><?= lang('gift_certificate_details') ?></h3>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('code', 'code', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<?php if (empty($row[ 'redemption' ])): ?>
								<div class="input-group">
									<?= form_input('code', set_value('code', $row[ 'code' ]), 'id="code" class="' . css_error('code') . ' form-control required" onclick="this.select()" readonly') ?>
									<span id="generate_code" class="cursor input-group-addon">
									<?= i('fa fa-refresh') ?> <?= lang('generate_code') ?></span>
								</div>
							<?php else: ?>
								<?= form_input('code', set_value('code', $row[ 'code' ]), 'id="code" class="' . css_error('code') . ' form-control required" onclick="this.select()" readonly') ?>
							<?php endif; ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('status', 'status', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_dropdown('status', options('active'), $row[ 'status' ], 'class="form-control required" id="status"') ?>
						</div>
						<?= lang('amount', 'amount', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_input('amount', set_value('amount', $row[ 'amount' ]), 'class="' . css_error('amount') . ' form-control required number" placeholder="100.00"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('description', 'description', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<?= form_input('description', set_value('description', $row[ 'description' ]), 'class="' . css_error('description') . ' form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('from_name', 'from_name', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_input('from_name', set_value('from_name', $row[ 'from_name' ]), 'class="' . css_error('from_name') . ' form-control required"') ?>
						</div>
						<?= lang('from_email', 'from_email', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_input('from_email', set_value('from_email', $row[ 'from_email' ]), 'class="' . css_error('from_name') . ' form-control required email"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('to_name', 'to_name', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_input('to_name', set_value('to_name', $row[ 'to_name' ]), 'class="' . css_error('to_name') . ' form-control required"') ?>
						</div>
						<?= lang('to_email', 'to_email', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<?= form_input('to_email', set_value('to_email', $row[ 'to_email' ]), 'class="' . css_error('to_name') . ' form-control required email"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('message', 'message', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<textarea name="message" class="form-control required"
							          rows="10"><?= $row[ 'message' ] ?></textarea>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
		<div class="tab-pane fade in" id="notes">
			<h3 class="text-capitalize"><?= lang('notes') ?></h3>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<?= lang('notes', 'notes', array( 'class' => 'col-md-3 control-label' )) ?>
						<div class="r col-md-5">
							<textarea name="notes" class="form-control"
							          rows="10"><?= $row[ 'notes' ] ?></textarea>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?php if (!empty($row[ 'redemption' ])): ?>
				<div class="tab-pane fade in" id="history">
				<h3 class="text-capitalize"><?= lang('redeemed_amounts') ?></h3>
				<hr/>

				<div class="row text-center text-capitalize">
					<div class="col-md-4"><?= tb_header('invoice_id', '', '') ?></div>
					<div class="col-md-4"><?= tb_header('date', '', '') ?></div>
					<div class="col-md-4"><?= tb_header('amount_redeemed', '', '') ?></div>
				</div>
				<hr/>
				<?php foreach ($row[ 'redemption' ] as $v): ?>
					<div class="row text-center text-capitalize">
						<div class="col-md-4">
							<a href="<?= admin_url(TBL_INVOICES . '/update/' . $v[ 'invoice_id' ]) ?>">
								<?= $v[ 'invoice_number' ] ?>
							</a>
						</div>
						<div class="col-md-4"><?= display_date($v[ 'date_purchased' ]) ?></div>
                        <div class="col-md-4"><span class="label label-danger"><?= format_amount($v[ 'amount' ]) ?></span></div>
					</div>
					<hr/>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<nav class="navbar navbar-fixed-bottom save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
					<?php if (CONTROLLER_FUNCTION == 'create'): ?>
						<button name="redir_button" value="1"
						        class="btn btn-success navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
						        id="update-button"
						        type="submit"><?= i('fa fa-plus') ?> <?= lang('save_add_another') ?></button>
					<?php endif; ?>
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('cert_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<script>
	$('#generate_code').click(function () {
		$.ajax({
			url: '<?=admin_url(CONTROLLER_CLASS . '/generate_serial')?>',
			type: 'GET',
			dataType: 'json',
			data: {code: $('#code').val()},
			success: function (response) {
				if (response.type == 'success') {
					$('#code').val(response.code);
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
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
						if (response['error_fields']) {
							$.each(response['error_fields'], function (key, val) {
								$('#' + key).addClass('error');
								$('#' + key).focus();
							});
						}
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