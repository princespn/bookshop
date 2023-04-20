<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<title><?= ucwords($page_title) ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="description" content="admin">
	<meta name="keywords" content="">
	<meta name="author" content="JROX.COM">
	<meta http-equiv="refresh" content="7200">
	<!-- Version <?= APP_VERSION ?> -->
    <noscript><meta http-equiv="refresh" content="0; url=<?=base_folder_path('javascript_required')?>"></noscript>
	<?php
	foreach (array( 'css/bootstrap-theme.css',
	                'css/style.css',
	                'css/animate.css',
	                'css/font-awesome/css/font-awesome.min.css',
	                'third/icheck/skins/minimal/grey.css',
	                'third/datepicker/css/bootstrap-datepicker.css',
	                'third/tabdrop/css/tabdrop.css',
	                'third/star-rating/star-rating.css',
	         ) as $c)
	{
		echo link_tag('themes/admin/' . $sts_admin_layout_theme . '/' . $c);
	}
	?>
	<?= link_tag('themes/css/flags/flags.css') ?>
	<?= link_tag('js/colorbox/colorbox.css') ?>
	<?= link_tag('js/select2/select2.css') ?>
	<?= link_tag('js/xeditable/bootstrap-editable.css') ?>

	<link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>"/>

	<script src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/js/jquery.js') ?>"></script>
	<script src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/js/bootstrap.js') ?>"></script>
	<script src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/third/datepicker/js/bootstrap-datepicker.js') ?>"></script>
	<script src="<?= base_url('js/jquery.validate.js') ?>"></script>
	<script src="<?= base_url('js/select2/select2.min.js') ?>"></script>
    <?php if (config_enabled('sts_admin_enable_admin_login_timer')): ?>
    <script>
        var ctime;
        function timer() {
            ctime=window.setTimeout("redirect()",<?=DEFAULT_ADMIN_SESSION_TIMER?>);
        }
        function redirect() {
            window.location = "<?=admin_url('logout?timer_expired=1&redirect=' . urlencode(uri_string()))?>";
        }
        function detime() {
            window.clearTimeout(ctime);
            timer();
        }
    </script>
    <?php endif; ?>
	<?= $meta_data ?>

</head>