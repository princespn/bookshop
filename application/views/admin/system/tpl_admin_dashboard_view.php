<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (config_option('show_welcome_screen')): ?>
	<?php if (config_enabled('sts_admin_show_getting_started_widget')): ?>
        <div class="row <?= DASHBOARD_ANIMATED ?> fadeIn">
            <div class="col-md-12 visible-lg">
                <div class="box-info">
                    <h2><?= lang('welcome_to_ecommerce_manager') ?></h2>
                    <div class="additional-btn">
                        <a class="additional-icon"
                           href="<?= admin_url('dashboard/getting_started') ?>"><?= i('fa fa-times') ?></a>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h3><?= i('fa fa-pencil') ?> <?= lang('customize_your_site') ?></h3>
                            <a href="<?= admin_url('layout_manager/config#home') ?>"
                               class="lead"><?= lang('select_homepage_layout') ?></a><br/>
                            <a href="<?= admin_url('themes/view_themes') ?>"
                               class="lead"><?= lang('select_theme_for_site') ?></a><br/>
                            <a href="<?= admin_url('themes/view_themes#logo') ?>"
                               class="lead"><?= lang('upload_own_logo') ?></a>
                        </div>
                        <div class="col-md-4">
                            <h3><?= i('fa fa-shopping-bag') ?> <?= lang('setup_your_store') ?></h3>
                            <a href="<?= admin_url('settings') ?>"
                               class="lead"><?= lang('update_your_site_information') ?></a><br/>
                            <a href="<?= admin_url('products/view') ?>"
                               class="lead"><?= lang('start_adding_your_products') ?></a><br/>
                            <a href="<?= admin_url('payment_gateways/view') ?>"
                               class="lead"><?= lang('enable_payment_options') ?></a>
                        </div>
                        <div class="col-md-4">
                            <h3><?= i('fa fa-question-circle') ?> <?= lang('get_some_quick_help') ?></h3>
                            <p><a href="" class="btn btn-lg btn-primary" data-toggle="modal"
                                  data-target="#welcome_modal">
									<?= i('fa fa-play') ?> <?= lang('watch_intro_tutorial') ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>
