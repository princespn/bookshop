<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<body class="tooltips" <?php if (config_enabled('sts_admin_enable_admin_login_timer')): ?>onload="timer()"
      onmousemove="detime()" <?php endif; ?>>
<div class="container">
    <div class="logo-brand header sidebar rows">
        <div class="logo">
			<?= anchor(admin_url(), heading(config_item('system_name'), 1)) ?>
        </div>
    </div>
    <div id="left-menu" class="left side-menu text-capitalize">
        <div class="body rows scroll-y">
            <div class="sidebar-inner slimscroller">
                <div class="media">
                    <a class="pull-left"
                       href="<?= admin_url('admin_users/update/' . $this->session->admin['admin_id']) ?>">
						<?php if ($this->session->admin['photo']): ?>
                            <img class="admin-avatar media-object img-circle"
                                 src="<?= $this->session->admin['photo'] ?>" alt="Avatar">
						<?php else: ?>
                            <img class="admin-avatar media-object img-circle"
                                 src="<?= base_url('images/thumbs/admins/5.jpg') ?>" alt="Avatar">
						<?php endif; ?>
                    </a>

                    <div class="media-body text-center">
						<?= lang('welcome_back') ?>,
                        <h3 class="media-heading"><strong><?= $this->session->admin['fname'] ?></strong></h3>
                        <a href="<?= admin_url('admin_users/update/' . $this->session->admin['admin_id']) ?>"><span
                                    class="label label-primary"><?= i('fa fa-pencil') ?> <?= lang('profile') ?></span></a>
                        <a href="<?= admin_url('logout') ?>" id="logout-button"><span
                                    class="label label-danger"><?= i('fa fa-unlock') ?> <?= lang('logout') ?></span></a>
                    </div>
                </div>
                <aside class="sidebar">
                    <nav class="sidebar-nav">
                        <ul id="menu" class="capitalize">
	                        <?php if (check_link_permissions(array('members'))):?>
                            <li class="<?= $menu == 'clients' ? 'active' : '' ?>"><a
                                        href="#"><?= i('fa fa-user') ?><?= i('fa fa-angle-double-down i-right') ?> <?= lang('contacts') ?></a>
                                <ul class="<?= $menu == 'clients' ? 'visible' : '' ?>">
                                    <li class="<?= uri() == 'members' ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('manage_contacts') ?></a>
                                        <ul class="<?= uri() == 'members' ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_all_contacts', TBL_MEMBERS . '/view/') ?></li>
                                            <li><?= admin_menu_link('view_customers', TBL_MEMBERS . '/view/?is_customer=1') ?></li>
                                            <li><?= admin_menu_link('add_contact', TBL_MEMBERS . '/create/') ?></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <?php endif; ?>
	                        <?php if (check_link_permissions(array('orders', 'invoices', 'subscriptions'))):?>
                            <li class="<?= $menu == TBL_ORDERS ? 'active' : '' ?>"><a
                                        href="#"><?= i('fa fa-truck') ?><?= i('fa fa-angle-double-down i-right') ?> <?= lang('orders') ?></a>
                                <ul class="<?= $menu == TBL_ORDERS ? 'visible' : '' ?>">
                                    <li class="<?= uri() == TBL_ORDERS ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('manage_orders') ?></a>
                                        <ul class="<?= uri() == TBL_ORDERS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_orders', TBL_ORDERS . '/view') ?></li>
                                            <li><?= admin_menu_link('create_order', TBL_ORDERS . '/create') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_INVOICES ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('manage_invoices') ?></a>
                                        <ul class="<?= uri() == TBL_INVOICES ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_invoices', TBL_INVOICES . '/view') ?></li>
                                            <li><?= admin_menu_link('invoice_payments', TBL_INVOICE_PAYMENTS . '/view') ?></li>
                                        </ul>
                                    </li>

                                    <li class="<?= uri() == 'subscriptions' ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('manage_subscriptions') ?></a>
                                        <ul class="<?= uri() == 'subscriptions' ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_subscriptions', 'subscriptions/view') ?></li>
                                        </ul>
                                    </li>

                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('products_categories', 'products_tags', 'brands', 'products_filters',
                                                                   'suppliers', 'products_attributes', 'products_specifications',
                                                                   'gift_certificates', 'products_reviews'))):?>
                            <li class="<?= $menu == TBL_PRODUCTS ? 'active' : '' ?>"><a
                                        href="#"><?= i('fa fa-shopping-cart') ?><?= i('fa fa-angle-double-down i-right') ?> <?= lang('products') ?></a>
                                <ul class="<?= $menu == TBL_PRODUCTS ? 'visible' : '' ?>">
                                    <li class="<?= uri() == TBL_PRODUCTS ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('manage_products') ?></a>
                                        <ul class="<?= $menu == TBL_PRODUCTS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_products', TBL_PRODUCTS . '/view') ?></li>
                                            <li><?= admin_menu_link('view_product_tags', TBL_PRODUCTS_TAGS . '/view') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_PRODUCTS_CATEGORIES ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('product_categories') ?></a>
                                        <ul class="<?= uri() == TBL_PRODUCTS_CATEGORIES ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_categories', TBL_PRODUCTS_CATEGORIES . '/view') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_BRANDS ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('product_brands') ?></a>
                                        <ul class="<?= uri() == TBL_BRANDS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_brands', TBL_BRANDS . '/view') ?></li>
                                            <li><?= admin_menu_link('create_brand', TBL_BRANDS . '/create') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_PRODUCTS_FILTERS ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('product_filters') ?></a>
                                        <ul class="<?= uri() == TBL_PRODUCTS_FILTERS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_filters', TBL_PRODUCTS_FILTERS . '/view') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_SUPPLIERS ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('product_' . SUPPLIERS) ?></a>
                                        <ul class="<?= uri() == TBL_SUPPLIERS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_' . SUPPLIERS, TBL_SUPPLIERS . '/view') ?></li>
                                            <li><?= admin_menu_link('create_' . SUPPLIERS, TBL_SUPPLIERS . '/create') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_PRODUCTS_ATTRIBUTES ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('product_attributes') ?></a>
                                        <ul class="<?= $menu == 'c' ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_attributes', TBL_PRODUCTS_ATTRIBUTES . '/view') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_PRODUCTS_SPECIFICATIONS ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('product_specifications') ?></a>
                                        <ul class="<?= $menu == 'c' ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_product_specs', TBL_PRODUCTS_SPECIFICATIONS . '/view') ?></li>
                                            <li><?= admin_menu_link('create_product_specs', TBL_PRODUCTS_SPECIFICATIONS . '/create') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_ORDERS_GIFT_CERTIFICATES ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('gift_certificates') ?></a>
                                        <ul class="<?= $menu == 'c' ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_gift_certificates', 'gift_certificates') ?></li>
                                            <li><?= admin_menu_link('create_gift_certificate', 'gift_certificates/create') ?></li>
                                        </ul>
                                    </li>

                                    <li><?= admin_menu_link('product_reviews', TBL_PRODUCTS_REVIEWS . '/view') ?></li>
                                    <li><?= admin_menu_link('wish_lists', TBL_WISH_LISTS . '/view') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('affiliate_marketing', 'affiliate_payment_options', 'affiliate_payments',
	                                                               'affiliate_traffic', 'affiliate_commissions', 'affiliate_commission_rules',
	                                                               'affiliate_groups'))):?>
							<?php if (config_enabled('affiliate_marketing')): ?>
                                <li class="<?= $menu == 'affiliates' ? 'active' : '' ?>"><a
                                            href="#"><?= i('fa fa-hand-o-right') ?><?= i('fa fa-angle-double-down i-right') ?> <?= lang('affiliate_marketing') ?></a>
                                    <ul class="<?= $menu == 'affiliates' ? 'visible' : '' ?>">
                                        <li class="<?= uri() == 'affiliates' ? 'active' : '' ?>"><a
                                                    href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('affiliates') ?></a>
                                            <ul class="<?= uri() == 'affiliates' ? 'visible' : '' ?>">
                                                <li><?= admin_menu_link('view_affiliates', TBL_MEMBERS . '/view/?is_affiliate=1') ?></li>
                                                <li><?= admin_menu_link('add_affiliate', TBL_MEMBERS . '/create/affiliate') ?></li>
                                                <li><?= admin_menu_link('affiliate_traffic', TBL_AFFILIATE_TRAFFIC . '/view') ?></li>
                                            </ul>
                                        </li>
                                        <li class="<?= uri() == TBL_AFFILIATE_COMMISSIONS ? 'active' : '' ?>"><a
                                                    href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('affiliate_commissions') ?></a>
                                            <ul class="<?= uri() == TBL_AFFILIATE_COMMISSIONS ? 'visible' : '' ?>">
                                                <li><?= admin_menu_link('view_commissions', TBL_AFFILIATE_COMMISSIONS . '/view') ?></li>
                                                <li><?= admin_menu_link('add_commission', TBL_AFFILIATE_COMMISSIONS . '/create') ?></li>
                                            </ul>
                                        </li>
                                        <li class="<?= $sub_menu == TBL_AFFILIATE_PAYMENTS ? 'active' : '' ?>"><a
                                                    href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('affiliate_payments') ?></a>
                                            <ul class="visible">
                                                <li><?= admin_menu_link('pay_affiliates', 'affiliate_payment_options/view') ?></li>
                                                <li><?= admin_menu_link('payment_history', TBL_AFFILIATE_PAYMENTS . '/view') ?></li>
                                            </ul>
                                        </li>
                                        <li class="<?= uri() == TBL_AFFILIATE_GROUPS ? 'active' : '' ?>"><a
                                                    href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('affiliate_groups') ?></a>
                                            <ul class="<?= uri() == TBL_AFFILIATE_GROUPS ? 'visible' : '' ?>">
                                                <li><?= admin_menu_link('view_affiliate_groups', TBL_AFFILIATE_GROUPS . '/view') ?></li>
												<?php if (config_enabled('enable_multi_affiliate_groups')): ?>
                                                    <li><?= admin_menu_link('add_affiliate_group', TBL_AFFILIATE_GROUPS . '/create') ?></li>
												<?php endif; ?>
                                            </ul>
                                        </li>
                                        <li><?= admin_menu_link('commission_rules', 'affiliate_commission_rules') ?></li>

                                        <li><?= admin_menu_link('affiliate_tools', 'affiliate_marketing') ?></li>
                                    </ul>
                                </li>
							<?php endif; ?>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('blog_posts', 'blog_comments', 'blog_categories', 'blog_tags',
                                                                   'site_pages', 'slide_shows', 'system_pages', 'media', 'gallery',
                                                                   'products_downloads', 'videos', 'site_map'))):?>
                            <li class="<?= $menu == 'content' ? 'active' : '' ?>"><a
                                        href="#"><?= i('fa fa-edit') ?><?= i('fa fa-angle-double-down i-right') ?> <?= lang('content_media') ?></a>
                                <ul class="<?= $menu == 'content' ? 'visible' : '' ?>">
                                    <li class="<?= $sub_menu == 'blog' ? 'active' : '' ?>"><a
                                                href="#"><?= i('fa fa-angle-double-right') ?><?= i('fa fa-angle-right i-right') ?> <?= lang('blog_posts') ?></a>
                                        <ul class="<?= uri() == TBL_BLOG_POSTS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_blog_posts', TBL_BLOG_POSTS . '/view') ?></li>
                                            <li><?= admin_menu_link('add_blog_post', TBL_BLOG_POSTS . '/create') ?></li>
											<?php if (config_option('sts_content_enable_comments') == 1): ?>
                                                <li><?= admin_menu_link('blog_comments', TBL_BLOG_COMMENTS . '/view') ?></li>
											<?php endif; ?>
                                            <li><?= admin_menu_link('blog_categories', TBL_BLOG_CATEGORIES . '/view') ?></li>
                                            <li><?= admin_menu_link('blog_tags', TBL_BLOG_TAGS . '/view') ?></li>
                                            <li><?= admin_menu_link('blog_groups', TBL_BLOG_GROUPS . '/view') ?></li>
                                        </ul>
                                    </li>
                                    <li><?= admin_menu_link('site_pages', TBL_SITE_PAGES . '/view') ?></li>
                                    <li><?= admin_menu_link('slide_shows', TBL_SLIDE_SHOWS . '/view') ?></li>
                                    <li><?= admin_menu_link('system_pages', TBL_SYSTEM_PAGES . '/view') ?></li>
                                    <li><?= admin_menu_link('image_manager', 'media/view') ?></li>
                                    <li><?= admin_menu_link('image_gallery', TBL_GALLERY . '/view') ?></li>
                                    <li><?= admin_menu_link('download_files', TBL_PRODUCTS_DOWNLOADS . '/view') ?></li>
                                    <li><?= admin_menu_link('video_manager', TBL_VIDEOS . '/view') ?></li>
                                    <li><?= admin_menu_link('site_map', 'site_map/view') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('support_tickets', 'support_categories', 'support_predefined_replies',
	                                                               'faq', 'kb_articles', 'kb_categories', 'forum_topics', 'forum_categories'))):?>
                            <li class="<?= $menu == TBL_SUPPORT_TICKETS ? 'active' : '' ?>">
                                <a href="#"><?= i('fa fa-life-ring') ?>
									<?= i('fa fa-angle-double-down i-right') ?> <?= lang('help_and_support') ?></a>
                                <ul class="<?= $menu == TBL_SUPPORT_TICKETS ? 'visible' : '' ?>">
									<?php if (config_enabled('sts_support_enable')): ?>
                                        <li class="<?= $sub_menu == TBL_SUPPORT_TICKETS ? 'active' : '' ?>">
                                            <a href="#"><?= i('fa fa-angle-double-right') ?>
												<?= i('fa fa-angle-right i-right') ?> <?= lang('support_tickets') ?></a>
                                            <ul class="<?= $menu == TBL_SUPPORT_TICKETS ? 'visible' : '' ?>">
                                                <li><?= admin_menu_link('active_tickets', TBL_SUPPORT_TICKETS . '/view/?closed=0') ?></li>
                                                <li><?= admin_menu_link('closed_tickets', TBL_SUPPORT_TICKETS . '/view/?closed=1') ?></li>
                                                <li><?= admin_menu_link('support_categories', TBL_SUPPORT_CATEGORIES) ?></li>
                                                <li><?= admin_menu_link('predefined_replies', TBL_SUPPORT_PREDEFINED_REPLIES . '/view/') ?></li>
                                            </ul>
                                        </li>
									<?php endif; ?>
                                    <li class="<?= uri() == TBL_FAQ ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('faq') ?></a>
                                        <ul class="<?= uri() == TBL_FAQ ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_faq', TBL_FAQ . '/view') ?></li>
                                            <li><?= admin_menu_link('create_faq', TBL_FAQ . '/create') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= $sub_menu == TBL_KB_ARTICLES ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('knowledgebase') ?></a>
                                        <ul class="<?= $sub_menu == TBL_KB_ARTICLES ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('kb_articles', TBL_KB_ARTICLES . '/view') ?></li>
                                            <li><?= admin_menu_link('add_kb_article', TBL_KB_ARTICLES . '/create') ?></li>
                                            <li><?= admin_menu_link('kb_categories', TBL_KB_CATEGORIES . '/view') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= $sub_menu == TBL_FORUM_TOPICS ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('simple_forum') ?></a>
                                        <ul class="<?= $sub_menu == TBL_FORUM_TOPICS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('forum_topics', TBL_FORUM_TOPICS . '/view') ?></li>
                                            <li><?= admin_menu_link('add_forum_topic', TBL_FORUM_TOPICS . '/create') ?></li>
                                            <li><?= admin_menu_link('forum_categories', TBL_FORUM_CATEGORIES . '/view') ?></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('report_archive', 'reports'))):?>
                            <li class="<?= $menu == 'reports' ? 'active' : '' ?>">
                                <a href="#"><?= i('fa fa-bar-chart-o') ?>
									<?= i('fa fa-angle-double-down i-right') ?>
									<?= lang('stats_reports') ?></a>
                                <ul class="<?= $menu == 'reports' ? 'visible' : '' ?>">
                                    <li><?= admin_menu_link('on_demand_reports', TBL_REPORTS) ?></li>
                                    <li><?= admin_menu_link('archived_reports', 'report_archive') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('discount_groups', 'coupons', 'rewards',
	                                                               'events_calendar', 'promotional_rules', 'tracking'))):?>
                            <li class="<?= $menu == 'promotions' ? 'active' : '' ?>">
                                <a href="#"><?= i('fa fa-bullhorn') ?>
									<?= i('fa fa-angle-double-down i-right') ?> <?= lang('promotions') ?></a>
                                <ul class="<?= $menu == 'promotions' ? 'visible' : '' ?>">
                                    <li class="<?= uri() == TBL_DISCOUNT_GROUPS ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('discount_groups') ?></a>
                                        <ul class="<?= uri() == TBL_DISCOUNT_GROUPS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_discount_groups', TBL_DISCOUNT_GROUPS . '/view') ?></li>
											<?php if (config_enabled('enable_multi_affiliate_groups')): ?>
                                                <li><?= admin_menu_link('create_discount_group', TBL_DISCOUNT_GROUPS . '/create') ?></li>
											<?php endif; ?>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_COUPONS ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('coupon_codes') ?></a>
                                        <ul class="<?= uri() == TBL_COUPONS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('view_coupons', TBL_COUPONS . '/view') ?></li>
                                            <li><?= admin_menu_link('create_coupons', TBL_COUPONS . '/create') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_REWARDS ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('loyalty_rewards') ?></a>
                                        <ul class="<?= uri() == TBL_REWARDS ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('reward_options', TBL_REWARDS . '/view') ?></li>
                                            <li><?= admin_menu_link('top_rewards_users', TBL_MEMBERS . '/view/?column=points') ?></li>
                                        </ul>
                                    </li>
                                    <li class="<?= uri() == TBL_EVENTS_CALENDAR ? 'active' : '' ?>">
										<?= admin_menu_link('events_calendar', TBL_EVENTS_CALENDAR . '/view') ?></li>

                                    <li><?= admin_menu_link('promotional_rules', 'promotional_rules/view') ?></li>

                                    <li><?= admin_menu_link('link_tracking', 'tracking/view') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('email_mailing_lists', 'email_templates',
	                                                               'email_queue', 'email_archive'))):?>
                            <li class="<?= $menu == 'email' ? 'active' : '' ?>">
                                <a href="#"><?= i('fa fa-envelope') ?>
									<?= i('fa fa-angle-double-down i-right') ?> <?= lang('email_tools') ?></a>
                                <ul class="<?= $menu == 'email' ? 'visible' : '' ?>">
                                    <li class="<?= uri() == TBL_EMAIL_MAILING_LISTS ? 'active' : '' ?>"><?= admin_menu_link('mailing_lists', TBL_EMAIL_MAILING_LISTS . '/view') ?></li>
                                    <li class="<?= uri() == TBL_EMAIL_TEMPLATES ? 'active' : '' ?>"><?= admin_menu_link('email_templates', TBL_EMAIL_TEMPLATES . '/view') ?></li>
                                    <li class="<?= uri() == TBL_EMAIL_QUEUE ? 'active' : '' ?>"><?= admin_menu_link('email_queue', TBL_EMAIL_QUEUE . '/view/') ?></li>
                                    <li class="<?= uri() == TBL_EMAIL_ARCHIVE ? 'active' : '' ?>"><?= admin_menu_link('email_archive', TBL_EMAIL_ARCHIVE . '/view') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('zones', 'tax_classes','tax_rates', 'countries',
	                                                               'shipping', 'languages', 'currencies', 'measurements'))):?>
                            <li class="<?= $menu == 'locale' ? 'active' : '' ?>">
                                <a href="#"><?= i('fa fa-map-marker') ?>
									<?= i('fa fa-angle-double-down i-right') ?> <?= lang('localization') ?></a>
                                <ul class="<?= $menu == 'locale' ? 'visible' : '' ?>">
                                    <li class="<?= $sub_menu == 'zones' ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('taxes_and_zones') ?></a>
                                        <ul class="<?= $sub_menu == 'zones' ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('tax_classes', 'tax_classes/view') ?>
                                            <li><?= admin_menu_link('tax_rates', 'tax_rates/view') ?></li>
                                            <li><?= admin_menu_link('regional_zones', 'zones/view') ?></li>
                                            <li><?= admin_menu_link('countries', TBL_COUNTRIES . '/view') ?></li>
                                        </ul>
                                    </li>
                                    <li><?= admin_menu_link('shipping', 'shipping/view') ?></li>
                                    <li><?= admin_menu_link('languages', TBL_LANGUAGES . '/view') ?></li>
                                    <li><?= admin_menu_link('currencies', TBL_CURRENCIES . '/view') ?></li>
                                    <li><?= admin_menu_link('measurements', TBL_MEASUREMENTS . '/update') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('data_import', 'data_export'))):?>
                            <li class="<?= $menu == 'import_export' ? 'active' : '' ?>">
                                <a href="#"><?= i('fa fa-download') ?>
									<?= i('fa fa-angle-double-down i-right') ?> <?= lang('data_import_export') ?></a>
                                <ul class="<?= $menu == 'import_export' ? 'visible' : '' ?>">
                                    <li><?= admin_menu_link('data_import', 'data_import/view') ?></li>
                                    <li><?= admin_menu_link('data_export', 'data_export/view') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
	                        <?php if (check_link_permissions(array('themes', 'layout_manager','template_manager',
	                                                               'site_menus', 'widgets', 'forms'))):?>
                            <li class="<?= $menu == 'design' ? 'active' : '' ?>">
                                <a href="#"><?= i('fa fa-picture-o') ?>
									<?= i('fa fa-angle-double-down i-right') ?> <?= lang('design_layout') ?></a>
                                <ul class="<?= $menu == 'design' ? 'visible' : '' ?>">
                                    <li class="<?= $sub_menu == 'layout' ? 'active' : '' ?>">
                                        <a href="#"><?= i('fa fa-angle-double-right') ?>
											<?= i('fa fa-angle-right i-right') ?> <?= lang('layout_and_themes') ?></a>
                                        <ul class="<?= $sub_menu == 'layout' ? 'visible' : '' ?>">
                                            <li><?= admin_menu_link('logo_and_themes', 'themes') ?></li>
                                            <li><?= admin_menu_link('site_layout', 'layout_manager') ?></li>
                                            <li><?= admin_menu_link('template_manager', 'template_manager') ?></li>
                                        </ul>
                                    </li>
                                    <li><?= admin_menu_link('site_menus', 'site_menus') ?></li>
	                                <?php if (check_site_builder()): ?>
                                    <li><?= admin_menu_link('widget_manager', 'widgets') ?></li>
                                    <?php endif; ?>
                                    <li><?= admin_menu_link('form_generator', 'forms') ?></li>
                                </ul>
                            </li>
	                        <?php endif; ?>
                        </ul>
                    </nav>
                </aside>
            </div>
        </div>
        <div class="footer rows text-center">
            <small class="animated fadeInUpBig copyright">
                &copy; <?= date('Y') ?>
				<?php if (!empty($poweredby)): ?>
                    <a href="https://www.jrox.com">JROX Technologies, Inc.</a>
				<?php else: ?>
                    All Rights Reserved.
				<?php endif; ?>
                v<?= APP_VERSION ?>
            </small>
        </div>
    </div>
    <div class="right content-page">
        <div class="header content rows-content-header">
            <button class="button-menu-mobile show-sidebar">
				<?= i('fa fa-bars') ?>
            </button>
            <div class="navbar navbar-default capitalize" role="navigation">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse"
                                data-target=".navbar-collapse">
							<?= i('fa fa-angle-double-down') ?>
                        </button>
                    </div>
                    <div class="navbar-collapse collapse">
						<?= form_open(admin_url('search/general'), 'class="navbar-form navbar-left" role="search"') ?>
                        <div class="form-group hidden-sm">
                            <input type="text" name="search_term" class="form-control"
                                   placeholder="<?= lang('enter_search_here') ?>">
                            <select name="table" class="form-control text-capitalize">
                                <option value="<?= TBL_MEMBERS ?>"><?= lang('contacts') ?></option>
                                <option value="<?= TBL_PRODUCTS ?>"><?= lang('products') ?></option>
                                <option value="<?= TBL_INVOICES ?>"><?= lang('invoices') ?></option>
                                <option value="<?= TBL_ORDERS ?>"><?= lang('orders') ?></option>
                                <option value="<?= TBL_AFFILIATE_COMMISSIONS ?>"><?= lang('commissions') ?></option>
                                <option value="<?= TBL_BLOG_POSTS ?>"><?= lang('blog_posts') ?></option>
                                <option value="<?= TBL_SUPPORT_TICKETS ?>"><?= lang('support_tickets') ?></option>
                            </select>
                        </div>
                        <button type="submit"
                                class="btn btn-default block-phone hidden-sm"><?= i('fa fa-search') ?> </button>
                        <a href="<?= admin_url('search/advanced') ?>"
                           class="btn btn-default block-phone"><?= lang('advanced_search') ?></a>
                        </form>
                        <ul class="nav navbar-nav navbar-right top-navbar text-capitalize">
                            <li>
                                <a href="<?= site_url() ?>" target="_blank">
                                    <small><?= i('fa fa-external-link') ?>
                                        <span class="hidden-md hidden-sm"><?= lang('view_site') ?></span>
                                    </small>
                                </a>
                            </li>
							<?php if (config_item('help_docs')): ?>
                                <li>
                                    <a href="<?= $help_docs ?>" target="_blank">
                                        <small><?= i('fa fa-question-circle') ?>
                                            <span class="hidden-md hidden-sm"><?= lang('help_docs') ?></span>
                                        </small>
                                    </a>
                                </li>
							<?php endif; ?>
							<?php if (config_item('help_videos')): ?>
                                <li>
                                    <a href="<?= $help_forum ?>" target="_blank">
                                        <small><?= i('fa fa-users') ?>
                                            <span class="hidden-md hidden-sm"><?= lang('support') ?></span>
                                        </small>
                                    </a>
                                </li>
							<?php endif; ?>
                            <li>
                                <a href="<?= admin_url('settings') ?>">
                                    <small><?= i('fa fa-cogs') ?>
                                        <span class="hidden-md hidden-sm"><?= lang('settings') ?></span>
                                    </small>
                                </a>
                            </li>
							<?php if (config_item('help_forum')): ?>
                                <li class="dropdown pad-right">
                                    <a href="<?= $help_forum ?>" target="_blank"><?= i('fa fa-users') ?><span
                                                class="label label-danger absolute"><?= i('fa fa-life-ring') ?></span></a>
                                </li>
							<?php endif; ?>
                        </ul>
                    </div>
                    <!-- End div .navbar-collapse -->
                </div>
                <!-- End div .container -->
            </div>
            <!-- END NAVBAR CONTENT-->
        </div>
        <!-- END CONTENT HEADER -->

        <div class="body content rows scroll-y">

            <div class="content-body">
                <div class="crumb-row visible-md visible-lg">
                    <div class="updates">
						<?php if (!empty($jam_license_alert)): ?>
							<?= $jam_license_alert ?>
						<?php else: ?>
                            <small class="text-muted text-capitalize">
								<?= lang('last_login') ?>
								<?php if ($this->session->admin['last_login_date'] != '0000-00-00 00:00:00'): ?>
									<?= display_date($this->session->admin['last_login_date'], TRUE) ?>
                                    - <?= $this->session->admin['last_login_ip'] ?>
								<?php else: ?>
									<?= lang('never') ?>
								<?php endif; ?>

                                <?php if ($this->uri->segment(2)): ?>|
                                    <a href="<?= $help_docs ?>"><?= i('fa fa-question-circle') ?></a> <?php endif; ?>
                            </small>

						<?php endif; ?>
                    </div>
					<?= set_breadcrumb() ?>
                </div>
                <div id="response">
					<?php if (!empty($error)): ?>
						<?= alert('error', $error) ?>
					<?php elseif ($this->session->flashdata('error')): ?>
						<?= alert('error', $this->session->flashdata('error')) ?>
					<?php
                    elseif (!empty($success)): ?>
						<?= alert('success', $success) ?>
					<?php
                    elseif ($this->session->flashdata('success')): ?>
						<?= alert('success', $this->session->flashdata('success')) ?>
					<?php endif; ?>
                    <h4 id="error-alert" class="alert alert-danger hover-msg animated shake"
                        style="display: none;"><?= i('fa fa-exclamation-triangle') ?> <?= lang('please_check_all_fields_for_errors') ?></h4>
                    <div></div>
                </div>