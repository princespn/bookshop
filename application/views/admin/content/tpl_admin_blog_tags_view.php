<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-8">
		<?= generate_sub_headline(lang('blog_tags'), 'fa-cloud', '' , FALSE) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-4 text-right">
		<a href="<?= admin_url(TBL_BLOG_POSTS . '/view') ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_blog_posts') ?></span></a>
	</div>
</div>
<hr/>

<div id="tagcloud" class="box-info">
	<h3 class="text-capitalize"><?= lang('blog_tag_cloud') ?></h3>
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
							<a data-href="<?= admin_url(TBL_BLOG_TAGS . '/delete/' . $v[ 'tag_id' ]) ?>"
                             data-toggle="modal" data-target="#confirm-delete" href="#"
                             class="btn btn-default <?= is_disabled('delete') ?>"><?= i('fa fa-minus-square') ?></a>
							<a href="#" class="btn btn-default editable <?= is_disabled('update') ?>"
                                 data-name="tag"
                                 data-type="textarea"
                                 data-pk="<?= $v[ 'tag_id' ] ?>"
                                 data-url="<?= admin_url(TBL_BLOG_TAGS . '/update/' . $v[ 'tag_id' ]) ?>"
                                 data-title="<?= lang('update_tag') ?>"><?= $v[ 'tag' ] ?></a>
							<button type="button" disabled class="btn btn-<?=$v['css']?>">
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