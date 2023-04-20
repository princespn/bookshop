<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update', array('id' => 'form'))) ?>
<div class="row">
	<div class="col-md-5">
		<?= generate_sub_headline(lang('member_credits'), 'fa-users', '', FALSE) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-7 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('add_credit') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info valign">
		<div class="row hidden-xs text-center">
			<div class="col-sm-2"><?= tb_header('date', 'date') ?></div>
			<div class="col-sm-3"><?= tb_header('name', 'fname') ?></div>
			<div class="col-sm-3"><?= tb_header('transaction_id', 'transaction_id') ?></div>
            <div class="col-sm-2"><?= tb_header('amount', 'amount') ?></div>
			<div class="col-sm-2"></div>
		</div>
		<hr class="hidden-xs"/>
		<?php foreach ($rows['values'] as $v): ?>
			<div class="hover">
				<div class="row text-center">
                    <div class="col-sm-2"><?= display_date($v['date']) ?></div>
                    <div class="col-sm-3">
                        <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
                        <?= $v['fname'] ?> <?=$v['lname']?>
                        </a>
                    </div>
                    <div class="col-sm-3"><?= $v['transaction_id'] ?></div>
                    <div class="col-sm-2"><?= format_amount($v['amount']) ?></div>
					<div class="col-sm-2 text-right">
						<a href="<?= admin_url(TBL_MEMBERS_CREDITS . '/update/' . $v['mcr_id']) ?>" class="btn btn-default"
						   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
						<a data-href="<?= admin_url(TBL_MEMBERS_CREDITS . '/delete/' . $v['mcr_id']) ?>"
						   data-toggle="modal" data-target="#confirm-delete" href="#"
						   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
					</div>
				</div>
				<hr/>
			</div>
		<?php endforeach; ?>
		<div class="row">
			<div class="col-sm-6">
			</div>
			<div class="col-sm-6 text-right">
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
	</div>
	<?php form_close() ?>
<?php endif; ?>