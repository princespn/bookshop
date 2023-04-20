<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline(lang('manage_forum_topic'), 'fa-edit', '', FALSE) ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(TBL_FORUM_TOPICS ) ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?>
			<span class="hidden-xs"><?= lang('view_forum_topics') ?></span></a>

	</div>
</div>
<hr/>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="box-info">
	<div class="add-content">
		<div class="tab-pane fade in active" id="reply">
			<h3 class="text-capitalize"><?= lang('add_your_topic') ?></h3>
			<hr/>
			<div class="row">
				<label class="col-md-1 control-label"><?= lang('category') ?></label>
				<div class="col-md-2 r">
					<?= form_dropdown('category_id', options('forum_categories'), $row['category_id'], 'id="category_id" class="form-control required"') ?>
				</div>
				<label class="col-md-1 control-label"><?= lang('status') ?></label>
				<div class="col-md-1 r">
					<?= form_dropdown('status', options('published'), $row['status'], 'id="status" class="form-control required"') ?>
				</div>
				<label class="col-md-1 control-label"><?= lang('pinned') ?></label>
				<div class="col-md-1 r">
					<?= form_dropdown('pinned', options('yes_no'), $row['pinned'], 'id="pinned" class="form-control required"') ?>
				</div>
			</div>
			<hr />
			<div class="row">
				<label class="col-md-1 control-label"><?= lang('topic_subject') ?></label>
				<div class="col-md-10 r">
					<?= form_input('title', set_value('title', $row['title']), 'id="title" class="' . css_error('title') . ' form-control required"') ?>
				</div>
			</div>
			<hr />
			<div class="row">
				<div class="col-md-12">
					<div id="reply_box">
						<div class="form-group">
							<label class="col-md-1 control-label"><?= lang('content') ?></label>
							<a name="reply"></a>
							<div class="col-md-10">
								<textarea name="topic"
						          class="editor  <?= css_error('topic') ?> form-control required"
						          id="reply_content"
						          rows="8"><?= set_value('topic', $row['topic']) ?></textarea>
								<?= form_hidden('reply_type', 'admin') ?>
								<?= form_hidden('admin_id', sess('admin', 'admin_id')) ?>
							</div>
						</div>
					</div>
					<hr/>
				</div>
			</div>
			<nav class="navbar navbar-fixed-bottom save-changes">
				<div class="container text-right">
					<div class="row">
						<div class="col-md-12">
							<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
							        id="update-button"
							        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
						</div>
					</div>
				</div>
			</nav>
		</div>
	</div>
</div>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
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