<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title"><?= i('fa fa-video-camera') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
    </div>
    <div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_videos') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
    <h3 class="text-capitalize"><?= lang('manage_video_details') ?></h3>
    <span><?= lang('configure_video_embed_details_description') ?></span>
    <hr/>
    <div class="form-group">
        <label class="col-md-3 control-label"><?= lang('video_name') ?></label>
        <div class="r col-md-5">
			<?= form_input('video_name', set_value('video_name', $row['video_name']), 'class="form-control required"') ?>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-3 control-label"><?= lang('embed_code') ?></label>
        <div class="r col-md-5">
            <textarea name="video_code" rows="3"
                      class="form-control required "><?= set_value('video_code', html_entity_decode($row['video_code']), FALSE) ?></textarea>

        </div>
    </div>
    <hr/>
	<?php if (CONTROLLER_FUNCTION == 'update'): ?>
        <div class="form-group">
            <div class="r col-md-5 col-md-offset-3">
                <a data-toggle="modal" data-target="#show-video" href="#"
                   class="btn btn-primary"><?= i('fa fa-youtube') ?> <span
                            class="hidden-xs"><?= lang('show_video') ?></span></a>
            </div>
        </div>
	<?php endif; ?>
    <hr/>
    <div class="form-group">
		<?= lang('screenshot', 'screenshot', 'class="col-md-3 control-label"') ?>

        <div class="col-lg-5">
            <div class="input-group">
                <input type="text" name="screenshot" value="<?= $row[ 'screenshot' ] ?>"
                       id="0"
                       class="form-control"/>
                <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?fldr=content&type=1&amp;akey=<?= $file_manager_key ?>&field_id=0"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-md-3 control-label"><?= lang('description') ?></label>
        <div class="r col-md-5">
            <textarea name="description" rows="5"
                      class="form-control"><?= set_value('description', $row['description']) ?></textarea>
        </div>
    </div>
    <hr/>
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
<div class="modal fade" id="show-video" tabindex="-1" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<?=html_entity_decode($row['video_code'])?>
        </div>
    </div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('video_id', $id) ?>
<?php endif; ?>
<?= form_close() ?>

<script>

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
