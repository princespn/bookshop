<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
	<div class="col-md-6">
		<?= generate_sub_headline('blog_posts', 'fa-rss', $rows[ 'total' ]) ?>
		<hr class="visible-xs"/>
	</div>
	<div class="col-md-6 text-right">
		<?= next_page('left', $paginate); ?>
        <a href="<?= site_url('blog') ?>" target="_blank"
           class="btn btn-info"><?= i('fa fa-external-link') ?> <span
                    class="hidden-xs"><?= lang('view_blog') ?></span></a>
		<a data-toggle="collapse" data-target="#search_block"
		   class="btn btn-primary"><?= i('fa fa-search') ?> <?= lang('search') ?></a>
		<a href="<?= admin_url(CONTROLLER_CLASS . '/create') ?>"
		   class="btn btn-primary <?= is_disabled('create') ?>"><?= i('fa fa-plus') ?> <span
				class="hidden-xs"><?= lang('create_blog_post') ?></span></a>
		<?= next_page('right', $paginate); ?>
	</div>
</div>
<hr/>
<div id="search_block" class="collapse">
	<?= form_open(admin_url(CONTROLLER_CLASS . '/general_search'), 'method="get" role="form" id="search-form" class="form-horizontal"') ?>
	<div class="box-info">
		<h4><?=i('fa fa-search')?> <?= lang('search_blog_posts') ?></h4>
		<div class="row">
			<div class="col-md-12">
				<div class="input-group">
					<input type="text" name="search_term" class="form-control required" placeholder="<?=lang('enter_search_term')?>">
					<span class="input-group-btn">
				        <button class="btn btn-default" type="submit"><?=lang('search')?></button>
				      </span>
				</div>
			</div>
		</div>
	</div>
	<?=form_close() ?>
</div>
<?php if (empty($rows[ 'values' ])): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_blog_post') ?>
<?php else: ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="box-info">
		<div class="hidden-xs">
			<div class="row text-capitalize">
				<div class="col-md-1 text-center hidden-xs"><?= tb_header('date', 'date_published') ?></div>
				<div class="col-md-9"><?= tb_header('title', 'title') ?></div>
				<div class="col-md-2"></div>
			</div>
			<hr/>
		</div>
		<?php foreach ($rows[ 'values' ] as $v): ?>
			<div class="row">
				<div class="r col-lg-1 col-md-1 hidden-xs text-center">
					<p><a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/blog_id/id/' . $v[ 'blog_id' ]) ?>"
					      class="<?= is_disabled('update', TRUE) ?>">
							<?php if ($v[ 'status' ] == 1): ?>
								<span class="label label-success"><?= lang('published') ?></span>
							<?php else: ?>
								<span class="label label-danger"><?= lang('draft') ?></span>
							<?php endif; ?>
						</a>
					</p>
					<p><small><?= local_date($v[ 'date_published' ]) ?></small></p>
					<p><?= form_checkbox('blog_id[]', $v[ 'blog_id' ]) ?></p>
				</div>
				<div class="r col-lg-9 col-md-8 col-sm-12">
					<h5><small class="pull-right visible-xs">
							<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/blog_id/id/' . $v[ 'blog_id' ]) ?>"
							   class="<?= is_disabled('update', TRUE) ?>">
								<?php if ($v[ 'status' ] == 1): ?>
									<span class="label label-success"><?= lang('published') ?></span>
								<?php else: ?>
									<span class="label label-danger"><?= lang('draft') ?></span>
								<?php endif; ?>
							</a>
						</small>
						<a href="<?= admin_url(TBL_BLOG_POSTS . '/update/' . $v[ 'blog_id' ]) ?>">
						<strong><?= $v[ 'title' ] ?></strong></a></h5>
					<p><?=word_limiter(strip_tags($v['overview']), 60)?></p>
					<p><span>
						<span class="label label-primary"><?=lang('by')?> <?= $v[ 'author' ] ?></span>
							<span class="label label-info"><?= $v[ 'tags' ] ?></span>
				<span class="label label-default"><?= $v[ 'category_name' ] ?></span></span>
					</p>
				</div>
				<div class="r col-lg-2  col-md-3 text-right">
					<a href="<?= base_url(config_item('blog_uri') . '/' . BLOG_PREPEND_LINK . '-' . $v[ 'url' ]) ?>?preview=1" class="btn btn-default"
					   title="<?= lang('view_post') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
					<?php if (config_option('sts_content_enable_comments') == 1): ?>
					<a href="<?= admin_url(TBL_BLOG_COMMENTS . '/view?p-blog_id=' . $v[ 'blog_id' ]) ?>"
					   class="btn btn-info" title="<?= lang('view_comments') ?>">
						<small
							style="font-size: 10px"><?= $v[ 'comments' ] ?></small> <?= i('fa fa-comments') ?>
					</a>
					<?php endif; ?>
					<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'blog_id' ]) ?>"
					   class="btn btn-default" title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
					<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'blog_id' ]) ?>"
					   data-toggle="modal" data-target="#confirm-delete" href="#"
					   class="md-trigger btn btn-danger hidden-xs <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
				</div>
			</div>
			<hr />
		<?php endforeach; ?>
		<div class="row hidden-xs">
			<div class="col-md-3">
				<div class="input-group text-capitalize">
					<span class="input-group-addon"><small> <?= form_checkbox('', '', '', 'class="check-all"') ?> <?= lang('mark_checked_as') ?></small></span>
					<?= form_dropdown('change-status', options('published', 'delete'), '', 'id="change-status" class="form-control"') ?>
					<span class="input-group-btn">
                    <button class="btn btn-primary <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= lang('save_changes') ?></button></span>
				</div>
			</div>
			<div class="col-md-9 text-right">
				<div class="btn-group hidden-xs">
					<?php if (!empty($paginate[ 'num_pages' ]) AND $paginate[ 'num_pages' ] > 1): ?>
						<button disabled
						        class="btn btn-default visible-lg"><?= $paginate[ 'num_pages' ] . ' ' . lang('total_pages') ?></button>
					<?php endif; ?>
					<button type="button" class="btn btn-primary dropdown-toggle"
					        data-toggle="dropdown"><?= i('fa fa-list') ?>
						<?= lang('select_rows_per_page') ?> <span class="caret"></span>
					</button>
					<?= $paginate[ 'select_rows' ] ?>
				</div>
			</div>
		</div>
		<div class="container text-center"><?= $paginate[ 'rows' ] ?></div>
	</div>
	<?= form_close() ?>
<?php endif ?>
<br/>
<!-- Load JS for Page -->
<script>
	$("#form").validate();
	$("#search-form").validate();
</script>
