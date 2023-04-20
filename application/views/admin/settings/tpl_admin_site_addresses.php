<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('site_addresses', 'fa-home', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>" class="btn btn-info <?= is_disabled('create', TRUE) ?>"><?= i('fa fa-plus') ?>
			<span class="hidden-xs"><?= lang('add_address') ?></span></a>
		<a href="<?= admin_url('settings') ?>"
		   class="btn btn-primary"><?= i('fa fa-cog') ?> <span class="hidden-xs"><?= lang('view_settings') ?></span></a>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<?php if (!empty($stores)): ?>
				<div class="row">
					<div class="col-md-6"><?= tb_header('address') ?></div>
					<div class="col-md-2 text-center"><?= tb_header('phone') ?></div>
					<div class="col-md-2 text-center"><?= tb_header('email') ?></div>
					<div class="col-md-2"></div>
				</div>
				<hr/>
				<?php foreach ($stores as $v): ?>
					<div class="row">
						<div class="col-md-6">
							<h5>
								<strong><?= $v['name'] . '</strong> - ' . $v['address_1'] . ' ' . $v['city'] . ' ' . $v['region_name'] . ' ' . $v['country_iso_code_3'] . ' ' . $v['postal_code'] ?>
							</h5>
						</div>
						<div class="col-md-2 text-center"><h5><?= $v['phone'] ?></h5></div>
						<div class="col-md-2 text-center"><h5><?= $v['email'] ?></h5></div>
						<div class="col-md-2 text-right">
							<?php if ($v['id'] != config_item('default_site_address')): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['id']) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
							<?php endif; ?>
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['id']) ?>" class="btn btn-default">
								<?= i('fa fa-pencil') ?></a>
						</div>
					</div>
					<hr/>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="alert alert-warning">
					<?= i('fa fa-info-circle') ?> <?= lang('no_addresses_found') ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

