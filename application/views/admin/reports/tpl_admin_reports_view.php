<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('admin_reports', 'fa-bar-chart', count($rows)) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<a href="<?= admin_url(TBL_MODULES . '/view?module_type=admin_reporting') ?>"
			   class="btn btn-primary"><?= i('fa fa-search') ?>
				<span class="hidden-xs"><?= lang('view_modules') ?></span></a>
		</div>
	</div>
	<hr/>
<?php if (empty($rows)): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="row">
		<?php foreach ($rows as $v): ?>
				<div class="r col-lg-4 col-md-6">
                    <div class="box-info">
					<h5 class="text-capitalize">
                        <div class="pull-right">
	                        <?php if (!empty($v['settings'])): ?>
                                <strong class="text-capitalize"><a
                                            href="<?= admin_url(CONTROLLER_CLASS . '/settings/' . $v['module_id']) ?>"
                                            class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
                                            title="<?= lang('settings') ?>"><?= i('fa fa-cogs') ?></a></strong>
	                        <?php endif; ?>
                        </div>
                        <a href="<?= admin_url(CONTROLLER_CLASS . '/generate/' . $v['module_id']) ?>"><?=i('fa fa-file-text-o')?> <?= check_desc($v['module_name']) ?></a>
					</h5>
					<small><?= check_desc($v['module_description']) ?></small>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>