<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url('email_send/queue_mass_email'), 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('sending_email', 'fa-envelope', '') ?>
	</div>
	<div class="col-md-8 text-right">
	</div>
</div>
<hr/>
<div class="box-info">
	<h3><?= lang('sending_emails') ?>. <?= lang('please_wait') ?>....</h3>
	<hr/>
	<div class="jumbotron dashed text-center">
		<div id="progress-box" class="text-center">
			<div id="progress-div" class="progress progress-striped active">
				<div id="progress-bar" class="progress-bar progress-bar-info" role="progressbar" style="width: 1%"></div>
			</div>
		</div>
		<div id="success-msg"></div>
		<div id="continue" class="hidden text-right">
			<hr />
			<a href="<?=admin_url('email_queue/view')?>" class="btn btn-primary"><?=i('fa fa-caret-right')?> <?=lang('continue')?></a>
		</div>
		<input type="hidden" name="offset" value="0" id="offset" />
		<input type="hidden" name="queued_total" value="<?=$queued_total?>"  />
	</div>
</div>
<script>
	submit_form();

	function submit_form(qid) {
		$.ajax({
			url: '<?=admin_url('email_queue/send_emails/')?>',
			type: 'POST',
			dataType: 'json',
			data: $('#form').serialize(),
			success: function (response) {
				if (response.type == 'continue') {
					$('#offset').val(response.offset);
					$("#progress-bar").css('width', response.width + '%');//update the progress bar width
					submit_form(); //run it again to queue the rest
				}
				else {
					$("#success-msg").html('<h3>'+ response.sent + ' <?=lang('emails_sent')?>!</h3>');
					$("#progress-bar").css('width', '100%');//update the progress bar width
					$("#continue").removeClass('hidden');
					$("#progress-div").removeClass('active');
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
</script>