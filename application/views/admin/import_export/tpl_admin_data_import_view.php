<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="input-group text-capitalize">
            <?= generate_sub_headline('data_import_modules', 'fa-upload', $rows['total']) ?>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <?= next_page('left', $paginate); ?>
        <a href="<?= admin_url('modules/view?module_type=data_import') ?>"
           class="btn btn-primary"><?= i('fa fa-search') ?> <span
                class="hidden-xs"><?= lang('view_modules') ?></span></a>
        <?= next_page('right', $paginate); ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <?php if (empty($rows['values'])): ?>
        <?= tpl_no_values() ?>
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
                        <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['module_id']) ?>"
                           class="btn btn-primary block-phone" title="<?= lang('edit') ?>">
                            <span><?= lang('get_started') ?></span> <?=i('fa fa-caret-right')?></a>
                    </div>
                </div>
                <hr/>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>