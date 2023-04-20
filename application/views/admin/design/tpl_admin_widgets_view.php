<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'id="form"') ?>
<div class="row">
	<div class="col-md-8">
		<?= generate_sub_headline('widgets', 'fa-list', $rows[ 'total' ]) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
		<?php if (config_item('more_widgets_url')): ?>
            <a href="<?= $more_widgets_url ?>" class="btn btn-info"
               target="_blank"><?= i('fa fa-download') ?> <?= lang('get_more_widgets') ?></a>
		<?php endif; ?>
        <a href="<?= admin_url('updates/check') ?>"
           class="btn btn-default <?= is_disabled('create') ?>"><?= i('fa fa-upload') ?> <span
                    class="hidden-xs"><?= lang('import_widget') ?></span></a>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('create_widget') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<div class="col-lg-12">
	<?php if (empty($rows[ 'values' ])): ?>
		<?= tpl_no_values() ?>
	<?php else: ?>
		<div class="row">
			<div class="gallery-wrap">
				<?php foreach ($rows[ 'values' ] as $v): ?>
					<div class="col-lg-3">
						<div class="thumbnail">
							<div class="gallery-item">
								<?php if (!empty($v[ 'thumbnail' ])): ?>
									<img src="<?= base_folder_path(SITE_BUILDER . '/assets/designs/preview/' . $v[ 'thumbnail' ]) ?>"
									     class="img-responsive img-center widget-thumbnail"/>
								<?php endif; ?>

								<div class="img-title">
									<h5>
										<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'widget_id' ]) ?>"
										   class="btn btn-default block-phone"
										   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
											<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'widget_id' ]) ?>"
											   data-toggle="modal" data-target="#confirm-delete" href="#"
											   class="md-trigger btn btn-danger block-phone <?= is_disabled('delete') ?>">
												<?= i('fa fa-trash-o') ?>
											</a>

									</h5>
								</div>
							</div>
							<div class="caption text-center">
								<strong><a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'widget_id' ]) ?>"
								           class="btn btn-default btn-block" data-toggle="popover" data-placement="bottom"
								           data-trigger="hover" data-content="<?=$v['description']?>"><?= ($v[ 'widget_name' ]) ?></a></strong>
							</div>
						</div>


					</div>
				<?php endforeach; ?>
			</div>
			<hr/>
		</div>
	<?php endif; ?>
</div>
<?= form_close(); ?>
<script>
	$(document).ready(function () {
		$('.gallery-item').hover(function () {
			$(this).find('.img-title').fadeIn(300);
		}, function () {
			$(this).find('.img-title').fadeOut(100);
		});
	});
</script>