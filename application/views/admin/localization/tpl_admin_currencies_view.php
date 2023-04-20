<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('currencies', 'fa-money', '') ?>
			</div>
		</div>
		<div class="col-md-4 text-right">
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('add_currency') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create', 'add_currency') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center hidden-xs"><?= tb_header('status', '', FALSE) ?></th>
					<th><?= tb_header('currency_name', '', FALSE) ?></th>
					<th class="text-center"><?= tb_header('code', '', FALSE) ?></th>
					<th class="text-center"><?= tb_header('value', '', FALSE) ?></th>
					<th class="text-center hidden-xs"><?= tb_header('format', '', FALSE) ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td style="width: 8%" class="text-center hidden-xs">
							<?php if ($v['code'] == $this->config->item('sts_site_default_currency')): ?>
								<span class="label label-success"><?= lang('default') ?></span>
							<?php else: ?>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/set_default/' . $v['code']) ?>">
									<span class="label label-danger"><?= lang('set_default') ?></span>
								</a>
							<?php endif; ?>
						</td>
						<td><?= $v['name'] ?> </td>
						<td style="width: 10%" class="text-center"><?= $v['code'] ?></td>
						<td style="width: 10%" class="text-center"><?= $v['value'] ?> </td>
						<td style="width: 10%" class="text-center hidden-xs">
							<strong><?= $v['symbol_left'] . number_format('9999.99', $v['decimal_places'], $v['decimal_point'], $v['thousands_point']) . $v['symbol_right'] ?></strong>
						</td>
						<td style="width: 15%" class="text-right">
							<?php if ($v['code'] != $this->config->item('sts_site_default_currency')): ?>
                            <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/currency_id/id/' . $v['currency_id']) ?>"
                               class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
							<?php endif; ?>
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['currency_id']) ?>"
							   class="btn btn-default <?= is_disabled('update', TRUE) ?>"
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v['currency_id'] > 1): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['currency_id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="6"></td>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?php form_close(); ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif; ?>