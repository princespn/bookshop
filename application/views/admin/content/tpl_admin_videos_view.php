<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline(CONTROLLER_CLASS, ' fa-video-camera', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
			   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
					class="hidden-xs"><?= lang('add_video') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'add_video') ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="box-info">
		<div class="row text-capitalize hidden-xs hidden-sm">
			<div class="col-md-1 text-center"><?= tb_header('sort', '', FALSE) ?></div>
			<div class="col-md-9"><?= tb_header('video_name', 'video_name') ?></div>
			<div class="col-md-2"></div>
		</div>
		<hr/>
		<div id="sortable">
			<?php foreach ($rows['values'] as $v): ?>
				<div class="ui-state-default" id="video_id-<?= $v['video_id'] ?>">
					<div class="row">
						<div class="col-md-1 text-center visible-lg visible-md">
							<span class="btn btn-primary handle <?= is_disabled('update', TRUE) ?>">
								<i class="fa fa-sort"></i></span>
						</div>
						<div class="col-md-9">
							<h5 class="handle cursor">
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['video_id']) ?>">
									<?= $v['video_name'] ?></a>
							</h5>
							<small class="text-muted"><?= $v['description'] ?></small>
						</div>
						<div class="col-md-2 text-right">
							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['video_id']) ?>"
							   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
							<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['video_id']) ?>"
							   data-toggle="modal" data-target="#confirm-delete" href="#"
							   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
						</div>
					</div>
					<hr/>
				</div>
			<?php endforeach; ?>
		</div>
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
	<?= form_close() ?>
	<div id="update"></div>
	<script>
		$(function () {
			$('#sortable').sortable({
				handle: '.handle',
				placeholder: "ui-state-highlight",
				update: function () {
					var order = $('#sortable').sortable('serialize');
					console.log(order);
					$("#update").load("<?=admin_url(CONTROLLER_CLASS . '/update_order/')?>?" + order);
				}
			});
		});

	</script>
<?php endif ?>