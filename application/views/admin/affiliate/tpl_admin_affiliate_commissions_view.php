<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
    <div class="row">
        <div class="col-md-8">
			<?= generate_sub_headline('affiliate_commissions', 'fa-bank', $rows['total']) ?>
            <hr class="visible-xs"/>
        </div>
        <div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
            <a data-toggle="collapse" data-target="#search_block"
               class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
            <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>" <?= is_disabled('create') ?>
               class="btn btn-primary"><?= i('fa fa-plus') ?> <span
                        class="hidden-xs"><?= lang('create_commission') ?></span></a>
			<?= next_page('right', $paginate); ?>
        </div>
    </div>
    <hr/>
    <div id="search_block" class="collapse">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
        <div class="box-info">
            <h4><?= i('fa fa-search') ?> <?= lang('search_commissions') ?></h4>
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
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_commission') ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
    <div class="box-info">
        <div class="<?= mobile_view('hidden-xs') ?>">
            <table class="table table-striped table-hover">
                <thead class="text-capitalize">
                <tr>
                    <th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
                    <th class="text-center"><?= tb_header('status', 'comm_status') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('approved', 'approved') ?></th>
                    <th class="text-center"><?= tb_header('date', 'date') ?></th>
                    <th class="text-center"><?= tb_header('affiliate', 'username') ?></th>
                    <th class="text-center"><?= tb_header('transaction_id', 'trans_id') ?></th>
                    <th class="text-center hidden-xs"><?= tb_header('invoice', 'invoice_number') ?></th>
                    <th class="text-center"><?= tb_header('commission_amount', 'amount') ?></th>
					<?php if (config_item('sts_affiliate_commission_levels') > 1): ?>
                        <th class="text-center hidden-xs"><?= tb_header('tier', 'commission_level') ?></th>
					<?php endif; ?>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $v): ?>
                    <tr <?php if ($v['commission_level'] > '1'): ?> class="sublevel" <?php endif; ?>>
                        <td class="text-center hidden-xs">
	                        <?php if ($v['comm_status'] != 'paid'): ?>
                            <?= form_checkbox('comm_id[]', $v['comm_id']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
							<?php if ($v['comm_status'] == 'pending'): ?>
                                <span class="label label-danger"> <?= lang('pending') ?></span>
							<?php elseif ($v['comm_status'] == 'unpaid') : ?>
                                <span class="label label-unpaid"><?= lang('unpaid') ?></span>
							<?php
							else : ?>
                                <span class="label label-success"><?= lang('paid') ?></span>
							<?php endif; ?>
                        </td>
                        <td class="text-center hidden-xs">
							<?php if ($v['approved'] == '0'): ?>
                                <span class="label label-danger"> <?= lang('no') ?></span>
							<?php elseif ($v['approved'] == '1') : ?>
                                <span class="label label-success"><?= lang('yes') ?></span>
							<?php endif; ?>
                        </td>
                        <td class="text-center"><?= local_date($v['date']) ?></td>
                        <td class="text-center"><a
                                    href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>"><?= $v['username'] ?></a>
                        </td>
                        <td class="text-center">
                            <strong <?php if ($v['commission_level'] > '1'): ?> class="sublevel" <?php endif; ?>>
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['comm_id']) ?>">
									<?= $v['trans_id'] ?>
                                </a>
                            </strong>
                        </td>
                        <td class="text-center hidden-xs">
							<?php if (!empty($v['invoice_id'])): ?>
                                <a href="<?= admin_url('invoices/update/' . $v['invoice_id']) ?>">
									<?= $v['invoice_number'] ?></a>
							<?php else: ?>
                                <small class="text-muted"><?= lang('none') ?></small>
							<?php endif; ?>
                        </td>
                        <td class="text-center">
                            <strong><?= format_amount($v['commission_amount']) ?></strong></td>
						<?php if (config_item('sts_affiliate_commission_levels') > 1): ?>
                            <td class="text-center hidden-xs">
								<?php if ($v['commission_level'] == 1): ?>
                                    <strong class="badge"><?= $v['commission_level'] ?></strong>
								<?php else: ?>
									<?= $v['commission_level'] ?>
								<?php endif; ?>
                            </td>
						<?php endif; ?>
                        <td class="text-right">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['comm_id']) ?>"
                               class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['comm_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </td>
                    </tr>
				<?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="8">
                        <div class="input-group text-capitalize">
                            <span class="input-group-addon"><?= form_checkbox('', '', '', 'class="check-all"') ?>
                                <small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('commissions', 'delete'), '', 'id="change-status" class="form-control"') ?>
                            <span class="input-group-btn">
                                <button class="btn btn-primary <?= is_disabled('update') ?>" type="submit">
                                    <?= lang('go') ?>
                                </button>
                            </span>
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
                        <div class="row">
                            <div class="col-xs-6">
                                <h5>
                                    <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
										<?= lang('affiliate') ?>: <?= $v['username'] ?>
                                    </a>
                                </h5>
                            </div>
                            <div class="col-xs-6 text-right">
                                <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['comm_id']) ?>"
                                   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> </a>
                                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['comm_id']) ?>"
                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-xs-8">
                                <span class="text-muted"><?= format_amount($v['commission_amount']) ?></span><br/>
                                <small class="text-muted">
									<?= $v['trans_id'] ?><br/>
									<?= display_date($v['date']) ?>
                                </small>
                            </div>
                            <div class="col-xs-4">
								<?php if ($v['comm_status'] == 'pending'): ?>
                                    <span class="pull-right label label-danger"> <?= lang('pending') ?></span>
								<?php elseif ($v['comm_status'] == 'unpaid') : ?>
                                    <span class="pull-right label label-warning"><?= lang('unpaid') ?></span>
								<?php
								else : ?>
                                    <span class="pull-right label label-success"><?= lang('paid') ?></span>
								<?php endif; ?>
                            </div>
                        </div>

                        <div class="text-right">

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