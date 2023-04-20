<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div>
	<?php if (empty($row)): ?>
        <hr />
		<?= tpl_no_values('', '', 'no_subscriptions_found', 'warning', FALSE) ?>
	<?php else: ?>
		<table class="table table-striped table-hover">
			<thead>
			<tr>
				<th class="text-center"><h5 class="table-header"><?=lang('status')?></h5></th>
				<th><h5 class="table-header"><?=lang('product_name')?></h5></th>
				<th class="text-center"><h5 class="table-header"><?=lang('start_date')?></h5></th>
				<th class="text-center"><h5 class="table-header"><?=lang('next_due_date')?></h5></th>
				<th class="text-center"><h5 class="table-header"><?=lang('amount')?></h5></th>
				<th class="text-center"><h5 class="table-header"><?=lang('interval')?></h5></th>
				<th class="text-right">
					<a href="<?= admin_url(CONTROLLER_CLASS . '/view/?member_id=' . $id) ?>"
					   class="btn btn-primary btn-sm pull-right"><?= i('fa fa-search') ?> <?= lang('view_subscriptions') ?></a>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($row as $v): ?>
				<tr>
					<td class="text-center"><?= set_status($v[ 'status' ], TRUE) ?></td>
					<td>
						<a href="<?= admin_url(TBL_PRODUCTS . '/update/' . $v['product_id']) ?>">
							<?= $v['product_name'] ?></a>
					</td>
					<td class="text-center"><?= display_date($v['start_date']) ?></td>
					<td class="text-center"><?= display_date($v['next_due_date']) ?></td>
					<td class="text-center"><?= format_amount($v[ 'product_price' ]) ?></td>
					<td class="text-center"><?= $v[ 'interval_amount' ] ?> <?= plural($v['interval_type']) ?></td>
					<td class="text-right">
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['sub_id']) ?>"
						   class="tip block-phone btn btn-default btn-sm <?= is_disabled('update') ?>" data-toggle="tooltip"
						   data-placement="bottom" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>



	<?php endif; ?>
</div>