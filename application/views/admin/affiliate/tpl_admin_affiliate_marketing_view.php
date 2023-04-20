<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
<div class="row">
    <div class="col-md-8">
        <?= generate_sub_headline('marketing_tools', 'fa-group', $rows['total']) ?>
        <hr class="visible-xs"/>
    </div>
    <div class="col-md-4 text-right">
        <a href="<?= admin_url(TBL_MODULES . '/view/?module_type=affiliate_marketing') ?>"
           class="btn btn-primary"><?= i('fa fa-plus') ?> <span
                class="hidden-xs"><?= lang('view_modules') ?></span></a>
    </div>
</div>
<hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values() ?>
<?php else: ?>
    <div class="box-info">
        <div class="hidden-xs">
            <div class="row text-capitalize">
                <div class="col-sm-1 text-center hidden-xs"><?= tb_header('status', '', FALSE) ?></div>
                <div class="col-sm-9"><?= tb_header('name', '', FALSE) ?></div>
                <div class="col-sm-2"></div>
            </div>
            <hr/>
        </div>
        <?php foreach ($rows['values'] as $v): ?>
            <div class="row">
                <div class="r col-sm-1 text-center hidden-xs"> <a
                        href="<?= admin_url('update_status/table/' . TBL_MODULES . '/type/module_status/key/module_id/id/' . $v['module_id']) ?>"
                        class="btn btn-default <?= is_disabled('update', true) ?>"><?= set_status($v['module_status']) ?></a></div>
                <div class="r col-sm-9">
                    <h5 class="text-capitalize"><a
                            href="<?= admin_url(CONTROLLER_CLASS . '/view_rows/0?module_id=' . $v['module_id']) ?>"><?= $v['module_name'] ?></a>
                    </h5>
                    <small><?= check_desc($v['module_description']) ?></small>
                </div>
                <div class="r col-sm-2 text-right">
                    <?php if (!empty($v['settings'])): ?>
                        <strong class="text-capitalize"><a
                                href="<?= admin_url(CONTROLLER_CLASS . '/settings/' . $v['module_id']) ?>"
                                class="tip btn btn-default block-phone" data-toggle="tooltip" data-placement="bottom"
                                title="<?= lang('settings') ?>"><?= i('fa fa-cogs') ?></a></strong>
                    <?php endif; ?>
                    <a href="<?= admin_url(CONTROLLER_CLASS . '/view_rows/0?module_id=' . $v['module_id']) ?>"
                       class="tip btn btn-primary block-phone"
                       data-toggle="tooltip" data-placement="bottom"
                       title="<?= lang('view') ?>"><?= i('fa fa-search') ?></a>
                </div>
            </div>
            <hr/>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?=form_close()?>