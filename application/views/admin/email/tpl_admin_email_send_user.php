<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="email_form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang('send_email'), 'fa-edit', '', FALSE) ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(TBL_EMAIL_QUEUE . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
			<span class="hidden-xs"><?= lang('view_email_queue') ?></span></a>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#html" role="tab" data-toggle="tab"><?= lang('html_email') ?></a></li>
		<li><a href="#text" role="tab" data-toggle="tab"><?= lang('text_email') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="html">
			<h3 class="text-capitalize"><?= lang('send_email') ?></h3>
			<hr/>
			<div class="form-group">
				<label class="col-md-2 control-label"><?= lang('to') ?></label>

				<div class="col-md-3">
					<?= form_input('primary_email', set_value('primary_email', $row[ 'primary_email' ]), 'class="form-control required email"') ?>
				</div>
				<?php if (!empty($templates)): ?>
					<label class="col-md-1 control-label"><?= lang('templates') ?></label>

					<div class="col-md-3">
						<?= form_dropdown('', options('email_templates', '', $templates), '', 'class="form-control" id="load_templates"') ?>
					</div>
				<?php endif; ?>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-2 control-label"><?= lang('subject') ?></label>

				<div class="col-md-3">
					<?= form_input('subject', set_value('subject', $row[ 'subject' ]), 'class="form-control required"') ?>
				</div>
				<label class="col-md-1 control-label"><?= lang('merge_fields') ?></label>

				<div class="col-md-3">
					<?= form_dropdown('', merge_fields(TBL_MEMBERS, $row), '', 'class="text-lowercase form-control" id="merge_fields"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-2 control-label"><?= lang('html') ?></label>

				<div class="col-md-7">
					<?= form_textarea('html_body', html_entity_decode(set_value('html_body', $row[ 'html_body' ])), 'class="editor ' . css_error('html_body') . ' form-control required"') ?>
					<?=form_hidden('html', '1')?>
				</div>
			</div>
			<hr/>
		</div>
		<div class="tab-pane fade in" id="text">
			<h3 class="text-capitalize"><?= lang('text_version') ?></h3>
			<hr/>
			<div class="form-group">
				<label class="col-md-2 control-label"><?= lang('text') ?></label>

				<div class="col-md-7">
					<?= form_textarea('text_body', strip_tags(set_value('text_body', $row[ 'text_body' ])), 'class="' . css_error('text_body') . ' form-control"') ?>
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
				<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
				        id="update-button"
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('send') ?></button>
			</div>
		</div>
	</div>
</nav>
<?=form_hidden('member_id', $id)?>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	$(function() {
		var validator = $("#email_form").submit(function() {
			// update underlying textarea before submit validation
			tinyMCE.triggerSave();
		}).validate({
			ignore: "",
			rules: {
				subject: "required",
				html_body: "required"
			},
			errorPlacement: function(label, element) {
				// position error label after generated textarea
				if (element.is("textarea")) {
					label.insertAfter(element.next());
				} else {
					label.insertAfter(element)
				}
			}
		});
		validator.focusInvalid = function() {
			// put focus on tinymce on submit validation
			if (this.settings.focusInvalid) {
				try {
					var toFocus = $(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
					if (toFocus.is("textarea")) {
						tinyMCE.get(toFocus.attr("id")).focus();
					} else {
						toFocus.filter(":visible").focus();
					}
				} catch (e) {
					// ignore IE throwing errors when focusing hidden elements
				}
			}
		}
	});

	<?=html_editor('init', 'html')?>


	$("select#load_templates").change(function () {
		$("select#load_templates option:selected").each(function () {
			if ($(this).attr("value") != "0") {
				id = $(this).attr("value");
				location.href = '<?=admin_url('email_send/user/' . $id . '?template=')?>' + id;
			}
		});
	}).change();

	$("select#merge_fields").change(function () {
		$("select#merge_fields option:selected").each(function () {
			id = $(this).attr("value");
			tinyMCE.execCommand('mceInsertContent', false, id);
			return false;
		});
	}).change();

</script>