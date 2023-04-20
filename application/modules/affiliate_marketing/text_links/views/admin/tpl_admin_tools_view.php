<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update_rows'), 'role="form" id="form"') ?>
<div class="row">
    <div class="col-md-8">
        <div class="input-group text-capitalize">
            <?= generate_sub_headline($module_row['module']['module_name'], 'fa-list', $rows['total']) ?>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <?= next_page('left', $paginate); ?>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>"
           class="btn btn-primary"><?= i('fa fa-list') ?> <span
                class="hidden-xs"><?= lang('view_affiliate_tools') ?></span></a>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/create/' . $module_row['module']['module_id']) ?>"
           class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                class="hidden-xs"><?= lang('add_text_link') ?></span></a>
        <?= next_page('right', $paginate); ?>
    </div>
</div>
<hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values('', FALSE) ?>
<?php else: ?>
    <div class="box-info">
        <div class="row">
            <div class="col-md-1 text-center"><?= tb_header('status', 'status') ?></div>
            <div class="col-md-9"><?= tb_header('text_link_name', 'name') ?></div>
            <div class="col-md-2"></div>
        </div>
        <hr />
        <div id="sortable">
            <?php foreach ($rows['values'] as $v): ?>
                <div class="ui-state-default" id="formid-<?= $v['id'] ?>">
                    <div class="row">
                        <div class="col-md-1 text-center">
                            <a href="<?= admin_url('update_status/table/' . config_item('module_table') . '/type/status/key/id/id/' . $v['id'] . '/' . $v['status']) ?>"
                               class="btn btn-default"><?= set_status($v['status']) ?></a>
                        </div>
                        <div class="col-md-9">
                            <h5><?= $v['name'] ?></h5>
                        </div>
                        <div class="col-md-2 text-right">
						<span class="btn btn-primary handle visible-lg <?= is_disabled('update', TRUE) ?>"><i
                                class="fa fa-sort"></i></span>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['id'] . '/' . $module_row['module']['module_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['id'] . '/' . $module_row['module']['module_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger visible-lg-inline <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </div>
                    </div>
                    <hr />
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <div class="col-sm-5 col-lg-3"></div>
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
    </div>
    <div id="update"></div>
    <?php if (!empty($paginate['rows'])): ?>
        <div class="text-center"><?= $paginate['rows'] ?></div>
    <?php endif; ?>
    </div>
<?php endif; ?>
<?= form_close() ?>
<script>
    $(function () {
        $('#sortable').sortable({
            handle: '.handle',
            placeholder: "ui-state-highlight",
            update: function () {
                var order = $('#sortable').sortable('serialize');
                $("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/' . $id)?>?" + order);
            }
        });
    });
</script>
