<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('archived_reports', 'fa-bar-chart', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<a href="<?= admin_url(TBL_REPORTS . '/view') ?>"
			   class="btn btn-primary"><?= i('fa fa-search') ?>
				<span class="hidden-xs"><?= lang('view_reports') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info">
		<div class="hidden-xs">
			<div class="row text-capitalize">
				<div class="col-md-1 text-center"><?= tb_header('date', '', FALSE) ?></div>
				<div class="col-md-9"><?= tb_header('report_name', '', FALSE) ?></div>
				<div class="col-md-2"></div>
			</div>
			<hr/>
		</div>
		<?php foreach ($rows['values'] as $v): ?>
			<div class="row">
				<div class="r col-md-1 text-center">
					<h5><?=display_date($v['report_date'])?></h5>
				</div>
				<div class="r col-md-9">
					<h5><?=$v['report_name']?></h5>
				</div>
				<div class="r col-md-2 text-right">
					<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v['id']) ?>"
					   data-toggle="modal" data-target="#confirm-delete" href="#"
					   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
					<a href="<?= admin_url(CONTROLLER_CLASS . '/generate/' . $v['id']) ?>"
					   class="tip btn btn-primary block-phone"
					   data-toggle="tooltip" data-placement="bottom"
					   title="<?= lang('view') ?>"><?= i('fa fa-search') ?></a>
				</div>
			</div>
			<hr/>
		<?php endforeach; ?>
		<div class="row">
			<div class="col-md-5"></div>
			<div class="col-md-7 text-right">
				<div class="btn-group hidden-xs">
					<?php if (!empty($paginate['num_pages']) AND $paginate['num_pages'] > 1): ?>
						<button disabled
						        class="btn btn-default visible-lg"><?= $paginate['num_pages'] . ' ' . lang('total_pages') ?></button>
					<?php endif; ?>
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
		<?php endif; ?>
	</div>
<?php endif; ?>