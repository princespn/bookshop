<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<h2 class="sub-header block-title"><?= i('fa fa-list') ?> <?= lang('manage_widget') ?></h2>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
		<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
		<?php if ($row[ 'widget_type' ] == 'custom'): ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/clone_widget/' . $id) ?>" class="btn btn-info"><i class="fa fa-clone"></i>
			<span class="hidden-xs"><?= lang('clone_widget') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><i class="fa fa-search"></i>
			<span class="hidden-xs"><?= lang('view_widgets') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
		<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<div class="row">
		<div class="col-md-12">
			<h3 class="text-capitalize" id="widget_name"><?= $row['widget_name'] ?></h3>
			<span id="description"><?=$row['description']?></span>
			<hr />
			<p class="alert alert-danger"><?= i('fa fa-info-circle') ?> <?= lang('warning_widget_code_advanced_editing') ?></p>
			<hr/>
			<div class="form-group">
				<?= lang('widget_name', 'widget_name', 'class="col-md-1 control-label"') ?>
				<div class="col-md-3">
					<?= form_input('widget_name', set_value('widget_name', $row[ 'widget_name' ]), 'maxlength="30" class="form-control required"') ?>
				</div>
				<?= lang('description', 'description', 'class="col-md-1 control-label"') ?>
				<div class="col-md-5">
					<?= form_input('description', set_value('description', $row[ 'description' ]), 'class="form-control required"') ?>
				</div>
			</div>
			<hr />

			<h5 class="text-capitalize"><?= lang('drag_drop_code') ?></h5>
			<span class="text-capitalize"><?=lang('widget_preview_code_description')?>
            <strong><?=lang('widget_preview_code_description_2')?></strong></span>
			<hr />
			<?= form_textarea('preview_code', set_value('preview_code', $row[ 'preview_code' ], FALSE), 'id="code2"  class=" form-control"') ?>
			<hr />

			<h5 class="text-capitalize"><?= lang('dynamic_template_code') ?></h5>
			<span class="text-capitalize"><?=lang('widget_template_code_description')?></span>
			<hr />
			<?= form_textarea('template_code', set_value('template_code', $row[ 'template_code' ], FALSE), 'id="code1"  class=" form-control"') ?>
			<hr />

			<h5 class="text-capitalize"><?= lang('optional_meta_data') ?></h5>
			<span class="text-capitalize"><?=lang('optional_meta_data_for_widget')?></span>
			<hr />
			<?= form_textarea('meta_data', set_value('meta_data', $row[ 'meta_data' ], FALSE), 'id="code3"  class=" form-control"') ?>

			<hr />
			<h5 class="text-capitalize"><?= lang('optional_javascript') ?></h5>
			<span class="text-capitalize"><?=lang('optional_javascript_for_footer')?></span>
			<hr />
			<?= form_textarea('footer_data', set_value('footer_data', $row[ 'footer_data' ], FALSE), 'id="code4"  class=" form-control"') ?>

			<hr />

		</div>
		<hr />
		</div>
	</div>
</div>
<nav class="navbar navbar-fixed-bottom  save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<?php if ($row['widget_type'] == 'system'):  ?>
					<p class="btn btn-danger navbar-btn">
						<?=i('fa fa-info-circle')?>
						<?=lang('system_widgets_not_editable_description')?>
					</p>
				<?php else: ?>
					<button id="update-button" class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				<?php endif; ?>

			</div>
		</div>
	</div>
</nav>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
<?=form_hidden('widget_id', $id)?>
	<?=form_hidden('widget_type', $row['widget_type'])?>
<?php else: ?>
	<?=form_hidden('widget_type', 'section')?>
	<?=form_hidden('widget_category', '51')?>
	<?=form_hidden('thumbnail', 'module-custom.png')?>
<?php endif; ?>
<?= form_close() ?>
<script>
	editor('code1');
    editor('code2');
	editor('code3');
	editor('code4');

	function editor(id)
	{
		CodeMirror.fromTextArea(document.getElementById(id), {
			lineNumbers: true,
			matchBrackets: true,
			styleActiveLine: true,
			mode: "xml",
			<?php if ($row['widget_type'] == 'system'):  ?>
			readOnly: true,
			<?php endif; ?>
			htmlMode: true
		});
	}

	$("#form").validate();

	<?php if ($row['widget_type'] == 'system'): ?>
	$('#form .form-control').attr('readonly', true);
	<?php endif; ?>
</script>
