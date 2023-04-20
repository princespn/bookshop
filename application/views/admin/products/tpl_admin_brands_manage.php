<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<?php if ($id > 1): ?>
				<a data-href="<?= admin_url(TBL_BRANDS . '/delete/' . $row[ 'brand_id' ]) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>

		<a href="<?= admin_url(TBL_BRANDS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_brands') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-2">
		<div class="thumbnail">
			<div class="photo-panel">
				<a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0&fldr=brands"
				   class="iframe cboxElement">
					<?= photo(CONTROLLER_METHOD, $row, 'img-responsive img-rounded', TRUE, 'image-0') ?></a>
			</div>
			<div class="caption text-center">
				<h3><?= $row[ 'brand_name' ] ?></h3>
			</div>
		</div>
	</div>
	<div class="col-md-10">
		<div class="box-info">
			<ul class="nav nav-tabs text-capitalize" role="tablist">
				<li class="active"><a href="#name" role="tab" data-toggle="tab"><?= lang('name') ?></a></li>
				<li><a href="#config" role="tab" data-toggle="tab"><?= lang('configuration') ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="name">
					<div class="hidden-xs">
						<h3 class="text-capitalize"> <?= lang('brand_description') ?></h3>
						<span><?= lang('set_locale_specific_descriptions_each_tab') ?></span>
					</div>
					<hr/>
					<ul class="nav nav-tabs text-capitalize" role="tablist">
						<?php foreach ($row[ 'lang' ] as $v): ?>
							<li <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> class="active" <?php endif; ?>>
								<a href="#<?= $v[ 'image' ] ?>" data-toggle="tab"><?= i('flag-' . $v[ 'image' ]) ?>
									<span
										class="visible-lg"><?= $v[ 'name' ] ?></span></a>
							</li>
						<?php endforeach; ?>
					</ul>
					<hr/>
					<div class="tab-content">
						<?php foreach ($row[ 'lang' ] as $v):?>
							<div class="tab-pane <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> active <?php endif; ?>" id="<?= $v[ 'image' ] ?>">
								<div class="form-group">
									<?= lang('brand_name', 'brand_name', 'class="col-md-3 control-label"') ?>

									<div class="col-lg-5">
										<?php if (count($row['lang']) > 1 && $v[ 'language_id' ] == $sts_site_default_language): ?>

											<div class="input-group">
												<?= form_input('lang[' . $v[ 'language_id' ] . '][brand_name]', set_value('brand_name', $v[ 'brand_name' ]), 'id="name-' . $v['language_id'] . '" class="' . css_error('brand_name') . ' form-control"') ?>
                                <span class="input-group-addon">
                                    <a href="javascript:void(0)" id="copy_fields"><?= i('fa fa-clone') ?> <?= lang('copy_field') ?></a></span>
											</div>
										<?php else: ?>
										<?= form_input('lang[' . $v[ 'language_id' ] . '][brand_name]', set_value('brand_name', $v[ 'brand_name' ]), 'id="name-' . $v['language_id'] . '" class="' . css_error('brand_name') . ' form-control"') ?>
										<?php endif; ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('brand_description', 'description', 'class="col-md-3 control-label"') ?>

									<div class="col-lg-7">
										<?= form_textarea('lang[' . $v[ 'language_id' ] . '][description]', set_value('description', $v[ 'description' ], FALSE), 'id="desc-' . $v['language_id'] . '" class="editor ' . css_error('description') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('meta_title', 'meta_title', 'class="col-md-3 control-label"') ?>

									<div class="col-lg-5">
										<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_title]', set_value('meta_title', $v[ 'meta_title' ]), 'class="' . css_error('meta_title') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<?= lang('meta_description', 'meta_description', 'class="col-md-3 control-label"') ?>

									<div class="col-lg-5">
										<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_description]', set_value('meta_description', $v[ 'meta_description' ]), 'class="' . css_error('meta_description') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label for="meta_keywords"
									       class="col-sm-3 control-label"><?= lang('meta_keywords') ?></label>

									<div class="col-lg-5">
										<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_keywords]', set_value('meta_keywords', $v[ 'meta_keywords' ]), 'class="' . css_error('meta_keywords') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
							</div>
							<?= form_hidden('lang[' . $v[ 'language_id' ] . '][language]', $v[ 'name' ]) ?>
						<?php endforeach; ?>
					</div>

				</div>
				<div class="tab-pane" id="config">
					<hr/>
					<div class="form-group">
						<?= lang('brand_status', 'brand_status', 'class="col-md-3 control-label"') ?>

						<div class="col-lg-5">
							<?= form_dropdown('brand_status', options('active'), $row[ 'brand_status' ], 'class="form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('sort_order', 'sort_order', 'class="col-md-3 control-label"') ?>

						<div class="col-lg-5">
							<?= form_input('sort_order', set_value('sort_order', $row[ 'sort_order' ]), 'class="' . css_error('sort_order') . ' form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('brand_image', 'brand_image', 'class="col-md-3 control-label"') ?>

						<div class="col-lg-5">
							<div class="input-group">
								<input type="text" name="brand_image" value="<?= $row[ 'brand_image' ] ?>" id="0"
								       class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('brand_banner', 'brand_banner', 'class="col-md-3 control-label"') ?>
						<div class="col-lg-5">
							<div class="input-group">
								<input type="text" name="brand_banner" value="<?= $row[ 'brand_banner' ] ?>" id="1"
								       class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=1"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
							</div>
						</div>
					</div>
					<hr/>
					<?php if (!empty($row[ 'brand_header_image' ])): ?>
						<div class="form-group">
							<label for="meta_title" class="col-sm-3 control-label"><?= lang('header_preview') ?></label>

							<div class="col-lg-5">
								<img src="<?= base_url($row[ 'brand_header_image' ]) ?>" id="image-1"
								     class="img-responsive img-thumbnail">
							</div>
						</div>
						<hr/>
					<?php endif; ?>
					<div class="form-group">
						<?= lang('notes', 'brand_notes', 'class="col-md-3 control-label"') ?>

						<div class="col-lg-5">
							<?= form_textarea('brand_notes', set_value('brand_notes', $row[ 'brand_notes' ]), 'class="' . css_error('brand_notes') . ' form-control"') ?>
						</div>
					</div>
					<hr/>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('brand_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-lg-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<button class="btn btn-success navbar-btn block-phone" name="redir_button" value="1"
					        id="update-button" <?= is_disabled('update', TRUE) ?>
					        type="submit"><?= i('fa fa-plus') ?> <?= lang('save_add_another') ?></button>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone"
				        id="update-button" <?= is_disabled('update', TRUE) ?>
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	<?php if (count($row['lang']) > 1): ?>
	$('#copy_fields').click(function () {
		<?php foreach ($row[ 'lang' ] as $k => $v): ?>
		<?php if ($v[ 'language_id' ] != $sts_site_default_language): ?>
		$('#name-<?=$v['language_id']?>').val($('#name-<?=$sts_site_default_language?>').val());
		<?php endif; ?>
		<?php endforeach; ?>
	});
	<?php endif; ?>

	$("#form").validate({
		ignore: "",
		submitHandler: function (form) {
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

</script>