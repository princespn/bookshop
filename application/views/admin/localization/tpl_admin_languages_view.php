<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
    <div class="row">
        <div class="col-md-8">
            <div class="input-group text-capitalize">
                <?= generate_sub_headline('manage_languages', 'fa-map-marker') ?>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('add_language') ?></span></a>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'add_language') ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <div>
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center hidden-xs"><?= tb_header('status', 'status') ?></th>
	                <th></th>
                    <th><?= tb_header('name', 'language') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('code', 'code') ?></th>
                    <th class="text-center"><?= tb_header('flag', 'flag') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows['values'] as $k => $v): ?>
	                <?php $k++ ?>
                    <tr>
                        <td style="width: 8%" class="text-center hidden-xs">
                            <?php if ($v['language_id'] != $sts_site_default_language): ?>
                                <a href="<?= admin_url('update_status/table/' . TBL_LANGUAGES . '/type/status/key/language_id/id/' . $v[ 'language_id' ]) ?>" <?= is_disabled('update', true) ?>
                                   class="btn btn-default <?= is_disabled('update', true) ?>"><?= set_status($v['status']) ?></a>
                            <?php else: ?>
                                <span class="label label-success"> <?= lang('default') ?></span>
                            <?php endif; ?>
                        </td>
	                    <td style="width:5%"><i class="flag-<?= $v['image'] ?>"></i></td>
                        <td>
	                        <input
		                        name="languages[<?= $v['language_id'] ?>][name]" <?= is_disabled('update', TRUE) ?>
		                        type="text" value="<?= trim($v['name']) ?>" class="form-control required"
		                        tabindex="<?= $k ?>"/>

                        </td>
                        <td class="text-center hidden-xs" style="width:8%"><input
		                        name="languages[<?= $v['language_id'] ?>][code]" <?= is_disabled('update', TRUE) ?>
		                        type="text" value="<?= trim($v['code']) ?>" class="form-control required"
		                        tabindex="<?= $k ?>"/></td>
                        <td class="text-center" style="width:8%">
	                        <input
		                        name="languages[<?= $v['language_id'] ?>][image]" <?= is_disabled('update', TRUE) ?>
		                        type="text" value="<?= trim($v['image']) ?>" class="form-control required"
		                        tabindex="<?= $k ?>"/>
                        </td>
                        <td class="text-right">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update_entries/' . $v['language_id'] . '?file=affiliate') ?>"
                               class="tip btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <?php if ($v['language_id'] != $sts_site_default_language): ?>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['language_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr class=" hidden-xs">
                    <td colspan="6">
	                    <button class="btn btn-primary <?= is_disabled('update', true) ?>"
	                            type="submit"><?= lang('save_changes') ?></button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php form_close(); ?>
    <br/>
    <!-- Load JS for Page -->
    <script>
        $("#form").validate();
    </script>
<?php endif; ?>