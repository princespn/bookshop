<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 * Copyright (c) 2007-2020, JROX Technologies, Inc.
 *
 * This script may be only used and modified in accordance to the license
 * agreement attached (license.txt) except where expressly noted within
 * commented areas of the code body. This copyright notice and the
 * comments above and below must remain intact at all times.  By using this
 * code you agree to indemnify JROX Technologies, Inc, its corporate agents
 * and affiliates from any liability that might arise from its use.
 *
 * Selling the code for this program without prior written consent is
 * expressly forbidden and in violation of Domestic and International
 * copyright laws.
 *
 * @package	eCommerce Suite
 * @author	JROX Technologies, Inc.
 * @copyright	Copyright (c) 2007 - 2019, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link	https://www.jrox.com
 * @filesource
 */

/*
|--------------------------------------------------------------------------
| Global Definitions
|--------------------------------------------------------------------------
|
| These are set for admin static defines
|
*/

define('DEFAULT_ADMIN_SESSION_TIMER', 14400000); //4 hours 14400000
define('ADMIN_MEMBERS_RECENT_DATA', 10);
define('TPL_ADMIN_CONTENT_TYPE', 'text/html');

define('MAX_THUMBNAIL_WIDTH', 300);
define('MAX_THUMBNAIL_HEIGHT', 230);

define('TPL_ADMIN_AJAX_LOAD', 'themes/admin/default/img/loading.gif');
define('TPL_DEFAULT_ADMIN_PHOTO', 'themes/admin/default/img/profile.png');
define('TPL_DEFAULT_MEMBER_PHOTO', 'themes/admin/default/img/profile.png');
define('TPL_DEFAULT_PRODUCT_PHOTO', 'themes/admin/default/img/camera.png');

define('TPL_ADMIN_CHEVRON', 'fa-chevron');
define('TPL_ADMIN_PASSWORD_METER', 'true');

define('TBL_ADMIN_DEFAULT_TOTAL_ROWS', 12);

define('TPL_ADMIN_HEADER', 'tpl_admin_header');
define('TPL_ADMIN_FOOTER', 'tpl_admin_footer');
define('TPL_ADMIN_HEADER_META', 'tpl_admin_header_meta');
define('TPL_ADMIN_FOOTER_META', 'tpl_admin_footer_meta');

