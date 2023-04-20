<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('invoice_payments', 'fa-file-text-o', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(TBL_INVOICE_PAYMENTS . '/create/') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?>
				<span class="hidden-xs"><?= lang('create_payment') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(TBL_INVOICE_PAYMENTS . '/create/', 'create_payment') ?>
<?php else: ?>
	<div class="box-info">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center"><?= tb_header('date', 'date') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('invoice_id', 'invoice_id') ?></th>
					<th class="text-center"><?= tb_header('transaction_id', 'transaction_id') ?></th>
					<th class="text-center"><?= tb_header('method', 'method') ?></th>
					<th class="text-center"><?= tb_header('amount', 'amount') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td style="width: 15%" class="text-center">
							<?= display_date($v['date']) ?>
						</td>
						<td class="text-center hidden-xs">
							<a href="<?= admin_url(TBL_INVOICES . '/update/' . $v['invoice_id']) ?>">
								<?= $v['invoice_number'] ?>
							</a>
						</td>
						<td class="text-center">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['invoice_payment_id']) ?>">
								<?= $v['transaction_id'] ?>
							</a>
						</td>
						<td class="text-center">
							<?= $v['method'] ?>
						</td>
						<td class="text-center">
							<?php if ($v['amount'] < 0): ?>
								<span class="label label-danger"><?= format_amount($v['amount']) ?></span>
							<?php else: ?>
								<?= format_amount($v['amount']) ?>
							<?php endif; ?>
						</td>
						<td class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['invoice_payment_id']) ?>"
							   class="btn btn-default" title="<?= lang('update') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['invoice_payment_id']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="6">
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
	<?= form_close() ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif ?>