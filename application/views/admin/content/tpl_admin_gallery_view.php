<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
		<?= generate_sub_headline(lang('image_gallery'), 'fa-photo', '', FALSE) ?>
        <hr class="visible-xs"/>
    </div>
    <div class="col-md-4 text-right">
		<?= next_page('left', $paginate); ?>
        <a href="<?= admin_url('update_status/settings/sts_content_enable_gallery/') ?>"
           class="btn btn-info <?= is_disabled('create') ?>">
		    <?php if (config_enabled('sts_content_enable_gallery')): ?>
			    <?= i('fa fa-info-circle') ?>
                <span class="hidden-xs"><?= lang('deactivate_gallery') ?></span>
		    <?php else: ?>
			    <?= i('fa fa-info-circle') ?>
                <span class="hidden-xs"><?= lang('activate_gallery') ?></span>

		    <?php endif; ?>
        </a>
        <a href="<?= site_url('gallery?preview=1') ?>" target="_blank"
           class="btn btn-default"><?= i('fa fa-external-link') ?> <span
                    class="hidden-xs"><?= lang('view_gallery') ?></span></a>
        <a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
           class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
                    class="hidden-xs"><?= lang('create_gallery_photo') ?></span></a>
		<?= next_page('right', $paginate); ?>
    </div>
</div>
<hr/>
<?php if (empty($rows['values'])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_gallery_photo') ?>
<?php else: ?>
    <div class="box-info">
        <h3 class="text-capitalize"><?= lang('photos') ?></h3>
        <span class="text-capitalize"><?= lang('gallery_photos_description') ?>. <?=lang('drag_drop_rearrange')?></span>
        <hr/>
		<?= form_open('', 'id="form" class="form-horizontal"') ?>
        <div class="row gallery-wrap items" id="sortable">
			<?php foreach ($rows['values'] as $v): ?>
                <div class="ui-state-default col-lg-2 col-sm-6" id="id-<?= $v['gallery_id'] ?>">
                    <div class="text-center handle cursor">
                            <div class="item">
                                <div class="thumbnail">
                                    <div class="gallery-item" style="height: 250px; overflow: auto">
										<?php if (!empty($v['gallery_photo'])): ?>
                                            <img src="<?= $v['gallery_photo'] ?>"
                                                 alt="preview" class="theme-preview img-responsive"/>
										<?php else: ?>
                                            <img src="<?= base_url('images/no-photo.jpg') ?>" alt="preview"
                                                 class="img-responsive"/>
										<?php endif; ?>
                                        <div class="img-title">
                                            <h5>
                                                <p><small><?=$v['gallery_name']?></small></p>
                                                <a href="<?= admin_url(TBL_GALLERY . '/update/' . $v['gallery_id']) ?>"
                                                   class="btn btn-sm btn-default <?= is_disabled('update', TRUE) ?>">
                                                    <i class="fa fa-pencil"></i> </a>
                                                <a data-href="<?= admin_url(TBL_GALLERY . '/delete/' . $v['gallery_id']) ?>"
                                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                                   class="md-trigger btn btn-danger visible-lg <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
		<?= form_close() ?>
    </div>
    <div id="update"></div>
    <script>
        $(function () {
            $('#sortable').sortable({
                handle: '.handle',
                placeholder: "ui-state-highlight",
                update: function () {
                    var order = $('#sortable').sortable('serialize');
                    $("#update").load("<?=admin_url('gallery/update_order')?>?" + order);
                }
            });
        });
        $(function () {
            $('.gallery-item').hover(function () {
                $(this).find('.img-title').fadeIn(300);
            }, function () {
                $(this).find('.img-title').fadeOut(100);
            });
        });
    </script>
<?php endif; ?>
