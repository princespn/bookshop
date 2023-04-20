<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
    </div>
    <div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
            <span class="hidden-xs"><?= lang('view_modules') ?></span></a>
    </div>
</div>
<hr/>
<div class="box-info">
    <ul class="nav nav-tabs text-capitalize">
        <li class="active"><a href="#main" data-toggle="tab"><?= lang('module_description') ?></a></li>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?php if (!empty($row['values'])): ?>
				<?php if ($row['module']['module_type'] == 'payment_gateways'): ?>
                    <li>
                        <a href="<?= admin_url($row['module']['module_type'] . '/update/' . $row['module']['module_id']) ?>"><?= lang('module_settings') ?></a>
                    </li>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
    </ul>
    <div class="tab-content">
        <div id="main" class="tab-pane fade in active">
			<?php if (CONTROLLER_CLASS == 'install'): ?>
                <hr/>
                <div class="form-group">
					<?= lang('module_type', 'module_type', array('class' => 'col-sm-3 control-label')) ?>
                    <div class="col-lg-5">
                        <span class="form-control"><?= lang($type) ?></span>
                    </div>
                </div>
			<?php endif; ?>
            <hr/>
            <div class="form-group">
				<?= lang('module_status', 'module_status', array('class' => 'col-sm-3 control-label')) ?>
                <div class="col-lg-5">
					<?= form_dropdown('module_status', options('active'), set_value('module_status', $row['module']['module_status']), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('module_name', 'module_name', array('class' => 'col-sm-3 control-label')) ?>
                <div class="col-lg-5">
					<?= form_input('module_name', set_value('module_name', $row['module']['module_name']), 'class="' . css_error('module_name') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('module_description', 'module_description', array('class' => 'col-sm-3 control-label')) ?>
                <div class="col-lg-5">
					<?= form_textarea('module_description', set_value('module_description', $row['module']['module_description']), 'class="' . css_error('module_description') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('module_sort_order', 'module_sort_order', array('class' => 'col-sm-3 control-label')) ?>
                <div class="col-lg-5">
					<?= form_input('module_sort_order', set_value('module_sort_order', $row['module']['module_sort_order']), 'class="' . css_error('module_sort_order') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
			<?php if (CONTROLLER_FUNCTION == 'install'): ?>
				<?php if (!empty($module_list)): ?>
                    <div class="form-group">
                        <label for="module_folder" class="col-sm-3 control-label"><?= lang('install_module') ?></label>

                        <div class="col-lg-5">
							<?= form_dropdown('module_folder', $module_list, set_value('module_folder', $row['module_folder']), 'class="required form-control"') ?>
                        </div>
                    </div>
                    <hr/>
				<?php endif; ?>
                <div class="form-group">
                    <label for="module_name" class="col-sm-3 control-label"><?= lang('upload_zip_file') ?></label>

                    <div class="col-lg-5">
                        <input type="file" name="zip_file" class="btn btn-default"
                               title="<?= lang('optional_upload_module_zip_file') ?>"/>
                    </div>
                </div>
                <hr/>
			<?php endif; ?>
        </div>
		<?php if (!empty($row['values'])): ?>
            <!--
			<div id="settings" class="tab-pane fade in">
				<hr/>
				<?php foreach ($row['values'] as $v): ?>
					<?php if ($v['type'] != 'hidden'): ?>
						<div class="form-group">
							<?= lang(format_settings_label($v['key'], $row['module']['module_type'], $row['module']['module_folder']), $v['key'], array('class' => 'col-md-3 control-label')) ?>

							<div class="col-lg-5">
								<?= generate_settings_field($v, set_value($v['key'], $v['value'])) ?>
							</div>
						</div>
						<hr/>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			-->
		<?php endif; ?>
    </div>
</div>
<nav class="navbar navbar-fixed-bottom  save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-lg-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
                    <input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
                           class="btn btn-success navbar-btn block-phone"/>
				<?php endif; ?>
                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('module_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<script>
    $("#form").validate();
</script>