<?php endif; ?>
<div class="row">
    <div class="col-md-3 col-xs-6 <?= DASHBOARD_ANIMATED ?> fadeInLeft">
        <div class="box-info">
            <div class="icon-box">
				<span class="fa-stack">
				  <i class="fa fa-circle fa-stack-2x success"></i>
				  <i class="fa fa-users fa-stack-1x fa-inverse"></i>
				</span>
            </div>
            <div class="text-box">
                <h3 id="total_users">0</h3>
                <p><?= strtoupper(lang('total_users')) ?></p>
            </div>
            <div class="clear"></div>
            <div class="progress progress-xs">
                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                     aria-valuemax="100" style="width: 100%">
                </div>
            </div>
            <p class="text-center"><strong id="daily_users">0</strong> <?= lang('have_signed_up_today') ?></p>
        </div>
    </div>
    <div class="col-md-3 col-xs-6 <?= DASHBOARD_ANIMATED ?> fadeInDown">
        <div class="box-info">
            <div class="icon-box">
				<span class="fa-stack">
				  <i class="fa fa-circle fa-stack-2x danger"></i>
				  <i class="fa fa-bell fa-stack-1x fa-inverse"></i>
				</span>
            </div>
            <div class="text-box">
                <h3 id="total_sales">0</h3>
                <p><?= strtoupper(lang('total_sales')) ?></p>
            </div>
            <div class="clear"></div>
            <div class="progress progress-xs">
                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                     aria-valuemax="100" style="width: 100%">
                </div>
            </div>
            <p class="text-center"><strong id="daily_sales">0</strong> <?= lang('in_sales_generated_today') ?></p>
        </div>
    </div>
    <div class="col-md-3 col-xs-6 <?= DASHBOARD_ANIMATED ?> fadeInUp">
        <div class="box-info">
            <div class="icon-box">
				<span class="fa-stack">
				  <i class="fa fa-circle fa-stack-2x info"></i>
				  <i class="fa fa-sitemap fa-stack-1x fa-inverse"></i>
				</span>
            </div>
            <div class="text-box">
                <h3 id="total_commissions">0</h3>
                <p><?= strtoupper(lang('commissions')) ?></p>
            </div>
            <div class="clear"></div>
            <div class="progress progress-xs">
                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                     aria-valuemax="100" style="width: 100%">
                </div>
            </div>
            <p class="text-center"><strong
                        id="daily_commissions">0</strong> <?= lang('in_commissions_generated_today') ?></p>
        </div>
    </div>
    <div class="col-md-3 col-xs-6 <?= DASHBOARD_ANIMATED ?> fadeInRight">
        <div class="box-info">
			<?php if (config_enabled('sts_support_enable')): ?>
                <div class="icon-box">
				<span class="fa-stack">
				  <i class="fa fa-circle fa-stack-2x warning"></i>
				  <i class="fa fa-question fa-stack-1x fa-inverse"></i>
				</span>
                </div>
                <div class="text-box">
                    <h3 id="total_tickets">0</h3>
                    <p><?= strtoupper(lang('support_tickets')) ?></p>
                </div>
                <div class="clear"></div>
                <div class="progress progress-xs">
                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100"
                         aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                    </div>
                </div>
                <p class="text-center"><strong id="daily_tickets">0</strong> <?= lang('have_been_logged_today') ?></p>
			<?php else: ?>
                <div class="icon-box">
				<span class="fa-stack">
				  <i class="fa fa-circle fa-stack-2x warning"></i>
				  <i class="fa fa-truck fa-stack-1x fa-inverse"></i>
				</span>
                </div>
                <div class="text-box">
                    <h3 id="total_tickets">0</h3>
                    <p><?= strtoupper(lang('total_orders')) ?></p>
                </div>
                <div class="clear"></div>
                <div class="progress progress-xs">
                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100"
                         aria-valuemin="0"
                         aria-valuemax="100" style="width: 100%">
                    </div>
                </div>
                <p class="text-center"><strong id="daily_tickets">0</strong> <?= lang('have_been_generated_today') ?>
                </p>
			<?php endif; ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-12 <?= DASHBOARD_ANIMATED ?> fadeInLeft">
                <div class="box-info">
                    <h2><?= lang('quick_overview') ?> - <?= lang(strtolower(current_date('F'))) ?> <?= current_date('Y') ?></h2>
                    <div class="additional-btn">
                        <a class="additional-icon" href="#" data-toggle="collapse" data-target="#stats"><i
                                    class="fa fa-chevron-down"></i></a>
                    </div>
                    <div id="stats" class="statistic-chart collapse in">
                        <div class="btn-group btn-group-xs pull-right">
							<?php if (config_enabled('affiliate_marketing')): ?>
                                <button id="last_30_comm"
                                        class="btn btn-default"><?= lang('monthly_commissions') ?></button>
                                <button id="last_30_sales" class="btn btn-default"><?= lang('monthly_sales') ?></button>
							<?php endif; ?>
                        </div>
                        <div id="chart_title" class="text-center text-muted"></div>
                        <div id="chart" style="height: 193px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 <?= DASHBOARD_ANIMATED ?> fadeInLeft">
                <div class="box-info full">
                    <h2><?= lang('latest_invoices') ?></h2>
                    <div class="additional-btn">
                        <a class="additional-icon" href="#" data-toggle="collapse" data-target="#invoices"><i
                                    class="fa fa-chevron-down"></i></a>
                    </div>
                    <div id="invoices" class="collapse in">
						<?php if (!empty($latest_invoices)): ?>
                            <div class="table-responsive">
                                <table data-sortable class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('date') ?></th>
                                        <th class="text-center"><?= lang('invoice') ?></th>
                                        <th><?= lang('name') ?></th>
                                        <th class="text-center"><?= lang('status') ?></th>
                                        <th class="text-center"><?= lang('amount') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php foreach ($latest_invoices as $v): ?>
                                        <tr>
                                            <td class="text-center"><?= display_date($v['date_purchased']) ?></td>
                                            <td class="text-center">
                                                <a href="<?= admin_url('invoices/update/' . $v['invoice_id']) ?>">
													<?= $v['invoice_number'] ?></a></td>
                                            <td><?= $v['customer_name']  ?></td>
                                            <td class="text-center">
								<span class="label label-default" style="background-color: <?= $v['color'] ?>">
									<?= $v['payment_status'] ?>
								</span>
                                            </td>
                                            <td class="text-center"><?= format_amount($v['total']) ?></td>
                                        </tr>
									<?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
						<?php else: ?>
                            <p class="lead text-center"><?= i('fa fa-info-circle') ?> <?= lang('no_invoices_yet') ?></p>
						<?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 <?= DASHBOARD_ANIMATED ?> fadeInUp">
                <div class="box-info full">
                    <h2><?= lang('latest_commissions') ?></h2>
                    <div class="additional-btn">
                        <a class="additional-icon" href="#" data-toggle="collapse" data-target="#commissions"><i
                                    class="fa fa-chevron-down"></i></a>
                    </div>
                    <div id="commissions" class="collapse in">
						<?php if (!empty($latest_commissions)): ?>
                            <div class="table-responsive">
                                <table data-sortable class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('date') ?></th>
                                        <th><?= lang('name') ?></th>
                                        <th class="text-center"><?= lang('status') ?></th>
                                        <th class="text-center"><?= lang('amount') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php foreach ($latest_commissions as $v): ?>
                                        <tr>
                                            <td class="text-center"><?= display_date($v['date']) ?></td>
                                            <td>
                                                <a href="<?= admin_url('affiliate_commissions/update/' . $v['comm_id']) ?>"><?= $v['username'] ?></a>
                                            </td>
                                            <td class="text-center">
												<?php if ($v['comm_status'] == 'pending'): ?>
                                                    <span class="label label-danger"> <?= lang('pending') ?></span>
												<?php elseif ($v['comm_status'] == 'unpaid') : ?>
                                                    <span class="label label-unpaid"><?= lang('unpaid') ?></span>
													<?php
												else : ?>
                                                    <span class="label label-success"><?= lang('paid') ?></span>
												<?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= format_amount($v['commission_amount']) ?></td>
                                        </tr>
									<?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
						<?php else: ?>
                            <p class="lead text-center"><?= i('fa fa-info-circle') ?> <?= lang('no_commissions_yet') ?></p>
						<?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="col-md-12 <?= DASHBOARD_ANIMATED ?> fadeInRight">
                <div class="box-info">
                    <h2><?= lang('latest_signups') ?></h2>
                    <div class="additional-btn">
                        <a class="additional-icon" href="#" data-toggle="collapse" data-target="#signups"><i
                                    class="fa fa-chevron-down"></i></a>
                    </div>
                    <div id="signups" class="collapse in">
                        <div class="row">
							<?php if (!empty($latest_signups)): ?>
								<?php foreach ($latest_signups as $v): ?>
                                    <div class="col-sm-3 col-sm-6">
                                        <div class="thumbnail dashboard-user cursor"
                                             onclick="window.location='<?= admin_url('members/update/' . $v['member_id']) ?>'">
                                            <div class="gallery-item">
												<?php if (!empty($v['profile_photo'])): ?>
                                                    <img src="<?= $v['profile_photo'] ?>" alt="preview"
                                                         class="theme-preview img-responsive"/>
												<?php else: ?>
                                                    <img src="<?= base_url('images/thumbs/admins/profile.png') ?>"
                                                         alt="preview"
                                                         class="theme-preview img-responsive"/>
												<?php endif; ?>
                                                <div class="hover">
                                                    <p>
                                                        <small><?= substr($v['fname'], 0, 10) ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php endforeach; ?>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
			<?php if (config_enabled('sts_support_enable')): ?>
                <div class="col-lg-12 <?= DASHBOARD_ANIMATED ?> fadeInDown">
                    <div class="box-info full">
                        <h2><?= lang('latest_tickets') ?></h2>
                        <div class="additional-btn">
                            <a class="additional-icon" href="#" data-toggle="collapse" data-target="#tickets"><i
                                        class="fa fa-chevron-down"></i></a>
                        </div>
                        <div id="tickets" class="collapse in">
							<?php if (!empty($latest_tickets)): ?>
                                <div class="table-responsive">
                                    <table data-sortable class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('date') ?></th>
                                            <th><?= lang('subject') ?></th>
                                            <th class="text-center"><?= lang('status') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
										<?php foreach ($latest_tickets as $v): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <a href="<?= admin_url('support_tickets/update/' . $v['ticket_id']) ?>">
	                                                    <?= display_date($v['date_added']) ?></a>
                                                </td>
                                                <td>
                                                    <a href="<?= admin_url('support_tickets/update/' . $v['ticket_id']) ?>">
														<?= character_limiter($v['ticket_subject'], 50) ?></a></td>
                                                <td class="text-center">
											 <span class="label label-default label-<?= $v['ticket_status'] ?>">
												 <?= lang($v['ticket_status']) ?></span>
                                                </td>
                                            </tr>
										<?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
							<?php else: ?>
                                <p class="lead text-center"><?= i('fa fa-info-circle') ?> <?= lang('no_tickets_yet') ?></p>
							<?php endif; ?>
                        </div>
                    </div>

                </div>
			<?php else: ?>
                <div class="col-lg-12 <?= DASHBOARD_ANIMATED ?> fadeInRight">
                    <div class="box-info full">
                        <h2><?= lang('latest_orders') ?></h2>
                        <div class="additional-btn">
                            <a class="additional-icon" href="#" data-toggle="collapse" data-target="#orders"><i
                                        class="fa fa-chevron-down"></i></a>
                        </div>
                        <div id="orders" class="collapse in">
							<?php if (!empty($latest_tickets)): ?>
                                <div class="table-responsive">
                                    <table data-sortable class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('date') ?></th>
                                            <th class="text-center"><?= lang('order') ?></th>
                                            <th><?= lang('order_name') ?></th>
                                            <th class="text-center"><?= lang('order_status') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
										<?php foreach ($latest_tickets as $v): ?>
                                            <tr>
                                                <td class="text-center"><?= display_date($v['date_ordered']) ?></td>
                                                <td class="text-center"><a
                                                            href="<?= admin_url('orders/update/' . $v['order_id']) ?>"><?= $v['order_number'] ?></a>
                                                </td>
                                                <td><?= character_limiter($v['order_name'], 15) ?></td>
                                                <td class="text-center">
											<span class="label label-default"
                                                  style="background-color: <?= $v['color'] ?>">
												<?= lang($v['order_status']) ?></span>
                                                </td>
                                            </tr>
										<?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
							<?php else: ?>
                                <p class="lead text-center"><?= i('fa fa-info-circle') ?> <?= lang('no_orders_yet') ?></p>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </div>
