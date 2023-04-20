<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<a href="<?= admin_url('template_manager') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('template_manager') ?></span></a>
	</div>
</div>
<hr/>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="box-info">
	<h3><?= $template ?></h3>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
				<?=i('fa fa-exclamation-circle')?> <strong><?=lang('warning')?></strong> <?=lang('editing_templates_warning')?>
            </div>
        </div>
    </div>
	<textarea id="code1" name="template_data"><?= $row['template_data'] ?></textarea>
</div>
<nav class="navbar navbar-fixed-bottom  save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-lg-12">
				<?php if (!empty($row['template_id'])): ?>
				<a href="<?= admin_url(CONTROLLER_CLASS . '/reset_template/' . $row['template_id'] . '/' . $sub_folder . '/' . $type . '/' . $category) ?>"
				   class="btn btn-success"><?= i('fa fa-undo') ?> <?= lang('reset_to_default') ?></a>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_hidden('template_name', $file); ?>
<?php if (!empty($type)): ?>
	<?= form_hidden('template_category', $type); ?>
<?php else: ?>
<?= form_hidden('template_category', $category); ?>
<?php endif; ?>
<?= form_hidden('base_category', $category); ?>
<?= form_close() ?>
<script>
	$(document).ready(function () {
		var editor_one = CodeMirror.fromTextArea(document.getElementById("code1"), {
			lineNumbers: true,
			matchBrackets: true,
			styleActiveLine: true,
			mode: "xml",
			htmlMode: true
		});
	});
</script>