<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-sm-4">
		<?= generate_sub_headline(lang('manage_support_ticket'), 'fa-edit', '', FALSE) ?>
	</div>
	<div class="col-sm-8 text-right">
		<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
		<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
		   data-target="#confirm-delete" href="#" 
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
				class="hidden-xs"><?= lang('delete') ?></span></a>
		<a href="<?= admin_url(TBL_SUPPORT_TICKETS . '/view?closed=0') ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?>
			<span class="hidden-xs"><?= lang('view_support_tickets') ?></span></a>
		<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>

	</div>
</div>
<hr/>
<div class="box-info">
	<div class="row">
		<div class="col-sm-10">
			<h3 class="text-capitalize"><?= $row[ 'ticket_subject' ] ?>
				<span class="badge label-<?= $row[ 'ticket_status' ] ?>">
					<?= lang($row[ 'ticket_status' ]) ?>
				</span>
				<br/>
				<small>
					<?= lang('by') ?>
					<span class="cursor"
					      onclick="location.href='<?= admin_url(TBL_MEMBERS . '/update/' . $row[ 'member_id' ]) ?>'">
					<?= $row[ 'fname' ] ?> <?= $row[ 'lname' ] ?>
				</span>
					<?= lang('on') ?>
					<?= display_date($row[ 'date_added' ], TRUE) ?>

				</small>
			</h3>
		</div>
		<div class="col-sm-1 visible-lg">
			<h3><span class="visible-lg label label-default label-<?= $row[ 'priority' ] ?>">
					<?= lang($row[ 'priority' ]) ?> <?= lang('priority') ?>
					</span>
			</h3>
		</div>
		<div class="col-sm-1 visible-lg">
			<h3 class="ticket-closed">
				<?php if ($row[ 'closed' ] == 1): ?>
				<span class="label label-success">
					<?= i('fa fa-check') ?> <?= lang('closed') ?>
					<?php else: ?>
					<span class="label label-warning">
					<?= i('fa fa-exclamation-circle') ?> <?= lang('open') ?>
					<?php endif; ?>
				</span>
			</h3>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-sm-12">
			<?php if (!empty($row[ 'replies' ])): ?>
				<?php foreach ($row[ 'replies' ] as $k => $v): ?>
					<div id="reply-box-<?= $v[ 'reply_id' ] ?>">
						<?= form_open(admin_url(CONTROLLER_CLASS . '/update_reply/' . $v[ 'reply_id' ]), 'role="form" id="reply-form-' . $v[ 'reply_id' ] . '"') ?>
						<div class="row">
							<?php if ($v[ 'reply_type' ] == 'admin'): ?>
								<div class="col-sm-2 col-md-1 text-center">
									<a href="<?= admin_url(TBL_ADMIN_USERS . '/update/' . $v[ 'admin_id' ]) ?>">
										<strong><?= $v[ 'admin_fname' ] ?> <br/><?= $v[ 'admin_lname' ] ?></strong>
									</a>
									<br />
									<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-responsive dash-photo') ?>
									<br/>
									<small>
										<?= local_date($v[ 'date' ]) ?>
									</small>
								</div>
							<?php else: ?>
								<div class="col-sm-2 col-md-1 text-center">
									<a href="<?= admin_url(TBL_MEMBERS . '/update/' . $v[ 'member_id' ]) ?>">
										<strong><?= $v[ 'member_fname' ] ?> <br/><?= $v[ 'member_lname' ] ?>
										</strong>
									</a>
									<br />
									<?= photo(CONTROLLER_METHOD, $v, 'img-thumbnail img-responsive dash-photo') ?>
									<br/>
									<small>
										<?= local_date($v[ 'date' ]) ?><br />
										<?=$v['ip_address']?>
									</small>
								</div>
							<?php endif; ?>
							<div class="col-sm-9 col-md-10">
								<div class="<?= $v[ 'reply_type' ] ?>-response">
									<blockquote id="view-box-<?= $v[ 'reply_id' ] ?>" class="view-<?= $v[ 'reply_id' ] ?> <?php if ($v[ 'reply_type' ] == 'admin'): ?>admin<?php endif; ?>">
										<?= format_response($v[ 'reply_content' ]) ?>
									</blockquote>
									<div class="reply-<?= $v[ 'reply_id' ] ?> hide">
										<textarea name="reply-content-<?= $v[ 'reply_id' ] ?>"
								          id="reply-text-<?= $v[ 'reply_id' ] ?>" class="form-control required"
								          rows="10"><?= html_escape($v[ 'reply_content' ]) ?></textarea>
									</div>
								</div>
							</div>
							<div class="col-sm-1 text-right">
								<div class="view-<?= $v[ 'reply_id' ] ?>">
									<?php if ($k != 0): ?>
									<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_reply/' . $v[ 'reply_id' ] . '/' . $id) ?>"
									   data-toggle="modal"
									   data-target="#confirm-delete" href="#" <?= is_disabled('delete') ?>
									   class="md-trigger btn btn-sm btn-danger"><?= i('fa fa-trash-o') ?> </a>
									<?php endif; ?>
									<a class="view-<?= $v[ 'reply_id' ] ?> btn btn-sm btn-default tip"
									   data-toggle="tooltip" data-placement="bottom"
									   title="<?= lang('edit_reply') ?>"
									   onclick="edit_reply('<?= $v[ 'reply_id' ] ?>')"><?= i('fa fa-pencil') ?></a>
								</div>
								<div class="reply-<?= $v[ 'reply_id' ] ?> hide">
									<a class="btn btn-sm btn-default tip" data-toggle="tooltip" data-placement="bottom"
									   title="<?= lang('cancel') ?>"
									   onclick="hide_reply('<?= $v[ 'reply_id' ] ?>')"><?= i('fa fa-undo') ?></a>
									<button class="btn btn-sm btn-info tip" data-toggle="tooltip"
									        data-placement="bottom"
									        title="<?= lang('save') ?>" type="submit"
									        onclick="save_reply('<?= $v[ 'reply_id' ] ?>')"><?= i('fa fa-refresh') ?></button>
								</div>
							</div>
						</div>
						<?php if (!empty($v[ 'attachments' ])): ?>
							<div class="row">
								<div class="col-sm-3 col-sm-offset-1">
									<hr/>
									<small class="text-capitalize"><?= lang('attachments') ?></small>
								    <br />
									<?= list_attachments($v[ 'attachments' ], $v[ 'reply_id' ]) ?>
                                </div>
							</div>
						<?php endif; ?>
						<hr/>
						<?= form_close() ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<ul class="resp-tabs nav nav-tabs text-capitalize" role="tablist">
		<li class="active"><a href="#reply" role="tab" data-toggle="tab"><?= lang('your_reply') ?></a></li>
		<li><a href="#notes" role="tab" data-toggle="tab"><?= lang('notes') ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="reply">
			<h3 class="text-capitalize"><?= lang('add_your_reply') ?></h3>
			<hr/>
			<?= form_open_multipart('', 'role="form" id="ticket_form" class="form-horizontal"') ?>
			<div class="row">
				<div class="col-sm-12">
					<div id="reply_box">
						<div class="form-group">
							<label class="r col-sm-1 control-label"><?= lang('merge_fields') ?></label>

							<div class="r col-sm-2">
								<?= form_dropdown('', merge_fields('admin_create_support_ticket_template'), '', 'class="text-lowercase form-control" id="merge_fields"') ?>
							</div>
							<?php if (!empty($predefined_replies)): ?>
								<label class="r col-sm-1 control-label"><?= lang('replies') ?></label>

								<div class="r col-sm-3">
									<?= form_dropdown('', options('replies', '', $predefined_replies), '', 'class="form-control" id="load_replies"') ?>
								</div>
							<?php endif; ?>
							<label class="r col-sm-1 control-label"><?= lang('category') ?></label>

							<div class="r col-sm-1">
								<?= form_dropdown('category_id', options('ticket_categories'), set_value('category_id', $row[ 'category_id' ]), 'class="form-control required"') ?>
							</div>
							<label class="r col-sm-1 control-label"><?= lang('priority') ?></label>

							<div class="r col-sm-1">
								<?= form_dropdown('priority', options('ticket_priority'), set_value('priority', $row[ 'priority' ]), 'class="form-control required"') ?>
							</div>
						</div>
						<hr/>
						<div class="form-group">
							<label class="col-sm-1 control-label"><?= lang('reply') ?></label>

							<div class="col-sm-10">
						<textarea name="reply_content"
						          class="editor ' . css_error('reply_content') . ' form-control required"
						          id="reply_content"
						          rows="15"><?= set_value('reply_content', $row[ 'reply_content' ]) ?></textarea>
								<?= form_hidden('reply_type', 'admin') ?>
								<?= form_hidden('ticket_id', $id) ?>
								<?= form_hidden('admin_id', sess('admin', 'admin_id')) ?>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<label class="col-sm-1 control-label"><?= lang('attachments') ?></label>
						<div class="col-sm-10">
							<div class="form-group">
								<div class="r col-sm-3 col-md-2">
									<a id="add-file" class="btn btn-info btn-block">
										<?= i('fa fa-plus') ?> <?= lang('add_more') ?>
									</a>
								</div>
								<div class="r col-sm-4 col-md-6">
									<input type="file" name="files[]" class="form-control"/>

									<div id="add-attachments"></div>
								</div>
								<label class="r col-sm-3  col-md-2 control-label"><?= lang('set_ticket_status') ?></label>

								<div class="r col-sm-2">
									<?= form_dropdown('ticket_status', options('ticket_status'), set_value('ticket_status', $default_set_ticket_status), 'class="form-control required"') ?>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-8">
									<small
										class="text-muted pull-right hidden-md-down"><?= lang('allowed_file_types') ?>
										: <?= str_replace('|', ',', $sts_support_upload_download_types) ?>
										<?= $sts_support_max_upload_per_reply ?> <?= lang('files_max_per_upload') ?>
									</small>
								</div>
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
							<a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/closed/key/ticket_id/id/' . $row[ 'ticket_id' ]) ?>"
							   class="btn btn-default navbar-btn block-phone">
								<?php if ($row[ 'closed' ] == 1): ?>
									<?= i('fa fa-exclamation-circle') ?> <?= lang('mark_open') ?>
								<?php else: ?>
									<?= i('fa fa-check') ?> <?= lang('mark_closed') ?>
								<?php endif; ?>
							</a>
							<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
							        id="update-button"
							        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
						</div>
					</div>
				</div>
			</nav>
			<?= form_close() ?>
		</div>
		<div class="tab-pane fade in" id="notes">
			<h3 class="text-capitalize"><?= lang('ticket_notes') ?></h3>
			<hr/>
			<div class="row">
				<div class="col-sm-12">
					<?= form_open(admin_url(CONTROLLER_CLASS . '/update_notes/' . $id), 'role="form" id="notes-form" class="form-horizontal"') ?>
					<textarea id="add-note" name="note" class="form-control required" rows="10" required><?php if (!empty($row['notes'])): ?><?=$row['notes']['note']?><?php endif; ?></textarea>
					<br/>
					<button class="btn btn-info"><?=i('fa fa-refresh')?> <?= lang('save_notes') ?></button>
					<?= form_hidden('note_id', $row['ticket_id']); ?>
					<?= form_close() ?>
				</div>
			</div>
		</div>
	</div>
