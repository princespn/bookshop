<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="alert alert-danger animated fadeIn">
    <h4 class="text-danger"><?=i('fa fa-info-circle')?> <?= lang($msg) ?></h4>

    <p><?= lang($sub) ?></p>
    <p><?php if (!empty($link)): ?><?=$link?><?php endif; ?></p>
    <p><a href="javascript:history.go(-1)"
          class="btn btn-danger"><?= i('fa fa-chevron-left') ?> <?= lang('go_back') ?></a></p>

</div>