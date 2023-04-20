<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-sm-4">
		<?= generate_sub_headline(lang('manage_forum_topic'), 'fa-edit', '', FALSE) ?>
	</div>
	<div class="col-sm-8 text-right">
		<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
		<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
		   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
				class="hidden-xs"><?= lang('delete') ?></span></a>
		<a href="<?= admin_url(TBL_FORUM_TOPICS) ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?>
			<span class="hidden-xs"><?= lang('view_forum_topics') ?></span></a>
		<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>

	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="headline" id="title-<?=$row['topic_id']?>"><span
			class="pull-right label label-info"><?= $row['category_name'] ?></span><?= $row['title'] ?></h3>
	<hr/>
	<?= form_open(admin_url(CONTROLLER_CLASS . '/update_topic/' . $row['topic_id']), 'role="form" id="topic-form-' . $row['topic_id'] . '"') ?>
	<div class="row">
		<?php if (!empty($row['admin_id'])): ?>
			<div class="col-sm-1 text-center">
				<?= photo(CONTROLLER_METHOD, $row, 'img-thumbnail img-responsive dash-photo') ?>
				<br/>
				<small>
					<a href="<?= admin_url(TBL_ADMIN_USERS . '/update/' . $row['admin_id']) ?>">
						<strong><?= $row['admin_fname'] ?> <br/><?= $row['admin_lname'] ?></strong>
					</a>
					<br/>
					<?= display_date($row['date_added']) ?><br/>
					<?= display_time($row['date_added']) ?>
				</small>
			</div>
		<?php else: ?>
			<div class="col-sm-1 text-center">
				<?= photo(CONTROLLER_METHOD, $row, 'img-thumbnail img-responsive dash-photo') ?>
				<br/>
				<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $row['member_id']) ?>">
					<strong><?= $row['member_fname'] ?> <br/><?= $row['member_lname'] ?></strong>
				</a>
				<br/>
				<small>
					<?= display_date($row['date_added']) ?><br/>
					<?= display_time($row['date_added']) ?>
				</small>
			</div>
		<?php endif; ?>
		<div class="col-sm-9">
			<div id="topic">
				<blockquote id="topic-<?=$row['topic_id']?>" class="view-topic"><?= format_response($row['topic']) ?></blockquote>
				<div class="reply-topic hide">
					<div class="form-group">
						<label for="title"><?=lang('title')?></label>
						<?= form_input('title', set_value('title', $row['title']), 'class="form-control required"') ?>
						</div>
						<div class="form-group">
							<label for="topic"><?=lang('topic')?></label>
						<textarea name="topic"
						          id="topic-text-<?= $row['topic_id'] ?>" class="form-control required"
						          rows="10"><?= html_escape($row['topic']) ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-2 text-right">
			<div class="view-topic">
				<a href="<?= admin_url('update_status/table/' . TBL_FORUM_TOPICS . '/type/status/key/topic_id/id/' . $row['topic_id']) ?>"
				   class="btn btn-default btn-sm"><?= set_status($row['status']) ?></a>
				<?php if (!empty($row['admin_id'])): ?>
					<a href="#reply" class="btn btn-primary btn-sm quote"
					   onclick="quote_text('<?= $row['admin_username'] ?>', 'topic-text-<?= $row['topic_id'] ?>')"><?= i('fa fa-quote-right') ?></a>
				<?php else: ?>
					<a href="#reply" class="btn btn-primary btn-sm quote"
					   onclick="quote_text('<?= $row['member_username'] ?>', 'topic-text-<?= $row['topic_id'] ?>')"><?= i('fa fa-quote-right') ?></a>
				<?php endif; ?>

				<a class="view-<?= $row['topic_id'] ?> btn btn-sm btn-default tip"
				   data-toggle="tooltip" data-placement="bottom"
				   title="<?= lang('edit_topic') ?>"
				   onclick="edit_topic('<?= $row['topic_id'] ?>')"><?= i('fa fa-pencil') ?></a>
			</div>
			<div class="reply-topic hide">
				<a class="btn btn-sm btn-default tip" data-toggle="tooltip" data-placement="bottom"
				   title="<?= lang('cancel') ?>"
				   onclick="hide_topic('<?= $row['topic_id'] ?>')"><?= i('fa fa-undo') ?></a>
				<button class="btn btn-sm btn-info tip" data-toggle="tooltip"
				        data-placement="bottom"
				        title="<?= lang('save') ?>" type="submit"
				        onclick="save_topic('<?=$row['topic_id']?>')"><?= i('fa fa-refresh') ?></button>
			</div>
		</div>
	</div>
	<hr/>
	<?= form_close() ?>
	<div class="row">
		<div class="col-sm-12">
			<?php if (!empty($row['topic_replies'])): ?>
				<?php foreach ($row['topic_replies'] as $k => $v): ?>
					<div id="reply-box-<?= $v['reply_id'] ?>">
						<?= form_open(admin_url(CONTROLLER_CLASS . '/update_reply/' . $v['reply_id']), 'role="form" id="reply-form-' . $v['reply_id'] . '"') ?>
						<div class="row">
							<?php if ($v['reply_type'] == 'admin'): ?>
								<div class="col-sm-1 text-center">
									<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-responsive dash-photo') ?>
									<br/>
									<small>
										<a href="<?= admin_url(TBL_ADMIN_USERS . '/update/' . $v['admin_id']) ?>">
											<strong><?= $v['admin_fname'] ?> <br/><?= $v['admin_lname'] ?></strong>
										</a>
										<br/>
										<?= display_date($v['date']) ?><br/>
										<?= display_time($v['date']) ?>
									</small>
								</div>
							<?php else: ?>
								<div class="col-sm-1 text-center">
									<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-responsive dash-photo') ?>
									<br/>
									<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v['member_id']) ?>">
										<strong><?= $v['member_fname'] ?> <br/><?= $v['member_lname'] ?>
										</strong>
									</a>
									<br/>
									<small>
										<?= display_date($v['date']) ?><br/>
										<?= display_time($v['date']) ?>
									</small>
								</div>
							<?php endif; ?>
							<div class="col-sm-9">
								<div class="<?= $v['reply_type'] ?>-response">
									<blockquote id="view-box-<?= $v['reply_id'] ?>"
									            class="view-<?= $v['reply_id'] ?> <?php if ($v['reply_type'] == 'admin'): ?>admin<?php endif; ?>">
										<?= format_response($v['reply_content']) ?>
									</blockquote>
									<div class="reply-<?= $v['reply_id'] ?> hide">
										<textarea name="reply-content-<?= $v['reply_id'] ?>"
										          id="reply-text-<?= $v['reply_id'] ?>" class="form-control required"
										          rows="10"><?= html_escape($v['reply_content']) ?></textarea>
									</div>
								</div>
							</div>
							<div class="col-sm-2 text-right">
								<div class="view-<?= $v['reply_id'] ?>">
									<a href="<?= admin_url('update_status/table/' . TBL_FORUM_TOPICS_REPLIES . '/type/status/key/reply_id/id/' . $v['reply_id']) ?>"
									   class="btn btn-default btn-sm"><?= set_status($v['status']) ?></a>
									<?php if ($v['reply_type'] == 'admin'): ?>
										<a href="#reply" class="btn btn-primary btn-sm quote"
										   onclick="quote_text('<?= $v['admin_username'] ?>', 'reply-text-<?= $v['reply_id'] ?>')"><?= i('fa fa-quote-right') ?></a>
									<?php else: ?>
										<a href="#reply" class="btn btn-primary btn-sm quote"
										   onclick="quote_text('<?= $v['username'] ?>', 'reply-text-<?= $v['reply_id'] ?>')"><?= i('fa fa-quote-right') ?></a>
									<?php endif; ?>
									<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_reply/' . $v['reply_id'] . '/' . $id) ?>"
									   data-toggle="modal"
									   data-target="#confirm-delete" href="#" <?= is_disabled('delete') ?>
									   class="md-trigger btn btn-sm btn-danger"><?= i('fa fa-trash-o') ?> </a>
									<a class="view-<?= $v['reply_id'] ?> btn btn-sm btn-default tip"
									   data-toggle="tooltip" data-placement="bottom"
									   title="<?= lang('edit_reply') ?>"
									   onclick="edit_reply('<?= $v['reply_id'] ?>')"><?= i('fa fa-pencil') ?></a>
								</div>
								<div class="reply-<?= $v['reply_id'] ?> hide">
									<a class="btn btn-sm btn-default tip" data-toggle="tooltip" data-placement="bottom"
									   title="<?= lang('cancel') ?>"
									   onclick="hide_reply('<?= $v['reply_id'] ?>')"><?= i('fa fa-undo') ?></a>
									<button class="btn btn-sm btn-info tip" data-toggle="tooltip"
									        data-placement="bottom"
									        title="<?= lang('save') ?>" type="submit"
									        onclick="save_reply('<?= $v['reply_id'] ?>')"><?= i('fa fa-refresh') ?></button>
								</div>
							</div>
						</div>
						<hr/>
						<?= form_close() ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="add-content">
		<div class="tab-pane fade in active" id="reply">
			<h3 class="text-capitalize"><?= lang('add_your_reply') ?></h3>
			<hr/>
			<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
			<div class="row">
				<div class="col-sm-12">
					<div id="reply_box">
						<div class="form-group">
							<label class="col-sm-1 control-label"><?= lang('reply') ?></label>
							<a name="reply"></a>
							<div class="col-sm-10">
								<textarea name="reply_content"
								          class="editor ' . css_error('reply_content') . ' form-control required"
								          id="reply_content"
								          rows="8"><?= set_value('reply_content') ?></textarea>
								<?= form_hidden('reply_type', 'admin') ?>
								<?= form_hidden('topic_id', $id) ?>
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
						<div class="col-sm-12">
							<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
							        id="update-button"
							        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
						</div>
					</div>
				</div>
			</nav>
			<?= form_close() ?>
		</div>
	</div>
