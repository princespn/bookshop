<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-file-text-o') ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<a data-href="<?=admin_url(TBL_AFFILIATE_COMMISSIONS . '/approve_commissions')?>" data-toggle="modal" data-target="#confirm-approval" href="#" class="md-trigger btn btn-info"><?=i('fa fa-refresh')?> <span class="hidden-xs"><?=lang('approve_pending_commissions')?></span></a>
			<a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/view') ?>"
			   class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_commissions') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows)): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="content">
		<div class="items">
		<?php foreach ($rows as $v): ?>
			<div class="item col-md-4 r">
				<div class="box-info">
					<div class="row">
						<div class="col-md-3">
							<?= photo(CONTROLLER_METHOD, $v, 'img-responsive') ?>
						</div>
						<div class="col-md-9">
							<h5 class="text-capitalize"><?= $v[ 'module_name' ] ?></h5>
							<span class="text-muted"><?= check_desc($v[ 'module_description' ]) ?></span>
							<hr/>
							<div class="r text-right">
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'module_id' ]) ?>"
								   class="btn btn-primary block-phone"><?=lang('get_started')?> <?= i('fa fa-caret-right') ?></a>

							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>

<div class="modal fade" id="confirm-approval" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="text-capitalize"><?=i('fa fa-info-circle')?> <?=lang('approve_all_pending_commissions')?></h3>
			</div>
			<div class="modal-body capitalize">
				<p class="text-capitalize"><?=lang('approve_all_pending_commissions_description')?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('cancel')?></button>
				<a href="<?=admin_url(TBL_AFFILIATE_COMMISSIONS . '/approve_commissions')?>" class="btn btn-primary">
					<?=i('fa fa-caret-right')?> <?=lang('proceed')?></a>
			</div>
		</div>
	</div>
</div>
