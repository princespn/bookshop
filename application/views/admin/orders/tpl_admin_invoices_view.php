<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('invoices', 'fa-file-text-o', $rows[ 'total' ]) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a data-toggle="collapse" data-target="#search_block"
			   class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
			<?php if (!empty($id)): ?>
				<a href="<?= admin_url(CONTROLLER_CLASS . '/create/' . $id) ?>"
				   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
						class="hidden-xs"><?= lang('create_invoice') ?></span></a>
			<?php else: ?>
				<a href="<?= admin_url(TBL_MEMBERS . '/view/') ?>"
				   class="btn btn-primary"><?= i('fa fa-search') ?> <span
						class="hidden-xs"><?= lang('view_members') ?></span></a>
			<?php endif; ?>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
	<div id="search_block" class="collapse">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
		<div class="box-info">
			<h4><?=i('fa fa-search')?> <?= lang('search_invoices') ?></h4>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
						<input type="text" name="search_term" class="form-control required" placeholder="<?=lang('enter_search_term')?>">
						<span class="input-group-btn">
				        <button class="btn btn-default" type="submit"><?=lang('search')?></button>
				      </span>
					</div>
				</div>
			</div>
		</div>
		<?=form_close() ?>
	</div>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="box-info">
		<div class="<?= mobile_view('hidden-xs') ?>">
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
					<th class="text-center"><?= tb_header('paid', 'payment_status_id') ?></th>
					<th class="text-center"><?= tb_header('date', 'date_purchased') ?></th>
					<th class="text-center"><?= tb_header('invoice_number', 'invoice_number') ?></th>
					<th><?= tb_header('client_name', 'customer_name') ?></th>
					<th class="text-center"><?= tb_header('amount', 'amount') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<tr>
						<td style="width: 5%"
						    class="text-center hidden-xs"><?= form_checkbox('id[]', $v[ 'invoice_id' ]) ?></td>
						<td style="width: 10%" class="text-center">
                            <span class="label label-default" style="background-color: <?= $v[ 'color' ] ?>">
                                <?= $v[ 'payment_status' ] ?></span>
						</td>
						<td style="width: 12%" class="text-center">
							<?= local_date($v[ 'date_purchased' ]) ?>
						</td>
						<td class="text-center">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'invoice_id' ]) ?>">
								<strong>
									<?php if (empty($v[ 'invoice_number' ])): ?>
										<?= $v[ 'invoice_id' ] ?>
									<?php else: ?>
										<?= $v[ 'invoice_number' ] ?>
									<?php endif; ?>
								</strong>
							</a>
						</td>
						<td>
							<?php if (!empty($v[ 'member_id' ])): ?>
								<a href="<?= admin_url('members/update/' . $v[ 'member_id' ]) ?>">
									<?= $v[ 'customer_name' ] ?>
								</a>
							<?php else: ?>
								<?= $v[ 'customer_name' ] ?>
							<?php endif; ?>
						</td>
						<td class="text-center"><?= format_amount($v[ 'total' ]) ?></td>
						<td class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/print_copy/' . $v[ 'invoice_id' ]) ?>"
							   class="btn btn-default" target="_blank"
							   title="<?= lang('print') ?>"><?= i('fa fa-print') ?></a>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/email/' . $v[ 'invoice_id' ]) ?>"
							   class="btn btn-default <?= is_disabled('update') ?>" title="<?= lang('email') ?>"><?= i('fa fa-envelope') ?></a>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'invoice_id' ]) ?>"
							   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'invoice_id' ]) ?>"
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
								<?= form_dropdown('change-status', options('payment_statuses'), '', ' class="form-control"') ?>
								<span class="input-group-btn">
                    <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= lang('go') ?></button></span>
							</div>
						<?php endif; ?>
					</td>
					<td colspan="3">
						<div class="btn-group hidden-xs pull-right">
							<?php if (!empty($paginate[ 'num_pages' ]) AND $paginate[ 'num_pages' ] > 1): ?>
								<button disabled
								        class="btn btn-default visible-lg"><?= $paginate[ 'num_pages' ] . ' ' . lang('total_pages') ?></button>
							<?php endif; ?>
							<button type="button" class="btn btn-primary dropdown-toggle"
							        data-toggle="dropdown"><?= i('fa fa-list') ?>
								<?= lang('select_rows_per_page') ?> <span class="caret"></span>
							</button>
							<?= $paginate[ 'select_rows' ] ?>
						</div>
					</td>

				</tr>
				</tfoot>
			</table>
		</div>
		<?php if (mobile_view()): ?>
			<div class="visible-xs">
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<div class="box-info card">
						<h5><span class="pull-right"><?= format_amount($v[ 'total' ]) ?></span>
							<?= $v[ 'customer_name' ] ?>
						</h5>
                        <hr />
						<div class="text-muted">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'invoice_id' ]) ?>">
								<?php if (empty($v[ 'invoice_number' ])): ?>
									<?= $v[ 'invoice_id' ] ?>
								<?php else: ?>
									<?= $v[ 'invoice_number' ] ?>
								<?php endif; ?>
                            </a><br />
							<?= display_date($v[ 'date_purchased' ], TRUE) ?>
						</div>
                        <hr/>
						<div class="text-right">
							<a href="<?= admin_url('update_status/' . CONTROLLER_CLASS . '/' . $v[ 'invoice_id' ]) ?>"
							   class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v[ 'payment_status' ]) ?></a>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/print/' . $v[ 'invoice_id' ]) ?>"
							   class="btn btn-default" title="<?= lang('print') ?>"><?= i('fa fa-print') ?></a>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/email/' . $v[ 'invoice_id' ]) ?>"
							   class="btn btn-default" title="<?= lang('email') ?>"><?= i('fa fa-envelope') ?></a>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'invoice_id' ]) ?>"
							   class="btn btn-default <?= is_disabled('update', TRUE) ?>"
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'invoice_id' ]) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="container text-center"><?= $paginate[ 'rows' ] ?></div>

	</div>
	<?= form_close() ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
		$("#search-form").validate();
	</script>
<?php endif ?>