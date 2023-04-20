<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<?= generate_sub_headline('google_site_map', 'fa-site_map') ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-4 text-right">
		<?php if (!empty($response)):  ?>
			<a href="javascript:history.go(-1)" class="btn btn-danger"><?=i('fa fa-undo')?> <?=lang('go_back')?></a>
		<?php else: ?>
		<a data-href="<?= admin_url(CONTROLLER_CLASS . '/view/notify') ?>"
		   data-toggle="modal" data-target="#notify" href="#"
		   class="md-trigger btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-upload') ?>
			<span class="hidden-xs"><?= lang('submit_to_google') ?></span>
		</a>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info site-map">
	<?php if (!empty($response)): ?>
		<h3><?=lang('submission_notifications')?></h3>
		<hr />
		<?php foreach ($response as $v): ?>
			<div class="row">
				<div class="col-md-12">
					<small><?= $v['url'] ?></small>
					<p><?= $v['response'] ?></p>
				</div>
			</div>
			<hr />
		<?php endforeach; ?>
	<?php else : ?>
		<div class="row text-capitalize hidden-xs hidden-sm">
			<div class="col-md-10"><h3><?= lang('xml_site_maps') ?></h3>
				<small><?= lang('site_maps_description') ?></small>
			</div>
			<div class="col-md-2"></div>
		</div>
		<hr/>
		<?php foreach ($site_maps as $v): ?>
			<div class="row">
			<?php if ($v == 'site_map_index'): ?>
				<div class="col-md-10">
					<h5><?= site_url('site_map/' . $v . '/site_map.xml') ?></h5>
					<small class="text-muted"><?= lang('site_map_' . $v . '_description') ?></small>
				</div>
				<div class="col-md-2 text-right">
					<a href="<?= site_url('site_map/' . $v . '/site_map.xml') ?>" class="btn btn-default"
					   target="_blank"><?= i('fa fa-external-link') ?></a>
				</div>
				<?php else: ?>
				<div class="col-md-10">
					<h5><?= site_url('site_map/id/' . $v . '.xml') ?></h5>
					<small class="text-muted"><?= lang('site_map_' . $v . '_description') ?></small>
				</div>
				<div class="col-md-2 text-right">
					<a href="<?= site_url('site_map/id/' . $v . '.xml') ?>" class="btn btn-default"
					   target="_blank"><?= i('fa fa-external-link') ?></a>
				</div>
				<?php endif; ?>
			</div>
			<hr/>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-labelledby="confirmLabel"
     aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body capitalize">
				<h3><?= i('fa fa-external-link') ?> <?= lang('confirm_submission') ?></h3>
				<?= lang('submit_site_maps_to_google') ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('cancel') ?></button>
				<a href="<?= admin_url(CONTROLLER_CLASS . '/view/notify') ?>"
				   class="btn btn-info"><?= lang('proceed') ?></a>
			</div>
		</div>
	</div>
</div>
