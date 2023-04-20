<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $sts_site_name ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="robots" content="noindex, nofollow">

    <?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/bootstrap.css'); ?>
    <?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/bootstrap-theme.css'); ?>
    <?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/style.css'); ?>
    <?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/style-responsive.css'); ?>
    <?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/animate.css'); ?>
    <?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/third/font-awesome/css/font-awesome.min.css'); ?>

    <link rel="shortcut icon" href="<?= base_url('js') ?>favicon.ico" type="image/x-icon"/>
</head>
<body>
<div class="container">
    <div class="full-content-center">
        <div class="login-wrap">
            <?php if ($this->session->success): ?>
                <?= alert('success', small($this->session->success), 'success') ?>
            <?php elseif (!empty($error)): ?>
                <?= alert('error', small($error), 'error', false) ?>
            <?php endif; ?>
            <div class="box-info capitalize text-center">
	            <?php if (empty($layout_design_admin_logo)): ?>
                    <h3><i class="fa fa-lock"></i> <?=lang('admin_login')?></h3>
	            <?php else: ?>
                <?= img($layout_design_admin_logo, '', 'class=login-logo') ?>
                <?php endif; ?>
                <hr />
                <?= form_open(base_url(ADMIN_LOGIN . '/reset_password'), 'role="form" id="validate-form"') ?>
                <div class="form-group login-input">
                    <i class="fa fa-envelope overlay"></i>
                    <?= form_input('email', '', 'id="email" class="form-control text-input" placeholder="' . lang('email_address') . '"') ?>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-info btn-block"><i
                                class="fa fa-key"></i> <?= lang('send_password_reset') ?></button>
                        <a class="btn btn-default btn-block" href="<?= base_url(ADMIN_LOGIN) ?>"><i
                                class="fa fa-lock"></i> <?= lang('login') ?></a>
                    </div>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/js/jquery.js') ?>"></script>
<script src="<?= base_url('js/jquery.validate.js') ?>"></script>
<script>
    $("#validate-form").validate({
        rules: {
            // simple rule, converted to {required:true}
            email: {
                required: true,
                email: true
            }
        },
    messages: {
		email: {
            required: "<?=lang('please_enter_valid_email')?>"
        }
    }
    });
</script>
</body>
</html><!-- Version <?= APP_VERSION ?> -->