<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (!empty($errors)): ?>
	<?= alert('error', $errors) ?>
<?php endif; ?>
<form action="" method="get" role="form" id="form" class="form" accept-charset="utf-8">
    <div class="card">
        <div class="card-body">
            <h3><?= i('fa fa-cog') ?> <?= lang('domain_information') ?></h3>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label"><?= lang('domain_name') ?></label>
                <div class="col-sm-9">
                    <input type="text"  name="base_domain" value="<?= set_value('base_domain', $base_domain) ?>"
                           class="form-control required <?= css_error('base_domain') ?>"
                           placeholder="your domain.com without the 'www'">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('sub_domain') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="base_sub_domain"
                           value="<?= set_value('base_sub_domain', $base_sub_domain) ?>"
                           class="form-control required <?= css_error('base_sub_domain') ?>"
                           placeholder="www">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('base_folder_path') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="base_folder_path"
                           value="<?= set_value('base_folder_path', $base_folder_path) ?>"
                           class="form-control <?= css_error('base_folder_path') ?>"
                           placeholder="<?= lang('base_folder_path_desc') ?>">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('system_email') ?></label>
                <div class="col-sm-9">
                    <input type="email"   name="system_email" value="<?= set_value('system_email') ?>"
                           class="form-control required email <?= css_error('system_email') ?>"
                           placeholder="system@yourdomain.com">
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h3><?= i('fa fa-database') ?> <?= lang('database_information') ?></h3>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('database_server') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="db_hostname" value="<?= set_value('db_hostname') ?>"
                           class="form-control required <?= css_error('db_hostname') ?>"
                           placeholder="<?= lang('enter_host_name') ?>">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('database_name') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="db_database" value="<?= set_value('db_database') ?>"
                           class="form-control required <?= css_error('db_database') ?>"
                           placeholder="enter the name of your database">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('database_username') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="db_username" value="<?= set_value('db_username') ?>"
                           class="form-control required <?= css_error('db_username') ?>"
                           placeholder="enter the username for your database">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('database_password') ?></label>
                <div class="col-sm-9">
                    <input type="password"   name="db_password" value="<?= set_value('db_password') ?>"
                           class="form-control required <?= css_error('db_password') ?>"
                           placeholder="enter the password for your database">
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h3><?= i('fa fa-user') ?> <?= lang('admin_information') ?></h3>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_first_name') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="admin_first_name" value="<?= set_value('admin_first_name') ?>"
                           class="form-control required <?= css_error('username') ?>"
                           placeholder="<?=lang('your_first_name')?>">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_last_name') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="admin_last_name" value="<?= set_value('admin_last_name') ?>"
                           class="form-control required <?= css_error('admin_last_name') ?>"
                           placeholder="<?=lang('your_last_name')?>">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_username') ?></label>
                <div class="col-sm-9">
                    <input type="text"   name="admin_username" minlength="6" value="<?= set_value('admin_username') ?>"
                           class="form-control required <?= css_error('username') ?>"
                           placeholder="username for your admin account - minimum of 6 characters">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_password') ?></label>
                <div class="col-sm-9">
                    <input type="password"   name="admin_password" minlength="8"
                           value="<?= set_value('admin_password') ?>"
                           class="form-control required <?= css_error('password') ?>"
                           placeholder="password for your admin account - minimum of 8 characters">
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-3 form-label"><?= lang('admin_email') ?></label>
                <div class="col-sm-9">
                    <input type="email"   name="admin_email" value="<?= set_value('admin_email') ?>"
                           class="form-control required <?= css_error('admin_email') ?>"
                           placeholder="admin@yourdomain.com">
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
	<?=form_hidden('lang', $lang)?>
	<?= form_hidden('step', 'configuration') ?>
	<?= form_hidden('process', '1') ?>
	<?php if ($this->input->post_get('script_timeout')): ?>
		<?= form_hidden('script_timeout', $this->input->post_get('script_timeout')) ?>
	<?php endif; ?>
	<?php if ($this->input->post_get('debug_level')): ?>
		<?= form_hidden('debug_level', $this->input->post_get('debug_level')) ?>
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