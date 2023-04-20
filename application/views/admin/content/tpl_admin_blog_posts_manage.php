<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_blog_post') ?>
<?php else: ?>
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
				<a href="<?= base_url(config_item('blog_uri') . '/' . BLOG_PREPEND_LINK . '-' . $row[ 'url' ]) ?>?preview=1" class="btn btn-primary"
				   title="<?= lang('view_post') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
			<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_posts') ?></span></a>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
			<?php endif; ?>
		</div>
	</div>
	<hr/>
	<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<ul class="resp-tabs nav nav-tabs responsive text-capitalize" role="tablist">
				<?php foreach ($row[ 'lang' ] as $v): ?>
					<li <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
						<a href="#<?= $v[ 'image' ] ?>" data-toggle="tab"><?= i('flag-' . $v[ 'image' ]) ?>
							<span class="visible-lg">
							<?php if (count($row[ 'lang' ]) > 1): ?>
								<?= $v[ 'name' ] ?>
							<?php else: ?>
								<?= lang('blog_details') ?>
							<?php endif; ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
				<li><a href="#config" role="tab" data-toggle="tab">
						<?= i('fa fa-cog') ?> <span class="visible-lg"><?= lang('options') ?></span></a>
				</li>
				<?php if (CONTROLLER_FUNCTION == 'update'): ?>
					<li><a href="#revisions" role="tab" data-toggle="tab">
							<?= i('fa fa-undo') ?> <span class="visible-lg"><?= lang('revisions') ?></span></a>
					</li>
				<?php endif; ?>

			</ul>

			<div class="tab-content">
				<?php foreach ($row[ 'lang' ] as $v): ?>
					<div
						class="tab-pane <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> active <?php endif; ?>"
						id="<?= $v[ 'image' ] ?>">
						<h3 class="text-capitalize">
							<span class="pull-right"><?= i('flag-' . $v[ 'image' ]) ?></span>
							<?= lang('blog_post_details') ?>
						</h3>
						<?php if (CONTROLLER_FUNCTION == 'update'): ?>
						<small>
							<a href="<?= base_url(config_item('blog_uri') . '/post/' . $row[ 'url' ]) ?>?preview=1<?php if ($v[ 'language_id' ] != $sts_site_default_language): ?>&lang=<?= strtolower($v[ 'language_id' ]) ?><?php endif; ?>"
							   target="_blank">
								<?= base_url(config_item('blog_uri') . '/post/' . $row[ 'url' ]) ?><?php if ($v[ 'language_id' ] != $sts_site_default_language): ?>?lang=<?= strtolower($v[ 'language_id' ]) ?><?php endif; ?></a>
						</small>
						<?php endif; ?>
						<hr/>
						<div class="form-group">
							<?= lang('title', 'title', 'class="col-md-1 control-label"') ?>
							<?php if ($v[ 'language_id' ] == $sts_site_default_language): ?>
								<div class="r col-md-5">
									<?= form_input('lang[' . $v[ 'language_id' ] . '][title]', set_value('title', $v[ 'title' ]), 'class="' . css_error('title') . ' form-control"') ?>
								</div>
								<div class="r col-md-2">
									<?= form_dropdown('status', options('published'), $row[ 'status' ], 'class="form-control"') ?>
								</div>
								<?= lang('publish_date', 'date_published', array( 'class' => 'col-md-1 control-label' )) ?>
								<div class="r col-md-2">
									<div class="input-group">
										<input type="text" name="date_published"
										       value="<?= set_value('date_published', $row[ 'date_formatted' ]) ?>"
										       class="form-control datepicker-input required"/>
										<span class="input-group-addon"><?= i('fa fa-calendar') ?></span>
									</div>
								</div>
							<?php else: ?>
								<div class="r col-md-10">
									<?= form_input('lang[' . $v[ 'language_id' ] . '][title]', set_value('title', $v[ 'title' ]), 'class="' . css_error('title') . ' form-control"') ?>
								</div>
							<?php endif; ?>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('summary', 'overview', 'class="col-md-1 control-label"') ?>

							<div class="col-md-10">
								<textarea class="form-control" name="lang[<?= $v[ 'language_id' ] ?>][overview]"
								          placeholder="<?= lang('short_summary_blog_post_description') ?>"
								          rows="2"><?= set_value('overview', $v[ 'overview' ]) ?></textarea>

							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('post', 'body', 'class="col-md-1 control-label"') ?>

							<div class="col-md-10">
								<?= form_textarea('lang[' . $v[ 'language_id' ] . '][body]', set_value('body', $v[ 'body' ], FALSE), 'class="editor ' . css_error('body') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('meta_title', 'meta_title', 'class="col-md-1 control-label"') ?>

							<div class="r col-md-3">
								<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_title]', set_value('meta_title', $v[ 'meta_title' ]), 'class="' . css_error('meta_title') . ' form-control" placeholder="' . lang('meta_tag_title_description') . '"') ?>
							</div>
							<?= lang('description', 'meta_description', 'class="col-md-1 control-label"') ?>

							<div class="r col-md-3">
								<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_description]', set_value('meta_description', $v[ 'meta_description' ]), 'class="' . css_error('meta_description') . ' form-control" placeholder="' . lang('meta_tag_description') . '"') ?>
							</div>
							<?= lang('keywords', 'meta_keywords', 'class="col-md-1 control-label"') ?>

							<div class="r col-md-2">
								<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_keywords]', set_value('meta_keywords', $v[ 'meta_keywords' ]), 'class="' . css_error('meta_keywords') . ' form-control" placeholder="' . lang('meta_tag_keywords_description') . '"') ?>
							</div>
						</div>
						<hr/>
					</div>
					<?= form_hidden('lang[' . $v[ 'language_id' ] . '][language]', $v[ 'name' ]) ?>
				<?php endforeach; ?>
				<div class="tab-pane" id="config">
					<h3 class="text-capitalize"><?= lang('seo_and_access_options') ?></h3>
					<hr/>
					<ul class="nav nav-tabs text-capitalize" role="tablist">
						<li class="active"><a href="#meta" role="tab" data-toggle="tab">
								<?= lang('meta_options') ?></a>
						</li>
						<li><a href="#media" role="tab" data-toggle="tab"><?= lang('media') ?></a></li>
						<li><a href="#visibility" role="tab" data-toggle="tab"><?= lang('visibility') ?></a></li>
						<li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="meta">
							<hr/>
							<div class="form-group">
								<?= lang('permalink', 'url', 'class="col-md-3 control-label"') ?>
								<div class="col-md-5">
									<?= form_input('url', set_value('url', $row[ 'url' ]), 'id="url" class="form-control" placeholder="' . lang('permalink_description') . '"') ?>
								</div>
							</div>
							<hr/>
							<div class="form-group">
								<?= lang('category', 'category_id', 'class="col-md-3 control-label"') ?>

								<div class="col-md-5">
									<select name="category_id" class="form-control" id="category_id">
										<?php if (!empty($row[ 'category_id' ])): ?>
											<option value="<?= $row[ 'category_id' ] ?>">
												<?= $row[ 'category_name' ] ?></option>
										<?php else: ?>
											<option value="0" selected>
												<?= lang('none') ?></option>
										<?php endif; ?>
									</select>
								</div>
							</div>
							<hr/>
							<div class="form-group">
								<?= lang('enable_comments', 'enable_comments', 'class="col-md-3 control-label"') ?>

								<div class="r col-md-5">
									<?= form_dropdown('enable_comments', options('yes_no'), $row[ 'enable_comments' ], 'class="form-control"') ?>
								</div>
							</div>
							<hr/>
							<div class="form-group">
								<?= lang('blog_tags', 'blog_tags', array( 'class' => 'col-lg-3 control-label' )) ?>
								<div class="col-lg-5">
									<select multiple id="blog_tags" class="form-control select2" name="blog_tags[]">
										<?php if (!empty($row[ 'blog_tags' ])): ?>
											<?php foreach ($row[ 'blog_tags' ] as $v): ?>
												<option value="<?= $v[ 'tag' ] ?>"
												        selected><?= $v[ 'tag' ] ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</div>
							</div>
							<hr/>
							<div class="form-group">
								<?= lang('sort_order', 'sort_order', 'class="col-md-3 control-label"') ?>

								<div class="col-md-2">
									<input type="number" name="sort_order"
									       value="<?= set_value('sort_order', $row[ 'sort_order' ]) ?>"
									       class="form-control digits">
								</div>
								<?= lang('views', 'views', 'class="col-md-1 control-label"') ?>
								<div class="col-md-2">
									<input type="number" name="views" value="<?= set_value('views', $row[ 'views' ]) ?>"
									       class="form-control digits">
								</div>
							</div>
							<hr/>
						</div>
						<div class="tab-pane" id="media">
							<hr/>
							<div class="form-group">
								<?= lang('overview_image', 'overview_image', 'class="col-md-3 control-label"') ?>

								<div class="col-lg-5">
									<div class="input-group">
										<input type="text" name="overview_image" value="<?= $row[ 'overview_image' ] ?>"
										       id="0"
										       class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?fldr=content&type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
									</div>
								</div>
							</div>
							<hr/>
							<div class="form-group">
								<?= lang('header_image', 'blog_header', 'class="col-md-3 control-label"') ?>

								<div class="col-lg-5">
									<div class="input-group">
										<input type="text" name="blog_header" value="<?= $row[ 'blog_header' ] ?>"
										       id="1"
										       class="form-control"/>
		                                <span class="input-group-addon">
		                                    <a href="<?= base_url() ?>filemanager/dialog.php?fldr=content&type=1&amp;akey=<?= $file_manager_key ?>&field_id=1"
		                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
									</div>
								</div>
							</div>
							<hr/>
                            <div class="form-group">
								<?= lang('video_embed', 'video_embed', 'class="col-md-3 control-label"') ?>

                                <div class="col-lg-5">

                                    <select id="video_embed" class="form-control select2"
                                            name="video_id">
		                                <?php if (!empty($row['video_id'])): ?>
                                            <option value="<?= $row['video_id'] ?>"
                                                    selected><?= $row['video_name'] ?></option>
		                                <?php endif; ?>
                                    </select>

                                </div>
                            </div>
                            <hr/>
							<div class="form-group">
								<?= lang('attached_files', 'blog_downloads', array( 'class' => 'col-lg-3 control-label' )) ?>
								<div class="col-lg-5">
									<select multiple id="blog_downloads" class="form-control select2"
									        name="blog_downloads[]">
										<?php if (!empty($row[ 'blog_downloads' ])): ?>
											<?php foreach ($row[ 'blog_downloads' ] as $v): ?>
												<option value="<?= $v[ 'download_id' ] ?>"
												        selected><?= $v[ 'download_name' ] ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</div>
							</div>
							<hr/>
						</div>
						<div class="tab-pane" id="visibility">
							<hr />
							<div class="form-group">
								<?= lang('require_registration', 'require_registration', 'class="col-md-3 control-label"') ?>

								<div class="r col-md-5">
									<?= form_dropdown('require_registration', options('yes_no'), $row[ 'require_registration' ], 'class="form-control" id="require_registration"') ?>
								</div>
							</div>
							<hr/>
							<div id="group-div">
								<div class="form-group">
									<?= lang('restrict_to_blog_groups', 'restrict_group', 'class="col-md-3 control-label"') ?>

									<div class="r col-md-5">
										<?= form_dropdown('restrict_group', options('yes_no'), $row[ 'restrict_group' ], 'class="form-control" id="restrict_group"') ?>
									</div>
								</div>
								<hr/>
								<div id="groups">
									<div class="form-group">
										<?= lang('blog_groups', 'blog_groups', array( 'class' => 'col-lg-3 control-label' )) ?>
										<div class="col-lg-5">
											<select multiple id="blog_groups" class="form-control select2"
											        name="blog_groups[]">
												<?php if (!empty($row[ 'blog_groups' ])): ?>
													<?php foreach ($row[ 'blog_groups' ] as $v): ?>
														<option value="<?= $v[ 'group_id' ] ?>"
														        selected><?= $v[ 'group_name' ] ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
									</div>
									<hr />
								</div>
								<div class="form-group">
									<?= lang('drip_feed_post', 'drip_feed', 'class="col-md-3 control-label"') ?>

									<div class="r col-md-5">
										<input type="number" name="drip_feed" value="<?= $row[ 'drip_feed' ] ?>"
										       class="form-control digits" placeholder="<?=lang('drip_feed_content_description')?>" />
									</div>
								</div>
								<hr />
							</div>
						</div>
						<div class="tab-pane" id="notes">
							<hr/>
							<div class="form-group">
								<?= lang('notes', 'notes', 'class="col-md-3 control-label"') ?>

								<div class="col-lg-5">
									<?= form_textarea('notes', set_value('notes', $row[ 'notes' ]), 'class="' . css_error('notes') . ' form-control"') ?>
								</div>
							</div>
                            <hr />
                            <div class="form-group">
								<?= lang('author', 'author', 'class="col-md-3 control-label"') ?>

                                <div class="col-md-5">
		                            <?= form_input('author', set_value('author', $row[ 'author' ]), 'id="url" class="form-control"') ?>
                                </div>
                            </div>
							<hr/>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="revisions">
					<div id="revisions-div"></div>
				</div>
			</div>
		</div>
	</div>

	<nav class="navbar navbar-fixed-bottom save-changes">
		<div class="container text-right">
			<div class="row">
				<div class="col-md-12">
					<?php if (CONTROLLER_FUNCTION == 'update'): ?>
						<a class="btn btn-success navbar-btn block-phone"
						   id="save-draft-button" <?= is_disabled('update', TRUE) ?>
						   type="submit"><?= i('fa fa-floppy-o') ?> <?= lang('save_draft') ?></a>
					<?php endif; ?>
					<button class="btn btn-info navbar-btn block-phone"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
				</div>
			</div>
		</div>
	</nav>
	<?php if (CONTROLLER_FUNCTION == 'update'): ?>
		<?= form_hidden('blog_id', $id) ?>
	<?php endif; ?>
	<?php if (CONTROLLER_FUNCTION == 'create'): ?>
		<?= form_hidden('author', sess('admin', 'fname') . ' ' . sess('admin', 'lname')) ?>
	<?php endif; ?>
	<?= form_close() ?>
	<script>

		<?=html_editor('init', 'blog')?>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
		<?php if (config_enabled('sts_content_enable_auto_save_drafts')): ?>
		window.onload = function () {
			setInterval(save_draft, <?=config_option('sts_content_auto_save_draft_interval') * 60000?>);
        };
		<?php endif; ?>

		$('#revisions-div').load('<?= admin_url(TBL_BLOG_POSTS . '/load_revisions/' . $id)?>');

		$('#save-draft-button').click(function () {
			save_draft();
		});

		function save_draft() {
			tinyMCE.triggerSave();
			$.ajax({
				url: '<?=admin_url(TBL_BLOG_POSTS . '/save_draft')?>',
				type: 'POST',
				dataType: 'json',
				data: $('#form').serialize(),
				beforeSend: function () {
					$('#save-draft-button').button('loading');
				},
				complete: function () {
					$('#save-draft-button').button('reset');
				},
				success: function (response) {
					$('.alert-danger').remove();
					$('.form-control').removeClass('error');

					if (response.type == 'success') {
						$('#response').html('<?=alert('success')?>');
						$('#msg-details').html(response.msg);
					}

					$('#revisions-div').load('<?= admin_url(TBL_BLOG_POSTS . '/load_revisions/' . $id)?>');

					setTimeout(function () {
						$('.alert-msg').fadeOut('slow');
					}, 5000);
				},
				error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}

		<?php endif; ?>

		$("#category_id").select2({
			ajax: {
				url: '<?=admin_url(TBL_BLOG_CATEGORIES . '/search/ajax/1')?>',
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
			minimumInputLength: 1
		});


		//blog tags
		$("#blog_tags").select2({
			tags: true
		});

		//downloads
		$("#blog_downloads").select2({
			ajax: {
				url: '<?=admin_url(TBL_PRODUCTS_DOWNLOADS . '/search/ajax/1')?>',
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
			minimumInputLength: 1
		});
		//groups
		$("#blog_groups").select2({
			ajax: {
				url: '<?=admin_url(TBL_BLOG_GROUPS . '/search/ajax/1')?>',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						group_name: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, page) {
					return {
						results: $.map(data, function (item) {
							return {
								id: item.group_id,
								text: item.group_name
							}
						})
					};
				},
				cache: true
			},
			minimumInputLength: 1
		});
		$("select#require_registration").change(function () {
			$("select#require_registration option:selected").each(function () {
				if ($(this).attr("value") == "1") {
					$("#group-div").show(100);
				}
				else {
					$("#group-div").hide(100);
				}
			});
		}).change();
		$("select#restrict_group").change(function () {
			$("select#restrict_group option:selected").each(function () {
				if ($(this).attr("value") == "1") {
					$("#groups").show(100);
				}
				else {
					$("#groups").hide(100);
				}
			});
		}).change();

		$("#form").validate({
			ignore: "",
			submitHandler: function (form) {
				tinyMCE.triggerSave();
				$.ajax({
					url: '<?=current_url()?>',
					type: 'POST',
					dataType: 'json',
					data: $('#form').serialize(),
					beforeSend: function () {
						$('#update-button').button('loading');
					},
					complete: function () {
						$('#update-button').button('reset');
					},
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

        $("#video_embed").select2({
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
	<?php if (config_item('load_google_fonts') == TRUE): ?>
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
        <script>
            WebFont.load({
                google: {
                    families: [<?php if (in_array($layout_design_theme_header_font, $google_fonts)):?>'<?=$layout_design_theme_header_font?>:100,200,300,400,500,600,700'<?php endif; ?><?php if (in_array($layout_design_theme_base_font, $google_fonts)): ?>,'<?=$layout_design_theme_base_font?>:100,200,300,400,500,600,700'<?php endif; ?>]
                }
            });
        </script>
	<?php endif; ?>
<?php endif; ?>