<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-sm-8">
		<?= generate_sub_headline('support_categories', 'fa-folder', $rows[ 'total' ]) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-sm-4 text-right">
		<?= next_page('left', $paginate); ?>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('create_category') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_category') ?>
<?php else: ?>
	<div class="box-info">
		<div class="row hidden-xs">
			<div class="col-sm-1 text-center"><?= tb_header('sort', '', FALSE) ?></div>
			<div class="col-sm-9"><?= tb_header('category_name', 'category_name') ?></div>
			<div class="col-sm-2"></div>
		</div>
		<hr/>
		<div id="sortable">
			<?php foreach ($rows[ 'values' ] as $v): ?>
				<div class="ui-state-default" id="catid-<?= $v[ 'category_id' ] ?>">
					<div class="row">
						<div class="col-sm-1 text-center visible-lg visible-md">
							<span class="btn btn-primary handle <?= is_disabled('update', TRUE) ?>">
								<i class="fa fa-sort"></i></span>
						</div>
						<div class="col-sm-9">
							<h5>
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'category_id' ]) ?>"><?= $v[ 'category_name' ] ?></a>
							</h5>
							<small><?= $v[ 'category_description' ] ?></small>
						</div>
						<div class="col-sm-2 text-right">

							<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'category_id' ]) ?>"
							   class="btn btn-default block-phone" title="<?= lang('edit') ?>"><i
									class="fa fa-pencil"></i>
								<span class="visible-xs"><?= lang('edit') ?></span> </a>
							<?php if ($v[ 'category_id' ] != $default_support_category_id): ?>
								<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'category_id' ]) ?>"
								   data-toggle="modal" data-target="#confirm-delete" href="#"
								   class="md-trigger btn btn-danger block-phone  <?= is_disabled('delete') ?>"><i
										class="fa fa-trash-o"></i> <span class="visible-xs"><?= lang('delete') ?></span></a>
							<?php endif; ?>
						</div>
					</div>
					<hr/>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-9 text-right">
				<div class="btn-group hidden-xs">
					<?php if (!empty($paginate[ 'num_pages' ]) AND $paginate[ 'num_pages' ] > 1): ?>
						<button disabled
						        class="btn btn-default visible-lg"><?= $paginate[ 'num_pages' ] . ' ' . lang('total_pages') ?></button>
					<?php endif; ?>
					<button type="button" class="btn btn-primary dropdown-toggle"
					        data-toggle="dropdown"><?= i('fa fa-list') ?>
						<?= lang('select_rows_per_page') ?> <span class="caret"></span>
					</button>
					<?= $paginate[ 'select_rows' ] ?>
				</div>
			</div>
		</div>
		<?php if (!empty($paginate[ 'rows' ])): ?>
			<div class="text-center"><?= $paginate[ 'rows' ] ?></div>
			<div class="text-center">
				<small class="text-muted"><?= $paginate[ 'num_pages' ] ?> <?= lang('total_pages') ?></small>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
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