<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/mass_update'), 'role="form" id="form"') ?>
	<div class="row">
		<div class="col-md-8">
			<?= generate_sub_headline('blog_comments', 'fa-rss', $rows[ 'total' ]) ?>
			<hr class="visible-xs"/>
		</div>
		<div class="col-md-4 text-right">
			<?= next_page('left', $paginate); ?>
			<div class="btn-group hidden-xs text-capitalize">
				<button type="button" class="btn btn-info dropdown-toggle"
				        data-toggle="dropdown"><?= i('fa fa-search-plus') ?> <?= lang('filter_comments') ?>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="<?= admin_url(TBL_BLOG_COMMENTS . '/view?p-status=0') ?>"><?= lang('view_unapproved_comments') ?></a></li>
					<li><a href="<?= admin_url(TBL_BLOG_COMMENTS . '/view?p-status=1') ?>"><?= lang('view_approved_comments') ?></a></li>
					<li><a href="<?= admin_url(TBL_BLOG_COMMENTS . '/view?type=member') ?>"><?= lang('view_user_comments') ?></a></li>
					<li><a href="<?= admin_url(TBL_BLOG_COMMENTS . '/view?type=admin') ?>"><?= lang('view_admin_comments') ?></a>
					</li>
				</ul>
			</div>
			<a href="<?= admin_url(TBL_BLOG_POSTS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_blog_posts') ?></span></a>
			<?= next_page('right', $paginate); ?>
		</div>
	</div>
	<hr/>
	<?php if (empty($rows[ 'values' ])): ?>
		<?= tpl_no_values() ?>
	<?php else: ?>
		<div class="box-info">
			<div class="hidden-xs">
				<div class="row text-capitalize">
					<div class="col-md-1 text-center"></div>
					<div class="col-md-9"><?= tb_header('comments', '', FALSE) ?></div>
					<div class="col-md-2"></div>
				</div>
				<hr/>
			</div>
			<?php foreach ($rows[ 'values' ] as $v): ?>
				<div class="row">
					<div class="r col-md-1 text-center">
						<p>
							<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-circle dash-photo') ?>
						</p>
						<p>
							<small class="text-muted">
								<?php if ($v[ 'type' ] == 'admin'): ?>
									<a href="<?= admin_url(TBL_ADMIN_USERS . '/update/' . $v[ 'user_id' ]) ?>">
										<strong><?= $v[ 'admin_username' ] ?></strong></a>
									<br /><small class="text-muted"><?= display_date($v[ 'date' ]) ?> </small>
									<br /><span class="label label-primary"><?=lang('admin')?></span>
								<?php else: ?>
									<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v[ 'user_id' ]) ?>">
										<strong><?= $v[ 'name' ] ?></strong></a>
									<br />
									<small class="text-muted"><?= display_date($v[ 'date' ]) ?> </small>
									<br /><span class="label label-success"><?=lang('user')?></span>
								<?php endif; ?>
							</small>
						</p>
						<p>
							<?= form_checkbox('id[]', $v[ 'id' ]) ?>
						</p>
					</div>
					<div class="r col-md-8">
						<h5><?= $v[ 'title' ] ?></h5>
						<p class="minimize"><?= $v[ 'comment' ] ?></p>
					</div>
					<div class="r col-md-3 text-right">
						<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/id/id/' . $v[ 'id' ]) ?>"
						   class="btn btn-default"><?= set_status($v[ 'status' ]) ?></a>
						<a href="<?= site_url(config_item('blog_uri') . '/post/' . $v[ 'url' ] . '#comments') ?>"
						   class="btn btn-default" target="_blank"
						   title="<?= lang('view_thread') ?>"><?= i('fa fa-comments') ?></a>
						<a href="<?= admin_url(CONTROLLER_CLASS . '/update/' . $v[ 'id' ]) ?>"
						   class="btn btn-default"
						   title="<?= lang('edit') ?>"><?= i('fa fa-pencil') ?></a>
						<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $v[ 'id' ]) ?>"
						   data-toggle="modal" data-target="#confirm-delete" href="#"
						   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?></a>
					</div>
				</div>
				<hr/>
			<?php endforeach; ?>
			<div class="row">
				<div class="col-md-3">
					<div class="input-group text-capitalize">
						<span class="input-group-addon"><small> <?= form_checkbox('', '', '', 'class="check-all"') ?> <?= lang('mark_checked_as') ?></small></span>
						<?= form_dropdown('change-status', options('approve', 'delete'), '', 'id="change-status" class="form-control"') ?>
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
	<?php endif; ?>
	<?= form_close() ?>
<script>

	jQuery(function () {
		var minimized_elements = $('p.minimize');
		var min_length = <?=MAX_CHARACTERS_BLOG_COMMENTS_VIEW?>;
		minimized_elements.each(function () {
			var t = $(this).text();
			if (t.length < min_length) return;

			$(this).html(
				t.slice(0, min_length) + '<span>... </span><a href="#" class="more label label-default"><?=lang('more')?></a>' +
				'<span style="display:none;">' + t.slice(min_length, t.length) + ' <a href="#" class="less label label-default"><?=lang('less')?></a></span>'
			);

		});

		$('a.more', minimized_elements).click(function (event) {
			event.preventDefault();
			$(this).hide().prev().hide();
			$(this).next().show();
		});

		$('a.less', minimized_elements).click(function (event) {
			event.preventDefault();
			$(this).parent().hide().prev().show().prev().show();
		});

	});
</script>