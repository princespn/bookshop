<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('follow_ups', 'fa-envelope', $rows['total']) ?>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create/' . $list['list_id']) ?>" <?= is_disabled('create') ?>
			   class="btn btn-primary"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('add_follow_up') ?></span></a>
			<a href="<?= admin_url(TBL_EMAIL_MAILING_LISTS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_mailing_lists') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/' . $list['list_id'], 'add_follow_up') ?>
<?php else: ?>
	<div class="box-info">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center"><?= tb_header('sequence', 'sequence') ?></th>
					<th class="text-center"><?= tb_header('send_after', 'days_apart') ?></th>
					<th><?= tb_header('name', 'follow_up_name') ?></th>
					<th><?= tb_header('email_subject', 'subject') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td style="width: 5%"  class="text-center">
							<?= form_dropdown('follow_up[' . $v['follow_up_id'] . '][sequence]', total_tiers($rows[ 'total' ]), $v['sequence'], 'class="form-control"') ?>
						</td>
						<td style="width: 10%" class="text-center">
							<div class="input-group">
							<input
								name="follow_up[<?= $v['follow_up_id'] ?>][days_apart]" <?= is_disabled('update', TRUE) ?>
								type="text" value="<?= $v['days_apart'] ?>" class="form-control required digits"/>
								<span class="input-group-addon"><?= lang('day_s') ?></span>
							</div>
						</td>
						<td style="width: 20%" class="hidden-xs"><h5><?= $v['follow_up_name'] ?></h5>
						</td>
						<td><h5><?= $v['subject'] ?></h5>
						</td>
						<td style="width: 15%" class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['follow_up_id']) ?>"
							   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['follow_up_id'] .'/' . $list['list_id']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot  class=" hidden-xs">
				<tr>
					<td colspan="3">
						<?php if ($this->sec->check_admin_permissions(CONTROLLER_CLASS, 'delete') == TRUE): ?>
							<button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
							        type="submit"><?= lang('save_changes') ?></button>
						<?php endif; ?>
					</td>
					<td colspan="2">
						<div class="btn-group pull-right">
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
    <?=form_hidden('list_id', $list['list_id'])?>
	<?php form_close(); ?>
	<br/>
	<div class="modal fade ajax-modal" id="view-email" tabindex="-1" role="dialog" aria-labelledby="modal-title"
	     aria-hidden="true">
		<div class="modal-dialog" id="modal-title">
			<div class="modal-content"></div>
		</div>
	</div>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif; ?>