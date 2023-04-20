<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<title><?= lang('login') ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="robots" content="noindex, nofollow">
	<?php if ($this->input->get('timer_expired')): ?>
        <meta http-equiv="refresh" content="3600"/>
	<?php endif; ?>

	<?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/bootstrap.css'); ?>
	<?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/bootstrap-theme.css'); ?>
	<?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/style.css'); ?>
	<?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/style-responsive.css'); ?>
	<?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/css/animate.css'); ?>
	<?= link_tag('themes/admin/' . $sts_admin_layout_theme . '/third/font-awesome/css/font-awesome.min.css'); ?>
    <noscript><h3> <?=lang('please_enable_javascript') ?></h3>
    <meta HTTP-EQUIV="refresh" content=0;url="<?=base_folder_path('javascript_required') ?>"></noscript>
	<link rel="shortcut icon" href="<?= base_url() ?>favicon.ico" type="image/x-icon"/>
</head>
<body id="login-body">
<div class="container">
	<div class="full-content-center animated fadeIn">
		<div class="login-wrap">
			<?php if ($this->input->get('timer_expired')): ?>
				<div class="alert alert-danger animated shake capitalize"><?= lang('session_expired') ?></div>
			<?php endif; ?>
			<?php if ($this->session->error): ?>
				<?= alert('error', $this->session->error, 'error', FALSE) ?>
			<?php endif; ?>
			<div class="box-info capitalize text-center">
                <?php if (empty($layout_design_admin_logo)): ?>
                <h3><i class="fa fa-lock"></i> <?=lang('admin_login')?></h3>
				<?php else: ?>
                <?= img($layout_design_admin_logo, '', 'class=login-logo') ?>
				<?php endif; ?>
                <hr/>
				<?= form_open(admin_url('login'), 'role="form" id="validate-form"') ?>
				<div
					class="form-group login-input <?php if ($this->session->flashdata('error_msg')): ?> has-error <?php endif; ?>">
					<i class="fa fa-sign-in overlay"></i>
					<input name="<?= $admin_login_username_field ?>" type="text" id="username"
					       class="form-control text-input" placeholder="<?= lang('username') ?>" required/>
				</div>
				<div
					class="form-group login-input <?php if ($this->session->flashdata('error_msg')): ?> has-error <?php endif; ?>">
					<i class="fa fa-key overlay"></i>
					<input name="<?= $admin_login_password_field ?>" type="password" id="password"
					       class="form-control text-input" placeholder="<?= lang('password') ?>" required/>
				</div>
				<?php if (!empty($languages) && count($languages) > 1): ?>
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
                            <?= form_dropdown('language', $languages, $sts_admin_default_language, 'class="form-control text-capitalize"') ?>
						</div>
					</div>
				</div>
				<?php else: ?>
					<?=form_hidden('language', $sts_admin_default_language)?>
				<?php endif; ?>
				<?php if (config_enabled('sts_form_enable_admin_login_captcha')): ?>
					<div class="row">
						<div class="col-sm-10 col-sm-offset-1">
							<div class="g-recaptcha text-center" data-size="normal" data-sitekey="<?= $sts_form_captcha_key ?>"></div>
						</div>
					</div>
					<br />
				<?php endif; ?>
				<div class="row">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary btn-lg btn-block"><i
								class="fa fa-lock"></i> <?= lang('login') ?></button>
					</div>
				</div>
				<?= form_hidden('page_redirect', $page_redirect) ?>
				</form>
			</div>
			<p class="text-center">
				<a href="<?= base_url(ADMIN_LOGIN . '/reset_password') ?>"><?= i('fa fa-question-circle') ?> <?= lang('forgot_password') ?></a>
            </p>
		</div>
	</div>
</div>
<script src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/js/jquery.js') ?>"></script>
<script src="<?= base_url('themes/admin/' . $sts_admin_layout_theme . '/js/bootstrap.js') ?>"></script>
<script src="<?= base_url('js/jquery.validate.js') ?>"></script>
<?php if (config_enabled('sts_form_enable_admin_login_captcha')): ?>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<script>
    $("#validate-form").validate({
        rules: {
            // simple rule, converted to {required:true}
            <?=$admin_login_username_field?>: {
                required: true,
                minlength: <?=$min_admin_username_length?>,
                maxlength: <?=$max_admin_username_length?>
            },
            <?=$admin_login_password_field?>: {
                required: true,
                minlength: 6,
                maxlength: 20
            }
        },
        messages: {
	        <?=$admin_login_username_field?>: {
                required: "<?=lang('username_required')?>"

            },
	        <?=$admin_login_password_field?>: {
                required: "<?=lang('password_required')?>"
            }
        }
    });
</script>
</body>
</html><!-- Version <?= APP_VERSION ?> -->