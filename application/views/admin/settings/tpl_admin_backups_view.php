<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
		<?= generate_sub_headline('system_backups', 'fa-database', '') ?>
    </div>
    <div class="col-md-8 text-right">
        <a href="<?= admin_url('settings') ?>" class="btn btn-primary"><?= i('fa fa-cog') ?>
            <span class="hidden-xs"><?= lang('settings') ?></span></a>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-9">
        <div class="box-info">
            <div class="alert alert-danger">
		        <?=i('fa fa-info-circle')?> <?=lang('files_backup_warning')?>
            </div>
            <ul class="nav nav-tabs text-capitalize" role="tablist">
                <li class="active"><a href="#db" role="tab" data-toggle="tab"><?= lang('database') ?></a></li>
                <li><a href="#files" role="tab" data-toggle="tab"><?= lang('files') ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="db">
                    <h3><?= lang('current_database_backups') ?></h3>
                    <span><?= lang('click_on_specific_backup_to_restore') ?></span>
                    <hr/>
					<?php if (!empty($db)): ?>
						<?php foreach ($db as $g): ?>
                            <div class="row">
                                <div class="col-md-10">
                                    <h5><?= $g ?></h5>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a href="<?= admin_url('backup/restore_db/' . $g) ?>" class="btn btn-default">
										<?= i('fa fa-undo') ?> <span><?= lang('restore') ?></span></a>
                                </div>
                            </div>
                            <hr/>
						<?php endforeach; ?>
					<?php else: ?>
                        <div class="alert alert-warning">
							<?= i('fa fa-info-circle') ?> <?= lang('no_backups_generated') ?>
                        </div>
					<?php endif; ?>
                    <div class="row">
                        <div class="col-md-12">
							<?php if (check_db_folder()): ?>
                                <button id="backup-db" class="backup btn btn-primary" type="submit">
									<?= i('fa fa-refresh') ?> <span
                                            class="hidden-xs"><?= lang('backup_database_now') ?></span></button>
							<?php else: ?>
                                <div class="alert alert-danger">
									<?= i('fa fa-info-circle') ?> <?= lang('invalid_backup_path_check_backup_folder_settings') ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="files">
                    <h3><?= lang('file_backups') ?></h3>
                    <span><?= lang('current_file_backups_description') ?></span>
                    <hr/>
					<?php if (!empty($files)): ?>
						<?php foreach ($files as $g): ?>
                            <div class="row">
                                <div class="col-md-10">
                                    <h5><?= $g ?></h5>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a href="<?= admin_url('backup/download_archive/' . $g) ?>" class="btn btn-default">
										<?= i('fa fa-download') ?> <span><?= lang('download') ?></span></a>
                                </div>
                            </div>
                            <hr/>
						<?php endforeach; ?>
					<?php else: ?>
                        <div class="alert alert-warning">
							<?= i('fa fa-info-circle') ?> <?= lang('no_backups_generated') ?>
                        </div>
					<?php endif; ?>
                    <div class="row">
						<?php if (check_db_folder()): ?>
                            <div class="col-md-4">
                                <div class="input-group text-capitalize">
									<?= form_dropdown('backup_path', options('backup_path'), '', 'id="change-status" class="form-control"') ?>
                                    <span class="input-group-btn">
                                        <button id="archive-file" class="backup btn btn-primary" type="submit">
                                            <?= i('fa fa-refresh') ?> <span
                                                    class="hidden-xs"><?= lang('backup_files_now') ?></span></button>
                                    </span>
                                </div>
                            </div>
						<?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-danger">
									<?= i('fa fa-info-circle') ?> <?= lang('invalid_backup_path_check_backup_folder_settings') ?>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?= settings_sidebar() ?>
</div>
<?= form_hidden('backup', '1') ?>
<?= form_close() ?>
<br/>
<script>
    $('#backup-db').click(function () {
        validate_form('db');
    });

    $('#archive-file').click(function () {
        validate_form('file');
    });

    function validate_form(type) {
        $("#form").validate({
            ignore: "",
            submitHandler: function (form) {
                $.ajax({
                    url: '<?=admin_url(CONTROLLER_CLASS . '/backup_db')?>/' + type,
                    type: 'POST',
                    dataType: 'json',
                    data: $('#form').serialize(),
                    beforeSend: function () {
                        $('.backup i ').addClass('fa-spin');
                        $('.backup span').html('<?=lang('please_wait')?>');
                    },
                    complete: function () {
                        $('.backup i ').removeClass('fa-spin');
                        $('.backup span').html('<?=lang('backup_now')?>');
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
    }

</script>

