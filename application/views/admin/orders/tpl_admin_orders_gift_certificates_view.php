<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="row">
        <div class="col-md-8">
			<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-gift', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
               class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                        class="hidden-xs"><?= lang('create_certificate') ?></span></a>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
    <div class="box-info">
        <div class="<?= mobile_view('hidden-xs') ?>">
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
                    <th class="text-center"><?= tb_header('status', 'status') ?></th>
                    <th class="text-center"><?= tb_header('code', 'code') ?></th>
                    <th class="text-center"><?= tb_header('from', 'from_name') ?></th>
                    <th class="text-center"><?= tb_header('to', 'to_name') ?></th>
                    <th class="text-center"><?= tb_header('amount', 'amount') ?></th>
                    <th class="text-center"><?= tb_header('redeemed', 'redeemed') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td style="width: 5%" class="text-center hidden-xs">
							<?= form_checkbox('cert_id[]', $v['cert_id']) ?></td>
                        <td style="width: 5%" class="text-center">
                            <a href="<?= admin_url('update_status/table/' . TBL_ORDERS_GIFT_CERTIFICATES . '/type/status/key/cert_id/id/' . $v['cert_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', TRUE) ?>">
								<?= set_status($v['status']) ?>
                            </a>
                        </td>
                        <td class="text-center"><h5><?= $v['code'] ?></h5></td>
                        <td class="text-center"><?= $v['from_name'] ?></td>
                        <td class="text-center"><?= $v['to_name'] ?></td>
                        <td style="width: 10%" class="text-center"><?= format_amount($v['amount']) ?></td>
                        <td style="width: 10%" class="text-center">
                            <span class="label label-danger"><?= format_amount($v['redeemed']) ?></span>
                        </td>
                        <td style="width: 20%" class="text-right">
							<?php if (!empty($v['to_email'])): ?>
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/email/' . $v['cert_id']) ?>"
                                   class="btn btn-default" title="<?= lang('email') ?>"><?= i('fa fa-envelope') ?></a>
							<?php endif; ?>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['cert_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['cert_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </td>
                    </tr>
				<?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
						<?php if ($this->sec->check_admin_permissions(CONTROLLER_CLASS, 'delete') == TRUE): ?>
                            <div class="input-group text-capitalize">
                                <span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
								<?= form_dropdown('change-status', options('active'), '', ' class="form-control"') ?>
                                <span class="input-group-btn">
                    <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
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
                        <h5><span class="pull-right"><?= format_amount($v['amount']) ?></span>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['cert_id']) ?>">
								<?= $v['code'] ?>
                            </a>
                        </h5>
                        <hr/>
                        <small>
                            <div class="row text-muted">
                                <div class="col-xs-6">
                                    <strong><?= lang('from') ?>:</strong> <?= $v['from_name'] ?><br/>
									<?= $v['from_email'] ?>
                                </div>
                                <div class="col-xs-6">
                                    <strong><?= lang('to') ?>:</strong> <?= $v['to_name'] ?><br/>
									<?= $v['from_email'] ?>
                                </div>
                            </div>
                        </small>
                        <hr/>
                        <div class="text-right">
                            <a href="<?= admin_url('update_status/table/' . TBL_ORDERS_GIFT_CERTIFICATES . '/type/status/key/cert_id/id/' . $v['cert_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', TRUE) ?>">
								<?= set_status($v['status']) ?>
                            </a>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/email/' . $v['cert_id']) ?>"
                               class="btn btn-default" title="<?= lang('email') ?>"><?= i('fa fa-envelope') ?></a>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['cert_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', TRUE) ?>"
                               title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['cert_id']) ?>"
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
    </script>
<?php endif ?>