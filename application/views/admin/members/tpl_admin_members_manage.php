<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="sub-header block-title">
			<?= i('fa fa-pencil') ?> <?= lang('update_user') ?>
        </h2>
    </div>
    <div class="col-md-7 text-right">
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('left', CONTROLLER_METHOD, $row['prev']) ?>
            <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete/' . $id) ?>" data-toggle="modal"
               data-target="#confirm-delete" href="#"
               class="md-trigger btn btn-danger <?= is_disabled('delete') ?>"><?= i('fa fa-trash-o') ?> <span
                        class="hidden-xs"><?= lang('delete') ?></span></a>
		<?php endif; ?>
        <a href="<?= admin_url(TBL_MEMBERS) ?>" class="btn btn-primary"><?= i('fa fa-search') ?>
            <span class="hidden-xs"><?= lang('view_contacts') ?></span></a>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= prev_next_item('right', CONTROLLER_METHOD, $row['next']) ?>
		<?php endif; ?>
    </div>
</div>
<hr/>
<div class="row">
    <div>
		<?= form_open_multipart('', 'role="form" id="form" class="form-horizontal"') ?>
        <div class="col-lg-2 text-center">
            <div class="thumbnail hidden-sm">
                <h3 class="text-capitalize"><span class="fname"><?= $row['fname'] ?></span></h3>
                <div class="profile-userpic">
                    <a class='iframe'
                       href="<?= base_url() ?>filemanager/dialog.php?fldr=members&type=1&akey=<?= $file_manager_key ?>&field_id=<?= $id ?>">
						<?= photo(CONTROLLER_METHOD, $row, 'image img-thumbnail', FALSE, 'image-' . $id) ?>
                    </a>
                    <br/>
                    <h6 class="text-capitalize"><span class="position"><?= $row['position'] ?></span></h6>
                </div>
                <div class="caption member-social-icons">
                    <hr/>
                    <p>
						<?php if ($row['facebook_id']): ?>
                            <a href="//facebook.com/<?= $row['facebook_id'] ?>" target="_blank"
                               class="btn btn-facebook">
								<?= i('fa fa-facebook') ?>
                            </a>
						<?php endif; ?>
						<?php if ($row['twitter_id']): ?>
                            <a href="//twitter.com/<?= $row['twitter_id'] ?>" target="_blank"
                               class="btn btn-twitter">
								<?= i('fa fa-twitter') ?>
                            </a>
						<?php endif; ?>
						<?php if ($row['linked_in_id']): ?>
                            <a href="//linkedin.com/in/<?= $row['linked_in_id'] ?>" target="_blank"
                               class="btn btn-linkedin">
								<?= i('fa fa-linkedin') ?>
                            </a>
						<?php endif; ?>
						<?php if ($row['instagram_id']): ?>
                            <a href="//instagram.com/<?= $row['instagram_id'] ?>" target="_blank"
                               class="btn btn-instagram ">
								<?= i('fa fa-instagram') ?>
                            </a>
						<?php endif; ?>
						<?php if ($row['pinterest_id']): ?>
                            <a href="//www.pinterest.com/<?= $row['pinterest_id'] ?>" target="_blank"
                               class="btn btn-pinterest ">
								<?= i('fa fa-pinterest') ?>
                            </a>
						<?php endif; ?>
						<?php if ($row['youtube_id']): ?>
                            <a href="//youtube.com/<?= $row['youtube_id'] ?>" target="_blank"
                               class="btn btn-youtube">
								<?= i('fa fa-youtube') ?>
                            </a>
						<?php endif; ?>
						<?php if ($row['tumblr_id']): ?>
                            <a href="//<?= $row['tumblr_id'] ?>.tumblr.com" target="_blank"
                               class="btn btn-tumblr">
								<?= i('fa fa-tumblr') ?>
                            </a>
						<?php endif; ?>
                    </p>
                    <p>
                        <a href="#" class="btn btn-default btn-block <?= is_disabled('update') ?>" role="button" data-toggle="modal"
                           data-target="#reset_password"><?= i('fa fa-unlock') ?> <?= lang('reset_member_password') ?></a>
                    </p>
                </div>

            </div>
        </div>
        <div class="col-lg-7">
            <div class="box-info">
                <ul class="resp-tabs nav nav-tabs responsive text-capitalize" role="tablist">
                    <li class="active"><a href="#overview" role="tab" data-toggle="tab"><?= i('fa fa-user') ?>
                            <span class="hidden-xs"><?= lang('overview') ?></span></a></li>
                    <li><a href="#profile" role="tab" data-toggle="tab"><?= i('fa fa-edit') ?>
                            <span class="hidden-xs"><?= lang('profile') ?></span></a></li>
                    <li><a href="#addresses" role="tab" data-toggle="tab"><?= i('fa fa-map-marker') ?>
                            <span class="hidden-xs"><?= lang('addresses') ?></span></a>
                    </li>
					<?php if (config_enabled('affiliate_marketing') && $row['is_affiliate'] == 1): ?>
                        <li><a href="#affiliate" role="tab" data-toggle="tab"><?= i('fa fa-thumbs-o-up') ?>
                                <span class="hidden-xs"><?= lang('affiliate') ?></span></a></li>
					<?php endif; ?>
                    <li><a href="#groups" role="tab" data-toggle="tab"><?= i('fa fa-group') ?>
                            <span class="hidden-xs"><?= lang('groups') ?></span></a></li>
					<?php if (!empty($custom_fields)): ?>
                        <li><a href="#custom" role="tab" data-toggle="tab"><?= i('fa fa-cogs') ?>
                                <span class="hidden-xs"><?= lang('custom') ?></span></a></li>
					<?php endif; ?>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="overview">
						<?php if ($row['status'] == 1): ?>
                            <span class="pull-right hidden-xs">
                                <a href="<?= admin_url() ?>members/login_member/<?= $id ?>" target="member-login"
                                   class="btn btn-primary btn-sm" role="button">
	                                <?= i('fa fa-key') ?> <?= lang('login_to_members_area') ?></a>
                            </span>
                            <span class="pull-right hidden-xs">
                                <a href="<?= admin_url('members/send_login_details/' . $id) ?>"
                                   class="btn btn-default btn-sm" role="button">
	                                <?= i('fa fa-unlock') ?> <?= lang('send_login_details') ?></a>&nbsp;
                            </span>
						<?php else: ?>
                            <span class="pull-right hidden-xs">
                               <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/status/key/member_id/id/' . $id) ?>"
                                  class="btn btn-sm btn-warning" role="button">
	                               <?= i('fa fa-key') ?> <?= lang('activate_user') ?></a>
                             </span>
						<?php endif; ?>
                        <h1 class="header"><span class="fname"><?= $row['fname'] ?></span> <span
                                    class="lname"><?= $row['lname'] ?></span></h1>

                        <div class="row">
                            <div class="col-md-8">
                                <h6><?= i('fa fa-envelope') ?>
                                    <span class="primary_email"><?= $row['primary_email'] ?></span>
                                </h6>
								<?php if (config_enabled('affiliate_marketing') && $row['is_affiliate'] == 1): ?>
                                    <h6><?= i('fa fa-globe') ?>
                                        <span class="affiliate_url"><?= affiliate_url($row['username']) ?></span>
                                    </h6>
									<?php if (!empty($row['sponsor_id'])): ?>
                                        <h6><?= i('fa fa-user') ?>
                                            <span class="text-capitalize"><?= lang('referred_by') ?>:</span>
                                            <a href="<?= admin_url(TBL_MEMBERS . '/update/' . $row['sponsor_id']) ?>">
												<?= $row['sponsor_username'] ?></a>
                                        </h6>
									<?php endif; ?>
								<?php endif; ?>
                            </div>
                            <div class="col-md-4 text-right visible-lg text-capitalize">
								<?php if (!empty($row['last_login_ip'])): ?>
                                    <h6><?= i('fa fa-calendar') ?> <?= lang('last_login') ?>
                                        : <?php if ($row['last_login_date'] == '0000-00-00 00:00:00'): ?>
											<?= lang('never') ?>
										<?php else: ?>
											<?= display_date($row['last_login_date']) ?>
										<?php endif; ?>
                                    </h6>
                                    <h6><?= i('fa fa-external-link') ?> <?= lang('last_login_ip') ?>:
                                        <a href="<?=EXTERNAL_IP_LOOKUP?><?= $row['last_login_ip'] ?>"
                                           target="_blank">
											<?= $row['last_login_ip'] ?>
                                        </a>
                                    </h6>
								<?php endif; ?>
								<?php if (!empty($row['home_phone'])): ?>
                                    <h6><?= i('fa fa-phone-square') ?> <?= $row['home_phone'] ?></h6>
								<?php endif; ?>
                            </div>
                        </div>
                        <hr/>

                        <a href="<?= admin_url(TBL_ORDERS . '/view/?p-member_id=' . $id) ?>"
                           class="btn btn-default btn-sm" role="button">
							<?= i('fa fa-shopping-cart') ?>
                            <span class="hidden-xs"><?= lang('view_orders') ?></span>
                        </a>

                        <a href="<?= admin_url(TBL_INVOICES . '/view/?member_id=' . $id) ?>"
                           class="btn btn-default btn-sm" role="button">
							<?= i('fa fa-list') ?>
                            <span class="hidden-xs"><?= lang('view_invoices') ?></span>
                        </a>
                        <a href="<?= admin_url(TBL_MEMBERS_CREDITS . '/view/?p-member_id=' . $id) ?>"
                           class="btn btn-default btn-sm" role="button">
		                    <?= i('fa fa-dollar') ?>
                            <span class="hidden-xs"><?= lang('view_credits') ?></span>
                        </a>
						<?php if (config_enabled('affiliate_marketing') && $row['is_affiliate'] == 1): ?>
                            <a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/view/?p-member_id=' . $id) ?>"
                               class="btn btn-default btn-sm" role="button">
								<?= i('fa fa-money') ?>
                                <span class="hidden-xs"><?= lang('view_commissions') ?></span>
                            </a>

                            <a href="<?= admin_url(TBL_AFFILIATE_PAYMENTS . '/view/?member_id=' . $id) ?>"
                               class="btn btn-default btn-sm" role="button">
								<?= i('fa fa-edit') ?>
                                <span class="hidden-xs"><?= lang('view_payments') ?></span>
                            </a>

                            <a href="<?= admin_url('affiliate_downline/view/' . $id) ?>"
                               target="_blank" class="btn btn-default btn-sm" role="button">
								<?= i('fa fa-sitemap') ?>
                                <span class="hidden-xs"><?= lang('view_referrals') ?></span>
                            </a>

                            <a href="<?= admin_url(TBL_AFFILIATE_TRAFFIC . '/view/?p-member_id=' . $id) ?>"
                               class="btn btn-default btn-sm" role="button">
								<?= i('fa fa-link') ?>
                                <span class="hidden-xs"><?= lang('view_traffic') ?></span>
                            </a>
                            <hr/>
                            <div class="stats-overview">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="box-info animated fadeInRight">
                                            <div class="icon-box">
			                                        <span class="fa-stack">
			                                           <?= i('fa fa-circle fa-stack-2x info') ?>
			                                           <?= i('fa fa-thumbs-up fa-stack-1x fa-inverse') ?>
			                                        </span>
                                            </div>
                                            <div class="text-box">
                                                <h3 id="total_commissions"><?= format_amount('0') ?></h3>
                                                <p class="text-capitalize"><?= lang('total_commissions') ?></p>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="progress progress-xs"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="box-info animated fadeInLeft">
                                            <div class="icon-box">
			                                        <span class="fa-stack">
				                                        <?= i('fa fa-circle fa-stack-2x info') ?>
				                                        <?= i('fa fa-link fa-stack-1x fa-inverse') ?>
			                                        </span>
                                            </div>
                                            <div class="text-box">
                                                <h3 id="total_affiliate_clicks">0</h3>
                                                <p class="text-capitalize"><?= lang('total_affiliate_clicks') ?></p>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="progress progress-xs"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="box-info animated fadeInDown">
                                            <div class="icon-box">
			                                        <span class="fa-stack">
			                                          <?= i('fa fa-circle fa-stack-2x info') ?>
			                                          <?= i('fa fa-group fa-stack-1x fa-inverse') ?>
			                                        </span>
                                            </div>
                                            <div class="text-box">
                                                <h3 id="total_referrals">0</h3>
                                                <p class="text-capitalize"><?= lang('affiliate_referrals') ?></p>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="progress progress-xs"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
                    <div class="tab-pane fade in" id="profile">
                        <br/>
                        <ul class="nav nav-tabs text-capitalize" role="tablist">
                            <li class="active"><a href="#name" role="tab" data-toggle="tab"><?= lang('account') ?></a>
                            </li>
                            <li><a href="#social_ids" role="tab" data-toggle="tab"><?= lang('social') ?></a></li>
                            <li><a href="#description" role="tab" data-toggle="tab"><?= lang('description') ?></a></li>
                            <li><a href="#permissions" role="tab" data-toggle="tab"><?= lang('permissions') ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="name">
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('account_status') ?></label>

                                    <div class="col-md-4">
										<?= form_dropdown('status', options('active'), set_value('status', $row['status']), 'class="form-control"') ?>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('confirmed_email') ?></label>

                                    <div class="col-md-4">
										<?= form_dropdown('email_confirmed', options('yes_no'), set_value('email_confirmed', $row['email_confirmed']), 'class="form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('fname') ?></label>

                                    <div class="col-md-4">
                                        <input name="fname" type="text" value="<?= set_value('fname', $row['fname']) ?>"
                                               class="form-control required"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('lname') ?></label>

                                    <div class="col-md-4">
                                        <input name="lname" type="text" value="<?= set_value('lname', $row['lname']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('company') ?></label>

                                    <div class="col-md-4">
                                        <input name="company" type="text"
                                               value="<?= set_value('company', $row['company']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('position') ?></label>

                                    <div class="col-md-4">
                                        <input name="position" type="text"
                                               value="<?= set_value('position', $row['position']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('username') ?></label>

                                    <div class="col-md-4">
                                        <input name="username" type="text" id="username"
                                               value="<?= set_value('username', $row['username']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('referred_by') ?></label>

                                    <div class="col-md-4">
                                        <select id="sponsor_id" class="form-control select2" name="sponsor_id">
                                            <option value="<?= $row['sponsor_id'] ?>"
                                                    selected><?= $row['sponsor_username'] ?></option>
                                        </select>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('email_address') ?></label>

                                    <div class="col-md-4">
                                        <input name="primary_email" type="text"
                                               value="<?= set_value('primary_email', $row['primary_email']) ?>"
                                               class="form-control required email"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('website') ?></label>

                                    <div class="col-md-4">
                                        <input name="website" type="text"
                                               value="<?= set_value('website', $row['website']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('work_phone') ?></label>

                                    <div class="col-md-4">
                                        <input name="work_phone" type="text"
                                               value="<?= set_value('work_phone', $row['work_phone']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('mobile_phone') ?></label>

                                    <div class="col-md-4">
                                        <input name="mobile_phone" type="text"
                                               value="<?= set_value('mobile_phone', $row['mobile_phone']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('home_phone') ?></label>

                                    <div class="col-md-4">
                                        <input name="home_phone" type="text"
                                               value="<?= set_value('home_phone', $row['home_phone']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('fax') ?></label>

                                    <div class="col-md-4">
                                        <input name="fax" type="text" value="<?= set_value('fax', $row['fax']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('birthdate') ?></label>

                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="text" name="birthdate"
                                                   value="<?= set_value('birthdate', $row['birthdate']) ?>"
                                                   class="form-control datepicker-input required"/>
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('points') ?></label>

                                    <div class="col-md-4">
                                        <input name="points" type="number"
                                               value="<?= set_value('points', $row['points']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade in" id="social_ids">
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('facebook_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="facebook_id" type="text"
                                               value="<?= set_value('facebook_id', $row['facebook_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('twitter_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="twitter_id" type="text"
                                               value="<?= set_value('twitter_id', $row['twitter_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('instagram_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="instagram_id" type="text"
                                               value="<?= set_value('instagram_id', $row['instagram_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('pinterest_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="pinterest_id" type="text"
                                               value="<?= set_value('pinterest_id', $row['pinterest_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('youtube_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="youtube_id" type="text"
                                               value="<?= set_value('youtube_id', $row['youtube_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('linked_in_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="linked_in_id" type="text"
                                               value="<?= set_value('linked_in_id', $row['linked_in_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('tumblr_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="tumblr_id" type="text"
                                               value="<?= set_value('tumblr_id', $row['tumblr_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="tab-pane fade in" id="description">
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?= lang('profile_photo') ?></label>

                                    <div class="col-md-6">
                                        <input type="text" name="profile_photo" value="<?= $row['profile_photo'] ?>"
                                               id="<?= $id ?>" class="form-control"/>
                                    </div>
                                    <div class="col-md-3">
                                        <a class='iframe block-phone btn btn-default text-center'
                                           href="<?= base_url() ?>filemanager/dialog.php?fldr=members&type=1&akey=<?= $file_manager_key ?>&field_id=<?= $id ?>">
											<?= i('fa fa-upload') ?> <?= lang('update_photo') ?></a>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?= lang('profile_background') ?></label>

                                    <div class="col-md-6">
                                        <input type="text" name="profile_background"
                                               value="<?= $row['profile_background'] ?>" id="<?= $id + 1 ?>"
                                               class="form-control"/>
                                    </div>
                                    <div class="col-md-3">
                                        <a class='iframe block-phone btn btn-default text-center'
                                           href="<?= base_url() ?>filemanager/dialog.php?fldr=backgrounds&type=1&akey=<?= $file_manager_key ?>&field_id=<?= $id + 1 ?>">
											<?= i('fa fa-upload') ?> <?= lang('update_background') ?></a>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?= lang('profile_line') ?></label>

                                    <div class="col-md-9">
                                        <input type="text" name="profile_line" value="<?= $row['profile_line'] ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?= lang('profile_description') ?></label>

                                    <div class="col-md-9">
										<textarea name="profile_description" rows="5"
                                                  class="form-control"><?= $row['profile_description'] ?></textarea>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="tab-pane fade in" id="permissions">
                                <hr/>
								<?php if (!empty($permission_fields)): ?>
									<?php foreach ($permission_fields as $v): ?>
										<?php if ($v == 'allow_forum_moderation'): ?>
											<?php if (!config_enabled('sts_forum_enable')): ?>
												<?php continue; ?>
											<?php endif; ?>
											<?php if (!check_section('forum_topics')): ?>
												<?php continue; ?>
											<?php endif; ?>
                                        <?php elseif ($v =='allow_downline_email'): ?>
											<?php if (!config_enabled('layout_enable_forced_matrix')): ?>
												<?php continue; ?>
											<?php endif; ?>
										<?php endif; ?>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label"><?= lang($v) ?></label>

                                            <div class="col-md-4">
												<?= form_dropdown($v, options('yes_no'), set_value($v, $row[$v]), 'class="form-control"') ?>
                                            </div>
                                        </div>
                                        <hr/>
									<?php endforeach; ?>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="addresses">

                        <h3 class="text-capitalize">
                            <a href="<?= admin_url(CONTROLLER_CLASS . '/add_address/' . $id) ?>"
                               class="btn btn-primary btn-sm pull-right"><?= i('fa fa-plus') ?> <?= lang('add_address') ?></a>
							<?= lang('address_book') ?></h3>
                        <hr/>
						<?php if (!empty($row['addresses'])): ?>
							<?php foreach ($row['addresses'] as $v): ?>
                                <div class="row">
                                    <div class="col-md-9">
                                        <address>
											<?php if (!empty($v['company'])): ?>
                                                <strong><?= $v['company'] ?></strong><br>
											<?php endif; ?>
                                            <strong><?= $v['fname'] ?> <?= $v['lname'] ?></strong><br>
											<?php if (!empty($v['address_1'])): ?>
												<?= $v['address_1'] ?><br>
											<?php endif; ?>
											<?php if (!empty($v['address_2'])): ?>
												<?= $v['address_2'] ?><br>
											<?php endif; ?>
											<?= $v['city'] ?>, <?= $v['region_name'] ?> <?= $v['postal_code'] ?><br>
											<?= $v['country_name'] ?><br>
											<?php if (!empty($v['phone'])): ?>
                                                <abbr title="Phone">P:</abbr> <?= $v['phone'] ?>
											<?php endif; ?>
                                        </address>
                                    </div>
                                    <div class="col-md-3 text-right">
										<?php if ($v['billing_default'] == 1): ?>
                                            <span class="tip btn btn-info btn-sm" data-toggle="tooltip"
                                                  data-placement="bottom"
                                                  title="<?= lang('default_billing_address') ?>">B</span>
										<?php endif; ?>
										<?php if ($v['shipping_default'] == 1): ?>
                                            <span class="tip btn btn-foursquare btn-sm" data-toggle="tooltip"
                                                  data-placement="bottom"
                                                  title="<?= lang('default_shipping_address') ?>">S</span>
										<?php endif; ?>
										<?php if ($v['payment_default'] == 1): ?>
                                            <span class="tip btn btn-flickr btn-sm" data-toggle="tooltip"
                                                  data-placement="bottom"
                                                  title="<?= lang('default_payment_address') ?>">P</span>
										<?php endif; ?>
                                        <a data-href="<?= admin_url(CONTROLLER_CLASS . '/delete_address/' . $v['id'] . '/' . $id) ?>"
                                           data-toggle="modal" data-target="#confirm-delete"
                                           href="#" <?= is_disabled('delete') ?>
                                           class="md-trigger btn btn-danger btn-sm"><?= i('fa fa-trash-o') ?></a>
                                        <a href="<?= admin_url(CONTROLLER_CLASS . '/update_address/' . $v['id']) ?>"
                                           class="btn btn-default btn-sm"><?= i('fa fa-pencil') ?></a>
                                    </div>
                                </div>
                                <hr/>
							<?php endforeach; ?>
						<?php endif; ?>
                        <div class="modal fade" id="confirm-delete-address" tabindex="-1" role="dialog"
                             aria-labelledby="modal-title"
                             aria-hidden="true">
                            <div class="modal-dialog" id="modal-title">
                                <div class="modal-content">
                                    <div class="modal-body capitalize">
                                        <h3><?= i('fa fa-trash-o') ?> <?= lang('confirm_deletion') ?></h3>
										<?= confirm_deletion() ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                                data-dismiss="modal"><?= lang('cancel') ?></button>
                                        <a href="#" class="btn btn-danger danger"><?= lang('delete') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane fade in" id="affiliate">
                        <br/>
                        <ul class="nav nav-tabs text-capitalize" role="tablist">
                            <li class="active"><a href="#affiliate_information" role="tab"
                                                  data-toggle="tab"><?= lang('affiliate_information') ?></a></li>
                            <li><a href="#alerts" role="tab" data-toggle="tab"><?= lang('alerts') ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="affiliate_information">
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('affiliate_group') ?></label>

                                    <div class="col-md-4">
                                        <select id="aff_group_name" class="form-control select2" name="affiliate_group">
                                            <option value="<?= $row['affiliate_group'] ?>" selected>
												<?php if (empty($row['aff_group_name'])): ?>
													<?= lang('none') ?>
												<?php else: ?>
													<?= $row['aff_group_name'] ?>
												<?php endif; ?>
                                            </option>
                                        </select>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('enable_custom_url') ?></label>

                                    <div class="col-md-4">
                                        <div class="input-group">
                                <span class="input-group-addon">
                                <?= form_checkbox('enable_custom_url', '1', $row['enable_custom_url']); ?>
                                </span>
											<?= form_input('custom_url_link', set_value('custom_url_link', $row['custom_url_link']), 'placeholder="' . base_url() . '" class="' . css_error('custom_url_link') . ' form-control"') ?>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('paypal_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="paypal_id" type="text"
                                               value="<?= set_value('paypal_id', $row['paypal_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('skrill_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="skrill_id" type="text"
                                               value="<?= set_value('skrill_id', $row['skrill_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('dwolla_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="dwolla_id" type="text"
                                               value="<?= set_value('dwolla_id', $row['dwolla_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('coinbase_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="coinbase_id" type="text"
                                               value="<?= set_value('coinbase_id', $row['coinbase_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('payment_name') ?></label>
                                    <div class="col-md-4">
                                        <input name="payment_name" type="text"
                                               value="<?= set_value('payment_name', $row['payment_name']) ?>"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label"><?= lang('payment_preference') ?></label>
                                    <div class="col-md-4">
                                        <input name="payment_preference_amount" type="text"
                                               value="<?= set_value('payment_preference_amount', $row['payment_preference_amount']) ?>"
                                               placeholder="100.00" class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">

                                    <label class="col-md-2 control-label"><?= lang('custom_id') ?></label>

                                    <div class="col-md-4">
                                        <input name="custom_id" type="text"
                                               value="<?= set_value('custom_id', $row['custom_id']) ?>"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><?= lang('bank_transfer_info') ?></label>

                                    <div class="col-md-10">
                                <textarea name="bank_transfer_info" class="form-control"
                                          rows="5"><?= set_value('bank_transfer_info', $row['bank_transfer_info']) ?></textarea>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="tab-pane fade in" id="alerts">
                                <hr/>
                                <div class="form-group">
                                    <label
                                            class="col-md-4 control-label"><?= lang('alert_downline_signup') ?></label>
                                    <div class="col-md-4">
										<?= form_dropdown('alert_downline_signup', options('yes_no'), set_value('alert_downline_signup', $row['alert_downline_signup']), 'class="form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label
                                            class="col-md-4 control-label"><?= lang('alert_new_commission') ?></label>

                                    <div class="col-md-4">
										<?= form_dropdown('alert_new_commission', options('yes_no'), set_value('alert_new_commission', $row['alert_new_commission']), 'class="form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?= lang('alert_payment_sent') ?></label>

                                    <div class="col-md-4">
										<?= form_dropdown('alert_payment_sent', options('yes_no'), set_value('alert_payment_sent', $row['alert_payment_sent']), 'class="form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="groups">
                        <h3 class="text-capitalize"><?= lang('member_groups') ?></h3>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?= lang('discount_group') ?></label>

                            <div class="col-md-4">
                                <select id="disc_group_name" class="form-control select2" name="discount_group">
                                    <option value="<?= $row['discount_group'] ?>" selected>
										<?php if (empty($row['disc_group_name'])): ?>
											<?= lang('none') ?>
										<?php else: ?>
											<?= $row['disc_group_name'] ?>
										<?php endif; ?>
                                    </option>
                                </select>
                            </div>
                            <label class="col-md-2 control-label"><?= lang('blog_group') ?></label>

                            <div class="col-md-4">
                                <select id="blog_group_name" class="form-control select2" name="blog_group">
                                    <option value="<?= $row['blog_group'] ?>" selected>
										<?php if (empty($row['blog_group_name'])): ?>
											<?= lang('none') ?>
										<?php else: ?>
											<?= $row['blog_group_name'] ?>
										<?php endif; ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?= lang('mailing_lists') ?></label>

                            <div class="col-md-10">
                                <select multiple id="mailing_lists" class="form-control select2"
                                        name="mailing_lists[]">
									<?php if (!empty($row['mailing_lists'])): ?>
										<?php foreach ($row['mailing_lists'] as $v): ?>
                                            <option value="<?= $v['list_id'] ?>"
                                                    selected><?= $v['list_name'] ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <hr/>
                    </div>
					<?php if (!empty($custom_fields)): ?>
                        <div class="tab-pane fade in" id="custom">
                            <h3 class="text-capitalize"><?= lang('custom_fields') ?></h3>
                            <hr/>
							<?php foreach ($custom_fields as $v): ?>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?= $v['field_name'] ?></label>

                                    <div class="col-md-8">
										<?= generate_custom_field($v, $v['field_value'], 'class="form-control"') ?>
                                    </div>
                                </div>
                                <hr/>
							<?php endforeach; ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
		<?php if (CONTROLLER_FUNCTION == 'update'): ?>
			<?= form_hidden('member_id', $id) ?>
		<?php endif; ?>
        <nav class="navbar navbar-fixed-bottom  save-changes">
            <div class="container text-right">
                <div class="row">
                    <div class="col-md-12">
						<?php if (CONTROLLER_FUNCTION == 'create'): ?>
                            <input type="submit" name="redir_button" value="<?= lang('save_add_another') ?>"
                                   class="btn btn-success navbar-btn block-phone"/>
						<?php endif; ?>
                        <button id="update-button" class="btn btn-info navbar-btn block-phone <?= is_disabled('update', TRUE) ?>"
                                type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                    </div>
                </div>
            </div>
        </nav>
		<?= form_close() ?>
    </div>
    <div class="col-lg-3">
        <p>
            <a href="<?= admin_url(TBL_ORDERS . '/create/?member_id=' . $id) ?>" class="list-group-item"><span
                        class="pull-right"><?= i('fa fa-list') ?></span><?= lang('create_an_order') ?></a>
            <a href="<?= admin_url(TBL_INVOICES . '/create/' . $id) ?>" class="list-group-item"><span
                        class="pull-right"><?= i('fa fa-edit') ?></span><?= lang('issue_new_invoice') ?></a>
			<?php if (config_enabled('sts_support_enable')): ?>
                <a href="<?= admin_url(TBL_SUPPORT_TICKETS . '/create/' . $id) ?>" class="list-group-item"><span
                            class="pull-right"><?= i('fa fa-support') ?></span> <?= lang('create_support_ticket') ?></a>
			<?php endif; ?>

            <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/is_customer/key/member_id/id/' . $id) ?>"
               class="list-group-item">
	            <?php if ($row['is_customer'] == 1): ?>
                    <span class="pull-right"><?= i('fa fa-minus-circle') ?></span><?= lang('deactivate_as_customer') ?>
            <?php else: ?>
                    <span class="pull-right"><?= i('fa fa-plus-circle') ?></span><?= lang('activate_as_customer') ?>
            <?php endif; ?>
            </a>
			<?php if (config_enabled('affiliate_marketing') && $row['is_affiliate'] == 1): ?>
                <a href="<?= admin_url('update_status/table/' . CONTROLLER_CLASS . '/type/is_affiliate/key/member_id/id/' . $id) ?>"
                   class="list-group-item">
                    <span
                            class="pull-right"><?= i('fa fa-minus-circle') ?></span><?= lang('deactivate_as_affiliate') ?>
                </a>
                <a href="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/create/?member_id=' . $id) ?>"
                   class="list-group-item"><span
                            class="pull-right"><?= i('fa fa-plus') ?></span><?= lang('add_affiliate_commission') ?>
                </a>
			<?php else: ?>
                <a href="<?= admin_url('affiliate_marketing/activate_account/' . $id) ?>"
                   class="list-group-item"><span
                            class="pull-right"><?= i('fa fa-thumbs-o-up') ?></span><?= lang('activate_as_affiliate') ?>
                </a>
			<?php endif; ?>

        </p>
        <div class="box-info">
            <div class="text-box">
                <h4><i class="fa fa-pencil pull-right"></i> <?= lang('add_client_note') ?></h4>
				<?= form_open(admin_url(TBL_MEMBERS_NOTES . '/create/' . $id), 'role="form" id="notes-form" class="form-horizontal"') ?>
                <textarea id="add-note" name="note" maxlength="255" class="form-control required" rows="5"
                          required></textarea>
                <br/>
                <button class="btn btn-default btn-block"><?= lang('add_quick_note') ?></button>
				<?= form_hidden('member_id', $id); ?>
				<?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="box-info">
        <ul class="resp-tabs nav nav-tabs responsive" role="tablist">
            <li class="active"><a href="#invoices" role="tab" data-toggle="tab"><?= lang('recent_invoices') ?></a>
            </li>
			<?php if (config_enabled('sts_support_enable')): ?>
                <li><a href="#tickets"
                       data-tab-remote="<?= admin_url(TBL_SUPPORT_TICKETS . '/member/' . $id) ?>"
                       role="tab"
                       data-toggle="tab"><?= lang('open_tickets') ?></a></li>
			<?php endif; ?>
			<?php if (config_enabled('affiliate_marketing') && $row['is_affiliate'] == 1): ?>
                <li><a href="#comms"
                       data-tab-remote="<?= admin_url(TBL_AFFILIATE_COMMISSIONS . '/member/' . $id) ?>"
                       role="tab"
                       data-toggle="tab"><?= lang('recent_commissions') ?></a>
                </li>
			<?php endif; ?>
            <li>
                <a href="#notes"
                   data-tab-remote="<?= admin_url(TBL_MEMBERS_NOTES . '/recent/' . $id) ?>"
                   role="tab"
                   data-toggle="tab"><?= lang('notes') ?></a>
            </li>
			<?php if ($sts_email_enable_archive == 1): ?>
                <li><a href="#emails" data-tab-remote="<?= admin_url(TBL_EMAIL_ARCHIVE . '/member/' . $id) ?>"
                       role="tab"
                       data-toggle="tab"><?= lang('emails') ?></a>
                </li>
			<?php endif; ?>
            <li><a href="#subscriptions" data-tab-remote="<?= admin_url('subscriptions/member/' . $id) ?>"
                   role="tab"
                   data-toggle="tab"><?= lang('recent_subscriptions') ?></a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="invoices">
                <div id="member-invoices"></div>
            </div>
			<?php if (config_enabled('sts_support_enable')): ?>
                <div class="tab-pane fade in" id="tickets">

                </div>
			<?php endif; ?>

			<?php if (config_enabled('affiliate_marketing') && $row['is_affiliate'] == 1): ?>
                <div class="tab-pane fade in" id="comms"></div>
			<?php endif; ?>
            <div class="tab-pane fade in" id="notes"></div>
			<?php if ($sts_email_enable_archive == 1): ?>
                <div class="tab-pane fade in" id="emails"></div>
			<?php endif; ?>
            <div class="tab-pane fade in" id="subscriptions"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="reset_password" tabindex="-1" role="dialog" aria-labelledby="modal-title"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<?= form_open(admin_url(CONTROLLER_CLASS . '/reset_password/' . $id), 'role="form" id="reset" class="form-horizontal"') ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="modal-title"><?= lang('reset_password') ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-md-4 control-label"><?= lang('new_password') ?></label>

                    <div class="col-md-7 check-password">
                        <input id="password" name="password" type="password" class="form-control required"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-4 control-label"><?= lang('confirm_password') ?></label>

                    <div class="col-md-7">
                        <input name="confirm_password" type="password" class="form-control required"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-md-4 control-label"><?= lang('send_password_to_user') ?></label>

                    <div class="col-md-7">
                        <div class=" checkbox">
							<?= form_checkbox('send_to_user', '1', '', 'class="form-control"'); ?>
                        </div>
                    </div>
                </div>
                <hr/>
            </div>
            <div class="modal-footer">
                <button id="close-password" type="button" class="btn btn-default"
                        data-dismiss="modal"><?= i('fa fa-close') ?> <?= lang('close') ?></button>
                <button type="submit"
                        class="btn btn-primary"><?= i('fa fa-refresh') ?>  <?= lang('save_changes') ?></button>
            </div>
			<?= form_hidden('member_id', $id) ?>
			<?= form_close() ?>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        var id = '#member-invoices';
        $(id).fadeOut('slow', function () {
            $(id).load('<?=admin_url('invoices/member/' . $id)?>');
            $(id).fadeIn('slow');
        });
    });

    $("#sponsor_id").select2({
        ajax: {
            url: '<?=admin_url(CONTROLLER_CLASS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    username: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.member_id,
                            text: item.username
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    //product categories
    $("#mailing_lists").select2({
        ajax: {
            url: '<?=admin_url(TBL_EMAIL_MAILING_LISTS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    list_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.list_id,
                            text: item.list_name
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $("#aff_group_name").select2({
        ajax: {
            url: '<?=admin_url(TBL_AFFILIATE_GROUPS . '/search/ajax')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    aff_group_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.group_id,
                            text: item.aff_group_name
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $("#disc_group_name").select2({
        ajax: {
            url: '<?=admin_url(TBL_DISCOUNT_GROUPS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    disc_group_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.group_id,
                            text: item.group_name
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $("#blog_group_name").select2({
        ajax: {
            url: '<?=admin_url(TBL_BLOG_GROUPS . '/search/ajax/')?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    blog_group_name: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.group_id,
                            text: item.group_name
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

	<?php if (TPL_ADMIN_PASSWORD_METER == TRUE): ?>
    $(document).ready(function () {
        var options = {};
        options.common = {
            minChar: <?=$min_member_password_length?>,
            usernameField: '#username'
        };
        options.rules = {
            activated: {
                wordTwoCharacterClasses: true,
                wordRepetitions: true,
                wordLowercase: 10,
                wordUppercase: 30,
                wordOneNumber: 30,
                wordThreeNumbers: 50,
                wordOneSpecialChar: 30,
                wordTwoSpecialChar: 50,
            }
        };
        options.ui = {
            showVerdictsInsideProgressBar: true,
            progressBarEmptyPercentage: 20,
            progressBarMinPercentage: 20
        };
        $('#password').pwstrength(options);
    });
	<?php endif; ?>

	<?php if (config_enabled('affiliate_marketing') && $row['is_affiliate'] == 1): ?>
    $(document).ready(function () {
        $.ajax({
            url: '<?=admin_url('affiliate_marketing/get_user_totals/' . $id)?>',
            dataType: 'json',
            success: function (response) {
                if (response.type == 'success') {
                    if (response['data']) {
                        $.each(response['data'], function (key, val) {
                            $('#' + key).html(val);
                        });
                    }
                }

                $('#msg-details').html(response.msg);
            },
            error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
	<?php endif; ?>

    $("#notes-form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url(TBL_MEMBERS_NOTES . '/create/' . $id) ?>',
                type: 'POST',
                dataType: 'json',
                data: $('#notes-form').serialize(),
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');
                        $('#response').html('<?=alert('success')?>');

                        $('#notes').load('<?= admin_url(TBL_MEMBERS_NOTES . '/recent/' . $id)?>');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

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

                        if (response['data']) {
                            $.each(response['data'], function (key, val) {
                                $('.' + key).html(val);
                            });
                        }

                        $('#response').html('<?=alert('success')?>');


                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
					<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });

    $("#reset").validate({
        rules: {
            password: {
                required: true,
                minlength: <?=config_option('min_member_password_length')?>
            },
            confirm_password: {
                equalTo: "#password"
            }
        },
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '<?=admin_url(TBL_MEMBERS . '/reset_password/' . $id)?>',
                type: 'POST',
                dataType: 'json',
                data: $('#reset').serialize(),
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        $('#response').html('<?=alert('success')?>');

                        $('#close-password').trigger('click');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);
                    }
                    else {
                        $('#response').html('<?=alert('error')?>');
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