</div>
<br/>
<!-- Load JS for Page -->
<script>

	function quote_text(user, id) {
		id = '[quote=' + user + ']' + $('#' + id).val() + '[/quote]';
		$('#reply_content').val($('#reply_content').val() + id);
	}

	function edit_reply(id) {
		$('.reply-' + id).removeClass('hide');
		$('.view-' + id).addClass('hide');
		$('#reply-text-' + id).focus();
	}

	function hide_reply(id) {
		$('.reply-' + id).addClass('hide');
		$('.view-' + id).removeClass('hide');
	}

	function edit_topic(id) {
		$('.reply-topic').removeClass('hide');
		$('.view-topic').addClass('hide');
		$('#topic-text-' + id).focus();
	}

	function hide_topic(id) {
		$('.reply-topic').addClass('hide');
		$('.view-topic').removeClass('hide');
	}

	function save_reply(id) {
		$("#reply-form-" + id).validate({
			ignore: "",
			submitHandler: function (form) {
				$.ajax({
					url: '<?=admin_url(CONTROLLER_CLASS . '/update_reply') ?>/' + id,
					type: 'POST',
					dataType: 'json',
					data: $('#reply-form-' + id).serialize(),
					success: function (response) {
						if (response.type == 'success') {
							$('.alert-danger').remove();
							$('.form-control').removeClass('error');
							$('#view-box-' + id).html(response.data);

							hide_reply(id);
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		});
	}

	function save_topic(id) {
		$("#topic-form-" + id).validate({
			ignore: "",
			submitHandler: function (form) {
				$.ajax({
					url: '<?=admin_url(CONTROLLER_CLASS . '/update_topic') ?>/' + id,
					type: 'POST',
					dataType: 'json',
					data: $('#topic-form-' + id).serialize(),
					success: function (response) {
						if (response.type == 'success') {
							$('.alert-danger').remove();
							$('.form-control').removeClass('error');
							if (response['data']) {
								$.each(response['data'], function (key, val) {
									$('#' + key + '-' + id).html(val);

								});
							}

							hide_topic(id);
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		});
	}

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