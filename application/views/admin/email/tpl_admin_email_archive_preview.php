<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title></title>
    <style>
        body {
            font-family: Arial;
            color: #333;
            font-size: 1rem;
        }
    </style>
</head>
<body>
<?php if (!empty($header)):?><?=$header?><?php endif; ?>
<?= html_entity_decode($row['html_body']) ?>
<?php if (!empty($footer)):?><?=$footer?><?php endif; ?>
</body>
</html>