</div>
<br/>
<!-- Load JS for Page -->
<script>

	$(function () {
		var validator = $("#ticket_form").submit(function () {
			// update underlying textarea before submit validation
			tinyMCE.triggerSave();
		}).validate({
			ignore: "",
			rules: {
				ticket_subject: "required",
				reply_content: "required"
			},
			errorPlacement: function (label, element) {
				// position error label after generated textarea
				if (element.is("textarea")) {
					label.insertAfter(element.next());
				} else {
					label.insertAfter(element)
				}
			}
		});
		validator.focusInvalid = function () {
			// put focus on tinymce on submit validation
			if (this.settings.focusInvalid) {
				try {
					var toFocus = $(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
					if (toFocus.is("textarea")) {
						tinyMCE.get(toFocus.attr("id")).focus();
					} else {
						toFocus.filter(":visible").focus();
					}
				} catch (e) {
					// ignore IE throwing errors when focusing hidden elements
				}
			}
		}
    });
    $("select#load_replies").change(function () {
		$("select#load_replies option:selected").each(function () {
			if ($(this).attr("value") != "0") {
				id = $(this).attr("value");

                $.ajax({
                    url: '<?=admin_url(CONTROLLER_CLASS . '/get_predefined_reply') ?>/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.type == 'success') {
                            $('#reply_content').val(response.data);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });


				//location.href = '<?=admin_url('support_tickets/update/' . $id . '?reply=')?>' + id + '#reply_box';
			}
		});
	}).change();

	$("select#merge_fields").change(function () {
		$("select#merge_fields option:selected").each(function () {
			id = $(this).attr("value");
			$('#reply_content').val($('#reply_content').val() + id);
			//tinyMCE.execCommand('mceInsertContent', false, id);
			return false;
		});
	}).change();

	var next = 2;

	$('#add-file').click(function () {
		if (next <= <?= $sts_support_max_upload_per_reply ?>) {
			$("#add-attachments").append('<div id="file-' + next + '"><br /><div class="input-group"><input type="file" name="files[]" class="form-control"/><div class="input-group-addon"><a href="javascript:remove_upload(\'#file-' + next + '\')"><i class="fa fa-trash-o "></i></a></div></div></div>');
			next++;
		}
	});

	function remove_upload(id) {
		$(id).fadeOut(300, function () {
			$(this).remove();
		});
		next--;
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

	$("#notes-form").validate({
		ignore: "",
		submitHandler: function (form) {
			$.ajax({
				url: '<?=admin_url(CONTROLLER_CLASS . '/update_notes/' . $id) ?>',
				type: 'POST',
				dataType: 'json',
				data: $('#notes-form').serialize(),
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