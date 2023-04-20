<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline(lang('link_tracker'), 'fa-file-text-o', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>" <?= is_disabled('create') ?>
			   class="btn btn-primary"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_tracker') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_tracker') ?>
<?php else: ?>
	<div class="box-info">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
		<div class="row text-capitalize hidden-xs hidden-sm">
			<div class="col-md-1 text-center"><?= tb_header('status', 'status') ?></div>
			<div class="col-md-6"><?= tb_header('name', 'name') ?></div>
			<div class="col-md-1 text-center"><?= tb_header('clicks', 'total') ?></div>
			<div class="col-md-1 text-center"><?= tb_header('member', 'username') ?></div>
			<div class="col-md-1 text-center"><?= tb_header('expires_on', 'end_date') ?></div>
			<div class="col-md-2"></div>
		</div>
		<hr/>
		<?php foreach ($rows['values'] as $v): ?>
			<div class="row">
				<div class="col-md-1 text-center hidden-xs hidden-sm">
					<a
						href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/tracking_id/id/' . $v['tracking_id']) ?>"
						class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
				</div>
				<div class="col-md-6">
					<span>
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['tracking_id']) ?>">
							<?=$v['name']?></a>
					</span><br />
					<small class="text-muted"><?=anchor(site_url('t/' . $v['tracking_id']), '', 'target="_blank"')?></small>
				</div>
				<div class="col-md-1 text-center"><h5><?= $v['total'] ?></h5></div>
				<div class="col-md-1  text-center hidden-xs hidden-sm">
					<h5>
					<?php if (!empty($v['username'])):  ?>
						<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>"><?= $v['username'] ?></a>
					<?php else: ?>
						<?=lang('none')?>
					<?php endif; ?>
					</h5>
				</div>
				<div class="col-md-1  text-center hidden-xs hidden-sm">
					<h5><?= display_date($v['end_date']) ?></h5>
				</div>
				<div class="col-md-2 text-right">
					<a href="<?= admin_url(TBL_TRACKING_REFERRALS . '/view/?tracking_id=' . $v['tracking_id']) ?>"
					   class="btn btn-info block-phone" title="<?= lang('url_referrals') ?>"><?= i('fa fa-search') ?> </a>
					<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['tracking_id']) ?>"
					   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> </a>
					<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['tracking_id']) ?>"
					   data-toggle="modal" data-target="#confirm-delete" href="#"
					   class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
				</div>
			</div>
			<hr/>
		<?php endforeach; ?>
		<div class="row">
			<div class="col-md-6"></div>
			<div class="col-md-6 text-right">
				<div class="btn-group hidden-xs">
					<button type="button" class="btn btn-primary dropdown-toggle"
					        data-toggle="dropdown"><?= i('fa fa-list') ?>
						<?= lang('select_rows_per_page') ?> <span class="caret"></span>
					</button>
					<?= $paginate['select_rows'] ?>
				</div>
			</div>
		</div>
		<?php if (!empty($paginate['rows'])): ?>
			<div class="text-center"><?= $paginate['rows'] ?></div>
			<div class="text-center">
				<small class="text-muted"><?= $paginate['num_pages'] ?> <?= lang('total_pages') ?></small>
			</div>
		<?php endif; ?>
		<?= form_close() ?>
	</div>
<?php endif; ?>