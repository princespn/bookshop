<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open_multipart('', 'role="form" id="ticket_form" class="form-horizontal"') ?>
<div class="row">
	<div class="col-md-4">
		<?= generate_sub_headline('create_support_ticket', 'fa-edit', '') ?>
	</div>
	<div class="col-md-8 text-right">
		<a href="<?= admin_url(TBL_SUPPORT_TICKETS . '/view?closed=0') ?>"
		   class="btn btn-primary"><?= i('fa fa-search') ?>
			<span class="hidden-xs"><?= lang('view_support_tickets') ?></span></a>
	</div>
</div>
<hr/>
<div class="box-info">
	<h3 class="text-capitalize"><?= lang('ticket_details') ?></h3>
	<hr/>
	<div class="row">
		<div class="col-md-7">
			<div class="form-group">
				<label class="r col-md-2 control-label"><?= lang('to') ?></label>

				<div class="r col-md-10">
					<span class="form-control">
							<?= $row[ 'fname' ] ?> <?= $row[ 'lname' ] ?> - <?= $row[ 'primary_email' ] ?>
						</span>

				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-2 control-label"><?= lang('title') ?></label>
				<div class="col-md-10">
					<?= form_input('ticket_subject', set_value('ticket_subject', $row[ 'ticket_subject' ]), 'class="form-control required"') ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-2 control-label"><?= lang('issue') ?></label>
				
				<div class="col-md-10">
					<?= form_textarea('reply_content', set_value('reply_content', $row[ 'reply_content' ], FALSE), 'class="editor ' . css_error('reply_content') . ' form-control required" id="reply_content"') ?>
					<?= form_hidden('ticket_status', 'new') ?>
					<?= form_hidden('member_id', $id) ?>
					<?= form_hidden('reply_type', 'admin') ?>
					<?= form_hidden('admin_id', sess('admin', 'admin_id')) ?>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="col-md-2 control-label"><?= lang('add_attachments') ?></label>
				<div class="col-md-10">
					<div class="row">
						<div class="col-md-3">
							<a id="add-file" class="btn btn-info btn-block">
								<?= i('fa fa-plus') ?> <?= lang('add_more') ?>
							</a>
						</div>
						<div class="col-md-9">
							<input type="file" name="files[]" class="form-control"/>

							<div id="add-attachments"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<small class="text-muted pull-right hidden-md-down"><?= lang('allowed_file_types') ?>
						: <?= str_replace('|', ',', $sts_support_upload_download_types) ?>
						<?= $sts_support_max_upload_per_reply ?> <?= lang('files_max_per_upload') ?>
					</small>
				</div>
			</div>
			<hr/>

		</div>
		<div class="col-md-4">
			<div class="box-info">
				<h5 class="text-capitalize"><?= lang('ticket_options') ?></h5>
				<hr/>
				<div class="form-group">
					<label class="r col-md-3 control-label"><?= lang('cc') ?></label>
					
					<div class="r col-md-7">
						<?= form_input('cc', set_value('cc'), 'class="form-control" placeholder="user1@domain.com,user2@domain.com"') ?>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="r col-md-3 control-label"><?= lang('send_email') ?></label>
					<div class="r col-md-7">
						<?= form_dropdown('send_email', options('yes_no'), '1', 'class="form-control required"') ?>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="r col-md-3 control-label"><?= lang('category') ?></label>
					
					<div class="r col-md-7">
						<?= form_dropdown('category_id', options('ticket_categories'), '', 'class="form-control required"') ?>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="r col-md-3 control-label"><?= lang('priority') ?></label>
					
					<div class="r col-md-7">
						<?= form_dropdown('priority', options('ticket_priority'), '', 'class="form-control required"') ?>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= lang('merge_fields') ?></label>
					
					<div class="col-md-7">
						<?= form_dropdown('', merge_fields('admin_create_support_ticket_template'), '', 'class="text-lowercase form-control" id="merge_fields"') ?>
					</div>
				</div>
				<hr/>
				
				<?php if (!empty($replies)): ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= lang('replies') ?></label>
						
						<div class="col-md-7">
							<?= form_dropdown('', options('replies', '', $replies), '', 'class="form-control" id="load_replies"') ?>
						</div>
					</div>
					<hr/>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<nav class="navbar navbar-fixed-bottom save-changes">
	<div class="container text-right">
		<div class="row">
			<div class="col-md-12">
				<button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
				        id="update-button"
				        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_and_create') ?></button>
			</div>
		</div>
	</div>
</nav>
<?= form_close() ?>
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
	})

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

                //location.href = '<?=admin_url('support_tickets/create/' . $id . '?reply=')?>' + id;
            }
        });
    }).change();
	
	$("select#merge_fields").change(function () {
		$("select#merge_fields option:selected").each(function () {
			id = $(this).attr("value");
			$('#reply_content').val($('#reply_content').val()+id);
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

</script>