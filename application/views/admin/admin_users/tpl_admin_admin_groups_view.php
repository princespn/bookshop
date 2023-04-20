<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="row">
        <div class="col-md-8">
            <?= generate_sub_headline('admin_groups', 'fa-group', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
            <a href="<?= admin_url('admin_users') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_administrators') ?></span></a>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('create_admin_group') ?></span></a>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_admin_group') ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <div>
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th><?= tb_header('admin_group', 'group_name', FALSE) ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td>
                            <h5 class="text-capitalize"><a
                                    href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['admin_group_id']) ?>"><?= $v['group_name'] ?></a>
                                <?php if ($v['admin_group_id'] == 1): ?>
                                    <small> - <?= lang('full_permissions_to_entire_system') ?></small>
                                <?php endif; ?>
                            </h5>
                        </td>
                        <td class="text-right">
                            <a href="<?= admin_url(TBL_ADMIN_USERS . '/view?admin_group_id=' . $v['admin_group_id']) ?>"
                               class="tip btn btn-default hidden-xs" data-toggle="tooltip" data-placement="bottom"
                               title="<?= lang('view_admins') ?>"><?= i('fa fa-search') ?></a>
                            <?php if ($v['admin_group_id'] > 1): ?>
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['admin_group_id']) ?>"
                                   class="btn btn-default hidden-xs"
                                   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['admin_group_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>