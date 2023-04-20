<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<?= form_open_multipart('', 'id="form" class="form-horizontal"') ?>
	<div class="row">
		<div class="col-md-5">
			<h2 class="sub-header block-title">
				<?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?>
			</h2>
		</div>
		<div class="col-md-7 text-right">
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
			<?php endif; ?>
			<?php if (!empty($row['list_id'])): ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view/?list_id=' . $row['list_id']) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_follow_ups') ?></span></a>
		<?php else: ?>
			<a href="<?= admin_url(TBL_EMAIL_MAILING_LISTS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_mailing_lists') ?></span></a>
		<?php endif; ?>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
			<?php endif; ?>
		</div>
	</div>
	<hr/>
	<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<h3><?=lang('manage_follow_up')?></h3>
			<span><?=lang('manage_follow_up_description')?></span>
			<hr/>
			<ul class="resp-tabs nav nav-tabs responsive text-capitalize" role="tablist">
				<?php foreach ($row['lang'] as $v): ?>
					<li <?php if ($v['language_id'] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
						<a href="#<?= $v['image'] ?>" data-toggle="tab"><?= i('flag-' . $v['image']) ?>
							<span class="visible-lg"><?= $v['name'] ?></span>
						</a>
					</li>
				<?php endforeach; ?>
				<li><a href="#options" data-toggle="tab"><?= lang('options') ?></a></li>
			</ul>
			<div class="tab-content">
				<?php foreach ($row['lang'] as $v): ?>
					<div
						class="tab-pane <?php if ($v['language_id'] == $sts_site_default_language): ?> active <?php endif; ?>"
						id="<?= $v['image'] ?>">
						<br/>
						<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
							<li class="active"><a href="#html" role="tab"
							                      data-toggle="tab"><?= lang('html') ?></a></li>
							<li><a href="#text" role="tab" data-toggle="tab"><?= lang('text') ?></a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane fade in active" id="html">
								<hr/>
								<div class="form-group">
									<label class="col-md-2 control-label"><?= lang('subject') ?></label>
									<div class="r col-md-5">
										<?= form_input('lang[' . $v['language_id'] . '][subject]', set_value('subject', $v['subject']), 'class="' . css_error('subject') . ' form-control"') ?>
									</div>
									<div class="col-md-3">
										<?= form_dropdown('', merge_fields('follow_ups'), '', 'class="form-control" id="merge_fields"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('html_email', 'html_body', 'class="col-md-2 control-label"') ?>
									<div class="col-md-8">
										<?= form_textarea('lang[' . $v['language_id'] . '][html_body]', set_value('html_body', $v['html_body'], FALSE), 'class="editor ' . css_error('html_body') . ' form-control"') ?>
									</div>
								</div>
							</div>
							<div class="tab-pane fade in" id="text">
								<hr/>
								<div class="form-group">
									<?= lang('text_email', 'text_body', 'class="col-md-2 control-label"') ?>
									<div class="col-md-8">
										<?= form_textarea('lang[' . $v['language_id'] . '][text_body]', set_value('text_body', $v['text_body'], FALSE), 'class="' . css_error('text_body') . ' form-control"') ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?= form_hidden('lang[' . $v['language_id'] . '][language]', $v['name']) ?>
				<?php endforeach; ?>
				<div class="tab-pane" id="options">
					<h3 class="text-capitalize">
						<?= lang('email_addresses') ?>
					</h3>
					<hr/>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= lang('follow_up_name') ?></label>
						<div class="r col-md-5">
							<?= form_input('follow_up_name', set_value('follow_up_name', $row['follow_up_name']), 'class="' . css_error('follow_up_name') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= lang('from_name') ?></label>
						<div class="r col-md-2">
							<?= form_input('from_name', set_value('from_name', $row['from_name']), 'class="' . css_error('from_name') . ' form-control"') ?>
						</div>
						<label class="col-md-1 control-label"><?= lang('from_email') ?></label>
						<div class="r col-md-2">
							<?= form_input('from_email', set_value('from_email', $row['from_email']), 'class="' . css_error('from_email') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
				</div>
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
		<?= form_hidden('follow_up_id', $id) ?>
	<?php endif; ?>
	<?= form_close() ?>
	<script>

		<?=html_editor('init', 'html')?>

		$("select#merge_fields").change(function () {
			$("select#merge_fields option:selected").each(function () {
				id = $(this).attr("value");
				tinyMCE.execCommand('mceInsertContent', false, id);
				return false;
			});
		}).change();

		$("#form").validate({
			ignore: "",
			submitHandler: function (form) {
				tinyMCE.triggerSave();
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
<?php endif; ?>