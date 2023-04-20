<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (!empty($errors)): ?>
	<?= alert('error', $errors) ?>
<?php endif; ?>
    <div class="card">
        <div class="card-body">
            <h3> <?= i('fa fa-info-circle') ?> <?= lang('save_your_login_information') ?>
                <hr/>
                <div class=" alert alert-danger">
					<h2><?= i('fa fa-exclamation-circle') ?> <strong><?= lang('important') ?></strong></h2>
                    <p class="lead"><?= lang('admin_login_unique') ?></p>
                    <hr />
                    <h3><strong><?= lang('admin_login_url') ?></strong>:</h3>
                    <h4><strong><?= config_item('base_url') . '/' . ADMIN_LOGIN ?></strong></h4>
                    <h4><strong><?= lang('username') ?></strong>: <?= $admin_username ?></h4>
                    <h4><strong><?= lang('password') ?></strong>: <?= $admin_password ?></h4>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h3 class="text-center"><?=lang('thanks_for_installing')?></h3>
                            <p class="lead text-center"><?= lang('before_login_please_share') ?> <i class="fa fa-smile-o"></i> </p>
                            <p class="text-center">
                                <!--
                                <iframe src="https://www.facebook.com/plugins/share_button.php?href=https%3A%2F%2Fwww.jrox.com&layout=button&size=small&appId=121081074586467&width=67&height=20" width="67" height="20" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>
                                <a class="btn btn-twitter twitter-share-button"
                                   href="https://twitter.com/intent/tweet?via=jroxdotcom&url=<?=urlencode('https://www.jrox.com')?>&text=<?=urlencode('Get a Full Blown eCommerce Suite Powered By @getbootstrap for Free')?>&hashtag=jrox">
	                                <?= lang('tweet') ?></a>
                                -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.jrox.com"
                                   class="btn btn-lg btn-facebook"
                                   onclick="centeredPopup(this.href,'myWindow','700','300','yes');return false"><?= i('fa fa-facebook') ?> <?= lang('share') ?></a>

                                <a href="https://twitter.com/intent/tweet?via=jroxdotcom&url=<?=urlencode('https://www.jrox.com')?>&text=<?=urlencode('Get a Full Blown eCommerce Suite Powered By @getbootstrap for Free')?>&hashtag=jrox"
                                   class="btn btn-lg  btn-twitter" onclick="centeredPopup(this.href,'myWindow','700','300','yes');return false"><?= i('fa fa-twitter') ?> <?= lang('tweet') ?></a>

                                <a href="http://www.linkedin.com/shareArticle?mini=true&url=https://www.jrox.com&title=<?=urlencode('Download eCommerce Suite Now')?>&summary=<?=urlencode('Get a Full Blown eCommerce Suite Powered By @getbootstrap for Free')?>&source=https://www.jrox.com" class="btn btn-lg  btn-linkedin" onclick="centeredPopup(this.href,'myWindow','700','300','yes');return false">
                                    <?= i('fa fa-linkedin') ?> <?= lang('share') ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <a href="javascript:window.print()" class="btn btn-secondary btn-lg btn-block" >
							<?= i('fa fa-print') ?> <?= lang('print_this_page') ?></a>
                    </div>
                    <div class="col-md-6">
                        <a id="continue" href="?step=redirect" target="_blank"
                           class="btn btn-primary btn-lg btn-block"><span id="wait">
                <i class="fa fa-caret-right"></i> <?= lang('click_here_to_login') ?></span></a>
                    </div>
                </div>
        </div>
    </div>
<script language="javascript">
    var popupWindow = null;
    function centeredPopup(url,winName,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings =
            'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
        popupWindow = window.open(url,winName,settings)
    }
</script>