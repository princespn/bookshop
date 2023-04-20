<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<title><?= ucwords(lang('affiliate_payment_invoice')) ?></title>
	<meta charset="<?=$this->config->item('charset')?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="description" content="admin">
	<meta name="keywords" content="">
	<meta name="author" content="JROX.COM">
	<meta http-equiv="refresh" content="7200">

	<?php
	$css = array('css/bootstrap-theme.css',
		'css/style.css',
		'css/style-responsive.css',
		'css/animate.css',
	);

	foreach ($css as $c) echo link_tag('themes/admin/' . $this->config->item('sts_admin_layout_theme') . '/' . $c);
	?>
	<?= link_tag('//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css') ?>


	<script src="<?= base_url('themes/admin/' . $this->config->item('sts_admin_layout_theme') . '/js/jquery.js') ?>"></script>
	<script src="<?= base_url('themes/admin/' . $this->config->item('sts_admin_layout_theme') . '/js/bootstrap.js') ?>"></script>

	<style>
		.heading {
			height: 100px;
		}
		.invoice {
			margin-bottom: 4em;
		}
		.description {
			height: 45em;
		}
	</style>

</head>
<body>