</div>
<?php if (config_option('show_welcome_screen')): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box-info">
                <h2><?= i('fa fa-question') ?> <?= lang('help_and_support') ?></h2>
                <div class="row">
                    <div class="col-md-4">
	                    <?php if (config_item('help_docs')): ?>
                        <a href="<?= $help_docs ?>" class="btn btn-block btn-lg btn-default" target="_blank">
							<?= i('fa fa-file-text-o') ?>
							<?= lang('browse_the_knowledgebase') ?></a>
	                    <?php endif; ?>
                    </div>
                    <div class="col-md-4">
	                    <?php if (config_item('help_videos')): ?>
                        <a href="<?= $help_videos ?>" class="btn btn-block btn-lg btn-default" target="_blank">
							<?= i('fa fa-video-camera') ?>
							<?= lang('watch_video_tutorials') ?></a>
	                    <?php endif; ?>
                    </div>
                    <div class="col-md-4">
						<?php if (config_item('help_forum')): ?>
                            <a href="<?= $help_forum ?>" class="btn btn-block btn-lg btn-default" target="_blank">
								<?= i('fa fa-question-circle') ?>
								<?= lang('ask_a_question') ?></a>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if (config_option('show_welcome_screen')): ?>
	<?php if (config_enabled('sts_admin_show_getting_started_widget')): ?>
        <div class="modal fade" id="welcome_modal" tabindex="-1" role="dialog" aria-labelledby="modal-title"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" id="modal-title">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="H1"><?= lang('watch_overview_ecommerce_manager') ?></h4>
                    </div>
                    <div class="modal-body">

                        <div class="row pull-top-small">
                            <div class="col-md-12">
                                <iframe width="900" height="500" src="<?= $getting_started_video ?>" frameborder="0"
                                        allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="<?= $help_videos ?>" class="btn btn-danger" target="_blank">
							<?= i('fa fa-video-camera') ?> <?= lang('watch_more_videos') ?></a>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>
