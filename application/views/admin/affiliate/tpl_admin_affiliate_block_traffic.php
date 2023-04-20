<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="input-group text-capitalize">
			<?= generate_sub_headline(lang('block_traffic'), 'fa-list', '', FALSE) ?>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <a href="<?= admin_url(TBL_AFFILIATE_TRAFFIC . '/view') ?>"
           class="btn btn-primary"><?= i('fa fa-search') ?>
            <span class="hidden-xs"><?= lang('view_traffic') ?></span></a>
    </div>
</div>
<hr/>
<?= form_open_multipart('', 'role="form" class="form-horizontal" id="form"') ?>
<div class="box-info">
    <ul class="nav nav-tabs text-capitalize">
        <li class="active"><a href="#sites" data-toggle="tab"><?= lang('block_websites') ?></a></li>
        <li><a href="#ips" data-toggle="tab"><?= lang('block_ip_addresses') ?></a></li>
    </ul>
    <div class="tab-content">
        <div id="sites" class="tab-pane fade in active">
            <div class="col-lg-12">
                <h3 class="text-capitalize"><?= lang('website_urls') ?></h3>
                <small class="text-muted"><?= lang('enter_full_website_url_one_line_at_time') ?></small>
                <hr/>
                <textarea name="sts_system_block_affiliate_websites" rows="25" placeholder="<?=site_url()?>"
                          class="form-control"><?= set_value('sts_system_block_affiliate_websites', $sts_system_block_affiliate_websites) ?></textarea>
            </div>
        </div>
        <div id="ips" class="tab-pane fade in">
            <div class="col-lg-12">
                <h3 class="text-capitalize"><?= lang('ip_addresses') ?></h3>
                <small class="text-muted"><?= lang('enter_ip_addresses_one_line_at_time') ?></small>
                <hr/>
                <textarea name="sts_system_block_affiliate_ip_addresses" rows="25" placeholder="192.168.1.1"
                          class="form-control"><?= set_value('sts_system_block_affiliate_ip_addresses', $sts_system_block_affiliate_ip_addresses) ?></textarea>
            </div>
        </div>
    </div>
</div>
<nav class="navbar navbar-fixed-bottom  save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-lg-12">
                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_close() ?>
<script>

</script>