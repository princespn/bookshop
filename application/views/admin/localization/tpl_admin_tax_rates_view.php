<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('tax_rates', 'fa-list') ?>
		</div>
	</div>
	<div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(TBL_TAX_RATES . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('add_tax_rate') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<?php if (!empty($rows['values'])): ?>
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th><?= tb_header('tax_rate_name', 'tax_rate_name') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('tax_type', 'tax_type') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('zone', 'zone') ?></th>
					<th class="text-center"><?= tb_header('tax_amount', 'tax_amount') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('amount_type', 'amount_type') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['tax_rate_id']) ?>"><?= $v['tax_rate_name'] ?></a>
						</td>
						<td class="text-center hidden-xs">
							<span class="label label-primary"><?= lang($v['tax_type']) ?></span>
						</td>
						<td class="text-center hidden-xs"><?= $v['zone_name'] ?></td>
						<td class="text-center"><?= $v['tax_amount'] ?></td>
						<td class="text-center hidden-xs">
							<span class="label label-default"><?= lang($v['amount_type']) ?></span>
						</td>
						<td class="text-right">
							<?php if ($v['tax_rate_id'] != 1): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['tax_rate_id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['tax_rate_id']) ?>"
							   class="btn btn-default" title="<?= lang('edit') ?>"><i class="fa fa-pencil"></i></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>
<br/>