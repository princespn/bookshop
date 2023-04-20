<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
		<?= generate_sub_headline('license', 'fa-key', '') ?>
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
            <h3><?= lang('current_license') ?></h3>
            <span><?= lang('license_page_description') ?></span>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('your_system_version') ?></label>

                <div class="col-md-5">
                    <p class="form-control-static"><?= APP_VERSION ?></p>
                </div>
                <!--
                <label class="col-md-2 control-label"><?= lang('system_key') ?></label>

                <div class="col-md-3">
                    <p class="form-control-static"><?=$sts_system_domain_key ?> - <?=$sts_system_internal_key ?></p>
                </div>
                -->
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('license_key') ?></label>

                <div class="col-md-5">
					<?= form_input('sts_site_key', '', 'class="' . css_error('sts_site_key') . ' form-control"') ?>
                </div>
            </div>
            <hr/>

            <div class="form-group">
                <label class="col-md-3 control-label"><?= lang('license_details') ?></label>

                <div class="col-md-5">
                    <div class="alert alert-info">
                        <small>
                            <ul class="list-unstyled">
	                            <?php if (!empty($enable_hosted_application)): ?>
                                    <li><strong><?= lang('jrox_hosted_license') ?></strong></li>
                                <?php elseif (!empty($license_data)): ?>
                                    <li><strong><?= lang('type') ?></strong> - <?= $license_data['product'] ?></li>
                                    <li><strong><?= lang('license') ?></strong> - <?= config_option('sts_site_key') ?>
                                    </li>
                                    <li><strong><?= lang('domains') ?></strong> - <?= $license_data['valid_domains'] ?>
                                    </li>
                                    <li><strong><?= lang('ip_addresses') ?></strong> - <?= $license_data['valid_ips'] ?>
                                    </li>
									<?php if ($license_data['cycle'] != 'One Time'): ?>
                                        <li><strong><?= lang('billing_cycle') ?></strong>
                                            - <?= $license_data['cycle'] ?>
                                        </li>
									<?php endif; ?>
                                    <li><strong><?= lang('status') ?></strong> - <?= $license_data['status'] ?></li>
								<?php else: ?>
                                    <li><strong><?= lang('type') ?></strong> - Community Version <?= APP_VERSION ?></li>
								<?php endif; ?>
                                <li><strong><?=lang('revision')?></strong> - <?=APP_REVISION_NUMBER?></li>
                            </ul>
                        </small>
                    </div>
					<?php if (!empty($matrix_data)): ?>
                        <div class="alert alert-info">
                            <small>
                                <ul class="list-unstyled">
                                    <li><strong><?= lang('type') ?></strong> - <?= $matrix_data['product'] ?></li>
                                    <li><strong><?= lang('license') ?></strong> - <?= $matrix_data['license_key'] ?>
                                    </li>
                                    <li><strong><?= lang('domains') ?></strong> - <?= $matrix_data['valid_domains'] ?>
                                    </li>
                                    <li><strong><?= lang('ip_addresses') ?></strong> - <?= $matrix_data['valid_ips'] ?>
                                    </li>
                                    <li><strong><?= lang('status') ?></strong> - <?= $matrix_data['status'] ?></li>
                                </ul>
                            </small>
                        </div>
					<?php endif; ?>
					<?php if (!empty($copyright_data)): ?>
                        <div class="alert alert-info">
                            <small>
                                <ul class="list-unstyled">
                                    <li><strong><?= lang('type') ?></strong> - <?= $copyright_data['product'] ?></li>
                                    <li><strong><?= lang('license') ?></strong> - <?= $copyright_data['license_key'] ?>
                                    </li>
                                    <li><strong><?= lang('status') ?></strong> - <?= $copyright_data['status'] ?></li>
                                </ul>
                            </small>
                        </div>
					<?php endif; ?>
                </div>
            </div>
            <hr/>
        </div>
    </div>
	<?= settings_sidebar() ?>
</div>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
                <a href="<?= admin_url(CONTROLLER_CLASS . '/reset') ?>" class="btn btn-danger navbar-btn block-phone"
                   id="update-button" <?= is_disabled('update', TRUE) ?>><?= i('fa fa-undo') ?> <?= lang('reset_license') ?></a>
                <button class="btn btn-info navbar-btn block-phone"
                        id="update-button" <?= is_disabled('update', TRUE) ?>
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('update_license') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_close() ?>
<br/>
<script>
    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url(CONTROLLER_CLASS . '/update/')?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form').serialize(),
                beforeSend: function () {
                    $('#update-button i ').addClass('fa-spin');
                    $('#update-button span').html('<?=lang('please_wait')?>');
                },
                complete: function () {
                    $('#update-button i ').removeClass('fa-spin');
                    $('#update-button span').html('<?=lang('backup_database_now')?>');
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
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
</script>

