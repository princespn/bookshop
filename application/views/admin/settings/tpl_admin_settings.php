<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'role="form" id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-4">
		<?= generate_sub_headline(lang('global_settings'), 'fa fa-cogs', '', FALSE) ?>
    </div>
    <div class="col-md-8 text-right">
        <a href="<?= admin_url('addresses') ?>" class="btn btn-primary"><?= i('fa fa-home') ?>
            <span class="hidden-xs"><?= lang('store_addresses') ?></span></a>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-9">
        <div class="box-info" id="settings-div">
            <ul id="main-tabs" class="resp-tabs nav nav-tabs responsive text-capitalize">
				<?php foreach ($settings['menu'] as $k => $v): ?>
					<?php if ($k == 'site'): ?>
                        <li class="active"><a href="#<?= $k ?>-tab" data-toggle="tab"><?= lang($k) ?></a></li>
					<?php else: ?>
                        <li><a href="#<?= $k ?>-tab" data-toggle="tab"><?= lang($k) ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
            </ul>
            <div class="tab-content responsive">
				<?php foreach ($settings['menu'] as $k => $v): ?>
                    <div id="<?= $k ?>-tab" class="tab-pane <?php if ($k == 'site'): ?>active<?php endif; ?>">
						<?php if (is_array($v)): ?>
                            <br/>
                            <ul class="tab-collapse nav nav-tabs responsive text-capitalize">
								<?php $i = 0; ?>
								<?php foreach ($v as $a => $b): ?>
									<?php if ($i == 0): ?>
                                        <li class="active"><a href="#<?= $a ?>-tab"
                                                              data-toggle="tab"><?= lang($a) ?></a>
                                        </li>
									<?php else: ?>
										<?php if ($a == 'kb'): ?>
											<?php if (!config_enabled('enable_section_kb_articles')): ?>
												<?php continue; ?>
											<?php endif; ?>
										<?php endif; ?>
										<?php if ($a == 'forum'): ?>
											<?php if (!config_enabled('enable_section_forum_topics')): ?>
												<?php continue; ?>
											<?php endif; ?>
										<?php endif; ?>
										<?php if ($a == 'network_marketing'): ?>
											<?php if (!config_enabled('layout_enable_forced_matrix')): ?>
												<?php continue; ?>
											<?php endif; ?>
										<?php endif; ?>
                                        <li><a href="#<?= $a ?>-tab" data-toggle="tab"><?= lang($a) ?></a></li>
									<?php endif; ?>
									<?php $i++; ?>
								<?php endforeach; ?>
                            </ul>
                            <div class="tab-content">
								<?php $i = 0; ?>
								<?php foreach ($v as $a => $c): ?>
									<?php if ($a == 'kb'): ?>
										<?php if (!config_enabled('enable_section_kb_articles')): ?>
											<?php continue; ?>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($a == 'forum'): ?>
										<?php if (!config_enabled('enable_section_forum_topics')): ?>
											<?php continue; ?>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($a == 'network_marketing'): ?>
										<?php if (!config_enabled('layout_enable_forced_matrix')): ?>
											<?php continue; ?>
										<?php endif; ?>
									<?php endif; ?>
                                    <div id="<?= $a ?>-tab"
                                         class="tab-pane <?php if ($i == 0): ?>active<?php endif; ?>">
                                        <hr/>
										<?php if ($a == 'cron_job'): ?>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">
													<?= lang('cron_job_run_every_15_minutes') ?></label>
                                                <div class="col-md-8">
                                                    <p class="form-control">curl
                                                        -s <?= site_url('cron/run/' . $sts_cron_password_key) ?>
                                                        &gt;&gt;
                                                        /dev/null 2&gt;&1 </p>
                                                    <p>
														<?= anchor_popup(base_url('cron/run/' . $sts_cron_password_key), i('fa fa-cog') . ' ' . lang('run_cron_job_manually'), array('class' => 'btn btn-warning btn-sm')) ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <hr/>
											<?php if (!empty($settings['config'][$c])): ?>
												<?php foreach ($settings['config'][$c] as $b): ?>
                                                    <div class="form-group"><label class="col-md-4 control-label">
												<span class="tip" data-toggle="tooltip" data-placement="bottom"
                                                      title="<?= lang($b['key'] . '_description') ?>">
                                                    <?= i('fa fa-question-circle') ?>
                                                    <?= lang($b['key']) ?></span></label>
                                                        <div class="col-md-6">
															<?= generate_settings_field($b, $b['value']) ?>
                                                        </div>
                                                    </div>
                                                    <hr/>
												<?php endforeach; ?>
											<?php endif; ?>
                                            <hr/>
                                            <h5><?= lang('cron_tasks') ?> - <?= lang('uncheck_to_disable') ?></h5>
                                            <hr/>
											<?php foreach ($cron_timers as $j): ?>
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <a href="<?= admin_url('update_status/table/' . TBL_TIMERS . '/type/status/key/id/id/' . $j['id']) ?>"
                                                               class="btn btn-default btn-sm <?= is_disabled('update', TRUE) ?>">
																<?= set_status($j['status']) ?>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-10"><?= lang($j['name']) ?></div>
                                                    </div>
                                                    <br/>
                                                </div>
											<?php endforeach ?>
										<?php elseif ($a == 'information'): ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label"><?= lang('php_version') ?></label>

                                                <div class="col-sm-5">
                                                    <p class="form-control-static">
                                                        <a class="iframe"
                                                           href="<?= admin_url('settings/view_phpinfo') ?>"><?= phpversion() ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group">
                                                <label
                                                        class="col-sm-3 control-label"><?= lang('mysql_version') ?></label>

                                                <div class="col-sm-5">
                                                    <p class="form-control-static"><?= $this->db->version() ?></p>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group">
                                                <label
                                                        class="col-sm-3 control-label"><?= lang('codeiginiter_version') ?></label>
                                                <div class="col-sm-5">
                                                    <p class="form-control-static"><?= CI_VERSION ?></p>
                                                </div>
                                            </div>
                                            <hr/>
										<?php else: ?>
											<?php if (!empty($settings['config'][$c])): ?>
												<?php foreach ($settings['config'][$c] as $b): ?>
                                                    <div class="form-group"><label class="col-md-4 control-label">
												<span class="tip" data-toggle="tooltip" data-placement="bottom"
                                                      title="<?= lang($b['key'] . '_description') ?>">
                                                     <?= i('fa fa-question-circle') ?>
                                                     <?= lang($b['key']) ?></span>
                                                        </label>
                                                        <div class="col-md-6">
															<?= generate_settings_field($b, $b['value']) ?>
                                                        </div>
                                                    </div>
                                                    <hr/>
													<?php if ($a == 'database'): ?>
                                                        <div class="form-group">
                                                            <label
                                                                    class="col-sm-4 control-label"><?= lang('database_host') ?></label>
                                                            <div class="col-sm-6">
                                                                <div class="form-control-static">
																	<span
                                                                            class="text-muted"><?= $this->db->hostname ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr/>
                                                        <div class="form-group">
                                                            <label
                                                                    class="col-sm-4 control-label"><?= lang('database_name') ?></label>
                                                            <div class="col-sm-6">
                                                                <div class="form-control-static">
																	<span class="text-muted"> <?= $this->db->database ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr/>
													<?php endif; ?>
												<?php endforeach; ?>
												<?php if ($a == 'system_settings'): ?>
                                                    <div class="form-group">
                                                        <label
                                                                class="col-md-4 control-label"><?= lang('current_debug_level') ?></label>
                                                        <div class="col-md-6">
                                                            <div class="form-control-static">
																<?php if (is_writable(APPPATH . '/config/debug.php')): ?>
                                                                    <a href="<?= admin_url('settings/set_debug/' . ENVIRONMENT) ?>"
                                                                       class="btn btn-sm btn-default">
																		<?= ENVIRONMENT ?></a>
																<?php else: ?>
																	<?= ENVIRONMENT ?>
																<?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr/>
												<?php endif; ?>
											<?php endif; ?>
										<?php endif; ?>
                                    </div>
									<?php $i++; ?>
								<?php endforeach; ?>
                            </div>
						<?php else: ?>
                            <hr/>
							<?php foreach ($settings['config'][$v] as $b): ?>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">
										<span class="tip" data-toggle="tooltip" data-placement="bottom"
                                              title="<?= lang($b['key'] . '_description') ?>"><?= lang($b['key']) ?></span></label>
                                    <div class="col-md-4">
										<?= generate_settings_field($b, $b['value']) ?>
                                    </div>
                                </div>
                                <hr/>
							<?php endforeach; ?>
						<?php
						endif; ?>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
    </div>
	<?= settings_sidebar() ?>
</div>
<nav class="navbar navbar-fixed-bottom save-changes">
    <div class="container text-right">
        <div class="row">
            <div class="col-md-12">
                <a id="generate_code" class="btn btn-default" class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>">
	                <?= i('fa fa-lock') ?> <?= lang('generate_api_keys') ?>
                </a>
                <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                        id="update-button" type="submit">
					<?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
            </div>
        </div>
    </div>
</nav>
<?= form_close() ?>
<script>
    $('#generate_code').click(function () {
        $('#sts_site_api_key').val(uuidv4());
        $('#sts_site_api_token').val(uuidv4());
    });

    function uuidv4() {
        return '<?=str_repeat('x', 40)?>'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=current_url()?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form').serialize(),
                beforeSend: function () {
                    $('#update-button').button('loading');
                },
                complete: function () {
                    $('#update-button').button('reset');
                },
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        $('#response').html('<?=alert('success')?>');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);

                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                        if (response['error_fields']) {
                            $.each(response['error_fields'], function (key, val) {
                                $('#' + key).addClass('error');
                                $('#' + key).focus();
                            });
                        }
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
</script>