define('TPL_ADMIN_ADMIN_GROUPS_CREATE', 'tpl_admin_admin_groups_manage');
define('TPL_ADMIN_ADMIN_GROUPS_UPDATE', 'tpl_admin_admin_groups_manage');
define('TPL_ADMIN_ADMIN_GROUPS_VIEW', 'tpl_admin_admin_groups_view');
define('TPL_ADMIN_ADMIN_USERS_CREATE', 'tpl_admin_admin_users_manage');
define('TPL_ADMIN_ADMIN_USERS_UPDATE', 'tpl_admin_admin_users_manage');
define('TPL_ADMIN_ADMIN_USERS_VIEW', 'tpl_admin_admin_users_view');
define('TPL_ADMIN_ADVANCED_SEARCH', 'tpl_admin_advanced_search');
define('TPL_ADMIN_ADVANCED_SEARCH_RESULTS', 'tpl_admin_advanced_search_results');
define('TPL_ADMIN_AFFILIATE_BLOCK_TRAFFIC', 'tpl_admin_affiliate_block_traffic');
define('TPL_ADMIN_AFFILIATE_COMMISSIONS_CREATE', 'tpl_admin_affiliate_commissions_manage');
define('TPL_ADMIN_AFFILIATE_COMMISSIONS_UPDATE', 'tpl_admin_affiliate_commissions_manage');
define('TPL_ADMIN_AFFILIATE_COMMISSIONS_VIEW', 'tpl_admin_affiliate_commissions_view');
define('TPL_ADMIN_AFFILIATE_COMMISSION_RULES_CREATE', 'tpl_admin_affiliate_commission_rules_manage');
define('TPL_ADMIN_AFFILIATE_COMMISSION_RULES_UPDATE', 'tpl_admin_affiliate_commission_rules_manage');
define('TPL_ADMIN_AFFILIATE_COMMISSION_RULES_VIEW', 'tpl_admin_affiliate_commission_rules_view');
define('TPL_ADMIN_AFFILIATE_DOWNLINE_VIEW', 'tpl_admin_affiliate_downline_view');
define('TPL_ADMIN_AFFILIATE_GROUPS_VIEW', 'tpl_admin_affiliate_groups_view');
define('TPL_ADMIN_AFFILIATE_GROUP_CREATE', 'tpl_admin_affiliate_group_manage');
define('TPL_ADMIN_AFFILIATE_GROUP_UPDATE', 'tpl_admin_affiliate_group_manage');
define('TPL_ADMIN_AFFILIATE_MARKETING_VIEW', 'tpl_admin_affiliate_marketing_view');
define('TPL_ADMIN_AFFILIATE_PAYMENT_OPTIONS_MANAGE', 'tpl_admin_affiliate_payment_options_manage');
define('TPL_ADMIN_AFFILIATE_PAYMENT_OPTIONS_VIEW', 'tpl_admin_affiliate_payment_options_view');
define('TPL_ADMIN_AFFILIATE_PAYMENTS_MANAGE', 'tpl_admin_affiliate_payments_manage');
define('TPL_ADMIN_AFFILIATE_PAYMENTS_VIEW', 'tpl_admin_affiliate_payments_view');
define('TPL_ADMIN_AFFILIATE_TRAFFIC_VIEW','tpl_admin_affiliate_traffic_view');
define('TPL_ADMIN_BACKUPS_VIEW', 'tpl_admin_backups_view');
define('TPL_ADMIN_BLOG_CATEGORIES_CREATE', 'tpl_admin_blog_categories_manage');
define('TPL_ADMIN_BLOG_CATEGORIES_UPDATE', 'tpl_admin_blog_categories_manage');
define('TPL_ADMIN_BLOG_CATEGORIES_VIEW', 'tpl_admin_blog_categories_view');
define('TPL_ADMIN_BLOG_COMMENTS_CREATE', 'tpl_admin_blog_comments_manage');
define('TPL_ADMIN_BLOG_COMMENTS_UPDATE', 'tpl_admin_blog_comments_manage');
define('TPL_ADMIN_BLOG_COMMENTS_VIEW', 'tpl_admin_blog_comments_view');
define('TPL_ADMIN_BLOG_GROUPS_CREATE', 'tpl_admin_blog_groups_manage');
define('TPL_ADMIN_BLOG_GROUPS_UPDATE', 'tpl_admin_blog_groups_manage');
define('TPL_ADMIN_BLOG_GROUPS_VIEW', 'tpl_admin_blog_groups_view');
define('TPL_ADMIN_BLOG_POSTS_CREATE', 'tpl_admin_blog_posts_manage');
define('TPL_ADMIN_BLOG_POSTS_UPDATE', 'tpl_admin_blog_posts_manage');
define('TPL_ADMIN_BLOG_POSTS_VIEW', 'tpl_admin_blog_posts_view');
define('TPL_ADMIN_BLOG_TAGS_CREATE', 'tpl_admin_blog_comments_manage');
define('TPL_ADMIN_BLOG_TAGS_VIEW', 'tpl_admin_blog_tags_view');
define('TPL_ADMIN_BRANDS_CREATE', 'tpl_admin_brands_manage');
define('TPL_ADMIN_BRANDS_UPDATE', 'tpl_admin_brands_manage');
define('TPL_ADMIN_BRANDS_VIEW', 'tpl_admin_brands_view');
define('TPL_ADMIN_COUNTRIES_CREATE', 'tpl_admin_countries_manage');
define('TPL_ADMIN_COUNTRIES_UPDATE', 'tpl_admin_countries_manage');
define('TPL_ADMIN_COUNTRIES_VIEW', 'tpl_admin_countries_view');
define('TPL_ADMIN_COUPONS_VIEW', 'tpl_admin_coupons_view');
define('TPL_ADMIN_COUPONS_CREATE', 'tpl_admin_coupons_manage');
define('TPL_ADMIN_COUPONS_UPDATE', 'tpl_admin_coupons_manage');
define('TPL_ADMIN_CURRENCIES_CREATE', 'tpl_admin_currencies_manage');
define('TPL_ADMIN_CURRENCIES_UPDATE', 'tpl_admin_currencies_manage');
define('TPL_ADMIN_CURRENCIES_VIEW', 'tpl_admin_currencies_view');
define('TPL_ADMIN_DASHBOARD_VIEW', 'tpl_admin_dashboard_view');
define('TPL_ADMIN_DATA_EXPORT_VIEW', 'tpl_admin_data_export_view');
define('TPL_ADMIN_DATA_IMPORT_VIEW', 'tpl_admin_data_import_view');
define('TPL_ADMIN_DISCOUNT_GROUPS_VIEW', 'tpl_admin_discount_groups_view');
define('TPL_ADMIN_DISCOUNT_GROUP_CREATE', 'tpl_admin_discount_group_manage');
define('TPL_ADMIN_DISCOUNT_GROUP_UPDATE', 'tpl_admin_discount_group_manage');
define('TPL_ADMIN_EMAIL_ARCHIVE_PREVIEW', 'tpl_admin_email_archive_preview');
define('TPL_ADMIN_EMAIL_ARCHIVE_VIEW', 'tpl_admin_email_archive_view');
define('TPL_ADMIN_EMAIL_MAILING_LISTS_VIEW', 'tpl_admin_email_mailing_lists_view');
define('TPL_ADMIN_EMAIL_MAILING_LISTS_CREATE', 'tpl_admin_email_mailing_lists_manage');
define('TPL_ADMIN_EMAIL_MAILING_LISTS_UPDATE', 'tpl_admin_email_mailing_lists_manage');
define('TPL_ADMIN_EMAIL_MAILING_LIST_MODULES_MANAGE', 'tpl_admin_email_mailing_list_modules_manage');
define('TPL_ADMIN_EMAIL_FLUSH_QUEUE', 'tpl_admin_email_flush_queue');
define('TPL_ADMIN_EMAIL_FOLLOW_UPS_VIEW', 'tpl_admin_email_follow_ups_view');
define('TPL_ADMIN_EMAIL_FOLLOW_UPS_CREATE', 'tpl_admin_email_follow_ups_manage');
define('TPL_ADMIN_EMAIL_FOLLOW_UPS_UPDATE', 'tpl_admin_email_follow_ups_manage');
define('TPL_ADMIN_EMAIL_MASS_EMAIL', 'tpl_admin_email_mass_email');
define('TPL_ADMIN_EMAIL_MEMBERS_MAILING_LISTS_VIEW', 'tpl_admin_email_members_mailng_lists_view');
define('TPL_ADMIN_EMAIL_QUEUE_PREVIEW', 'tpl_admin_email_queue_preview');
define('TPL_ADMIN_EMAIL_QUEUE_VIEW', 'tpl_admin_email_queue_view');
define('TPL_ADMIN_EMAIL_SEND_USER', 'tpl_admin_email_send_user');
define('TPL_ADMIN_EMAIL_TEMPLATES_VIEW', 'tpl_admin_email_templates_view');
define('TPL_ADMIN_EMAIL_TEMPLATES_CREATE', 'tpl_admin_email_templates_manage');
define('TPL_ADMIN_EMAIL_TEMPLATES_UPDATE', 'tpl_admin_email_templates_manage');
define('TPL_ADMIN_ERROR_PAGES_VIEW', 'tpl_admin_error_pages_view');
define('TPL_ADMIN_EVENTS_CALENDAR_EVENTS', 'tpl_admin_events_calendar_events');
define('TPL_ADMIN_EVENTS_CALENDAR_VIEW', 'tpl_admin_events_calendar_view');
define('TPL_ADMIN_EVENTS_CALENDAR_CREATE', 'tpl_admin_events_calendar_manage');
define('TPL_ADMIN_EVENTS_CALENDAR_UPDATE', 'tpl_admin_events_calendar_manage');
define('TPL_ADMIN_FAQ_UPDATE', 'tpl_admin_faq_manage');
define('TPL_ADMIN_FAQ_VIEW', 'tpl_admin_faq_view');
define('TPL_ADMIN_FORMS_UPDATE', 'tpl_admin_forms_manage');
define('TPL_ADMIN_FORMS_VIEW', 'tpl_admin_forms_view');
define('TPL_ADMIN_FORM_FIELDS_MANAGE', 'tpl_admin_form_fields_manage');
define('TPL_ADMIN_FORM_FIELD_UPDATE', 'tpl_admin_form_field_manage');
define('TPL_ADMIN_FORUM_CATEGORIES_VIEW', 'tpl_admin_forum_categories_view');
define('TPL_ADMIN_FORUM_CATEGORIES_CREATE', 'tpl_admin_forum_categories_manage');
define('TPL_ADMIN_FORUM_CATEGORIES_UPDATE', 'tpl_admin_forum_categories_manage');
define('TPL_ADMIN_FORUM_TOPICS_VIEW', 'tpl_admin_forum_topics_view');
define('TPL_ADMIN_FORUM_TOPICS_CREATE', 'tpl_admin_forum_topics_create');
define('TPL_ADMIN_FORUM_TOPICS_UPDATE', 'tpl_admin_forum_topics_manage');
define('TPL_ADMIN_GALLERY_VIEW', 'tpl_admin_gallery_view');
define('TPL_ADMIN_GALLERY_CREATE', 'tpl_admin_gallery_manage');
define('TPL_ADMIN_GALLERY_UPDATE', 'tpl_admin_gallery_manage');
define('TPL_ADMIN_INVOICES_CREATE', 'tpl_admin_invoices_create');
define('TPL_ADMIN_INVOICES_MANAGE', 'tpl_admin_invoices_manage');
define('TPL_ADMIN_INVOICES_PAYMENTS_MANAGE', 'tpl_admin_invoices_payments_manage');
define('TPL_ADMIN_INVOICES_VIEW', 'tpl_admin_invoices_view');
define('TPL_ADMIN_INVOICES_PRINT', 'tpl_admin_invoices_print');
define('TPL_ADMIN_INVOICE_PAYMENTS_VIEW', 'tpl_admin_invoices_payments_view');
define('TPL_ADMIN_KB_ARTICLES_CREATE', 'tpl_admin_kb_articles_manage');
define('TPL_ADMIN_KB_ARTICLES_UPDATE', 'tpl_admin_kb_articles_manage');
define('TPL_ADMIN_KB_ARTICLES_VIEW', 'tpl_admin_kb_articles_view');
define('TPL_ADMIN_KB_CATEGORIES_CREATE', 'tpl_admin_kb_categories_manage');
define('TPL_ADMIN_KB_CATEGORIES_UPDATE', 'tpl_admin_kb_categories_manage');
define('TPL_ADMIN_KB_CATEGORIES_VIEW', 'tpl_admin_kb_categories_view');
define('TPL_ADMIN_LANGUAGES_CREATE', 'tpl_admin_languages_manage');
define('TPL_ADMIN_LANGUAGES_UPDATE', 'tpl_admin_languages_manage');
define('TPL_ADMIN_LANGUAGES_ENTRIES_UPDATE', 'tpl_admin_languages_entries_manage');
define('TPL_ADMIN_LANGUAGES_VIEW', 'tpl_admin_languages_view');
define('TPL_ADMIN_LAYOUT_DASHBOARD_ICONS', 'tpl_admin_layout_dashboard_icons');
define('TPL_ADMIN_LAYOUT_DASHBOARD_UPDATE', 'tpl_admin_layout_dashboard_update');
define('TPL_ADMIN_LAYOUT_MANAGE', 'tpl_admin_layout_manage');
define('TPL_ADMIN_LAYOUT_VIEW', 'tpl_admin_layout_view');
define('TPL_ADMIN_LICENSE', 'tpl_admin_license');
define('TPL_ADMIN_LOGIN', 'tpl_admin_login');
define('TPL_ADMIN_MEASUREMENTS_MANAGE', 'tpl_admin_measurements_manage');
define('TPL_ADMIN_MEMBERS_ADDRESSES_MANAGE', 'tpl_admin_members_addresses_manage');
define('TPL_ADMIN_MEMBER_CREDITS_VIEW', 'tpl_admin_member_credits_view');
define('TPL_ADMIN_MEMBER_CREDITS_CREATE', 'tpl_admin_member_credits_manage');
define('TPL_ADMIN_MEMBER_CREDITS_UPDATE', 'tpl_admin_member_credits_manage');
define('TPL_ADMIN_MEMBERS_VIEW', 'tpl_admin_members_view');
define('TPL_ADMIN_MEMBER_POINTS_VIEW', 'tpl_admin_member_points_view');
define('TPL_ADMIN_MEMBERS_SUBSCRIPTIONS_VIEW', 'tpl_admin_members_subscriptions_view');
define('TPL_ADMIN_MEMBERS_SUBSCRIPTIONS_CREATE', 'tpl_admin_members_subscriptions_manage');
define('TPL_ADMIN_MEMBERS_SUBSCRIPTIONS_UPDATE', 'tpl_admin_members_subscriptions_manage');
define('TPL_ADMIN_MEMBERS_UPDATE', 'tpl_admin_members_manage');
define('TPL_ADMIN_MODULES_CREATE', 'tpl_admin_modules_manage');
define('TPL_ADMIN_MODULES_UPDATE', 'tpl_admin_modules_manage');
define('TPL_ADMIN_MODULES_VIEW', 'tpl_admin_modules_view');
define('TPL_ADMIN_ORDERS_CREATE', 'tpl_admin_orders_create');
define('TPL_ADMIN_ORDERS_GIFT_CERTIFICATES_CREATE', 'tpl_admin_orders_gift_certificates_manage');
define('TPL_ADMIN_ORDERS_GIFT_CERTIFICATES_UPDATE', 'tpl_admin_orders_gift_certificates_manage');
define('TPL_ADMIN_ORDERS_GIFT_CERTIFICATES_VIEW', 'tpl_admin_orders_gift_certificates_view');
define('TPL_ADMIN_ORDERS_GIFT_CERTIFICATES_HISTORY', 'tpl_admin_orders_gift_certificates_history');
define('TPL_ADMIN_ORDERS_PRINT', 'tpl_admin_orders_print');
define('TPL_ADMIN_ORDERS_STATUSES', 'tpl_admin_orders_statuses');
define('TPL_ADMIN_ORDERS_UPDATE', 'tpl_admin_orders_manage');
define('TPL_ADMIN_ORDERS_VIEW', 'tpl_admin_orders_view');
define('TPL_ADMIN_PAYMENT_OPTIONS_VIEW', 'tpl_admin_payment_options_view');
define('TPL_ADMIN_PHPINFO', 'tpl_admin_phpinfo');
define('TPL_ADMIN_PROCESS_LOGIN', 'tpl_admin_process_login');
define('TPL_ADMIN_PRODUCTS_ASSIGN_AFFILIATE_GROUPS', 'tpl_admin_products_assign_affiliate_groups');
define('TPL_ADMIN_PRODUCTS_ASSIGN_ATTRIBUTES', 'tpl_admin_products_assign_attributes');
define('TPL_ADMIN_PRODUCTS_ASSIGN_SPECIFICATIONS', 'tpl_admin_products_assign_specifications');
define('TPL_ADMIN_PRODUCTS_ATTRIBUTES_VIEW', 'tpl_admin_products_attributes_view');
define('TPL_ADMIN_PRODUCTS_CATEGORIES_CREATE', 'tpl_admin_products_categories_manage');
define('TPL_ADMIN_PRODUCTS_CATEGORIES_UPDATE', 'tpl_admin_products_categories_manage');
define('TPL_ADMIN_PRODUCTS_CATEGORIES_VIEW', 'tpl_admin_products_categories_view');
define('TPL_ADMIN_PRODUCTS_DOWNLOADS_VIEW', 'tpl_admin_products_downloads_view');
define('TPL_ADMIN_PRODUCTS_DOWNLOADS_CREATE', 'tpl_admin_products_downloads_manage');
define('TPL_ADMIN_PRODUCTS_DOWNLOADS_UPDATE', 'tpl_admin_products_downloads_manage');
define('TPL_ADMIN_PRODUCTS_FILTERS_UPDATE', 'tpl_admin_products_filters_manage');
define('TPL_ADMIN_PRODUCTS_FILTERS_VIEW', 'tpl_admin_products_filters_view');
define('TPL_ADMIN_PRODUCTS_REVIEWS_VIEW_CREATE', 'tpl_admin_products_reviews_manage');
define('TPL_ADMIN_PRODUCTS_REVIEWS_VIEW_UPDATE', 'tpl_admin_products_reviews_manage');
define('TPL_ADMIN_PRODUCTS_REVIEWS_VIEW', 'tpl_admin_products_reviews_view');
define('TPL_ADMIN_PRODUCTS_SPECIFICATIONS_CREATE', 'tpl_admin_products_specifications_manage');
define('TPL_ADMIN_PRODUCTS_SPECIFICATIONS_UPDATE', 'tpl_admin_products_specifications_manage');
define('TPL_ADMIN_PRODUCTS_SPECIFICATIONS_VIEW', 'tpl_admin_products_specifications_view');
define('TPL_ADMIN_PRODUCTS_AFFILIATE_UPDATE', 'tpl_admin_products_affiliate_manage');
define('TPL_ADMIN_PRODUCTS_CERTIFICATE_UPDATE', 'tpl_admin_products_certificate_manage');
define('TPL_ADMIN_PRODUCTS_UPDATE', 'tpl_admin_products_manage');
define('TPL_ADMIN_PRODUCTS_TAGS_VIEW', 'tpl_admin_products_tags_view');
define('TPL_ADMIN_PRODUCTS_VIEW', 'tpl_admin_products_view');
define('TPL_ADMIN_PRODUCTS_ATTRIBUTES_CREATE', 'tpl_admin_products_attributes_manage');
define('TPL_ADMIN_PRODUCTS_ATTRIBUTES_UPDATE', 'tpl_admin_products_attributes_manage');
define('TPL_ADMIN_PROMOTIONAL_RULES_VIEW', 'tpl_admin_promotional_rules_view');
define('TPL_ADMIN_PROMOTIONAL_RULES_CREATE', 'tpl_admin_promotional_rules_manage');
define('TPL_ADMIN_PROMOTIONAL_RULES_UPDATE', 'tpl_admin_promotional_rules_manage');
define('TPL_ADMIN_QUEUE_MASS_EMAIL', 'tpl_admin_queue_mass_email');
define('TPL_ADMIN_REGIONS_CREATE', 'tpl_admin_regions_manage');
define('TPL_ADMIN_REGIONS_UPDATE', 'tpl_admin_regions_manage');
define('TPL_ADMIN_REGIONS_VIEW', 'tpl_admin_regions_view');
define('TPL_ADMIN_REPORT_ARCHIVE_GENERATE', 'tpl_admin_report_archive_generate');
define('TPL_ADMIN_REPORTS_ARCHIVE_VIEW', 'tpl_admin_reports_archive_view');
define('TPL_ADMIN_REPORTS_VIEW', 'tpl_admin_reports_view');
define('TPL_ADMIN_RESET_PASS', 'tpl_admin_reset_password');
define('TPL_ADMIN_RESET_PASS_CONFIRM', 'tpl_admin_reset_pass_confirm');
define('TBL_ADMIN_RESTORE_DATABASE', 'tpl_admin_restore_database');
define('TPL_ADMIN_RESULTS_VIEW', 'tpl_admin_results_view');
define('TPL_ADMIN_REWARDS_VIEW', 'tpl_admin_rewards_view');
define('TPL_ADMIN_REWARDS_HISTORY_VIEW', 'tpl_admin_rewards_history_view');
define('TPL_ADMIN_REWARDS_CREATE', 'tpl_admin_rewards_manage');
define('TPL_ADMIN_REWARDS_UPDATE', 'tpl_admin_rewards_manage');
define('TPL_ADMIN_SEARCH_RESULTS', 'tpl_admin_search_results');
define('TPL_ADMIN_SETTINGS', 'tpl_admin_settings');
define('TPL_ADMIN_SETTINGS_SIDEBAR', 'tpl_admin_settings_sidebar');
define('TPL_ADMIN_SHIPPING_UPDATE', 'tpl_admin_shipping_manage');
define('TPL_ADMIN_SHIPPING_VIEW', 'tpl_admin_shipping_view');
define('TPL_ADMIN_SITE_BUILDER_LAYOUT','tpl_admin_site_builder_layout');
define('TPL_ADMIN_SITE_BUILDER_UPDATE', 'tpl_admin_site_builder_manage');
define('TPL_ADMIN_SITE_BUILDER_BASIC_JS', 'tpl_admin_site_builder_basic_js');
define('TPL_ADMIN_SITE_BUILDER_CONTENT_JS', 'tpl_admin_site_builder_content_js');
define('TPL_ADMIN_SITE_BUILDER_EXAMPLES_JS', 'tpl_admin_site_builder_examples_js');
define('TPL_ADMIN_SITE_BUILDER_IDEAS_HTML', 'tpl_admin_site_builder_ideas_html');
define('TPL_ADMIN_SITE_LAYOUT_VIEW', 'tpl_admin_site_layout_view');
define('TPL_ADMIN_SITE_MAP_VIEW', 'tpl_admin_site_map_view');
define('TPL_ADMIN_SITE_MENUS_CREATE', 'tpl_admin_site_menus_manage');
define('TPL_ADMIN_SITE_MENUS_UPDATE', 'tpl_admin_site_menus_manage');
define('TPL_ADMIN_SITE_MENUS_VIEW', 'tpl_admin_site_menus_view');
define('TPL_ADMIN_SITE_PAGES_VIEW', 'tpl_admin_site_pages_view');
define('TPL_ADMIN_SITE_PAGES_CREATE', 'tpl_admin_site_pages_manage');
define('TPL_ADMIN_SITE_PAGES_UPDATE', 'tpl_admin_site_pages_manage');
define('TPL_ADMIN_SLIDE_SHOWS_VIEW', 'tpl_admin_slide_shows_view');
define('TPL_ADMIN_SLIDE_SHOWS_CREATE', 'tpl_admin_slide_shows_manage');
define('TPL_ADMIN_SLIDE_SHOWS_UPDATE', 'tpl_admin_slide_shows_manage');
define('TPL_ADMIN_SITE_ADDRESS_MANAGE', 'tpl_admin_site_address_manage');
define('TPL_ADMIN_SITE_ADDRESSES', 'tpl_admin_site_addresses');
define('TPL_ADMIN_SUPPORT_CATEGORIES_CREATE', 'tpl_admin_support_categories_manage');
define('TPL_ADMIN_SUPPORT_CATEGORIES_UPDATE', 'tpl_admin_support_categories_manage');
define('TPL_ADMIN_SUPPORT_CATEGORIES_VIEW', 'tpl_admin_support_categories_view');
define('TPL_ADMIN_SUPPORT_PREDEFINED_REPLIES_CREATE', 'tpl_admin_support_predefined_replies_manage');
define('TPL_ADMIN_SUPPORT_PREDEFINED_REPLIES_UPDATE', 'tpl_admin_support_predefined_replies_manage');
define('TPL_ADMIN_SUPPORT_PREDEFINED_REPLIES_VIEW', 'tpl_admin_support_predefined_replies_view');
define('TPL_ADMIN_SUPPORT_TICKETS_VIEW', 'tpl_admin_support_tickets_view');
define('TPL_ADMIN_SUPPORT_TICKET_CREATE', 'tpl_admin_support_ticket_create');
define('TPL_ADMIN_SUPPORT_TICKET_UPDATE', 'tpl_admin_support_ticket_manage');
define('TPL_ADMIN_SYSTEM_PAGES_VIEW', 'tpl_admin_system_pages_view');
define('TPL_ADMIN_SYSTEM_PAGES_UPDATE', 'tpl_admin_system_pages_manage');
define('TPL_ADMIN_TAX_CLASSES_CREATE', 'tpl_admin_tax_classes_manage');
define('TPL_ADMIN_TAX_CLASSES_UPDATE', 'tpl_admin_tax_classes_manage');
define('TPL_ADMIN_TAX_CLASSES_VIEW', 'tpl_admin_tax_classes_view');
define('TPL_ADMIN_TAX_RATES_ASSIGN','tpl_admin_tax_rates_assign');
define('TPL_ADMIN_TAX_RATES_CREATE', 'tpl_admin_tax_rates_manage');
define('TPL_ADMIN_TAX_RATES_UPDATE', 'tpl_admin_tax_rates_manage');
define('TPL_ADMIN_TAX_RATES_VIEW', 'tpl_admin_tax_rates_view');
define('TPL_ADMIN_TEMPLATE_MANAGER_UPDATE', 'tpl_admin_template_manager_manage');
define('TPL_ADMIN_TEMPLATE_MANAGER_VIEW', 'tpl_admin_template_manager_view');
define('TPL_ADMIN_THEMES_VIEW', 'tpl_admin_themes_view');
define('TPL_ADMIN_TRACKING_VIEW', 'tpl_admin_tracking_view');
define('TPL_ADMIN_TRACKING_CREATE', 'tpl_admin_tracking_manage');
define('TPL_ADMIN_TRACKING_UPDATE', 'tpl_admin_tracking_manage');
define('TPL_ADMIN_TRACKING_REFERRALS_VIEW', 'tpl_admin_tracking_referrals_view');
define('TPL_ADMIN_TRANSACTIONS_VIEW', 'tpl_admin_transactions_view');
define('TPL_ADMIN_UPDATES_RUN', 'tpl_admin_updates_run');
define('TPL_ADMIN_UPDATES_VIEW', 'tpl_admin_updates_view');
define('TPL_ADMIN_UTILITIES_VIEW', 'tpl_admin_utilities_view');
define('TPL_ADMIN_SUPPLIERS_CREATE', 'tpl_admin_suppliers_manage');
define('TPL_ADMIN_SUPPLIERS_UPDATE', 'tpl_admin_suppliers_manage');
define('TPL_ADMIN_SUPPLIERS_VIEW', 'tpl_admin_suppliers_view');
define('TPL_ADMIN_VIDEOS_MANAGE', 'tpl_admin_videos_manage');
define('TPL_ADMIN_VIDEOS_VIEW', 'tpl_admin_videos_view');
define('TPL_ADMIN_VIDEOS_CREATE', 'tpl_admin_videos_manage');
define('TPL_ADMIN_VIDEOS_UPDATE', 'tpl_admin_videos_manage');
define('TPL_ADMIN_WIDGETS_CREATE', 'tpl_admin_widgets_manage');
define('TPL_ADMIN_WIDGETS_UPDATE', 'tpl_admin_widgets_manage');
define('TPL_ADMIN_WIDGETS_VIEW', 'tpl_admin_widgets_view');
define('TPL_ADMIN_WISH_LISTS_VIEW', 'tpl_admin_wish_lists_view');
define('TPL_ADMIN_ZONES_CREATE', 'tpl_admin_zones_manage');
define('TPL_ADMIN_ZONES_UPDATE', 'tpl_admin_zones_manage');
define('TPL_ADMIN_ZONES_VIEW', 'tpl_admin_zones_view');
define('TPL_AJAX_LOAD_REVISIONS', 'tpl_ajax_load_revisions');
define('TPL_AJAX_MEMBERS_EMAIL_ARCHIVE', 'tpl_ajax_members_email_archive');
define('TPL_AJAX_MEMBERS_NOTES', 'tpl_ajax_members_notes');
define('TPL_AJAX_ORDERS_ADDRESSES', 'tpl_ajax_orders_addresses');
define('TPL_AJAX_ORDERS_BILLING_INFORMATION', 'tpl_ajax_orders_billing_information');
define('TPL_AJAX_ORDERS_CLIENT_PROFILE', 'tpl_ajax_orders_client_profile');
define('TPL_AJAX_ORDERS_GENERATE_PAYMENT', 'tpl_ajax_orders_generate_payment');
define('TPL_AJAX_ORDERS_PRINT_POSTAGE', 'tpl_ajax_orders_print_postage');
define('TPL_AJAX_ORDERS_PRODUCT_ATTRIBUTES','tpl_ajax_orders_product_attributes');
define('TPL_AJAX_ORDERS_PRODUCT_CONTENTS','tpl_ajax_orders_product_contents');
define('TPL_AJAX_ORDERS_SET_DISCOUNTS', 'tpl_ajax_orders_set_discounts');
define('TPL_AJAX_ORDERS_SET_SHIPPING', 'tpl_ajax_orders_set_shipping');
define('TPL_AJAX_ORDERS_SHIPPING_OPTIONS', 'tpl_ajax_orders_shipping_options');
define('TPL_AJAX_ORDERS_UPDATE_PRODUCT_CONTENTS', 'tpl_ajax_orders_update_product_contents');
define('TPL_AJAX_MEMBER_INVOICES_VIEW', 'tpl_ajax_member_invoices_view');
define('TPL_AJAX_MEMBER_TICKETS_VIEW', 'tpl_ajax_member_tickets_view');
define('TPL_AJAX_MEMBER_COMMISSIONS_VIEW', 'tpl_ajax_member_commissions_view');
define('TPL_AJAX_MEMBER_SUBSCRIPTIONS_VIEW', 'tpl_ajax_member_subscriptions_view');

/* End of file admin_defines.php */
/* Location: ./application/config/admin_defines.php */