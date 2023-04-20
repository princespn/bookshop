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
			<?php if ($row[ 'category_id' ] != $default_forum_category_id): ?>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
				   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
		<?php endif; ?>
		<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
				class="hidden-xs"><?= lang('view_categories') ?></span></a>
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
				<h3 class="text-capitalize"> <?= lang('category_details') ?></h3>
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
							<?= lang('category_name', 'category_name', 'class="col-md-2 control-label"') ?>

							<div class="col-md-5">
								<?php if (count($row['lang']) > 1 && $v['language_id'] == $sts_site_default_language): ?>
									<div class="input-group">
										<?= form_input('lang[' . $v['language_id'] . '][category_name]', set_value('category_name', $v['category_name']), 'id="name-' . $v['language_id'] . '" class="' . css_error('category_name') . ' form-control "') ?>
										<span class="input-group-addon">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?=lang('copy_to_other_language_tabs')?>"
                                       id="copy_fields" class="tip"><?= i('fa fa-clone') ?> <?= lang('copy_field') ?></a></span>
									</div>
								<?php else: ?>
									<?= form_input('lang[' . $v[ 'language_id' ] . '][category_name]', set_value('category_name', $v[ 'category_name' ]), 'id="name-' . $v['language_id'] . '" class="' . css_error('category_name') . ' form-control"') ?>
								<?php endif; ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('description', 'description', '" class="col-md-2 control-label"') ?>

							<div class="col-md-5">
								<?= form_textarea('lang[' . $v[ 'language_id' ] . '][description]', set_value('description', $v[ 'description' ], FALSE), 'id="description-' . $v['language_id'] . '" class="' . css_error('description') . ' form-control"') ?>
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
				<?= lang('status', 'category_status', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<?= form_dropdown('category_status', options('active'), $row[ 'category_status' ], 'class="form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('category_url', 'category_url', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<?= form_input('category_url', set_value('category_url', $row[ 'category_url' ]), 'class="' . css_error('sort_order') . ' form-control"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<?= lang('sort_order', 'sort_order', 'class="col-md-3 control-label"') ?>

				<div class="col-md-5">
					<input type="number" name="sort_order" value="<?= set_value('sort_order', $row[ 'sort_order' ]) ?>"
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
	<?= form_hidden('category_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>
<script>
	<?php if (count($row['lang']) > 1): ?>
	$('#copy_fields').click(function () {
		<?php foreach ($row['lang'] as $k => $v): ?>
		<?php if ($v['language_id'] != $sts_site_default_language): ?>
		$('#name-<?=$v['language_id']?>').val($('#name-<?=$sts_site_default_language?>').val());
		$('#description-<?=$v['language_id']?>').val($('#description-<?=$sts_site_default_language?>').val());
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
