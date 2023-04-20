<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
    </div>
    <div class="col-md-8 text-right">
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?php if ($id > 1): ?>
                <a data-href="<?= admin_url(TBL_ADMIN_GROUPS . '/delete/' . $id) ?>" data-toggle="modal"
                   data-target="#confirm-delete" href="#"
                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><i class="fa fa-trash-o"></i> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= admin_url(TBL_ADMIN_GROUPS . '/view') ?>" class="btn btn-primary"><i class="fa fa-search"></i>
            <span class="hidden-xs"><?= lang('view_admin_groups') ?></span></a>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-12">
        <div class="box-info">
            <h4 class="header"><i class="fa fa-cog"></i> <?= lang(CONTROLLER_FUNCTION) ?></h4>
            <div class="form-group">
                <label for="aff_group_name" class="col-sm-3 control-label"><?= lang('group_name') ?></label>
                <div class="col-lg-5">
                    <?= form_input('group_name', set_value('group_name', $row['group_name']), 'class="' . css_error('group_name') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-sm-3 control-label"><?= lang('view_permissions') ?></label>

                <div class="col-lg-9">
                    <?php foreach ($permissions['view'] as $k => $v): ?>
                        <div class="pull-left col-sm-12 col-md-3"><?= $v ?> <?= lang($k) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-md-3 control-label"></label>

                <div class="col-lg-9">
                    <div class="pull-left col-sm-12 col-md-3">
                        <?= form_checkbox('', '', '', 'class="view-all"') ?>
                        <strong><?= lang('select_deselect') ?></strong>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-md-3 control-label"><?= lang('create_permissions') ?></label>

                <div class="col-md-9">
                    <?php foreach ($permissions['create'] as $k => $v): ?>
                        <div class="pull-left col-sm-12 col-md-3"><?= $v ?> <?= lang($k) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-md-3 control-label"></label>

                <div class="col-lg-9">
                    <div class="pull-left col-sm-12 col-md-3">
                        <?= form_checkbox('', '', '', 'class="create-all"') ?>
                        <strong><?= lang('select_deselect') ?></strong>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-md-3 control-label"><?= lang('update_permissions') ?></label>

                <div class="col-md-9">
                    <?php foreach ($permissions['update'] as $k => $v): ?>
                        <div class="pull-left col-sm-12 col-md-3"><?= $v ?> <?= lang($k) ?> </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-md-3 control-label"></label>

                <div class="col-lg-9">
                    <div class="pull-left col-sm-12 col-md-3">
                        <?= form_checkbox('', '', '', 'class="update-all"') ?>
                        <strong><?= lang('select_deselect') ?></strong>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-md-3 control-label"><?= lang('delete_permissions') ?></label>

                <div class="col-md-9">
                    <?php foreach ($permissions['delete'] as $k => $v): ?>
                        <div class="pull-left col-sm-12 col-md-3"><?= $v ?> <?= lang($k) ?> </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <hr/>
            <div class="form-group text-capitalize">
                <label for="permissions" class="col-md-3 control-label"></label>

                <div class="col-lg-9">
                    <div class="pull-left col-sm-12 col-md-3">
                        <?= form_checkbox('', '', '', 'class="delete-all"') ?>
                        <strong><?= lang('select_deselect') ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
    <?= form_hidden('admin_group_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-lg-12">
                <?php if (CONTROLLER_FUNCTION == 'create'): ?>
                    <input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
                           class="btn btn-success navbar-btn block-phone"/>
                <?php endif; ?>
                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', true) ?>" id="update-button"
                        type="submit"><?=i('fa fa-refresh')?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
    $("#form").validate();

    $('.view-all')
        .on('ifChecked', function (event) {
            $('.view').iCheck('check');
        })
        .on('ifUnchecked', function () {
            $('.view').iCheck('uncheck');
        });
    $('.create-all')
        .on('ifChecked', function (event) {
            $('.create').iCheck('check');
        })
        .on('ifUnchecked', function () {
            $('.create').iCheck('uncheck');
        });
    $('.update-all')
        .on('ifChecked', function (event) {
            $('.update').iCheck('check');
        })
        .on('ifUnchecked', function () {
            $('.update').iCheck('uncheck');
        });
    $('.delete-all')
        .on('ifChecked', function (event) {
            $('.delete').iCheck('check');
        })
        .on('ifUnchecked', function () {
            $('.delete').iCheck('uncheck');
        });
</script>