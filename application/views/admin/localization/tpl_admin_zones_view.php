<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
<div class="row">
	<div class="col-md-8">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline('manage_zones', 'fa-list') ?>
		</div>
	</div>
	<div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(TBL_ZONES . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('create_zone') ?></span></a>
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
					<th><?= tb_header('zone', 'zone_name') ?></th>
					<th class="hidden-xs"><?= tb_header('description', 'zone_description') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['zone_id']) ?>"><?= $v['zone_name'] ?></a>
						</td>
						<td><?= $v['zone_description'] ?></td>
						<td class="text-right">
							<?php if ($v['zone_id'] != 1): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['zone_id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['zone_id']) ?>"
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