<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Page Not Found</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body class="mt-3">
<div class="container">
    <div class="row">
        <div class="col-10 offset-1">
            <div class="alert alert-danger">
                <h4 class="alert-heading"><?php echo $heading; ?></h4>
                <p><?php echo $message; ?></p>
                <hr />
                <p><a href="javascript:history.go(-1)" class="btn btn-lg btn-danger">
                        Go Back</a>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>