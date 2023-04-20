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
			<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_templates') ?></span></a>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
			<?php endif; ?>
		</div>
	</div>
	<hr/>
	<div class="row">
	<div class="col-md-12">
		<div class="box-info">

                <?php if (CONTROLLER_FUNCTION == 'create'): ?>
                <h3><?=lang('create_custom_template')?></h3>
                <?php else: ?>
                    <h3><?= humanize(lang($row['template_name'])) ?></h3>
                    <span><?= lang($row['description']) ?></span>
                <?php endif; ?>
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
							<li class="active"><a href="#html-<?=$v['language_id']?>" role="tab"
							                      data-toggle="tab"><?= lang('html') ?></a></li>
							<li><a href="#text-<?=$v['language_id']?>" role="tab" data-toggle="tab"><?= lang('text') ?></a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane fade in active" id="html-<?=$v['language_id']?>">
								<hr/>
								<div class="form-group">
									<label class="col-md-2 control-label"><?= lang('subject') ?></label>
									<div class="r col-md-5">
										<?= form_input('lang[' . $v['language_id'] . '][subject]', set_value('subject', $v['subject'], FALSE), 'class="' . css_error('subject') . ' form-control"') ?>
									</div>
									<div class="col-md-3">
										<?= form_dropdown('', merge_fields($row['template_name']), '', 'class="form-control" id="merge_fields"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('html_email', 'html_body', 'class="col-md-2 control-label"') ?>
									<div class="col-md-8">
										<?= form_textarea('lang[' . $v['language_id'] . '][html_body]', set_value('html_body', $v['html_body'], FALSE), 'rows="20" class="editor ' . css_error('html_body') . ' form-control"') ?>
									</div>
								</div>
							</div>
							<div class="tab-pane fade in" id="text-<?=$v['language_id']?>">
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
					<div class="form-group">
						<label class="col-md-3 control-label"><?= lang('cc') ?></label>
						<div class="r col-md-2">
							<?= form_input('cc', set_value('cc', $row['cc']), 'class="' . css_error('cc') . ' form-control"') ?>
						</div>
						<label class="col-md-1 control-label"><?= lang('bcc') ?></label>
						<div class="r col-md-2">
							<?= form_input('bcc', set_value('bcc', $row['bcc']), 'class="' . css_error('bcc') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
					<?php if ($row['email_type'] == 'custom' || CONTROLLER_FUNCTION == 'create'): ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?= lang('template_name') ?></label>
							<div class="r col-md-5">
								<?= form_input('template_name', set_value('template_name', $row['template_name']), 'class="' . css_error('template_name') . ' form-control"') ?>
							</div>
						</div>
						<hr />
						<div class="form-group">
						<label class="col-md-3 control-label"><?= lang('description') ?></label>
						<div class="r col-md-5">
							<?= form_input('description', set_value('description', $row['description']), 'class="' . css_error('description') . ' form-control"') ?>
						</div>
					</div>
					<hr />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<nav class="navbar navbar-fixed-bottom save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
					<?php if ($this->input->get('text_only')): ?>
						<a href="<?=admin_url(CONTROLLER_CLASS . '/update/' . $id)?>" class="btn btn-success navbar-btn block-phone">
							<?= i('fa fa-edit') ?> <?=lang('enable_wysiwyg')?>
						</a>
					<?php else: ?>
					<a href="?text_only=1" class="btn btn-danger navbar-btn block-phone">
						<?= i('fa fa-edit') ?> <?=lang('disable_wysiwyg')?>
					</a>
					<?php endif; ?>
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
	<?php if (CONTROLLER_FUNCTION == 'update'): ?>
		<?= form_hidden('template_id', $id) ?>
		<?= form_hidden('email_type', $row['email_type']) ?>
	<?php else: ?>
		<?= form_hidden('email_type', 'custom') ?>
	<?php endif; ?>

	<?= form_close() ?>
	<script>

		<?php if (!$this->input->get('text_only')): ?>
		<?=html_editor('init', 'html')?>
		<?php endif; ?>

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