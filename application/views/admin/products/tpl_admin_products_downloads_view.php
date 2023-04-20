<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('download_files', 'fa-list', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('add_download_file') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'add_download_file') ?>
<?php else: ?>
	<div class="box-info">
		<div class="row text-capitalize hidden-xs hidden-sm">
			<div class="col-md-8"><?= tb_header('download_name', 'download_name') ?></div>
			<div class="col-md-2"><?= tb_header('file_name', 'file_name') ?></div>
			<div class="col-md-2"></div>
		</div>
		<hr/>
		<?php foreach ($rows['values'] as $v): ?>
			<div class="row">
				<div class="col-md-8">
					<span><?= $v['download_name'] ?></span> - <small class="text-muted"><?= $v['description'] ?></small>

				</div>
				<div class="col-md-2"><h6><?=$v['file_name']?></h6></div>
				<div class="col-md-2 text-right">
					<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['download_id']) ?>"
					   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
					<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['download_id']) ?>"
					   data-toggle="modal" data-target="#confirm-delete" href="#"
					   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
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
	</div>
<?php endif ?>