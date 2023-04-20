<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<?= generate_sub_headline('product_tags', 'fa-cloud') ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-4 text-right">
        <a data-toggle="collapse" data-target="#add_tags_block"
           class="btn btn-primary"><?= i('fa fa-plus') ?> <?= lang('add_tags') ?></a>
		<a href="<?= admin_url(TBL_PRODUCTS . '/view') ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_products') ?></span></a>
	</div>
</div>
<hr/>
<div id="add_tags_block" class="collapse">
	<?= form_open(admin_url(CONTROLLER_CLASS . '/add_tags'), 'method="post" role="form" id="add-tag-form" class="form-horizontal"') ?>
    <div class="box-info">
        <h4><?= lang('add_tags') ?></h4>
        <div class="row">
            <div class="col-md-12">
                <div class="input-group">
                    <input type="text" name="tags" class="form-control required" placeholder="<?=lang('enter_tags_separated_by_commas')?>">
                    <span class="input-group-btn">
				        <button class="btn btn-default" type="submit"><?=lang('submit')?></button>
				      </span>
                </div>
            </div>
        </div>
    </div>
	<?=form_close() ?>
</div>
<div id="tagcloud" class="box-info">
	<h3 class="text-capitalize"><?= lang('product_tag_cloud') ?></h3>
	<hr/>
	<div class="row">
		<div class="col-md-12">
			<?php if (empty($rows)): ?>
				<div class="alert alert-warning">
					<p class="text-warning"><?= i('fa fa-info-circle') ?> <?= lang('no_tags_yet') ?></p>
				</div>
			<?php else: ?>
				<?php foreach ($rows as $v): ?>
					<div class="tagbox">
						<div class="btn-group" role="group" aria-label="...">
							<a data-href="<?= admin_url(TBL_PRODUCTS_TAGS . '/delete/' . $v[ 'tag_id' ]) ?>"
                             data-toggle="modal" data-target="#confirm-delete" href="#" <?= is_disabled('delete') ?>
                             class="btn btn-default"><?= i('fa fa-minus-square') ?></a>
							<a href="#" class="btn tip btn-default editable <?= is_disabled('update') ?>"
                                 data-name="tag"
                                 data-type="textarea"
                                 data-pk="<?= $v[ 'tag_id' ] ?>"
                                 data-url="<?= admin_url(TBL_PRODUCTS_TAGS . '/update/' . $v[ 'tag_id' ]) ?>"
                               data-title="<?= lang('update_tag') ?>"><?= $v[ 'tag' ] ?></a>
							<button class="btn btn-<?=$v['css']?> tip" data-toggle="tooltip" data-placement="bottom" title="<?=$v['products']?> <?=lang('products')?> <?= $v[ 'count' ] ?> <?=lang('tag_views')?>">
								<small><?= kmbt($v[ 'count' ]) ?></small></button>
						</div>

					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<!-- Load JS for Page -->
<script src="<?= base_url('js/xeditable/bootstrap-editable.min.js') ?>"></script>
<script>
	$.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.ajaxOptions = {type: "GET"};
	$(document).ready(function () {
		$('.editable').editable({
			rows: 4
		});
	});
</script>