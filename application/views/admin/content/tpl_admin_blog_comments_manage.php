<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (empty($row)): ?>
	<?= tpl_no_values() ?>
<?php else: ?>
	<div class="row">
		<div class="col-md-4">
			<?= generate_sub_headline(CONTROLLER_CLASS, 'fa-edit', '') ?>
		</div>
		<div class="col-md-8 text-right">
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('left', CONTROLLER_METHOD, $row[ 'prev' ]) ?>
				<a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $row[ 'id' ]) ?>"
				   data-toggle="modal" data-target="#confirm-delete" href="#"
				   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
						class="hidden-xs"><?= lang('delete') ?></span></a>
			<?php endif; ?>
			<a href="<?= admin_url(CONTROLLER_CLASS . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
				<span class="hidden-xs"><?= lang('view_blog_comments') ?></span></a>
			<?php if (CONTROLLER_FUNCTION == 'update'): ?>
				<?= prev_next_item('right', CONTROLLER_METHOD, $row[ 'next' ]) ?>
			<?php endif; ?>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-12">
			<div class="box-info">
				<h3><a href="<?= site_url($blog_uri . '/post/' . $row[ 'url' ] . '#comments') ?>"
				       target="_blank"><?= $row[ 'title' ] ?></a></h3>
				<hr class="hidden-xs"/>
				<?= form_open('', 'role="form" id="update-form" class="form-horizontal"') ?>
				<?php if (!empty($row[ 'parent_comment' ])): ?>
					<div class="form-group">
						<?= lang('parent_response', 'parent_response', array( 'class' => 'col-md-2 control-label' )) ?>

						<div class="col-md-7">
							<blockquote class="text-muted">
								<p><?= nl2br_except_pre($row[ 'parent_comment' ]) ?></p>
								<footer>
									<strong><?= $row[ 'parent_username' ] ?></strong> <?= display_date($row[ 'parent_date' ]) ?>
								</footer>
							</blockquote>
						</div>
					</div>
					<hr/>
				<?php endif; ?>
				<div id="view-box-<?= $row[ 'id' ] ?>" class="view-<?= $row[ 'id' ] ?>">
					<div class="form-group">
						<?= lang('comment', 'date', array( 'class' => 'col-md-2 control-label' )) ?>

						<div class="col-md-6">
							<div id="view-box-<?= $row[ 'id' ] ?>" class="view-<?= $row[ 'id' ] ?>">
								<blockquote>
									<?= nl2br_except_pre($row[ 'comment' ]) ?>
									<footer>
										<strong><?= $row[ 'username' ] ?></strong> <?= display_date($row[ 'date' ]) ?>
									</footer>
								</blockquote>
							</div>
						</div>
						<div class="col-md-1 text-right">
							<div class="view-<?= $row[ 'id' ] ?>">
								<a class="view-<?= $row[ 'id' ] ?> btn btn-sm btn-default tip"
								   data-toggle="tooltip" data-placement="bottom"
								   title="<?= lang('edit_reply') ?>"
								   onclick="edit_reply('<?= $row[ 'id' ] ?>')"><?= i('fa fa-pencil') ?> <?= lang('edit_comment') ?></a>
							</div>

						</div>
					</div>
				</div>
				<div class="comment-<?= $row[ 'id' ] ?> hide">
					<div class="form-group">
						<?= lang('admin_response', 'member_id', array( 'class' => 'col-md-2 control-label' )) ?>
						<div class="r col-md-2">
                            <?php if ($row['type'] == 'admin'): ?>
                                <p class="form-control"><?=$row['admin_fname']?> <?=$row['admin_lname']?></p>
                            <?php else: ?>
							<select id="user_id" class="form-control select2 edit-comment"
							        name="user_id" disabled>
								<option value="<?= set_value('user_id', $row[ 'user_id' ]) ?>"
								        selected><?= set_value('username', $row[ 'username' ]) ?></option>
							</select>
                            <?php endif; ?>
						</div>
						<?= lang('approved', 'status', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-1">
							<?= form_dropdown('status', options('yes_no'), $row[ 'status' ], 'class="form-control required" id="comm_status"') ?>
						</div>
						<?= lang('submitted_on', 'date', array( 'class' => 'col-md-1 control-label' )) ?>
						<div class="r col-md-2">
							<div class="input-group">
								<input type="text" name="reply_date"
								       value="<?= set_value('date', $row[ 'date_formatted' ]) ?>"
								       class="edit-comment form-control datepicker-input required" disabled/>
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
					</div>
					<hr/>
					<div class="form-group">
						<?= lang('comment', 'date', array( 'class' => 'col-md-2 control-label' )) ?>

						<div class="col-md-6">
							<textarea name="comment" id="comment-text-<?= $row[ 'id' ] ?>"
							          class="form-control required edit-comment"
							          rows="10" disabled><?= $row[ 'comment' ] ?></textarea>
                            <br />
                            <button class="btn btn-info btn-sm <?= is_disabled('update', TRUE) ?>"
                                    id="update-comment"
                                    type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
						</div>
						<div class="col-md-1 text-right">
							<div class="comment-<?= $row[ 'id' ] ?> hide">
								<a class="btn btn-sm btn-default tip" data-toggle="tooltip" data-placement="bottom"
								   title="<?= lang('view_only') ?>"
								   onclick="hide_reply('<?= $row[ 'id' ] ?>')"><?= i('fa fa-undo') ?> <?= lang('view_only') ?></a>

							</div>
						</div>
					</div>
				</div>
				<?php if (CONTROLLER_FUNCTION == 'update'): ?>
					<?= form_hidden('id', $row[ 'id' ]) ?>
				<?php endif; ?>
				<?= form_close() ?>
				<hr/>
				<div class="row">
					<div class="col-md-9 col-md-offset-2">
						<h3 class="text-capitalize"><?= lang('admin_reply') ?></h3>
					</div>
				</div>
				<hr/>
				<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
				<div class="form-group">
					<?= lang('add_your_reply', 'admin_reply', array( 'class' => 'col-md-2 control-label' )) ?>

					<div class="col-md-7">
						<?= form_textarea('admin_reply', '', 'id="admin_reply" class="form-control required"') ?>
					</div>
				</div>
				<hr/>
				<div class="form-group">
					<?= lang('reply_to', 'reply_to', array( 'class' => 'col-md-2 control-label required' )) ?>

					<div class="col-md-3">
						<select class="form-control required"
						        name="parent_id">
							<option value="0" selected><?= lang('comment_thread') ?></option>
							<?php if (!empty($row[ 'parent_id' ])): ?>
								<option value="<?= $row[ 'parent_id' ] ?>"><?= lang('user_response') ?></option>
							<?php else: ?>
								<option value="<?= $row[ 'id' ] ?>"><?= lang('user_response') ?></option>
							<?php endif; ?>
						</select>
					</div>
					<?= lang('reply_date', 'reply_date', array( 'class' => 'col-md-2 control-label required' )) ?>
					<div class="r col-md-2">
						<div class="input-group">
							<input type="text" name="reply_date"
							       value="<?= set_value('reply_date', display_date('', FALSE, 2, TRUE)) ?>"
							       class="form-control datepicker-input required"/>
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>
                <nav class="navbar navbar-fixed-bottom save-changes">
                    <div class="container text-right">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="reset"
                                        class="btn btn-default navbar-btn block-phone <?= is_disabled('update', TRUE) ?>">
									<?= i('fa fa-undo') ?> <?= lang('reset') ?>
                                </button>
                                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                        id="update-button"
                                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                            </div>
                        </div>
                    </div>
                </nav>
				<?php if (CONTROLLER_FUNCTION == 'update'): ?>
					<?= form_hidden('id', $id) ?>
				<?php endif; ?>
				<?= form_hidden('blog_id', $row['blog_id']) ?>
				<?= form_hidden('status', '1') ?>
				<?= form_hidden('user_id', sess('admin', 'admin_id')) ?>
				<?= form_close() ?>
				<hr/>

			</div>
		</div>
	</div>
	<script>
		function edit_reply(id) {
			$('.comment-' + id).removeClass('hide');
			$('.edit-comment').attr('disabled', false);
			$('.view-' + id).addClass('hide');
			$('#admin_reply').removeClass('required');
			$('.alert-danger').remove();
			$('.form-control').removeClass('error');
			$('#comment-text-' + id).focus();
		}

		function hide_reply(id) {
			$('.comment-' + id).addClass('hide');
			$('.edit-comment').attr('disabled', true);
			$('#admin_reply').addClass('required');
			$('.view-' + id).removeClass('hide');
		}

		$("#member_id").select2({
			ajax: {
				url: '<?=admin_url(TBL_MEMBERS . '/search/ajax/')?>',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						username: params.term, // search term
						page: params.page
					};
				},
				processResults: function (data, page) {
					return {
						results: $.map(data, function (item) {
							return {
								id: item.member_id,
								text: item.username
							}
						})
					};
				},
				cache: true
			},
			minimumInputLength: 2
		});
        $("#update-form").validate({
            ignore: "",
            submitHandler: function (form) {
                $.ajax({
                    url: '<?=current_url()?>',
                    type: 'POST',
                    dataType: 'json',
                    data: $('#update-form').serialize(),
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
                            if (response['error_fields']) {
                                $.each(response['error_fields'], function (key, val) {
                                    $('#' + key).addClass('error');
                                    $('#' + key).focus();
                                });
                            }
                        }

                        $('#msg-details').html(response.msg);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
						<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        });
		$("#form").validate({
			ignore: "",
			submitHandler: function (form) {
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
							if (response['error_fields']) {
								$.each(response['error_fields'], function (key, val) {
									$('#' + key).addClass('error');
									$('#' + key).focus();
								});
							}
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