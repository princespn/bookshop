<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values() ?>
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
				<a href="<?= site_url($row['url']) ?>" class="btn btn-primary"
				   title="<?= lang('view_post') ?>" target="_blank"><?= i('fa fa-external-link') ?></a>
			<?php endif; ?>
			<a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
					class="hidden-xs"><?= lang('view_pages') ?></span></a>
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
			</ul>

			<div class="tab-content">
				<?php foreach ($row['lang'] as $v): ?>
					<div
						class="tab-pane <?php if ($v['language_id'] == $sts_site_default_language): ?> active <?php endif; ?>"
						id="<?= $v['image'] ?>">
						<h3 class="text-capitalize">
							<span class="pull-right"><?= i('flag-' . $v['image']) ?></span>
							<?= $v['title'] ?>
						</h3>
						<?php if (CONTROLLER_FUNCTION == 'update'): ?>
							<small>
								<a href="<?= site_url($row['url']) ?>?<?php if ($v['language_id'] != $sts_site_default_language): ?>lang=<?= strtolower($v['language_id']) ?><?php endif; ?>"
								   target="_blank">
									<?= site_url($row['url']) ?><?php if ($v['language_id'] != $sts_site_default_language): ?>?lang=<?= strtolower($v['language_id']) ?><?php endif; ?></a>
							</small>
						<?php endif; ?>
						<hr/>
						<div class="form-group">
							<?= lang('title', 'title', 'class="col-md-1 control-label"') ?>
							<div class="r col-md-10">
								<?= form_input('lang[' . $v['language_id'] . '][title]', set_value('title', $v['title']), 'class="' . css_error('title') . ' form-control" placeholder="' . lang('title') . '"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<?= lang('page_content', 'page_content', 'class="col-md-1 control-label"') ?>
							<div class="col-md-10">
								<?= form_textarea('lang[' . $v['language_id'] . '][page_content]', set_value('page_content', $v['page_content'], FALSE), 'class="editor ' . css_error('body') . ' form-control"') ?>
							</div>
						</div>
						<hr/>
					</div>
					<?= form_hidden('lang[' . $v['language_id'] . '][language]', $v['name']) ?>
				<?php endforeach; ?>
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

		<?=html_editor('init', 'text_only')?>

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
<?php endif; ?>