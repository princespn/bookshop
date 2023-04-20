<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title">
			<?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?>
		</h2>
	</div>
	<div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
			   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
					class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_articles') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="box-info">
	<ul class="nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#name" role="tab" data-toggle="tab"><?= lang('name') ?></a></li>
		<li><a href="#config" role="tab" data-toggle="tab"><?= lang('configuration') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="name">
			<div class="hidden-xs">
				<h3 class="text-capitalize"> <?= lang('article_details') ?></h3>
				<span><?= lang('set_locale_specific_descriptions_each_tab') ?></span>
			</div>
			<hr/>
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<?php foreach ($row[ 'lang' ] as $v): ?>
					<li <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
						<a href="#<?= $v[ 'image' ] ?>" data-toggle="tab"><?= i('flag-' . $v[ 'image' ]) ?>
							<span class="visible-lg"><?= $v[ 'name' ] ?></span></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<br/>
			<div class="tab-content">
				<?php foreach ($row[ 'lang' ] as $v): ?>
					<div
						class="tab-pane fade in <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> active <?php endif; ?>"
						id="<?= $v[ 'image' ] ?>">
						<div class="form-group">
							<?= lang('kb_title', 'kb_title', 'class="col-md-2 control-label"') ?>

							<div class="col-md-5">
								<?= form_input('lang[' . $v[ 'language_id' ] . '][kb_title]', set_value('kb_title', $v[ 'kb_title' ]), 'class="' . css_error('kb_title') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('kb_body', 'kb_body', 'class="col-md-2 control-label"') ?>

							<div class="col-md-7">
								<?= form_textarea('lang[' . $v[ 'language_id' ] . '][kb_body]', set_value('kb_body', $v[ 'kb_body' ], FALSE), 'class="editor ' . css_error('kb_body') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('meta_title', 'meta_title', 'class="col-md-2 control-label"') ?>

							<div class="col-md-5">
								<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_title]', set_value('meta_title', $v[ 'meta_title' ]), 'class="' . css_error('meta_title') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('meta_keywords', 'meta_keywords', 'class="col-md-2 control-label"') ?>

							<div class="col-md-5">
								<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_keywords]', set_value('meta_keywords', $v[ 'meta_keywords' ]), 'class="' . css_error('meta_keywords') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('meta_description', 'meta_description', 'class="col-md-2 control-label"') ?>

							<div class="col-md-5">
								<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_description]', set_value('meta_description', $v[ 'meta_description' ]), 'class="' . css_error('meta_description') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
					</div>
					<?= form_hidden('lang[' . $v[ 'language_id' ] . '][language]', $v[ 'name' ]) ?>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="tab-pane fade in" id="config">
			<hr/>
			<div class="form-group">
				<?= lang('status', 'status', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<?= form_dropdown('status', options('active'), $row[ 'status' ], 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('featured', 'featured', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<?= form_dropdown('featured', options('yes_no'), $row[ 'featured' ], 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('category', 'category_id', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<select name="category_id" class="form-control" id="category_id">
						<?php if (!empty($row[ 'category_id' ])): ?>
							<option value="<?= $row[ 'category_id' ] ?>"><?= $row[ 'category_name' ] ?></option>
						<?php endif; ?>
					</select>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('permalink', 'url', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<?= form_input('url', set_value('url', $row[ 'url' ]), 'id="url" class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('kb_downloads', 'kb_downloads', array( 'class' => 'col-lg-3 control-label' )) ?>
				<div class="col-lg-5">
					<select multiple id="kb_downloads" class="form-control select2" name="kb_downloads[]">
						<?php if (!empty($row[ 'kb_downloads' ])): ?>
							<?php foreach ($row[ 'kb_downloads' ] as $v): ?>
								<option value="<?= $v[ 'download_id' ] ?>"
								        selected><?= $v[ 'download_name' ] ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
			</div>
			<hr />
			<div class="form-group">
				<?= lang('kb_videos', 'kb_videos', array( 'class' => 'col-lg-3 control-label' )) ?>
				<div class="col-lg-5">
					<select multiple id="kb_videos" class="form-control select2" name="kb_videos[]">
						<?php if (!empty($row[ 'kb_videos' ])): ?>
							<?php foreach ($row[ 'kb_videos' ] as $v): ?>
								<option value="<?= $v[ 'video_id' ] ?>"
								        selected><?= $v[ 'video_name' ] ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
			</div>
			<hr />
			<div class="form-group">
				<?= lang('sort_order', 'sort_order', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<input type="number" name="sort_order" value="<?= set_value('sort_order', $row[ 'sort_order' ]) ?>"
					       class="form-control digits">
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('views', 'views', 'class="col-md-3 control-label"') ?>
				<div class="col-md-5">
					<input type="number" name="views" value="<?= set_value('views', $row[ 'views' ]) ?>"
					       class="form-control digits">
				</div>
			</div>
			<hr/>
		</div>
	</div>

	<nav class="navbar navbar-fixed-bottom save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('kb_id', $id) ?>
<?php endif; ?>
<?= form_hidden('modified_by', sess('admin', 'username')) ?>
<?= form_close() ?>
<script>

	<?=html_editor('init', 'blog')?>

	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
			tinyMCE.triggerSave();
			$.ajax({
				url: '<?=current_url()?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
				success: function (response) {
					if (response.type == 'success') {
						$('.alert-danger').remove();
						$('.form-control').removeClass('error');

						if (response.redirect) {
							location.href = response.redirect;
						}
						else {
							$('#response').html('<?=alert('success')?>');

							setTimeout(function () {
								$('.alert-msg').fadeOut('slow');
							}, 5000);
						}
					}
					else {
						$('#response').html('<?=alert('error')?>');
					}

					$('#msg-details').html(response.msg);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});

	$("#category_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_KB_CATEGORIES . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					category_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.category_id,
							text: item.category_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	//downloads
	$("#kb_downloads").select2({
		ajax: {
			url: '<?=admin_url(TBL_PRODUCTS_DOWNLOADS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					download_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.download_id,
							text: item.download_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});

	//videos
	$("#kb_videos").select2({
		ajax: {
			url: '<?=admin_url(TBL_VIDEOS . '/search/ajax/')?>',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					video_name: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data, page) {
				return {
					results: $.map(data, function (item) {
						return {
							id: item.video_id,
							text: item.video_name
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 2
	});
</script>
