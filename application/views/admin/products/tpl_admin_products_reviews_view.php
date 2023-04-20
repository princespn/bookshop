<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-7 col-lg-5">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('product_reviews', 'fa-tags', $rows[ 'total' ]) ?>
			</div>
		</div>
		<div class="col-md-5 col-lg-7 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_review') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div class="<?= mobile_view('hidden-xs') ?>">
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('member', 'member_id') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('approved', 'status') ?></th>
					<th class="text-center"><?= tb_header('ratings', 'ratings') ?></th>
					<th class="hidden-xs"><?= tb_header('product_review', 'comment') ?></th>
					<th class="text-center"><?= tb_header('sort', 'sort_order') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<tr>
						<td style="width: 5%"
						    class="text-center"><?= form_checkbox('reviews[' . $v[ 'id' ] . '][id]', $v[ 'product_id' ]) ?></td>
						<td style="width: 10%" class="text-center hidden-xs"><?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-circle dash-photo') ?>
							<br/>
                            <a href="<?=admin_url(TBL_MEMBERS .'/update/' . $v['member_id'])?>"><small><?= format_name($v[ 'username' ]) ?></small></a>
						</td>
						<td style="width: 5%" class="text-center hidden-xs">
							<a href="<?= admin_url('update_status/table/' . TBL_PRODUCTS_REVIEWS . '/type/status/key/id/id/' . $v[ 'id' ]) ?>"
							   class="btn btn-default <?= is_disabled('update', TRUE) ?>">
							<?= set_status($v[ 'status' ]) ?></a>
						</td>
						<td style="width: 10%" class="text-center">
							<?= format_ratings($v[ 'ratings' ]) ?>
							<small class="text-center"><?=display_date($v['date'])?></small>
						</td>
						<td class="hidden-xs">
							<h5><a href="<?= admin_url('products/update/' . $v[ 'product_id' ]) ?>">
									<?= $v[ 'product_name' ] ?></a>
							</h5>
							<strong><?=$v['title']?></strong> - <?= word_limiter($v[ 'comment' ], 50) ?>
						</td>
						<td style="width: 7%" class="text-center">
							<input type="number" class="form-control digits required" name="reviews[<?= $v[ 'id' ] ?>][sort_order]"
						                                                 value="<?= $v[ 'sort_order' ] ?>"/></td>
						<td style="width: 10%" class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'id' ]) ?>"
							   class="btn btn-default"
							   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'id' ]) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="4">
						<div class="input-group text-capitalize">
							<span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
							<?= form_dropdown('change-status', options('approve', 'delete'), '', 'id="change-status" class="form-control"') ?>
							<span class="input-group-btn">
                        <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                                type="submit"><?= lang('save_changes') ?></button>
                    </span>
						</div>
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
						<h2>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'id' ]) ?>"><?= $v[ 'product_name' ] ?></a>
						</h2>

						<div class="additional-btn">
							<a class="additional-icon" href="#" data-toggle="collapse"
							   data-target="#box-<?= $v[ 'id' ] ?>"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div id="box-<?= $v[ 'id' ] ?>" class="collapse in">
							<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-responsive') ?>
							<hr/>
							<input class="rating form-control hide" value="<?= $v[ 'ratings' ] ?>"
							       data-symbol="&#xf005;"
							       data-glyphicon="false" data-rating-class="rating-fa" data-size="xs"
							       data-show-clear="false" data-show-caption="false">

							<p><strong><?=$v['title']?></strong> - <?= $v[ 'comment' ] ?></p>

							<p class="text-right"><a
									href="<?= admin_url('clients/update/' . $v[ 'member_id' ]) ?>"><?= lang('by') ?>
									: <?= $v[ 'username' ] ?></a></p>
							<hr/>
							<div class="text-right">
								<a href="<?= admin_url('update_status/' . CONTROLLER_CLASS . '/' . $v[ 'id' ]) ?>"
								   class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v[ 'status' ]) ?></a>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'id' ]) ?>"
								   class="btn btn-default"
								   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> <?= lang('edit') ?></a>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'id' ]) ?>"
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
	<script
		src="<?= base_url(); ?>themes/admin/<?= $sts_admin_layout_theme ?>/third/star-rating/star-rating.min.js"></script>
	<script>
		$("#form").validate();
	</script>
<?php endif; ?>