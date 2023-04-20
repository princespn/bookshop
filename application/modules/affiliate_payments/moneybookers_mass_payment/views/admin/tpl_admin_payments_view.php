<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/generate_mass_payment'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline($row[ 'module' ][ 'module_name' ], 'fa-file-text-o') ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>" <?= is_disabled('create') ?>
			   class="btn btn-primary"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('create_commission') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info">
		<div class="<?= mobile_view('hidden-xs') ?>">
			<table class="table table-striped table-hover">
				<thead class="text-capitalize">
				<tr>
					<th class="text-center hidden-xs"><?= form_checkbox('', '', '', 'class="check-all"') ?></th>
					<th class="text-center hidden-xs"><?= tb_header('name', '', FALSE) ?></th>
					<th class="text-center"><?= tb_header('payment_name', '', FALSE) ?></th>
					<th class="text-center hidden-xs"><?= tb_header('skrill_id', '', FALSE) ?></th>
					<th class="text-center hidden-xs"><?= tb_header('minimum', '', FALSE) ?></th>
					<th class="text-center hidden-xs"><?= tb_header('commissions', '', FALSE) ?></th>
					<th class="text-center hidden-xs"><?= tb_header('amount', '', FALSE) ?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<?php if ($v[ 'total_amount' ] < config_option('sts_affiliate_min_payment_amount')): ?>
						<?php if (config_enabled('module_affiliate_payments_moneybookers_mass_payment_exclude_minimum')): ?>
							<?php continue; ?>
						<?php endif; ?>
					<?php endif; ?>
					<tr>
						<td class="text-center">
							<?= form_checkbox('select[]', $v[ 'member_id' ]) ?>
						</td>
						<td class="text-center">
							<a href="<?=admin_url(TBL_MEMBERS . '/update/' . $v['member_id'])?>"><?= $v[ 'member_fname' ] ?> <?= $v[ 'member_lname' ] ?></a>
							<?= form_hidden('member[' . $v[ 'member_id' ] . '][member_name]', $v[ 'member_fname' ] . ' ' . $v['member_lname']) ?>
						</td>
						<td class="text-center">
							<?= $v[ 'fname' ] ?> <?= $v[ 'lname' ] ?>
							<?= form_hidden('member[' . $v[ 'member_id' ] . '][payment_name]', $v[ 'fname' ] . ' ' . $v['lname']) ?>
						</td>
						<td class="text-center">
							<?= $v[ 'skrill_id' ] ?>
							<?= form_hidden('member[' . $v[ 'member_id' ] . '][skrill_id]', $v[ 'skrill_id' ]) ?>
						</td>
						<td class="text-center">
							<?php if ($v[ 'payment_preference_amount' ] > 0): ?>
								<?= format_amount($v[ 'payment_preference_amount' ], FALSE) ?>
							<?php else: ?>
								<?= format_amount(config_option('sts_affiliate_min_payment_amount'), FALSE) ?>
							<?php endif; ?>
						</td>
						<td class="text-center">
							<?= $v[ 'total_commissions' ] ?>
							<?= form_hidden('member[' . $v[ 'member_id' ] . '][total_commissions]', $v[ 'total_commissions' ]) ?>
						</td>
						<td class="text-center">
							<?php if (defined('AFFILIATE_MARKETING_CHARGE_FEES')): ?>
								<?php $v['total_amount'] -= $v['fee']; ?>
							<?php endif; ?>
							<?= format_amount($v[ 'total_amount' ], FALSE) ?> <?=config_option('module_affiliate_payments_moneybookers_mass_payment_currency')?>
							<?= form_hidden('member[' . $v[ 'member_id' ] . '][total_amount]', $v[ 'total_amount' ]) ?>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="7">
						<div class="input-group text-capitalize">
							<span class="input-group-addon">
								<?= form_checkbox('', '', '', 'class="check-all"') ?>
								<small><?= lang('mark_checked_as') ?></small></span>
							<select name="payment_type" class="form-control">
								<option value="generate_file"><?=lang('include_check_in_mass_payment')?></option>
								<option value="<?=$row['module']['module_name']?>"><?=lang('mark_as_paid')?></option>
							</select> <span class="input-group-btn">
							<span class="input-group-btn">
                                <button class="btn btn-primary <?= is_disabled('update') ?>" type="submit">
	                                <?= lang('generate') ?>
                                </button>
                            </span>
						</div>
					</td>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?=form_hidden('module_id', $id)?>
	<?= form_close() ?>
	<br/>
	<!-- Load JS for Page -->
	<script>
		$("#form").validate();
	</script>
<?php endif ?>