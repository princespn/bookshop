<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="col-md-3">
	<div class="list-group-item">
		<a class="pull-right additional-icon" href="#" data-toggle="collapse" data-target="#box-1"><i
				class="fa fa-chevron-down"></i></a>
		<strong><?= i('fa fa-cog') ?> <?= lang('site_settings') ?></strong>
	</div>
	<div id="box-1" class="collapse in">
		<a href="<?= admin_url(TBL_MODULES . '/view/') ?>" class="list-group-item">
			<span class="pull-right fa-stack fa-lg">
				<?=i('fa fa-circle fa-stack-2x text-info')?>
				<?=i('fa fa-cogs fa-stack-1x fa-inverse')?>
			</span>
			<h4 class="list-group-item-heading"><?= lang('manage_modules') ?></h4>
			<p class="list-group-item-text"><?= lang('manage_modules_description') ?></p>
		</a>
		<a href="<?= admin_url('payment_gateways/view/') ?>" class="list-group-item">
			<span class="pull-right fa-stack fa-lg">
				<?=i('fa fa-circle fa-stack-2x text-info')?>
				<?=i('fa fa-credit-card fa-stack-1x fa-inverse')?>
			</span>
			<h4 class="list-group-item-heading"><?= lang('payment_options') ?></h4>
			<p class="list-group-item-text"><?= lang('payment_options_description') ?></p>
		</a>
		<a href="<?= admin_url(TBL_ADMIN_USERS . '/view/') ?>" class="list-group-item">
			<span class="pull-right fa-stack fa-lg">
				<?=i('fa fa-circle fa-stack-2x text-info')?>
				<?=i('fa fa-users fa-stack-1x fa-inverse')?>
			</span>
			<h4 class="list-group-item-heading"><?= lang('manage_admins') ?></h4>
			<p class="list-group-item-text"><?= lang('manage_admins_description') ?></p>
		</a>
        <a href="<?= admin_url('utilities/view') ?>" class="list-group-item">
			<span class="pull-right fa-stack fa-lg">
				<?=i('fa fa-circle fa-stack-2x text-info')?>
				<?=i('fa fa-cog fa-stack-1x fa-inverse')?>
			</span>
            <h4 class="list-group-item-heading"><?= lang('system_utilities') ?></h4>
            <p class="list-group-item-text"><?= lang('system_utilities_description') ?></p>
        </a>
		<a href="<?= admin_url('license/update/') ?>" class="list-group-item">
			<span class="pull-right fa-stack fa-lg">
				<?=i('fa fa-circle fa-stack-2x text-info')?>
				<?=i('fa fa-key fa-stack-1x fa-inverse')?>
			</span>
			<h4 class="list-group-item-heading"><?= lang('view_license') ?></h4>
			<p class="list-group-item-text"><?= lang('view_license_description') ?></p>
		</a>
	</div>
</div>