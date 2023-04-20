<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (!empty($errors)): ?>
	<?= alert('error', $errors) ?>
<?php endif; ?>
<form action="" method="get" role="form" id="form" class="form-horizontal" accept-charset="utf-8">
    <div class="card">
        <div class="card-body">
            <h3><?= i('fa fa-user') ?> <?= lang('admin_information') ?></h3>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_first_name') ?></label>
                <div class="col-sm-9">
                    <input type="text" name="admin_first_name" value="<?= set_value('admin_first_name') ?>"
                           class="form-control required <?= css_error('username') ?>"
                           placeholder="first name of your admin account">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_last_name') ?></label>
                <div class="col-sm-9">
                    <input type="text" name="admin_last_name" value="<?= set_value('admin_last_name') ?>"
                           class="form-control required <?= css_error('admin_last_name') ?>"
                           placeholder="last name of your admin account">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_username') ?></label>
                <div class="col-sm-9">
                    <input type="text" name="admin_username" minlength="6" value="<?= set_value('admin_username') ?>"
                           class="form-control required <?= css_error('username') ?>"
                           placeholder="username for your admin account - minimum of 6 characters">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_password') ?></label>
                <div class="col-sm-9">
                    <input type="password" name="admin_password" minlength="8"
                           value="<?= set_value('admin_password') ?>"
                           class="form-control required <?= css_error('password') ?>"
                           placeholder="password for your admin account - minimum of 8 characters">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_email') ?></label>
                <div class="col-sm-9">
                    <input type="text" name="admin_email" value="<?= set_value('admin_email') ?>"
                           class="form-control required email <?= css_error('admin_email') ?>"
                           placeholder="your admin email address">
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12">
                    <button id="continue"
                            class="btn btn-primary btn-lg btn-block"><span id="wait">
                <i class="fa fa-caret-right"></i> <?= lang('click_here_to_continue') ?></span></button>
                </div>
            </div>
        </div>
    </div>
	<?=form_hidden('base_domain', $base_domain)?>
	<?=form_hidden('base_sub_domain', $base_sub_domain)?>
	<?=form_hidden('base_folder_path', $base_folder_path)?>
	<?=form_hidden('db_hostname', $db_host)?>
	<?=form_hidden('db_database', $db_name)?>
	<?=form_hidden('db_username', $db_user)?>
	<?=form_hidden('db_password', $db_password)?>
	<?=form_hidden('cron_key', $cron_key)?>
	<?=form_hidden('system_email', $system_email)?>
	<?= form_hidden('step', 'configuration') ?>
	<?= form_hidden('process', '1') ?>
    <?php if ($this->input->post_get('script_timeout')): ?>
	    <?= form_hidden('script_timeout', $this->input->post_get('script_timeout')) ?>
    <?php endif; ?>
</form>
<script>
    $("#form").validate({
        submitHandler: function(form) {
            form.submit();
            $('#continue').addClass('disabled');
            $('#wait').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('please_wait_while_your_system_is_setup')?>');
        }
    });

</script>