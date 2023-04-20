<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline(CONTROLLER_CLASS, ' fa-list', $rows['total']) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right"></div>
	</div>
	<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="box-info">
		<div class="row text-capitalize hidden-xs hidden-sm">
			<div class="col-md-10"><?= tb_header('page_name', '', '') ?></div>
			<div class="col-md-2"></div>
		</div>
		<hr/>
		<?php foreach ($rows['values'] as $v): ?>
			<div class="row">
				<div class="col-md-10">
					<h5><a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['page_id']) ?>">
							<?= $v['title'] ?></a></h5>
				</div>
				<div class="col-md-2 text-right">
					<a href="<?= site_url($v['url']) ?>" class="btn btn-default hidden-xs"
					   title="<?= lang('view_page') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
					<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v['page_id']) ?>"
					   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
				</div>
			</div>
			<hr/>
		<?php endforeach; ?>
	</div>
<?php endif ?>