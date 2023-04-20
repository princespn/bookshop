<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-6">
		<?= generate_sub_headline($sub_headline, 'fa-upload', '') ?>
	</div>
	<div class="col-md-6 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_import_options') ?></span></a>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<h3><?= $title ?></h3>
			<hr/>
			<?php if (!empty($results)): ?>
				<div class="alert alert-success text-success">
					<?= $results['total'] ?> <?= lang('imported_successfully') ?></div>
				<hr/>
				<?php if (!empty($results['error'])): ?>
					<h3><?= lang('import_errors') ?></h3>
					<hr/>
					<?= $results['error'] ?>
				<?php endif; ?>
			<?php else: ?>
				<?= tpl_no_values('', '', 'no_data_found') ?>
			<?php endif; ?>
		</div>
	</div>
</div>
