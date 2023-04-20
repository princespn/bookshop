<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('', 'id="form" class="form-horizontal"') ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title"><?= i('fa fa-pencil') ?> <?= lang(CONTROLLER_FUNCTION . '_' . singular(CONTROLLER_CLASS)) ?></h2>
    </div>
    <div class="col-md-7 text-right">
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <?php if ($row['admin_id'] > 1): ?>
                <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $row['admin_id']) ?>" data-toggle="modal"
                   data-target="#confirm-delete" href="#"
                   class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= admin_url(CONTROLLER_CLASS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                class="hidden-xs"><?= lang('view_administrators') ?></span></a>
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
        <?php endif; ?>
    </div>
</div>
<hr/>
<div class="box-info">
<div class="row">
<div class="col-md-2 text-center">
    <div class="thumbnail hidden-sm">
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <?= photo(CONTROLLER_METHOD, $row, 'image', FALSE, 'image-' . $row['admin_id']) ?>
        <?php else: ?>
            <?= img(TPL_DEFAULT_ADMIN_PHOTO) ?>
        <?php endif; ?>
        <div class="caption text-capitalize">
            <?php if (CONTROLLER_FUNCTION == 'update'): ?>
                <hr/>
                <h4><?= lang('update_admin_photo') ?></h4>
                <div class="form-group">
                    <a class="iframe btn btn-default btn-sm"
                       href="<?= base_url() ?>filemanager/dialog.php?type=1&akey=<?= $file_manager_key ?>&field_id=<?= $row['admin_id'] ?>"><?= lang('upload_photo') ?></a>
                    <input type="hidden" name="photo" value="<?= $row['photo'] ?>" id="<?= $row['admin_id'] ?>"/>
                </div>
                <hr/>
                <?php if (!empty($row['last_login_ip'])): ?>
                    <p class="text-center">
                        <?= lang('last_login_ip') ?><br/>
                        <a class="btn btn-default btn-sm"
                           href="<?=EXTERNAL_IP_LOOKUP?><?= $row['last_login_ip'] ?>" target="_blank">
                            <?= $row['last_login_ip'] ?>
                        </a>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <h4><?= lang('add_administrator') ?></h4>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="col-md-10">
    <ul class="nav nav-tabs text-capitalize" role="tablist">
        <li class="active"><a href="#edit" role="tab" data-toggle="tab"><?= lang('edit_details') ?></a></li>
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <li><a href="#alerts" role="tab" data-toggle="tab"><?= lang('admin_alerts') ?></a></li>
	    <?php if ( $row['admin_id'] != '1'): ?>
            <li><a href="#restrictions" role="tab" data-toggle="tab"><?= lang('restrictions') ?></a></li>
        <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="edit">
            <div class="hidden-xs">
                <?php if (CONTROLLER_FUNCTION == 'update'): ?>
                    <?php if (!empty($row['last_login_date'])): ?>
                    <?php if ($row['last_login_date'] != '0000-00-00 00:00:00'): ?>
                        <a class="btn btn-default pull-right btn-sm disabled"><?= lang('last_login_date') ?>:
                            <?= display_date($row['last_login_date'], true) ?></a>
                    <?php endif; ?>
                    <?php endif; ?>
                    <h3 class="header capitalize"><?= $row['fname'] ?> <?= $row['lname'] ?> </h3>
                    <h6><?= i('fa fa-envelope') ?> <?= $row['primary_email'] ?></h6>
                <?php else: ?>
                    <h3 class="header"><?= lang('new_admin_details') ?></h3>
                <?php endif; ?>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('status', 'status', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?php if ($row['admin_id'] != 1 OR CONTROLLER_FUNCTION == 'add'): ?>
                        <?= form_dropdown('status', array('active' => lang('active'), 'inactive' => lang('inactive')), $row['status'], 'class="form-control"') ?>
                    <?php else: ?>
                        <?= lang('active', 'active', array('class' => 'control-label')) ?>
                        <?= form_hidden('status', 'active') ?>
                    <?php endif; ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('first_name', 'fname', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_input('fname', set_value('fname', $row['fname']), 'class="' . css_error('fname') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('last_name', 'lname', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_input('lname', set_value('lname', $row['lname']), 'class="' . css_error('lname') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('username', 'username', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_input('username', set_value('username', $row['username']), 'id="username" class="' . css_error('username') . ' form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('primary_email', 'primary_email', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_input('primary_email', set_value('primary_email', $row['primary_email']), 'class="' . css_error('primary_email') . ' form-control required email"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('admin_group', 'admin_group_id', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?php if ($row['admin_id']  == 1): ?>
                        <p class="form-control"><?= lang('root_administrator') ?></p>
                    <?php else: ?>
                        <?= form_dropdown('admin_group_id', $admin_groups, $row['admin_group_id'], 'class="form-control"') ?>
                    <?php endif; ?>
                </div>
            </div>
            <hr/>
            <div class="form-group meter">
                <?= lang('password', 'apassword', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_password('apassword', $this->input->post('apassword'), 'id="apassword" class="' . css_error('apassword') . ' form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('confirm_password', 'passconf', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_password('passconf', $this->input->post('passconf'), 'class="' . css_error('passconf') . ' form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('rows_per_page', 'rows_per_page', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_dropdown('rows_per_page', options('db_select_page_rows'), $row['rows_per_page'], 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <?= lang('admin_home_page', 'admin_home_page', array('class' => 'col-md-3 control-label')) ?>
                <div class="col-md-5">
                    <?= form_dropdown('admin_home_page', options('admin_home_page_redirect'), $row['admin_home_page'], 'class="form-control"') ?>
                </div>
            </div>
        </div>
        <?php if (CONTROLLER_FUNCTION == 'update'): ?>
            <div class="tab-pane fade in" id="alerts">
                <h3 class="header"> <?= lang('administrative_alerts') ?></h3>
                <h6><?= lang('administrative_alerts_desc') ?></h6>
                <hr/>
	            <?php if (!empty($admin_alerts)): ?>
                <?php foreach ($admin_alerts as $v): ?>
		            <div class="form-group">
			            <?= lang($v, $v, array('class' => 'col-md-4 control-label')) ?>
			            <div class="col-md-4">
				            <?= form_dropdown($v, bool('yes', 'no'), set_value($v, $row[$v]), 'class="form-control"') ?>
			            </div>
		            </div>
		            <hr/>
	            <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php if ( $row['admin_id'] != '1'): ?>
        <div class="tab-pane fade in" id="restrictions">
            <h3 class="header"> <?= lang('restrictions') ?></h3>
            <hr />
            <div class="form-group">
		        <?= lang('show_assigned_tickets_only', 'show_assigned_tickets_only', array('class' => 'col-md-4 control-label')) ?>
                <div class="col-md-4">
			        <?= form_dropdown('show_assigned_tickets_only', bool('yes', 'no'), set_value('show_assigned_tickets_only', $row['show_assigned_tickets_only']), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if (CONTROLLER_FUNCTION == 'update'): ?>
        <?= form_hidden('admin_id', $row['admin_id']) ?>
    <?php endif; ?>
    <nav class="navbar navbar-fixed-bottom  save-changes">
        <div class="container text-right">
            <div class="row">
                <div class="col-md-12">
                    <?php if (CONTROLLER_FUNCTION == 'create'): ?>
                        <input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
                               class="btn btn-success navbar-btn block-phone"/>
                    <?php endif; ?>
                    <button class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                            type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                </div>
            </div>
        </div>
    </nav>
</div>
</div>
</div>
<?= form_close() ?>
<br/>
<!-- Load JS for Page -->
<script>
    $("#form").validate(
        {
            rules: {
                username: {
                    required: true,
                    minlength: 6,
                    maxlength: 20
                },

                apassword: {
                    <?php if (CONTROLLER_FUNCTION == 'create'): ?>
                    required: true,
                    <?php endif; ?>
                    minlength: 8,
                    maxlength: 30
                },
                passconf: {
                    <?php if (CONTROLLER_FUNCTION == 'create'): ?>
                    required: true,
                    <?php endif; ?>
                    equalTo: "#apassword"
                }
            }
        }
    );
    <?php if (TPL_ADMIN_PASSWORD_METER == true): ?>
    $(document).ready(function () {
        var options = {};
        options.common = {
	        minChar: 8,
	        usernameField: '#username'
        };
        options.rules = {
            activated: {
                wordNotEmail: -100,
                wordLength: -50,
                wordSimilarToUsername: -100,
                wordSequences: -20,
                wordTwoCharacterClasses: 2,
                wordRepetitions: -25,
                wordLowercase: 1,
                wordUppercase: 3,
                wordOneNumber: 3,
                wordThreeNumbers: 5,
                wordOneSpecialChar: 3,
                wordTwoSpecialChar: 5,
                wordUpperLowerCombo: 2,
                wordLetterNumberCombo: 2,
                wordLetterNumberCharCombo: 2
            }
        };
        options.ui = {
            showVerdictsInsideProgressBar: true,
	        progressBarEmptyPercentage: 20,
	        progressBarMinPercentage: 20
        };
        $('#apassword').pwstrength(options);
    });
    <?php endif; ?>
</script>