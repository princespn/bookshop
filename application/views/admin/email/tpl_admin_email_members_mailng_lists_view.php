<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update', array('id' => 'form'))) ?>
<div class="row">
	<div class="col-md-5">
		<?= generate_sub_headline('subscribers', 'fa-users', $rows['total']) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-7 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(TBL_MEMBERS . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('add_contact') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(TBL_MEMBERS . '/create/client', 'add_user') ?>
<?php else: ?>
	<div class="box-info">
		<div>
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center"><?= tb_header('sequence', 'sequence') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('follow_up_name', 'follow_up_name') ?></th>
					<th><?= tb_header('email_address', 'email_address') ?></th>
					<th class="hidden-xs"><?= tb_header('next_send_date', 'send_date') ?></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows['values'] as $v): ?>
					<tr>
						<td style="width: 5%"  class="text-center">
							<?= form_dropdown('sort[' . $v['eml_id'] . ']', total_tiers($sequence + 1), $v['sequence_id'], 'class="form-control"') ?>
						</td>
						<td style="width: 20%" class="text-center hidden-xs">
							<?php if (empty($v['follow_up_name'])): ?>
								<span class="text-muted"><?=lang('no_follow_ups_scheduled')?></span>
							<?php else: ?>
								<?= heading($v['follow_up_name'], 5) ?>
							<?php endif; ?>
						</td>
						<td>
							<?= heading(mailto($v['email_address']), 5) ?>
							<span><a
									href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>"><?= $v['fname'] . ' ' . $v['lname'] ?></a></span>
						</td>
						<td  style="width: 20%"  class="hidden-xs">
							<?= heading(display_date($v['send_date'], TRUE), 5) ?>
						</td>
						<td style="width: 15%" class="text-right">
							<a data-href="<?= admin_url(TBL_EMAIL_MAILING_LISTS. '/delete_subscriber/' . $v['eml_id']) ?>/2/"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="3">
						<?php if ($this->sec->check_admin_permissions(CONTROLLER_CLASS, 'delete') == TRUE): ?>
							<button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
							        type="submit"><?= lang('save_changes') ?></button>
						<?php endif; ?>
					</td>
					<td colspan="2" class=" hidden-xs">
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
	<?php form_close() ?>
<?php endif; ?>
