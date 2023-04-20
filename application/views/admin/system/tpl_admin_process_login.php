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
    <div class="full-content-center animated fadeInUp">
        <div class="login-wrap">
            <div class="box-info capitalize text-center">
	            <?php if (empty($layout_design_admin_logo)): ?>
                    <h3><i class="fa fa-lock"></i> <?=lang('admin_login')?></h3>
	            <?php else: ?>
                <?= img($layout_design_admin_logo, '', 'class=login-logo') ?>
                <?php endif; ?>
                <h3><?= lang('please_wait') ?></h3>
                <h5><a href="<?= admin_url() ?>" class=""><?= lang('if_not_forwarded_click_here') ?></a></h5>

                <p><img src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/img/loading.gif') ?>" alt=""/></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script>
    $( document ).ready(function() {
	    setTimeout(function(){
		    window.location='<?= $homepage ?>';
	    }, 2000);

    });
</script>