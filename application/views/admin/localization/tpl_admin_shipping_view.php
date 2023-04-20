<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="input-group text-capitalize">
            <?= generate_sub_headline('shipping_modules', 'fa-anchor', $rows['total']) ?>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <?= next_page('left', $paginate); ?>
        <a href="<?= admin_url('modules/view?module_type=shipping') ?>"
           class="btn btn-primary"><?= i('fa fa-search') ?> <span
                class="hidden-xs"><?= lang('view_modules') ?></span></a>
        <?= next_page('right', $paginate); ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <?php if (empty($rows['values'])): ?>
        <?= tpl_no_values(TBL_MEMBERS . '/create/affiliate', 'add_user') ?>
    <?php else: ?>
        <div role="tabpanel" class="tab-pane active" id="admin">
            <hr/>
            <?php foreach ($rows['values'] as $v): ?>
                <div class="row text-capitalize">
                    <div class="col-sm-10">
                        <h5>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['module_id']) ?>"><?= humanize($v['module_name']) ?></a>
                        </h5>
                        <small><?= check_desc($v['module_description']) ?></small>
                    </div>
                    <div class="col-sm-2 text-right">
                        <a href="<?= admin_url('update_status/table/' . TBL_MODULES . '/type/module_status/key/module_id/id/' . $v['module_id']) ?>"
                           class="btn btn-default <?= is_disabled('update', true) ?> "><?= set_status($v['module_status']) ?></a>
                        <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['module_id']) ?>"
                           class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i class="fa fa-pencil"></i>
                            <span class="visible-xs"><?= lang('edit') ?></span> </a>
                    </div>
                </div>
                <hr/>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>