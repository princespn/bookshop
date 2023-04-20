<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-5">
		<h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
	</div>
	<div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
			<?php if ($row[ 'category_id' ] != $default_support_category_id): ?>
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
					<label for="question"
					       class="col-sm-3 control-label"><?= lang('category_name') ?></label>

					<div class="col-lg-5">
						<?= form_input('lang[' . $v[ 'language_id' ] . '][category_name]', set_value('category_name', $v[ 'category_name' ]), 'class="' . css_error('question') . ' form-control"') ?>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label for="answer"
					       class="col-sm-3 control-label"><?= lang('description') ?></label>

					<div class="col-lg-7">
						<?= form_textarea('lang[' . $v[ 'language_id' ] . '][category_description]', set_value('category_description', $v[ 'category_description' ], FALSE), 'class="form-control"') ?>
					</div>
				</div>
				<hr/>
			</div>
			<?= form_hidden('lang[' . $v[ 'language_id' ] . '][language]', $v[ 'name' ]) ?>
		<?php endforeach; ?>
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
<?= form_close() ?>
<script>
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

						$('#response').html('<?=alert('success')?>');

						setTimeout(function () {
							$('.alert-msg').fadeOut('slow');
						}, 5000);
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
