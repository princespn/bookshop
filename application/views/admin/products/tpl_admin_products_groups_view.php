<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-7">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('product_groups', 'fa-tags', $rows[ 'total' ]) ?>
			</div>
		</div>
		<div class="col-md-5 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?> "><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_product_group') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_product_group') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div class="<?= mobile_view('hidden-xs') ?>">
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th><?= tb_header('group_name', 'group_name') ?></th>
					<th><?= tb_header('sort', 'sort_order') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<tr>
						<td style="width: 70%" style="width: 3%">
							<input type="text" class="form-control required"
							       name="group_name[<?= $v[ 'group_id' ] ?>]"
							       value="<?= $v[ 'group_name' ] ?>"/>
						</td>
						<td style="width: 10%">
							<input type="number" class="form-control digits required"
							       name="sort_order[<?= $v[ 'group_id' ] ?>]" value="<?= $v[ 'sort_order' ] ?>"/>
						</td>
						<td style="width: 20%" class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'group_id' ]) ?>"
							   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v[ 'group_id' ] != $default_product_group_id): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'group_id' ]) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="2">
						<button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
						        type="submit"><?= lang('save_changes') ?></button>
					</td>
					<td colspan="4">
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
						<h2>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'group_id' ]) ?>"><?= $v[ 'group_name' ] ?></a>
						</h2>

						<div class="additional-btn">
							<a class="additional-icon" href="#" data-toggle="collapse"
							   data-target="#box-<?= $v[ 'group_id' ] ?>"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div id="box-<?= $v[ 'group_id' ] ?>" class="collapse in">
							<?= photo(CONTROLLER_METHOD, $v, 'img-responsive', TRUE) ?>
							<hr/>
							<div class="text-right">
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'group_id' ]) ?>"
								   class="btn btn-default"
								   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> <?= lang('edit') ?></a>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'group_id' ]) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <?= lang('delete') ?></a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="container text-center"><?= $paginate[ 'rows' ] ?></div>
	</div>
	<?php form_close(); ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif; ?>