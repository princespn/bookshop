<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="row">
        <div class="col-md-8">
            <?= generate_sub_headline(lang('affiliate_payments'), 'fa-truck', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
            <?= next_page('left', $paginate); ?>
            <a href="<?= admin_url('affiliate_payment_options/view') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('make_payments') ?></span></a>
            <?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
    <?= tpl_no_values('affiliate_payment_options/view', 'make_payments') ?>
<?php else: ?>
    <div class="box-info">
        <div class="<?= mobile_view('hidden-xs') ?>">
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('id', 'aff_pay_id') ?></th>
                    <th class="text-center"><?= tb_header('date', 'date') ?></th>
                    <th class="text-center"><?= tb_header('payment_name', 'payment_name') ?></th>
                    <th class="text-center"><?= tb_header('payment_type', 'payment_type') ?></th>
                    <th class="text-center"><?= tb_header('amount', 'payment_amount') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td style="width: 5%"  class="text-center hidden-xs"><?= form_checkbox('id[]', $v['aff_pay_id']) ?></td>
                        <td style="width: 5%"  class="text-center hidden-xs">
                            <span class="badge"><?= $v['aff_pay_id'] ?></span></td>
                        <td style="width: 10%" class="text-center">
                            <?= display_date($v['payment_date']) ?></td>
                        <td class="text-center">
                            <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
                                <?= $v['payment_name'] ?></a>
                        </td>
                        <td class="text-center">
                            <span class="label label-info label-<?=$v['payment_type']?>">
                                <?= $v['payment_type'] ?></span>
                        </td>
                        <td style="width: 10%" class="text-center">
                            <strong><?= format_amount($v['payment_amount']) ?></strong>
                        </td>
                        <td style="width: 10%" class="text-right">
                            <a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/view/?payment_id=' . $v['aff_pay_id']) ?>"
                               class="btn btn-default hidden-xs" title="<?= lang('view_associated_commissions') ?>">
                                <i class="fa fa-search"></i></a>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['aff_pay_id']) ?>" class="btn btn-default"
                               title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['aff_pay_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <?php if ($this->sec->check_admin_permissions(CONTROLLER_CLASS, 'delete') == true): ?>
                            <div class="input-group text-capitalize">
                                <span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
                                <?= form_dropdown('change-status', options('export','delete'), '', 'id="change-status" class="form-control"') ?>
                                <span class="input-group-btn">
                    <button class="btn btn-primary <?= is_disabled('update', true) ?>"
                            type="submit"><?= lang('go') ?></button></span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td colspan="3">
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
        <?php if (mobile_view()): ?>
            <div class="visible-xs">
                <?php foreach ($rows['values'] as $v): ?>
                    <div class="box-info card">
                        <h5><span class="pull-right"><?= format_amount($v['payment_amount']) ?></span>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['aff_pay_id']) ?>">
	                            <?= $v['payment_name'] ?>
                            </a>
                        </h5>
                        <div class="text-muted">
                            <?= $v['payment_type'] ?><br />
	                        <?= display_date($v['payment_date']) ?>
                        </div>
                        <hr/>
                        <div class="text-right">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['aff_pay_id']) ?>" class="btn btn-default"
                               title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> </a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['aff_pay_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger"><?= i('fa fa-trash-o') ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="container text-center"><?= $paginate['rows'] ?></div>

    </div>
    <?= form_close() ?>
    <br/>
    <!-- Load JS for Page -->
    <script>
        $("#form").validate();
    </script>
<?php endif ?>