<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('blog_categories', 'fa-folder', $rows[ 'total' ]) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_category') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_category') ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="hidden-xs"><?= tb_header('visible', 'status') ?></th>
					<th><?= tb_header('category_name', 'category_name') ?></th>
					<th class="hidden-xs"><?= tb_header('sort', 'sort_order') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<tr>
						<td style="width: 3%" class="hidden-xs">
							<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/category_id/id/' . $v['category_id'] . '/' . $v['status']) ?>"
							   class="btn btn-default"><?= set_status($v['status']) ?></a>
						</td>
						<td>
							<input type="text" class="form-control required"
							       name="category_name[<?= $v[ 'category_id' ] ?>]" <?= is_disabled('update') ?>
							       value="<?= $v[ 'category_name' ] ?>"/>
						</td>
						<td style="width: 10%" class="hidden-xs">
							<input type="number" class="form-control digits required"
							       name="sort_order[<?= $v[ 'category_id' ] ?>]" value="<?= $v[ 'sort_order' ] ?>"/>
						</td>
						<td style="width: 20%" class="text-right">
							<?php if (!$disable_sql_category_count): ?>
								<a href="<?= admin_url(TBL_BLOG_POSTS . '/view?p-category_id=' . $v[ 'category_id' ]) ?>"
								   class="tip btn btn-primary hidden-xs" data-toggle="tooltip" data-placement="bottom"
								   title="<?= $v[ 'total' ] ?> <?= lang('posts') ?>">
									<small style="font-size: 10px"><?= $v[ 'total' ] ?> <?= i('fa fa-pencil') ?></small>
								</a>
							<?php endif; ?>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'category_id' ]) ?>"
							   class="btn btn-default <?= is_disabled('update') ?>"
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<?php if ($v['category_id'] != config_option('default_blog_category_id')): ?>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'category_id' ]) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="2">
						<button class="btn btn-primary <?= is_disabled('update', true) ?>"
						        type="submit"><?= lang('save_changes') ?></button>
					</td>
					<td colspan="2" class="hidden-xs">
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
		<div class="container text-center"><?= $paginate[ 'rows' ] ?></div>

	</div>
	<?= form_close() ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif ?>