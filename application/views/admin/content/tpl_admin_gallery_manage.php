<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
		<?= generate_sub_headline(lang('gallery_photo'), 'fa-edit', '', FALSE) ?>
    </div>
    <div class="col-md-8 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <a data-href="<?= admin_url(TBL_GALLERY . '/delete/' . $row['gallery_id']) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>

        <a href="<?= admin_url(TBL_GALLERY . '/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_gallery') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-2">
        <div class="thumbnail">
            <div class="photo-panel">
                <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0&fldr=gallery"
                   class="iframe cboxElement">

					<?= photo(CONTROLLER_METHOD, $row, 'img-responsive img-rounded', TRUE, 'image-0') ?></a>
            </div>
        </div>
    </div>
    <div class="col-md-10">
        <div class="box-info">
            <hr/>
            <div class="form-group">
				<?= lang('status', 'gallery_status', 'class="col-md-3 control-label"') ?>

                <div class="col-md-2">
					<?= form_dropdown('gallery_status', options('active'), $row['gallery_status'], 'class="form-control required"') ?>
                </div>
	            <?= lang('sort_order', 'sort_order', 'class="col-md-1 control-label"') ?>
    
                <div class="col-md-2">
                    <input type="number" name="sort_order" value="<?= empty($row['sort_order']) ? '0' : $row['sort_order'] ?>"
                           class="form-control required digits" />
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('gallery_photo', 'gallery_photo', 'class="col-md-3 control-label"') ?>

                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="gallery_photo" value="<?= $row['gallery_photo'] ?>" id="0"
                               class="form-control required"/>
                        <span class="input-group-addon">
                                    <a href="<?= base_url() ?>filemanager/dialog.php?type=1&amp;akey=<?= $file_manager_key ?>&field_id=0&fldr=gallery"
                                       class="iframe cboxElement"><?= i('fa fa-camera') ?> <?= lang('select_image') ?></a></span>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('name', 'gallery_name', 'class="col-md-3 control-label"') ?>
                <div class="col-md-5">
                        <input type="text" name="gallery_name" value="<?= $row['gallery_name'] ?>"
                               class="form-control required"/>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('url', 'gallery_url', 'class="col-md-3 control-label"') ?>
                <div class="col-md-5">
                    <input type="text" name="gallery_url" value="<?= $row['gallery_url'] ?>"
                           class="form-control" placeholder="<?=base_url()?>"/>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('description', 'gallery_description', 'class="col-md-3 control-label"') ?>

                <div class="col-md-5">
					<?= form_textarea('gallery_description', set_value('gallery_description', $row['gallery_description']), 'class="' . css_error('gallery_description') . ' form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
    </div>
</div>
<?php if (CONTROLLER_FUNCTION == 'update'): ?>
	<?= form_hidden('gallery_id', $id) ?>
<?php endif; ?>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
				<?php if (CONTROLLER_FUNCTION == 'create'): ?>
                    <button class="btn btn-success navbar-btn block-phone" name="redir_button" value="1"
                            id="update-button" <?= is_disabled('update', TRUE) ?>
                            type="submit"><?= i('fa fa-plus') ?> <?= lang('save_add_another') ?></button>
				<?php endif; ?>
                <button class="btn btn-info navbar-btn block-phone"
                        id="update-button" <?= is_disabled('update', TRUE) ?>
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
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