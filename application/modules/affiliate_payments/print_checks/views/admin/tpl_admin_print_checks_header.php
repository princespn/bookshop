<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<!-- load html charset -->
	<meta http-equiv="Content-Type" content="text/html; charset=<?=$this->config->item('charset')?>" />

	<!-- load page title -->
	<title><?=$this->lang->line('affiliate_payment_checks')?></title>

	<!-- load admin stylesheets -->
	<style media="screen, print">
		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size:11px;
			letter-spacing: 0.1em;
		}

		.checkBg {
			background: #fff;
			width: 690px;
			height: 792px;
			margin:0;
			padding:0;
		}

		.check-box {
			background: #eee;
			height:300px;
			margin-bottom:1em;
			margin-right: 5px;
		}

		.check-date {
			float:right;
			margin:1em 1.3em;
		}

		.check-name {
			position: relative;
			top: 5.9em;
			left: 5em;
		}

		.payment-amount {
			position: relative;
			top: 4.5em;
			left: 55em;
		}

		.payment-words {
			position: relative;
			top: 6.6em;
			left: 2em;
		}

		.payment-address {
			position: relative;
			top: 8.2em;
			left: 2em;
		}

		.second-box {
			height:310px;
			margin:1em;
		}

		.third-box {
			height:280px;
			margin:1em;
		}

		.box-note-left {
			float:left;
			margin-left: 1em;
			margin-right: 5em;
		}

		.box-note-right {
			float:right;
			margin-right:1em;
		}

		@media print {
			.noPrint { display: none; }
			.checkBg { border: none; }
		}


	</style>
</head>
<body id="main_body"  <?php if ($this->config->item('sts_invoice_autload_print_window') == 1) echo 'onload="window.print()"'; ?>>