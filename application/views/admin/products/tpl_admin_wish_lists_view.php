<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-7">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-file-text-o', $rows[ 'total' ]) ?>
			</div>
		</div>
		<div class="col-md-5 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(TBL_MEMBERS . '/view/') ?>"
			   class="btn btn-primary "><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_members') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th><?= tb_header('member', 'name') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<tr>
						<td style="width: 30%">
							<strong><a href="<?=admin_url(TBL_MEMBERS . '/update/' . $v['member_id'])?>"><?=$v['fname']?> <?=$v['lname']?></a></strong>
						</td>
						<td style="width: 10%" class="text-right">
							<a href="<?=site_url('wish_list/' . $v['username']) ?>" target="_blank"
							   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
							   title="<?= lang('view_list') ?>"><?= i('fa fa-search') ?></a>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'wish_list_id' ]) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td>
						<button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
						        type="submit"><?= lang('save_changes') ?></button>
					</td>
					<td>
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
	<?php form_close(); ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif; ?>