<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update')) ?>
    <div class="row">
        <div class="col-md-8">
            <?= generate_sub_headline('administrators', 'fa-lock', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
            <?= next_page('left', $paginate); ?>
            <a href="<?= admin_url(TBL_ADMIN_GROUPS) ?>" class="btn btn-primary"><?= i('fa fa-key') ?> <span
                    class="hidden-xs"><?= lang('view_admin_groups') ?></span></a>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('add_admin') ?></span></a>
            <?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'add_admin') ?>
<?php else: ?>
    <div class="box-info">
        <div class="row hidden-xs text-center">
            <div class="col-sm-1 hidden-xs hidden-sm">
                <h5><?= form_checkbox('', '', '', 'class="check-all"') ?></h5>
            </div>
            <div class="col-sm-1"></div>
            <div class="col-sm-1"><?= tb_header('status', 'status') ?></div>
            <div class="col-sm-2"><?= tb_header('name', 'fname') ?></div>
            <div class="col-sm-2"><?= tb_header('username', 'username') ?></div>
            <div class="col-sm-3 hidden-sm hidden-md"><?= tb_header('email_address', 'primary_email') ?></div>
            <div class="col-sm-2"></div>
        </div>
        <hr/>
        <?php foreach ($rows['values'] as $v): ?>
            <div class="hover">
                <div class="row text-center valign">
                    <div class="col-sm-1 hidden-xs hidden-sm">
                        <?php if ($v['admin_id'] > 1): ?>
                            <?= form_checkbox('id[]', $v['admin_id']) ?>
                        <?php endif; ?>
                    </div>
                    <div
                        class="r col-sm-1"><?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-circle dash-photo') ?></div>
                    <div class="r col-sm-1">
                        <h5>
                            <?php if ($v['status'] == 'active'): ?>
                            <span class="label label-success">
            <?php else : ?>
                                <span class="label label-warning">
            <?php endif; ?>
            <?= lang($v['status']) ?>
            </span>
                        </h5>
                    </div>
                    <div class="col-sm-2">
                        <h5>
                            <a href="<?= admin_url('admin_users/update/' . $v['admin_id']) ?>"><?= $v['fname'] . ' ' . $v['lname'] ?></a>
                            <br/>
                            <small class="text-muted capitalize">
                                <?php if ($v['last_login_date'] != '0000-00-00 00:00:00') echo lang('last_login_date') . ': ' . display_date($v['last_login_date']) ?>
                            </small>
                        </h5>
                    </div>
                    <div class="col-sm-2"><?= heading($v['username'], 5) ?></div>
                    <div class="col-sm-3 hidden-sm hidden-md"><?= heading(mailto($v['primary_email']), 5) ?></div>
                    <div class="col-sm-6 col-md-5 col-lg-2 text-right">
                        <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['admin_id']) ?>"
                           class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i class="fa fa-pencil"></i>
                            <span class="visible-xs"><?= lang('edit') ?></span> </a>
                        <a href="mailto:<?= $v['primary_email'] ?>" class="btn btn-default block-phone"><i
                                class="fa fa-envelope"></i> <span class="visible-xs"><?= lang('email') ?></span></a>
                        <?php if ($v['admin_id'] > 1): ?>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['admin_id']) ?>/2/"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>"><i
                                    class="fa fa-trash-o"></i> <span class="visible-xs"><?= lang('delete') ?></span></a>
                        <?php endif; ?>
                    </div>
                </div>
                <hr/>
            </div>
        <?php endforeach; ?>
        <div class="row">
            <div class="col-sm-5 col-lg-3">
                <div class="input-group hidden-xs hidden-sm">
                    <span class="input-group-addon"><?= lang('mark_checked_as') ?> </span>
                    <?= form_dropdown('change-status', options('admin_status', 'deleted'), '', 'id="change-status" class="form-control"') ?>
                    <span class="input-group-btn">
                            <button class="btn btn-primary <?= is_disabled('update', true) ?>"
                                    type="submit"><?= lang('go') ?></button></span>
                </div>
            </div>
            <div class="col-sm-7 col-md-6 col-lg-9 text-right">
                <div class="btn-group hidden-xs">
                    <?php if (!empty($paginate['num_pages']) AND $paginate['num_pages'] > 1): ?>
                        <button disabled
                                class="btn btn-default visible-lg"><?= $paginate['num_pages'] . ' ' . lang('total_pages') ?></button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-primary dropdown-toggle"
                            data-toggle="dropdown"><?= i('fa fa-list') ?>
                        <?= lang('select_rows_per_page') ?> <span class="caret"></span>
                    </button>
                    <?= $paginate['select_rows'] ?>
                </div>
            </div>
        </div>
        <?php if (!empty($paginate['rows'])): ?>
            <div class="text-center"><?= $paginate['rows'] ?></div>
        <?php endif; ?>
    </div>
    <?= form_hidden('redirect', query_url()) ?>
    <?php form_close() ?>
<?php endif; ?>