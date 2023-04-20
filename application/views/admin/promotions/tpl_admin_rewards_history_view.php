<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline(lang('rewards_history'), 'fa-list') ?>
			</div>
		</div>
		<div class="col-md-4 text-right">
			<a href="<?= admin_url(CONTROLLER_CLASS . '/view/') ?>"
			   class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_rewards') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th><?= tb_header('name', 'name', FALSE) ?></th>
					<th class="text-center"><?= tb_header('type', 'type') ?></th>
					<th class="text-center"><?= tb_header('date', 'date_added') ?></th>
					<th class="text-center"><?= tb_header('points', 'points') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td>
							<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
							<?=$v['fname']?> <?=$v['lname']?>
							</a>
						</td>
						<td class="text-center"><?=lang($v['type'])?></td>
						<td class="text-center"><?=display_date($v['date_added'])?></td>
						<td class="text-center"><?=$v['points']?></td>
						<td class="text-right">

							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/update_user/' . $v['member_id'] . '/' . $v['points_id'] . '/' . $v['points']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class=" btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>

						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr class=" hidden-xs">
					<td colspan="5">
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
		<?php if (!empty($paginate['rows'])): ?>
			<div class="text-center"><?= $paginate['rows'] ?></div>
			<div class="text-center">
				<small class="text-muted"><?= $paginate['num_pages'] ?> <?= lang('total_pages') ?></small>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>