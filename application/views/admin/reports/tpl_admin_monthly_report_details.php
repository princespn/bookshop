<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values('', '', 'no_records_found') ?>
<?php else: ?>
	<div class="row">
		<div class="col-md-5">
			<h2 class="sub-header block-title"><?= i('fa fa-file-text-o') ?>
				<?php if (uri(5) == 'archive'): ?>
					<?=lang('archived_report')?>
				<?php else: ?>
					<?= lang('monthly_reports') ?>
				<?php endif; ?>
			</h2>
		</div>
		<div class="col-md-7 text-right">
			<?php if (empty($no_archive)): ?>
				<a href="<?= site_url($this->uri->uri_string()) ?>/archive"
				   class="btn btn-primary"><?= i('fa fa-save') ?>
					<span class="hidden-xs"><?= lang('archive_report') ?></span></a>
			<?php endif; ?>
			<a href="<?= admin_url(TBL_REPORTS) ?>" class="btn btn-primary">
				<?= i('fa fa-search') ?> <span class="hidden-xs"><?= lang('view_reports') ?></span></a>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-lg-12">
			<div class="box-info">
				<div class="row">
					<div class="col-md-9"><h3
							class="visible-md visible-lg text-capitalize"> <?= lang($report['title']) ?></h3></div>
					<div class="col-md-3">
						<?php if (uri(5) != 'archive'): ?>
						<?php if (!empty($report['dates'])): ?>
							<span class="pull-right"><?= $report['dates'] ?></span>
						<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<hr/>
				<div id="chart_report"></div>
				<hr/>
				<?php if (!empty($report['rows'])): ?>
					<div class="text-right">
						<a class="btn btn-info show-table">
							<?= i('fa fa-search') ?> <span
								class="show-text"> <?= lang('show') ?></span> <?= lang('daily_stats') ?>
						</a>
					</div>
					<br/>
					<div class="collapse" id="table-stats">
						<table id="reports-table" class="table table-striped table-hover">
							<thead>
							<tr>
								<th><?= lang('date') ?></th>
								<th class="text-center"><?= lang('amount') ?></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($report['rows'] as $k => $v): ?>
								<tr>
									<td><?= current_date('M') ?> <?= $k ?> <?= current_date('Y') ?></td>
									<td class="text-center" style="width: 20%">
										<?php if (!empty($report['currency'])): ?>
											<?= format_amount($v) ?>
										<?php else: ?>
											<?= $v ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
							<tfoot>
							<tr>
								<td colspan="2"></td>
							</tr>
							</tfoot>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<script src="<?= base_url('js/highcharts/highcharts.js') ?>"></script>
	<script>
		$('a.show-table').click(function () {
			$('#table-stats').collapse('toggle');
		});
		$('#table-stats').on('hidden.bs.collapse', function () {
			$('.show-text').html('<?=lang('show')?>');
		});
		$('#table-stats').on('shown.bs.collapse', function () {
			$('.show-text').html('<?=lang('hide')?>');
		});
	</script>
	<?php if (!empty($report['chart'])): ?>
		<script><?= $report['chart'] ?></script>
	<?php endif; ?>
<?php endif; ?>
