<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (!empty($errors)): ?>
	<?= alert('error', $errors) ?>
<?php endif; ?>
<form action="" method="get" role="form" id="form" class="form-horizontal" accept-charset="utf-8">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
	                <?php if (!empty($requirements['errors'])): ?>
                        <div class="alert alert-danger">
                            <h3><?= i('fa fa-cog') ?> <?= lang('system_errors') ?></h3>
                            <hr/>
			                <?= $requirements['errors'] ?>
                        </div>
	                <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
					<?php if (!empty($requirements['errors'])): ?>
                        <p class="lead text-danger text-center"><?= i('fa fa-times-circle') ?> <?= lang('please_fix_errors_to_continue') ?></p>
                        <p><a href="#" onclick="window.location.reload()" id="continue"
                              class="btn btn-danger btn-lg btn-block"><?= i('fa fa-refresh') ?> <?= lang('refresh_this_page') ?></a>
                        </p>
                        <p class="lead text-center">
                        <button id="continue"
                                class="btn btn-info btn-lg btn-block"><?= i('fa fa-caret-right') ?> <?= lang('try_install_anyway') ?></button>
					<?php else: ?>
                        <h3><?= i('fa fa-cog') ?> <?= lang('cpanel_info') ?></h3>
                        <hr/>
                        <div class="form-group row">
                            <label class="col-sm-3 form-label"><?= lang('domain_name') ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="base_domain"
                                       value="<?= set_value('base_domain', $base_domain) ?>"
                                       class="form-control required <?= css_error('base_domain') ?>"
                                       placeholder="your domain.com without the 'www'">
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <label class="col-sm-3 form-label"><?= lang('cpanel_username') ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="cpanel_username"
                                       value="<?= set_value('cpanel_username', $this->input->get('cpanel_username')) ?>"
                                       class="form-control required <?= css_error('cpanel_username') ?>"
                                       placeholder="username">
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <label class="col-sm-3 form-label"><?= lang('cpanel_password') ?></label>
                            <div class="col-sm-9">
                                <input type="password" name="cpanel_password"
                                       value="<?= set_value('cpanel_password', $this->input->get('cpanel_password')) ?>"
                                       class="form-control required <?= css_error('cpanel_password') ?>"
                                       placeholder="cpanel_password">
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row">
                            <label class="col-sm-3 form-label"><?= lang('your_email') ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="system_email"
                                       value="<?= set_value('system_email', $this->input->get('system_email')) ?>"
                                       class="form-control required email <?= css_error('system_email') ?>"
                                       placeholder="you@domain.com">
                            </div>
                        </div>
                        <hr />
                        <button id="continue"
                                class="btn btn-primary btn-lg btn-block"><?= i('fa fa-caret-right') ?> <?= lang('click_here_to_continue') ?></button>
					<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
	<?= form_hidden('lang', $lang) ?>
	<?= form_hidden('step', 'requirements') ?>
	<?=form_hidden('base_folder_path', $base_folder_path)?>
	<?= form_hidden('process', '1') ?>
</form>
<script>
    <?php if ($this->input->get('auto_submit')): ?>
    $(function() {
        $('#form').submit();
    });
    <?php endif; ?>
    $("#form").validate({
        submitHandler: function(form) {
            form.submit();
            $('#continue').addClass('disabled');
            $('#wait').html('<?=i('fa fa-spinner fa-spin')?> <?=lang('please_wait')?>');
        }
    });
</script>
