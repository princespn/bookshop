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
				<?= lang('monthly_top_reports') ?>
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
				<?php if (empty($report['rows'])): ?>
					<?= tpl_no_values('', '', 'no_data_found') ?>
				<?php else: ?>
					<div class="row">
						<div class="col-lg-7">
							<div class="box-info">
								<div id="chart_report"></div>
							</div>

						</div>
						<div class="col-lg-5">
							<div class="box-info">
								<div class="row">
									<div class="col-md-12">
										<h3><?= lang('affiliates') ?></h3>
									</div>
								</div>
								<hr/>
								<?php foreach ($report['data'] as $k => $v): ?>
									<div class="row">
										<div class="col-md-1">
											<?php if (!empty($v['profile_photo'])): ?>
												<?= img($v['profile_photo'], $v['name'], array('class' => 'img-thumbnail img-circle img-responsive')); ?>
											<?php else: ?>
												<?= img(TPL_DEFAULT_ADMIN_PHOTO, $v['name'], array('class' => 'img-thumbnail img-circle img-responsive')); ?>
											<?php endif; ?>
										</div>
										<div class="col-md-9">
											<h5>
												<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>"><?= $v['fname'] ?> <?= $v['lname'] ?></a>
												-
												<small class="text-muted"><?= $v['name'] ?></small>
											</h5>

										</div>
										<div class="col-md-2 text-center">
											<h5>
												<?php if (!empty($report['currency'])): ?>
													<?= format_amount($v['amount']) ?>
												<?php else: ?>
													<?= (int)$v['amount'] ?>
												<?php endif; ?>
											</h5>
										</div>
									</div>
									<hr/>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<hr/>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<script src="<?= base_url('js/highcharts/highcharts.js') ?>"></script>
	<?php if (!empty($report['chart'])): ?>
		<script><?= $report['chart'] ?></script>
	<?php endif; ?>
<?php endif; ?>
