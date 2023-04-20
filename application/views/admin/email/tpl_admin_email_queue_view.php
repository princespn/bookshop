<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<div class="input-group text-capitalize">
				<?= generate_sub_headline('queued_email', 'fa-envelope', $rows['total']) ?>
			</div>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/reset/') ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#"
			   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('empty_queue') ?></span></a>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/flush/') ?>"
			   class="btn btn-primary <?= is_disabled('update', TRUE) ?>"><?= i('fa fa-refresh') ?> <span
					class="hidden-xs"><?= lang('flush_queue') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
	<div class="box-info mass-edit">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('date', 'send_date') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('recipient', 'recipient_email') ?></th>
					<th><?= tb_header('subject', 'subject') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td  style="width: 5%" class="text-center hidden-xs"><?= form_checkbox('id[' . $v['id'] . ']', $v['id']) ?></td>
						<td style="width: 12%" class="text-center hidden-xs"><?= display_date($v['send_date'], TRUE) ?></td>
						<td style="width: 15%" class="text-center hidden-xs"><h5><?= $v['recipient_name'] ?></h5><span
								class="label label-primary"><?= $v['primary_email'] ?></span></td>
						<td>
							<div class="overflow">
								<h5><?= $v['subject'] ?></h5>
								<small><?= word_limiter(strip_tags($v['html_body']), 50) ?></small>
							</div>
						</td>
						<td style="width: 15%" class="text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/view_email/' . $v['id']) ?>"
							   class="iframe btn btn-default"><?= i('fa fa-search') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['id']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr class=" hidden-xs">
					<td colspan="3">
						<?php if ($this->sec->check_admin_permissions(CONTROLLER_CLASS, 'delete') == TRUE): ?>
							<div class="input-group text-capitalize">
								<span class="input-group-addon"><small><?= lang('mark_checked_as') ?></small></span>
								<?= form_dropdown('change-status', options('email_queue', 'delete'), '', 'id="change-status" class="form-control"') ?>
								<span class="input-group-btn">
                        <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                                type="submit"><?= lang('save_changes') ?></button>
                    </span>
							</div>
						<?php endif; ?>
					</td>
					<td colspan="2">
						<div class="btn-group hidden-xs pull-right">
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
		$("#form").validate()
	</script>
<?php endif; ?>