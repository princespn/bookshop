<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="row">
        <div class="col-md-8">
			<?= generate_sub_headline('orders', 'fa-truck', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
            <a data-toggle="collapse" data-target="#search_block"
               class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                        class="hidden-xs"><?= lang('create_order') ?></span></a>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
    <div id="search_block" class="collapse">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
        <div class="box-info">
            <h4><?= i('fa fa-search') ?> <?= lang('search_orders') ?></h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <input type="text" name="search_term" class="form-control required"
                               placeholder="<?= lang('enter_search_term') ?>">
                        <span class="input-group-btn">
				        <button class="btn btn-default" type="submit"><?= lang('search') ?></button>
				      </span>
                    </div>
                </div>
            </div>
        </div>
		<?= form_close() ?>
    </div>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_order') ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="box-info">
        <div class="<?= mobile_view('hidden-xs') ?>">
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
                    <th class="text-center"><?= tb_header('payment', 'invoice_payment_status') ?></th>
                    <th class="text-center"><?= tb_header('order_number', 'order_number') ?></th>
                    <th class="text-center"><?= tb_header('date', 'date_ordered') ?></th>
                    <th><?= tb_header('client_name', 'order_name') ?></th>
                    <th class="text-center"><?= tb_header('status', 'status') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td style="width: 5%"
                            class="text-center hidden-xs"><?= form_checkbox('id[]', $v['order_id']) ?></td>
                        <td style="width: 5%" class="text-center">
							<?php if (!empty($v['invoice_payment_status'])): ?>
                                <span class="label label-default"
                                      style="background-color: <?= $v['invoice_payment_color'] ?>">
                                <?= lang($v['invoice_payment_status']) ?></span>
							<?php else: ?>
                                <strong><?= lang('none') ?></strong>
							<?php endif; ?>
                        </td>
                        <td class="text-center"><a
                                    href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['order_id']) ?>"><strong><?= $v['order_number'] ?></strong>
                        </td>
                        <td class="text-center">
                            <span><?= local_date($v['date_ordered']) ?></span>
                        </td>
                        <td><?= $v['order_name'] ?></td>
                        <td style="width: 10%" class="text-center"><span
                                    class="label label-default"
                                    style="background-color: <?= $v['color'] ?>"><?= lang($v['order_status']) ?></span>
                        </td>
                        <td class="text-right">
                          <?php if (!empty($v['invoice_id'])): ?>
                              <a href="<?= admin_url(TBL_INVOICES . '/update/' . $v['invoice_id']) ?>"
                                 class="btn btn-default"
                                 title="<?= lang('invoice') ?>"><?= i('fa fa-file-text-o') ?></a>
                            <?php endif; ?>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/packing_list/' . $v['order_id']) ?>"
                               class="btn btn-default" target="_blank"
                               title="<?= lang('print') ?>"><?= i('fa fa-print') ?></a>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/email/' . $v['order_id']) ?>"
                               class="btn btn-default <?= is_disabled('update') ?>" title="<?= lang('email') ?>"><?= i('fa fa-envelope') ?></a>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['order_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['order_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </td>
                    </tr>
				<?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4">
                        <div class="input-group text-capitalize">
                            <span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('order_statuses'), '', 'id="change-status" class="form-control"') ?>
                            <span class="input-group-btn">
                    <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= lang('go') ?></button></span>
                        </div>
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
                        <h5><?= $v['order_name'] ?></h5>
                        <hr/>
                        <div class="text-muted">
                            <span class="pull-right label label-default <?= $v['order_status'] ?>"><?= lang($v['order_status']) ?></span>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['order_id']) ?>">
								<?= $v['order_number'] ?></a>
                            <br/>
							<?= display_date($v['date_ordered'], TRUE) ?>
                        </div>
                        <hr/>
                        <div class="text-right">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['order_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> </a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['order_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
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
        $("#search-form").validate();
    </script>
<?php endif ?>