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
				<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
                <a href="<?= site_url(config_item('page_uri') . '/' . $row['url']) ?>?preview=1" class="btn btn-primary"
                   title="<?= lang('view_post') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
		        <?php if ($row['type'] == 'builder'): ?>
                <a data-href="<?= admin_url('site_builder/reset/' . $row['page_id']) ?>" data-toggle="modal"
                   data-target="#confirm-reset" href="#"
                   class="md-trigger btn btn-info"><?= i('fa fa-undo') ?>
                    <span class="hidden-xs"><?= lang('reset_to_default') ?></span></a>
                <?php endif; ?>
                <?php if ($id > 1): ?>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?>
                    <span class="hidden-xs"><?= lang('delete') ?></span></a>
				<?php endif; ?>
			<?php endif; ?>

			<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
                <span class="hidden-xs"><?= lang('view_pages') ?></span></a>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
			<?php endif; ?>
		</div>
	</div>
	<hr/>
	<div class="row">
	<div class="col-md-12">
		<div class="box-info">
			<ul class="resp-tabs nav nav-tabs responsive text-capitalize" role="tablist">
                <?php if ($row['type'] == 'builder'): ?>
                    <li class="active">
                        <a href="#sb" data-toggle="tab">
			                <?= i('fa fa-cog') ?> <span class="visible-lg"><?= lang('site_builder') ?></span>
                        </a></li>
                <?php else: ?>
				<?php foreach ($row['lang'] as $v): ?>
					<li <?php if ($v['language_id'] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
						<a href="#<?= $v['image'] ?>" data-toggle="tab"><?= i('flag-' . $v['image']) ?>
							<span class="visible-lg">
							<?php if (count($row['lang']) > 1): ?>
								<?= $v['name'] ?>
							<?php else: ?>
								<?= lang('page_content') ?>
							<?php endif; ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
				<?php endif; ?>
				<li>
                    <a href="#options" data-toggle="tab">
						<?= i('fa fa-cog') ?> <span class="visible-lg"><?= lang('options') ?></span>
					</a>
                </li>
				<?php if (check_site_builder() && $row['type'] == 'builder'): ?>
                <li><a href="<?= base_url(SITE_BUILDER . '/' . $id . '?full_screen=1') ?>" target="_top">
						<?= i('fa fa-cog') ?> <span class="visible-lg"><?=lang('full_screen')?>
                        </span>
                    </a></li>
                <?php endif; ?>
			</ul>
			<div class="tab-content">
                <?php if (check_site_builder() && $row['type'] == 'builder'): ?>
                <div class="tab-pane active"id="sb">
                    <iframe id="media" src="<?= base_url( SITE_BUILDER . '/' . $id) ?>" height="800"></iframe>
                </div>
                 <?php else: ?>
				<?php foreach ($row['lang'] as $v): ?>

					<div
						class="tab-pane <?php if ($v['language_id'] == $sts_site_default_language): ?> active <?php endif; ?>"
						id="<?= $v['image'] ?>">
						<h3 class="text-capitalize">
							<span class="pull-right"><?= i('flag-' . $v['image']) ?></span>
							<?= lang('site_page_content') ?>
						</h3>
						<?php if (CONTROLLER_FUNCTION == 'update'): ?>
							<small>
								<a href="<?= site_url(config_item('page_uri') . '/' . $row['url']) ?>?preview=1<?php if ($v['language_id'] != $sts_site_default_language): ?>&lang=<?= strtolower($v['language_id']) ?><?php endif; ?>"
								   target="_blank">
									<?= site_url(config_item('page_uri') . '/' . $row['url']) ?><?php if ($v['language_id'] != $sts_site_default_language): ?>?lang=<?= strtolower($v['language_id']) ?><?php endif; ?></a>
							</small>
						<?php endif; ?>
						<hr/>
						<div class="form-group">
							<?= lang('page_content', 'page_content', 'class="col-md-1 control-label"') ?>

							<div class="col-md-10">
								<?= form_textarea('lang[' . $v['language_id'] . '][page_content]', set_value('page_content', $v['page_content'], FALSE), 'class="editor ' . css_error('body') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('meta_title', 'meta_title', 'class="col-md-1 control-label"') ?>

							<div class="r col-md-3">
								<?= form_input('lang[' . $v['language_id'] . '][meta_title]', set_value('meta_title', $v['meta_title']), 'class="' . css_error('meta_title') . ' form-control" placeholder="' . lang('meta_tag_title_description') . '"') ?>
							</div>
							<?= lang('description', 'meta_description', 'class="col-md-1 control-label"') ?>

							<div class="r col-md-3">
								<?= form_input('lang[' . $v['language_id'] . '][meta_description]', set_value('meta_description', $v['meta_description']), 'class="' . css_error('meta_description') . ' form-control" placeholder="' . lang('meta_tag_description') . '"') ?>
							</div>
							<?= lang('keywords', 'meta_keywords', 'class="col-md-1 control-label"') ?>

							<div class="r col-md-2">
								<?= form_input('lang[' . $v['language_id'] . '][meta_keywords]', set_value('meta_keywords', $v['meta_keywords']), 'class="' . css_error('meta_keywords') . ' form-control" placeholder="' . lang('meta_tag_keywords_description') . '"') ?>
							</div>
						</div>
						<hr/>
					</div>
					<?= form_hidden('lang[' . $v['language_id'] . '][language]', $v['name']) ?>
				<?php endforeach; ?>
				<?php endif; ?>
				<div class="tab-pane" id="options">
					<h3 class="text-capitalize">
						<?= lang('page_options') ?>
					</h3>
					<hr />
					<div class="form-group">
						<?= lang('title', 'title', 'class="col-md-3 control-label"') ?>
						<div class="col-md-5">
							<?= form_input('title', set_value('title', $row['title']), 'id="title" class="form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('permalink', 'url', 'class="col-md-3 control-label"') ?>
						<div class="col-md-5">
							<?= form_input('url', set_value('url', $row['url']), 'id="url" class="form-control" placeholder="' . lang('permalink_description') . '"') ?>
						</div>
					</div>
					<hr/>
                     <?php if ($row['type'] == 'builder'): ?>
                    <div class="form-group">
						<?= lang('enable_header', 'enable_header', 'class="col-md-3 control-label"') ?>

                        <div class="r col-md-2">
	                        <?= form_dropdown('enable_header', options('yes_no'), $row[ 'enable_header' ], 'class="form-control" id="enable_header"') ?>
                        </div>
						<?= lang('enable_footer', 'enable_footer', 'class="col-md-1 control-label"') ?>

                        <div class="r col-md-2">
	                        <?= form_dropdown('enable_footer', options('yes_no'), $row[ 'enable_footer' ], 'class="form-control" id="enable_footer"') ?>
                        </div>
                    </div>
                    <hr/>
                    <?php endif; ?>
					</div>
			</div>
		</div>
	</div>
    <div class="modal fade" id="confirm-reset" tabindex="-1" role="dialog" aria-labelledby="modal-headline"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body capitalize">
                    <h3 id="modal-headline"><i class="fa fa-undo "></i> <?= lang('confirm_reset') ?></h3>
					<?= lang('this_will_reset_to_default') ?>. <?= lang('are_you_sure_you_want_to_do_this') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?= lang('cancel') ?></button>
                    <a href="<?= admin_url('site_builder/reset/' . $row['page_id']) ?>"
                       class="btn btn-danger danger"><?= lang('proceed') ?></a>
                </div>
            </div>
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
	<?php if (CONTROLLER_FUNCTION == 'update'): ?>
		<?= form_hidden('page_id', $id) ?>
	<?php endif; ?>
	<?= form_close() ?>
	<script>

		<?=html_editor('init', 'html')?>

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