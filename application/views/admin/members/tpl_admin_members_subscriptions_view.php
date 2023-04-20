<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
		<?php if (!empty($member)): ?>
			<?= generate_sub_headline(lang('subscriptions') . ' - ' . $member['fname'] . ' ' . $member['lname'], 'fa-file-text-o', $rows['total']) ?>
		<?php else: ?>
			<?= generate_sub_headline('subscriptions', 'fa-file-text-o', $rows['total']) ?>
		<?php endif; ?>
        <hr class="visible-xs"/>
    </div>
    <div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
        <a href="<?= admin_url(TBL_ORDERS . '/create/') ?>"
           class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('create_subscription') ?></span></a>
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
                    <th class="text-center"><?= tb_header('status', 'status') ?></th>
                    <th><?= tb_header('name', 'name', FALSE) ?></th>
                    <th class="text-center"><?= tb_header('start_date', 'start_date') ?></th>
                    <th class="text-center"><?= tb_header('next_due_date', 'next_due_date') ?></th>
                    <th class="text-center"><?= tb_header('amount', 'amount') ?></th>
                    <th class="text-center"><?= tb_header('interval', 'interval') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($rows['values'] as $v): ?>
                    <tr>
                        <td class="text-center">
                            <a href="<?= admin_url('update_status/table/' . TBL_MEMBERS_SUBSCRIPTIONS . '/type/status/key/sub_id/id/' . $v['sub_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
                        </td>
                        <td>
                            <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
		                        <?= $v['fname'] ?> <?=$v['lname']?></a> -
                           <small class="text-muted">
                               <a href="<?= admin_url(TBL_PRODUCTS . '/update/' . $v['product_id']) ?>">
		                           <?= $v['product_name'] ?></a>
                           </small>
                        </td>
                        <td class="text-center"><?= display_date($v['start_date']) ?></td>
                        <td class="text-center"><?= display_date($v['next_due_date']) ?></td>
                        <td class="text-center"><?= format_amount($v['product_price']) ?></td>
                        <td class="text-center"><?= $v['interval_amount'] ?> <?= $v['interval_type'] ?></td>
                        <td class="text-right">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['sub_id']) ?>"
                               class="tip block-phone btn btn-default <?= is_disabled('update') ?>"
                               data-toggle="tooltip"
                               data-placement="bottom" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
                            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['sub_id']) ?>"
                               data-toggle="modal" data-target="#confirm-delete" href="#"
                               class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                        </td>
                    </tr>
				<?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5"></td>
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
                        <h5><span class="pull-right">
                              <a href="<?= admin_url('update_status/table/' . TBL_MEMBERS_SUBSCRIPTIONS . '/type/status/key/sub_id/id/' . $v['sub_id']) ?>"
                                 class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
                        </span>
                            <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
								<?= $v['fname'] ?> <?= $v['lname'] ?></a>
                        </h5>
                        <div>
                            <a href="<?= admin_url(TBL_PRODUCTS . '/update/' . $v['product_id']) ?>" class="text-muted">
								<?= $v['product_name'] ?></a></div>
                        <hr/>
                        <small class="text-muted">
                            <div class="row">
                                <div class="col-xs-6">
									<?= lang('start_date') ?> - <?= display_date($v['start_date']) ?>
                                </div>
                                <div class="col-xs-6">
									<?= lang('next_due_date') ?> - <?= display_date($v['next_due_date']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
									<?= lang('amount') ?> - <?= display_date($v['product_price']) ?>
                                </div>
                                <div class="col-xs-6">
									<?= lang('interval') ?>
                                    - <?= $v['interval_amount'] ?> <?= $v['interval_type'] ?>
                                </div>
                            </div>
                        </small>
                    </div>
				<?php endforeach; ?>
            </div>
            <hr/>
			<?php if (!empty($paginate['rows'])): ?>
                <div class="text-center"><?= $paginate['rows'] ?></div>
                <div class="text-center">
                    <small class="text-muted"><?= $paginate['num_pages'] ?> <?= lang('total_pages') ?></small>
                </div>
			<?php endif; ?>
		<?php endif; ?>
    </div>


<?php endif ?>
