<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Installer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="description" content="admin">
    <meta name="keywords" content="">
    <meta name="author" content="JROX.COM">
    <meta http-equiv="refresh" content="7200">

    <meta property="og:url"           content="https://www.jrox.com" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="JROX.COM eCommerce Suite" />
    <meta property="og:description"   content="eCommerce and Affiliate Marketing System All-In-One" />
    <meta property="og:image"         content="https://www.jrox.com/images/logo.png" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <script src="//use.fontawesome.com/b97e5ba918.js"></script>

    <?php if ($this->input->get('step') == 'finish'): ?>
    <script>window.twttr = (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0],
                t = window.twttr || {};
            if (d.getElementById(id)) return t;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js, fjs);

            t._e = [];
            t.ready = function(f) {
                t._e.push(f);
            };

            return t;
        }(document, "script", "twitter-wjs"));</script>
    <?php endif; ?>
    <style>

        body { margin-top: 2em; }

        .card { margin-bottom:  2rem; }

        textarea.error, input.error {
            border: 1px solid red
        }

        label.error {
            color: #a94442;
            font-size: 10px;
            font-family: 'Open Sans', Arial
        }

        textarea.error, input.error {
            background-color: #F2DEDE !important;
            border: 1px solid #a94442;
            -moz-box-shadow: 1px 1px 2px 2px #ddd;
            -webkit-box-shadow: 1px 1px 2px 2px #ddd;
            box-shadow: 1px 1px 2px 2px #ddd
        }

        .btn-facebook {
            background: #45619D;
            border-color: #4D6CAD;
            color: #fff
        }

        .btn-facebook:hover {
            background: #395289;
            border-color: #4D6CAD;
            color: #fff
        }

        .btn-twitter {
            background: #00ACEE;
            border-color: #00B7FC;
            color: #fff
        }

        .btn-twitter:hover {
            background: #03A0DE;
            border-color: #00B7FC;
            color: #fff
        }
        .alert h4 { font-size: 1.2rem; }

        .btn-linkedin {
            background-color: #0085AE;
            border-color: #0085AE;
            color: #fff
        }

        .btn-linkedin:hover {
            background-color: #0090bd;
            border-color: #0085AE;
            color: #fff
        }

        #status {
            color: #eee;
            background-color: #111;
            font-size: 13px;
            text-align: left;
            font-family: Monospace;
        }

        .hover-msg {
            position: fixed;
            z-index: 999999;
            bottom: 2.1875rem;
            right: 5%;
            min-width: 20%;
            border-radius: 0.3125rem
        }
    </style>


    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="//cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
</head>
<body class="bg-light">
<div class="container">
    <div class="row">
        <div class="col-8 offset-2">
            <div class="header clearfix">
                <i class="flag-en"></i>
                <h3><strong><?= i('fa fa-cogs') ?> <?= lang('installation_process') ?> - <?= lang($title) ?></strong></h3>
            </div>
            <br />