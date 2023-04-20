<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
    <div class="row">
        <div class="col-md-8">
            <div class="input-group text-capitalize">
                <?= generate_sub_headline($title, 'fa-map-marker', '', false, 'none') ?>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <?= next_page('left', $paginate); ?>
            <a href="<?= admin_url(TBL_COUNTRIES . '/view/') ?>"
               class="btn btn-primary hidden-xs <?= is_disabled('create') ?>"><?= i('fa fa-search') ?> <?= lang('view_countries') ?></a>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create/' . $this->input->get('country_id')) ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('add_region') ?></span></a>
            <?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values(CONTROLLER_CLASS . '/create/' . $this->input->get('country_id'), 'add_region') ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <div>
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('visible', 'status') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('country', 'country_name') ?></th>
                    <th><?= tb_header('region', 'region_name') ?></th>
                    <th class="text-center"><?= tb_header('code', 'region_code') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td class="text-center"><?= form_checkbox('region[' . $v['region_id'] . '][update]', $v['region_id']) ?></td>
                        <td style="width: 8%" class="text-center hidden-xs">
                            <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/region_id/id/' . $v['region_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', true) ?> "><?= set_status($v['status']) ?></a>
                        </td>
                        <td class="text-center hidden-xs">
                            <?= i('flag-' . strtolower($v['country_iso_code_2'])) ?> <?= $v['country_name'] ?></td>
                        <td>
                            <input type="text" class="form-control required" name="region[<?= $v['region_id'] ?>][region_name]"
                                   value="<?= $v['region_name'] ?>"/>
                        </td>
                        <td style="width: 8%" class="text-center">
                            <?= $v['region_code'] ?>
                        </td>
                        <td class="text-right">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['region_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['region_id'] . '/' . $country_id) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4">
                        <div class="input-group text-capitalize">
                            <span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
                            <?= form_dropdown('change-status', options('active', 'deleted'), '', 'id="change-status" class="form-control"') ?>
                            <span class="input-group-btn">
                        <button class="btn btn-primary <?= is_disabled('update', true) ?>"
                                type="submit"><?= lang('save_changes') ?></button>
                    </span>
                        </div>
                    </td>
                    <td colspan="2">
                        <div class="btn-group hidden-xs pull-right">
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
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="container text-center"><?= $paginate['rows'] ?></div>
    </div>
    <?php form_close(); ?>
    <br/>
    <!-- Load JS for Page -->
    <script>
        $("#form").validate();
    </script>
<?php endif; ?>