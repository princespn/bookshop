<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values(CONTROLLER_CLASS . '/create/', 'create_group') ?>
<?php else: ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<?php if ($id > 1): ?>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $row[ 'group_id' ]) ?>"
				   data-toggle="modal" data-target="#confirm-delete" href="#"
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>

		<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
			<span class="hidden-xs"><?= lang('view_product_groups') ?></span></a>
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
				<a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
				   class="iframe cboxElement">
					<?= photo(CONTROLLER_METHOD, $row, 'img-responsive img-rounded', TRUE, 'image-0') ?></a>
			</div>
			<div class="caption text-center">
				<h3><?= $row[ 'group_name' ] ?></h3>
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
						<h3 class="text-capitalize"> <?= lang('group_description') ?></h3>
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
						<?php foreach ($row[ 'lang' ] as $v): ?>
							<div
								class="tab-pane <?php if ($v[ 'language_id' ] == $sts_site_default_language): ?> active <?php endif; ?>"
								id="<?= $v[ 'image' ] ?>">
								<div class="form-group">
									<label for="group_name"
									       class="col-sm-3 control-label"><?= lang('group_name') ?></label>

									<div class="col-lg-5">
										<?php if (count($row['lang']) > 1 && $v[ 'language_id' ] == $sts_site_default_language): ?>

											<div class="input-group">
												<?= form_input('lang[' . $v[ 'language_id' ] . '][group_name]', set_value('group_name', $v[ 'group_name' ]), 'id="name-' . $v['language_id'] . '" class="' . css_error('group_name') . ' form-control"') ?>
												<span class="input-group-addon">
                                    <a href="javascript:void(0)" id="copy_fields"><?= i('fa fa-clone') ?> <?= lang('copy_field') ?></a></span>
											</div>
										<?php else: ?>
											<?= form_input('lang[' . $v[ 'language_id' ] . '][group_name]', set_value('group_name', $v[ 'group_name' ]), 'id="name-' . $v['language_id'] . '" class="' . css_error('group_name') . ' form-control"') ?>
										<?php endif; ?>


									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label for="group_description"
									       class="col-sm-3 control-label"><?= lang('group_description') ?></label>

									<div class="col-lg-7">
										<?= form_textarea('lang[' . $v[ 'language_id' ] . '][description]', set_value('description', $v[ 'description' ],FALSE), 'class="editor ' . css_error('description') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label for="meta_title"
									       class="col-sm-3 control-label"><?= lang('meta_title') ?></label>

									<div class="col-lg-5">
										<?= form_input('lang[' . $v[ 'language_id' ] . '][meta_title]', set_value('meta_title', $v[ 'meta_title' ]), 'class="' . css_error('meta_title') . ' form-control"') ?>
									</div>
								</div>
								<hr/>
								<div class="form-group">
									<label for="meta_description"
									       class="col-sm-3 control-label"><?= lang('meta_description') ?></label>

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
						<label for="meta_title" class="col-sm-3 control-label"><?= lang('status') ?></label>

						<div class="col-lg-5">
							<?= form_dropdown('group_status', options('active'), $row[ 'group_status' ], 'class="form-control"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label for="meta_title" class="col-sm-3 control-label"><?= lang('sort_order') ?></label>

						<div class="col-lg-5">
							<?= form_input('sort_order', set_value('sort_order', $row[ 'sort_order' ]), 'class="' . css_error('sort_order') . ' form-control required"') ?>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label for="meta_title" class="col-sm-3 control-label"><?= lang('group_image') ?></label>

						<div class="col-lg-5">
							<div class="input-group">
								<input type="text" name="image" value="<?= $row[ 'image' ] ?>" id="0"
								       class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label for="meta_title" class="col-sm-3 control-label"><?= lang('group_banner') ?></label>

						<div class="col-lg-5">
							<div class="input-group">
								<input type="text" name="group_banner"
								       value="<?= $row[ 'group_banner' ] ?>" id="1" class="form-control"/>
                                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=1"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
							</div>
						</div>
					</div>
					<hr/>
					<?php if (!empty($row[ 'group_header_image' ])): ?>
						<div class="form-group">
							<label for="meta_title" class="col-sm-3 control-label"><?= lang('header_preview') ?></label>

							<div class="col-lg-5">
								<img src="<?= base_url($row[ 'group_header_image' ]) ?>" id="image-1"
								     class="img-responsive img-thumbnail">
							</div>
						</div>
						<hr/>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('group_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-lg-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
					<input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
					       class="btn btn-success navbar-btn block-phone"/>
				<?php endif; ?>
				<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
				        id="update-button"
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
	<?php if (count($row[ 'lang' ]) > 1): ?>
	$('#copy_fields').click(function () {
		<?php foreach ($row[ 'lang' ] as $k => $v): ?>
		<?php if ($v[ 'language_id' ] != $sts_site_default_language): ?>
		$('#name-<?=$v[ 'language_id' ]?>').val($('#name-<?=$sts_site_default_language?>').val());
		<?php endif; ?>
		<?php endforeach; ?>
	});
	<?php endif; ?>

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

	<?=html_editor('init', 'text_only')?>

	$("#parent_id").select2({
		ajax: {
			url: '<?=admin_url(TBL_PRODUCTS_CATEGORIES . '/search/ajax/')?>',
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
		minimumInputLength: 2
	});

</script>
<?php endif; ?>