<?php endif; ?>
<script>

    $(document).ready(function () {

		<?php foreach ($dashboard_items_total as $v): ?>
        $.ajax({
            url: '<?=admin_url('dashboard/get_data/' . $v)?>',
            type: 'get',
            dataType: 'json',
            success: function (response) {
                if (response['data']) {
                    $.each(response['data'], function (key, val) {
                        $('#' + key).html(val);
                        $('#' + key).addClass('<?=DASHBOARD_ANIMATED?> fadeIn');
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

		<?php endforeach; ?>

        $('.dashboard-user').hover(function () {
            $(this).find('.hover').fadeIn(300);
        }, function () {
            $(this).find('.hover').fadeOut(100);
        });

        $(".theme-img").colorbox({rel: 'theme-img'});

    });

    var chart = Morris.Bar({
        // ID of the element in which to draw the chart.
        element: 'chart',
        // Set initial data (ideally you would provide an array of default data)
        data: [0, 0],
        xkey: 'day',
        ykeys: ['amount'],
        labels: ['sales'],
        barColors: ['#3498DB'],
    });

    function load_chart(a) {
        $.ajax({
            url: '<?=admin_url('dashboard/get_data/')?>' + a,
            type: 'get',
            dataType: 'json',
            success: function (response) {
                if (response['type'] == 'success') {
                    $('#chart_title').html(response['title']);
                    chart.setData(JSON.parse(response['data']));
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
				<?=js_debug()?>(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    load_chart('last_30_sales');
    $('#last_30_comm').click(function () {
        load_chart('last_30_comm');
    });

    $('#last_30_sales').click(function () {
        load_chart('last_30_sales');
    });

</script>
