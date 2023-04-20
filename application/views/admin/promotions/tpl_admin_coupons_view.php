<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
    <div class="row">
        <div class="col-md-8">
            <div class="input-group text-capitalize">
                <?= generate_sub_headline('Pin codes', 'fa-tags', $rows['total']) ?>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <?= next_page('left', $paginate); ?>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('create_coupon') ?></span></a>
            <?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values(CONTROLLER_CLASS . '/create', 'create_coupon') ?>
<?php else: ?>
    <div class="box-info mass-edit">
        <div>
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center hidden-xs"><?= tb_header('status', 'status') ?></th>
                    <th class="text-center"><h5 class="table-header"><a hreaf="">Serial Number</a></h5></th>

                    <th class="text-center"><?= tb_header('coupon_code', 'coupon_code') ?></th>
                    <th class="text-center"><?= tb_header('coupon_amount', 'coupon_amount') ?></th>
                    <th class="text-center"><?= tb_header('type', 'coupon_type') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('Date Generated', 'start_date') ?></th>
                    <th class="text-center"><?= tb_header('Expiry Date', 'end_date') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('Vendor Name', 'coupon_uses') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td style="width: 8%" class="text-center hidden-xs">
                            <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/coupon_id/id/' . $v['coupon_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', true) ?>"><?= set_status($v['status']) ?></a>
                        </td>
                        <td class="text-center"><h5><?= '000000000' ?><?= $v['coupon_id'] ?></h5></td>

                        <td class="text-center"><h5><?= $v['coupon_code'] ?></h5></td>
                        <td class="text-center"><?= $v['coupon_amount'] ?></td>
                        <td class="text-center">
                            <span class="label label-<?php if ($v['coupon_type'] == 'flat'): ?>info<?php else: ?>primary<?php endif; ?>">
                                <?= lang($v['coupon_type']) ?></span>
                        </td>
                        <td class="text-center hidden-xs">
                            <span class="label label-success">
                            <?= display_date($v['start_date']) ?>
                        </span>
                        </td>
                        <td class="text-center">
                               <span class="label label-danger">
                            <?= display_date($v['end_date']) ?></span></td>
                        <td class="text-center hidden-xs"><?= $v['coupon_uses'] ?></td>
                        <td class="text-right">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['coupon_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['coupon_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr class=" hidden-xs">
                    <td colspan="4">
                    </td>
                    <td colspan="4">
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