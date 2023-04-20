<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
<div class="row">
	<div class="col-md-4">
		<div class="input-group text-capitalize">
			<?= generate_sub_headline(lang('template_manager'), 'fa-list', '', FALSE) ?>
		</div>
	</div>
	<div class="col-md-8">
    </div>
</div>
<br />
<div class="box-info min-pad-bottom">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
				<?=i('fa fa-exclamation-circle')?> <strong><?=lang('warning')?></strong> <?=lang('editing_templates_warning')?>
            </div>
        </div>
    </div>
	<ul class="nav nav-tabs text-capitalize">
		<?php $i = 0; ?>
		<?php foreach ($rows as $k => $v): ?>
			<?php if ($k != 'js' && $k != 'rss'): ?>
				<li <?php if ($i == '0'): ?> class="active" <?php endif; ?>>
					<a href="#<?= $k ?>-tab" role="tab" data-toggle="tab"><?= lang($k) ?></a>
				</li>
				<?php $i++; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<li>
			<a href="#modules" role="tab" data-toggle="tab"><?= lang('modules') ?></a>
		</li>
	</ul>
	<?php $i = 0; ?>
	<div class="tab-content">
		<?php foreach ($rows as $k => $v): ?>
			<?php if ($k != 'js' && $k != 'rss'): ?>
				<div id="<?= $k ?>-tab" class="tab-pane <?php if ($i == '0'): ?>active<?php endif; ?>">
					<hr/>
					<?php foreach ($v as $a): ?>
                        <?php if (in_array($a, $templates_not_for_editing)) { continue; } ?>
						<div class="row">
							<div class="col-lg-11">
								<h5><a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $k . '/' . $a) ?>"><?= $a ?></a> -
									<small><?= lang('tpl_desc_' . str_replace('.tpl', '', $a)) ?></small>
								</h5>
							</div>
							<div class="col-lg-1 text-right">
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $k . '/' . $a) ?>"
								   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
								   title="<?= lang('code_view') ?>"><?= i('fa fa-file-code-o') ?></a>
							</div>
						</div>
						<hr/>
						<?php $i++; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		<div id="modules" class="tab-pane">
			<hr/>
			<?php $i = 0; ?>
			<ul class="nav nav-tabs text-capitalize">
				<?php foreach ($module_rows['path'] as $m => $n): ?>
					<li <?php if ($i == '0'): ?> class="active" <?php endif; ?>>
						<a href="#<?= $m ?>" role="tab" data-toggle="tab"><?= lang($m) ?></a>
					</li>
					<?php $i++ ?>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content">
				<?php $i = 0; ?>
				<?php foreach ($module_rows['path'] as $m => $n): ?>
				<div id="<?= $m ?>" class="tab-pane <?php if ($i == '0'): ?>active<?php endif; ?>">
					<hr/>
					<?php foreach ($n as $r => $s): ?>
						<?php if (in_array($module_rows['name'][$m][$r], $templates_not_for_editing)) { continue; } ?>
						<div class="row">
							<div class="col-lg-11">
								<h5><a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $m . '/' . $s) ?>"><?= $module_rows['name'][$m][$r]?></a>
								</h5>
							</div>
							<div class="col-lg-1 text-right">
								<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $m . '/' . $s) ?>"
								   class="tip btn btn-default" data-toggle="tooltip" data-placement="bottom"
								   title="<?= lang('code_view') ?>"><?= i('fa fa-file-code-o') ?></a>
							</div>
						</div>
						<hr/>
					<?php endforeach; ?>
				</div>
					<?php $i++ ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<br />