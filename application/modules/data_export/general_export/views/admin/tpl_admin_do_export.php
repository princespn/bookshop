<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang('export_file'), 'fa-download', '', FALSE) ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary">
			<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_export_options') ?></span></a>
	</div>
</div>
<hr/>

<div class="row" id="fields">
	<div class="col-md-12">
		<div class="box-info">
			<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
			<h3><?= lang('download_file') ?></h3>
			<span><?= lang('generate_export_file_description') ?></span>
			<hr/>
			<a href="javascript:history.go(-1)" class="btn btn-default"><i class="fa fa-undo "></i> Go Back</a>
			<button class="btn btn-info navbar-btn block-phone"
			        id="update-button" <?= is_disabled('update', TRUE) ?>
			        type="submit"> <?= i('fa fa-download') ?> <?= lang('click_here_to_generate_file') ?></button>
			<hr/>
			<?= form_hidden('module_id', $id) ?>
			<?= form_close() ?>
		</div>
	</div>
</div>