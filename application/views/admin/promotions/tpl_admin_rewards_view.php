<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline(lang(CONTROLLER_CLASS), 'fa-file-text-o', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/history') ?>"
			   class="btn btn-info"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('rewards_history') ?></span></a>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>" <?= is_disabled('create') ?>
			   class="btn btn-primary"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_reward') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_reward') ?>
<?php else: ?>
	<div class="box-info">
		<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
		<div class="row text-capitalize hidden-xs hidden-sm">
			<div class="col-md-1 text-center"><h5><?= lang('priority') ?></h5></div>
			<div class="col-md-1 text-center"><h5><?= lang('status') ?></h5></div>
			<div class="col-md-5"><h5><?= lang('reward_type') ?></h5></div>
			<div class="col-md-1 text-center"><h5><?= lang('points') ?></h5></div>
			<div class="col-md-1 text-center"><h5><?= lang('starts_on') ?></h5></div>
			<div class="col-md-1 text-center"><h5><?= lang('expires_on') ?></h5></div>
			<div class="col-md-2"></div>
		</div>
		<hr/>
		<?php foreach ($rows['values'] as $v): ?>
			<div class="row">
				<div class="col-md-1 hidden-xs hidden-sm">
					<?= form_dropdown('rules[' . $v['rule_id'] . ']', total_tiers($rows[ 'total' ]), $v['sort_order'], 'class="form-control"') ?>
				</div>
				<div class="col-md-1 text-center hidden-xs hidden-sm">
					<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/rule_id/id/' . $v['rule_id']) ?>"
						class="btn btn-default <?= is_disabled('update', TRUE) ?>"><?= set_status($v['status']) ?></a>
				</div>
				<div class="col-md-5">
					<h5>
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['rule_id']) ?>">
							<?= lang($v['rule']) ?></a>
					</h5>
					<small><?=$v['description']?></small>
				</div>
				<div class="col-md-1 text-center hidden-xs hidden-sm">
					<h5><?= $v['points'] ?></h5>
				</div>
				<div class="col-md-1  text-center hidden-xs hidden-sm">
					<h5>
						<?php if ($v['rule'] == 'reward_user_birthday'): ?>
							<?= lang('NA') ?>
						<?php else: ?>
							<?= display_date($v['start_date']) ?>
						<?php endif; ?>
					</h5>
				</div>
				<div class="col-md-1  text-center hidden-xs hidden-sm">
					<h5>
						<?php if ($v['rule'] == 'reward_user_birthday'): ?>
							<?= lang('NA') ?>
						<?php else: ?>
							<?= display_date($v['end_date']) ?>
						<?php endif; ?>
					</h5>
				</div>
				<div class="col-md-2 text-right">
					<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['rule_id']) ?>"
					   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?> </a>
					<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['rule_id']) ?>"
					   data-toggle="modal" data-target="#confirm-delete" href="#"
					   class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
				</div>
			</div>
			<hr/>
		<?php endforeach; ?>
		<div class="row">
			<div class="col-md-6">
				<button class="btn btn-primary block-phone <?= is_disabled('update') ?>" type="submit">
					<?= lang('save_changes') ?>
				</button>
			</div>
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