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
| These are set for global definitions
|
*/

define('CACHE_TIME_LIMIT', 300);
define('DEFAULT_SKU_LENGTH', 8);
define('SUPPLIERS', 'suppliers');
define('MEMBERS_ROUTE', 'members');
define('CHECKOUT_ROUTE', 'checkout');
define('SQL_TIME_FORMAT', '%h:%i %p');
define('BACKUP_MEMORY_LIMIT', '512M');
define('BACKUP_FILES_PATH', APPPATH);

define('CRON_MEMORY_LIMIT', '512M');
define('CRON_TIME_LIMIT', '900');
define('TPL_AJAX_LIMIT', 25);
define('TPL_CATEGORY_PATH_SEPARATOR', ' / ');
define('TPL_FILE_EXT', 'tpl');
define('MEMBER_RECORD_LIMIT', 10000);
define('TINYMCE_COMPRESSOR', 'min'); //min or gzip
define('MAX_BLOG_POST_REVISIONS', 10);
define('MAX_CHARACTERS_BLOG_COMMENTS_VIEW', 500);
define('TAG_CLOUD_LIMIT', '100');
define('SITE_MAP_LIMIT', 50000);
define('RSS_LIMIT', 50000);
define('DEFAULT_TOTAL_SIMILAR_PRODUCTS', 4);
define('DEFAULT_TOTAL_RELATED_BLOGS', 6);
define('BLOG_PREPEND_LINK', 'post');
define('PRODUCT_PREPEND_LINK' , 'details');
define('DEFAULT_MEMBER_LOGOUT_PAGE', 'login');
define('DEFAULT_PAGINATION_CSS_SIZE', 'pagination-lg');
define('DEFAULT_TOTAL_FORUM_LATEST_TOPICS', 5);
define('TBL_MEMBERS_DEFAULT_TOTAL_ROWS', 10);

define('LOGIN_USERNAME_FIELD', 'primary_email');
define('DEFAULT_MEMBER_PASSWORD_LENGTH', 8);
define('CONFIRM_ID_LENGTH', 12);

define('ALERT_ANIMATION_SUCCESS', 'fadeIn');
define('ALERT_ANIMATION_ERROR', 'shake');
define('COOKIE_CONSENT', 'cookie_consent');
define('FIELD_READ_ONLY', 'readonly');

//product stuff
define('DEFAULT_PRODUCT_DATE_EXPIRATION', 788400000); //default is 25 years
define('DEFAULT_PRODUCT_DATE_AVAILABILITY', 2592000);
define('DEFAULT_PRODUCT_INVENTORY_AMOUNT', 1000);
define('DEFAULT_DECIMAL_ROUNDOFF', 2);
define('DEFAULT_PRODUCT_TAX_CLASS', 1);
define('DEFAULT_COLUMN_DECIMAL_LENGTH' , 15);
define('DEFAULT_ROUND_UP', PHP_ROUND_HALF_UP);
define('DEFAULT_VAT_FORMULA', 'EUROPE');
define('PERCENT_LC', 20);
define('NUMBER_LC_CHECK', 5);

//comment out and remove if you dont want gift certificates calculated  as a discount
//define('TREAT_GIFT_CERTIFICATES_AS_DISCOUNTS', TRUE);

define('DEFAULT_FILE_UPLOAD_BUTTON_CSS', 'btn btn-secondary btn-block');

//affiliate stuff
define('DEFAULT_AFFILIATE_USERNAME_URI', 1);
define('DEFAULT_AFFILIATE_TOOL_URI', 2);
define('DEFAULT_AFFILIATE_TOOL_MODULE_URI', 3);
define('DEFAULT_AFFILIATE_TOOL_ID_URI', 4);
define('DEFAULT_AFFILIATE_LINK_PROTOCOL', 'http://');
define('DEFAULT_ATTRIBUTE_TEXTAREA_LENGTH', '1000');
define('REQUIRE_UNIQUE_AFFILIATE_TRANSACTION_IDS', TRUE);
//define('AFFILIATE_MARKETING_CHARGE_FEES', TRUE); //only if you want to charge a fee for each commission earned

//default photo
define('DEFAULT_PHOTO_MAX_WIDTH', 1920);
define('DEFAULT_PHOTO_MAX_HEIGHT', 1080);
define('DEFAULT_PHOTO_MEMBERS_UPLOAD_PATH', PUBPATH . '/images/uploads/members');

//FORUM
define('DISABLE_CODE_ON_FORUM_POSTS', TRUE);

//updates
define('DEFAULT_FILE_UPDATES_UPLOAD_PATH', APPPATH . 'updates');
define('DEFAULT_FILE_UPDATES_UPLOAD_TYPE', 'zip');
define('DELETE_UPDATE_FILE_AFTER_INSTALL', TRUE);

//content
define('DEFAULT_CONTENT_UPLOAD_PATH', PUBPATH . '/images/uploads/content/');
define('DEFAULT_CONTENT_SITE__BUILDER_PATH', 'images/uploads/content/');
define('AUTO_SAVE_SITE_BUILDER_PAGES', FALSE);

//dashboard
define('DASHBOARD_ANIMATED', 'animated');

//for generating serial codes for gift certificates...
define('SERIAL_CODE_LENGTH', 5);  //make sure to set this to at least 3 or it will error out..
define('SERIAL_CODE_PARTS', 4); //must be at least 1
define('SERIAL_CODE_UPPERCASE', TRUE);
define('SERIAL_CODE_STRING_TYPE', 'alnum'); //alpha, alnum, basic, numeric, nozero, md5, sha1
define('DEFAULT_COUPON_CODE_LENGTH', 14);
define('DEFAULT_GIFT_CERTIFICATE_THEME_ID', 1);

//order stuff
define('ORDER_NUMBER_GENERATOR_LENGTH', 10);
define('ORDER_NUMBER_GENERATOR_TYPE', 'numeric'); //alnum numeric or alpha

//api stuff
define('API_DEFAULT_NUMBER_ROWS', 25);

define('GC_CART_INTERVAL', 30); //garbage collector for cart table in days

//recaptcha
define('CAPTCHA_FIELD', 'g-recaptcha-response');
define('CAPTCHA_SERVER', 'https://www.google.com/recaptcha/api/siteverify');

//ip lookup
define('EXTERNAL_IP_LOOKUP', 'https://www.melissa.com/v2/lookups/iplocation/ip/');
define('MIN_AGE_LIMIT', 13);
define('MAX_AGE_LIMIT', 24);

//tables
define('TBL_ADMIN_ALERTS', 'admin_alerts');
define('TBL_ADMIN_GROUPS', 'admin_groups');
define('TBL_ADMIN_USERS', 'admin_users');
define('TBL_AFFILIATE_ARTICLE_ADS', 'affiliate_article_ads');
define('TBL_AFFILIATE_BANNERS', 'affiliate_banners');
define('TBL_AFFILIATE_COMMISSIONS', 'affiliate_commissions');
define('TBL_AFFILIATE_COMMISSION_RULES', 'affiliate_commission_rules');
define('TBL_AFFILIATE_EMAIL_ADS', 'affiliate_email_ads');
define('TBL_AFFILIATE_GROUPS', 'affiliate_groups');
define('TBL_AFFILIATE_HTML_ADS', 'affiliate_html_ads');
define('TBL_AFFILIATE_PAYMENTS', 'affiliate_payments');
define('TBL_AFFILIATE_TEXT_ADS', 'affiliate_text_ads');
define('TBL_AFFILIATE_TEXT_LINKS', 'affiliate_text_links');
define('TBL_AFFILIATE_TRAFFIC', 'affiliate_traffic');
define('TBL_AFFILIATE_VIRAL_PDFS', 'affiliate_viral_pdfs');
define('TBL_AFFILIATE_VIRAL_VIDEOS', 'affiliate_viral_videos');
define('TBL_ATTRIBUTE_CATEGORIES', 'attribute_categories');
define('TBL_BLOG_CATEGORIES', 'blog_categories');
define('TBL_BLOG_CATEGORIES_NAME', 'blog_categories_name');
define('TBL_BLOG_POSTS', 'blog_posts');
define('TBL_BLOG_POSTS_REVISIONS', 'blog_posts_revisions');
define('TBL_BLOG_POSTS_REVISIONS_NAME', 'blog_posts_revisions_name');
define('TBL_BLOG_TO_DOWNLOADS', 'blog_to_downloads');
define('TBL_BLOG_TO_GROUPS', 'blog_to_groups');
define('TBL_BLOG_TO_TAGS', 'blog_to_tags');
define('TBL_BLOG_TAGS', 'blog_tags');
define('TBL_BLOG_POSTS_NAME', 'blog_posts_name');
define('TBL_BRANDS', 'brands');
define('TBL_BRANDS_NAME', 'brands_name');
define('TBL_BLOG_COMMENTS', 'blog_comments');
define('TBL_BLOG_PERMISSIONS', 'blog_permissions');
define('TBL_CART', 'cart');
define('TBL_CART_ITEMS', 'cart_items');
define('TBL_CART_TOTALS', 'cart_totals');
define('TBL_CART_UPLOADS', 'cart_uploads');
define('TBL_CC_TYPES', 'cc_types');
define('TBL_BLOG_GROUPS', 'blog_groups');
define('TBL_COUNTRIES', 'countries');
define('TBL_COUPONS', 'coupons');
define('TBL_COUPONS_PRODUCTS', 'coupons_products');
define('TBL_CURRENCIES', 'currencies');
define('TBL_DISCOUNT_GROUPS', 'discount_groups');
define('TBL_EMAIL_ARCHIVE', 'email_archive');
define('TBL_EMAIL_BOUNCES', 'email_bounces');
define('TBL_EMAIL_FOLLOW_UPS', 'email_follow_ups');
define('TBL_EMAIL_FOLLOW_UPS_NAME', 'email_follow_ups_name');
define('TBL_EMAIL_MAILING_LISTS', 'email_mailing_lists');
define('TBL_EMAIL_QUEUE', 'email_queue');
define('TBL_EMAIL_TEMPLATES', 'email_templates');
define('TBL_EMAIL_TEMPLATES_NAME', 'email_templates_name');
define('TBL_EVENTS_CALENDAR', 'events_calendar');
define('TBL_FAQ', 'faq');
define('TBL_FAQ_NAME', 'faq_name');
define('TBL_FORMS', 'forms');
define('TBL_FORM_FIELDS', 'form_fields');
define('TBL_FORM_FIELDS_NAME', 'form_fields_name');
define('TBL_FORUM_CATEGORIES', 'forum_categories');
define('TBL_FORUM_CATEGORIES_NAME', 'forum_categories_name');
define('TBL_FORUM_TOPICS', 'forum_topics');
define('TBL_FORUM_TOPICS_REPLIES', 'forum_topics_replies');
define('TBL_GALLERY', 'gallery');
define('TBL_INVOICE_PAYMENTS', 'invoice_payments');
define('TBL_INVOICE_ITEMS', 'invoice_items');
define('TBL_INVOICE_TOTALS', 'invoice_totals');
define('TBL_INVOICES', 'invoices');
define('TBL_IPN_LOG', 'ipn_log');
define('TBL_KB_CATEGORIES', 'kb_categories');
define('TBL_KB_CATEGORIES_NAME', 'kb_categories_name');
define('TBL_KB_CATEGORIES_PATH', 'kb_categories_path');
define('TBL_KB_ARTICLES', 'kb_articles');
define('TBL_KB_ARTICLES_NAME', 'kb_articles_name');
define('TBL_KB_TO_DOWNLOADS', 'kb_to_downloads');
define('TBL_KB_TO_VIDEOS', 'kb_to_videos');
define('TBL_LANGUAGES', 'languages');
define('TBL_LAYOUT_BOXES', 'layout_boxes');
define('TBL_LAYOUT_MENUS', 'layout_menus');
define('TBL_MEASUREMENTS', 'measurements');
define('TBL_MEMBERS', 'members');
define('TBL_MEMBERS_ADDRESSES', 'members_addresses');
define('TBL_MEMBERS_ALERTS', 'members_alerts');
define('TBL_MEMBERS_AUTOSHIPMENTS', 'members_autoshipments');
define('TBL_MEMBERS_CREDITS', 'members_credits');
define('TBL_MEMBERS_CUSTOM_FIELDS', 'members_custom_fields');
define('TBL_MEMBERS_TO_CUSTOM_FIELDS', 'members_to_custom_fields');
define('TBL_MEMBERS_DASHBOARD', 'members_dashboard');
define('TBL_MEMBERS_DOWNLOADS', 'members_downloads');
define('TBL_MEMBERS_DISCOUNT_GROUPS', 'members_discount_groups');
define('TBL_MEMBERS_EMAIL_MAILING_LIST', 'members_email_mailing_list');
define('TBL_MEMBERS_AFFILIATE_GROUPS', 'members_affiliate_groups');
define('TBL_MEMBERS_SUBSCRIPTIONS', 'members_subscriptions');
define('TBL_MEMBERS_SUBSCRIPTIONS_HISTORY', 'members_subscriptions_history');
define('TBL_MEMBERS_NOTES', 'members_notes');
define('TBL_MEMBERS_PASSWORDS', 'members_passwords');
define('TBL_MEMBERS_PERMISSIONS', 'members_permissions');
define('TBL_MEMBERS_PROFILES', 'members_profiles');
define('TBL_MIGRATIONS', 'migrations');
define('TBL_MODULES', 'modules');
define('TBL_ORDERS', 'orders');
define('TBL_ORDERS_GIFT_CERTIFICATES', 'orders_gift_certificates');
define('TBL_ORDERS_GIFT_CERTIFICATES_HISTORY', 'orders_gift_certificates_history');
define('TBL_ORDERS_ITEMS', 'orders_items');
define('TBL_ORDERS_ITEM_ATTRIBUTES', 'orders_item_attributes');
define('TBL_ORDERS_SHIPPING', 'orders_shipping');
define('TBL_ORDERS_STATUS', 'orders_status');
define('TBL_PAYMENT_STATUS', 'payment_status');
define('TBL_PRODUCTS', 'products');
define('TBL_PRODUCTS_ATTRIBUTES', 'products_attributes');
define('TBL_PRODUCTS_ATTRIBUTES_NAME', 'products_attributes_name');
define('TBL_PRODUCTS_ATTRIBUTES_OPTIONS_NAME','products_attribute_options_name');
define('TBL_PRODUCTS_CATEGORIES', 'products_categories');
define('TBL_PRODUCTS_CATEGORIES_NAME', 'products_categories_name');
define('TBL_PRODUCTS_CATEGORIES_PATH', 'products_categories_path');
define('TBL_PRODUCTS_DOWNLOADS', 'products_downloads');
define('TBL_PRODUCTS_DOWNLOADS_NAME', 'products_downloads_name');
define('TBL_PRODUCTS_FILTERS', 'products_filters');
define('TBL_PRODUCTS_FILTERS_VALUES', 'products_filters_values');
define('TBL_PRODUCTS_NAME', 'products_name');
define('TBL_PRODUCTS_PHOTOS', 'products_photos');
define('TBL_PRODUCTS_REVIEWS', 'products_reviews');
define('TBL_PRODUCTS_TAGS', 'products_tags');
define('TBL_PRODUCTS_TO_AFF_GROUPS', 'products_to_aff_groups');
define('TBL_PRODUCTS_TO_ATTRIBUTES', 'products_to_attributes');
define('TBL_PRODUCTS_ATTRIBUTES_OPTIONS','products_attribute_options');
define('TBL_PRODUCTS_SPECIFICATIONS', 'products_specifications');
define('TBL_PRODUCTS_SPECIFICATIONS_NAME', 'products_specifications_name');
define('TBL_PRODUCTS_SPECIFICATIONS_GROUP', 'products_specifications_group');
define('TBL_PRODUCTS_TO_SPECIFICATIONS_NAME', 'products_to_specifications_name');
define('TBL_PRODUCTS_TO_PRICING', 'products_to_pricing');
define('TBL_PRODUCTS_TO_ATTRIBUTES_VALUES', 'products_to_attributes_values');
define('TBL_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
define('TBL_PRODUCTS_TO_DISC_GROUPS', 'products_to_disc_groups');
define('TBL_PRODUCTS_TO_DOWNLOADS', 'products_to_downloads');
define('TBL_PRODUCTS_TO_RESTRICT_GROUPS', 'products_to_restrict_groups');
define('TBL_PRODUCTS_TO_WISH_LISTS', 'products_to_wish_lists');
define('TBL_PRODUCTS_TO_TAGS', 'products_to_tags');
define('TBL_PRODUCTS_CROSS_SELLS', 'products_cross_sells');
define('TBL_PROMOTIONAL_RULES', 'promotional_rules');
define('TBL_PROMOTIONAL_ITEMS', 'promotional_items');
define('TBL_REGIONS', 'regions');
define('TBL_REPORT_ARCHIVE', 'report_archive');
define('TBL_REPORTS', 'reports');
define('TBL_REWARDS_HISTORY', 'rewards_history');
define('TBL_REWARDS', 'rewards');
define('TBL_SECURITY', 'security');
define('TBL_SESSIONS', 'sessions');
define('TBL_SETTINGS', 'settings');
define('TBL_SITE_MENUS_NAME', 'site_menus_name');
define('TBL_SITE_PAGES', 'site_pages');
define('TBL_SITE_PAGES_NAME', 'site_pages_name');
define('TBL_SLIDE_SHOWS', 'slide_shows');
define('TBL_SLIDE_SHOWS_NAME', 'slide_shows_name');
define('TBL_SUPPORT_CATEGORIES', 'support_categories');
define('TBL_SUPPORT_CATEGORIES_NAME', 'support_categories_name');
define('TBL_SUPPORT_TICKETS', 'support_tickets');
define('TBL_SUPPORT_TICKETS_REPLIES', 'support_tickets_replies');
define('TBL_SUPPORT_TICKETS_NOTES', 'support_tickets_notes');
define('TBL_SUPPORT_PREDEFINED_REPLIES', 'support_predefined_replies');
define('TBL_SYSTEM_PAGES', 'system_pages');
define('TBL_SYSTEM_PAGES_NAME', 'system_pages_name');
define('TBL_TAX_CLASSES', 'tax_classes');
define('TBL_TAX_RATES', 'tax_rates');
define('TBL_TAX_RATE_RULES', 'tax_rate_rules');
define('TBL_ZONES', 'zones');
define('TBL_TRACKING', 'tracking');
define('TBL_REGIONS_TO_ZONES', 'regions_to_zones');
define('TBL_TRACKING_REFERRALS', 'tracking_referrals');
define('TBL_TRANSACTIONS', 'transactions');
define('TBL_SUPPLIERS', 'suppliers');
define('TBL_WEIGHT', 'weight');
define('TBL_PAGE_TEMPLATES', 'page_templates');
define('TBL_CACHE', 'cache');
define('TBL_SITE_ADDRESSES', 'site_addresses');
define('TBL_SITE_MENUS', 'site_menus');
define('TBL_SITE_MENUS_LINKS', 'site_menus_links');
define('TBL_SITE_MENUS_LINKS_NAME', 'site_menus_links_name');
define('TBL_WIDGETS', 'widgets');
define('TBL_WIDGETS_CATEGORIES', 'widgets_categories');
define('TBL_WIDGETS_TO_CATEGORIES', 'widgets_to_categories');
define('TBL_MEMBERS_SPONSORS', 'members_sponsors');
define('TBL_MEMBERS_BLOG_GROUPS', 'members_blog_groups');
define('TBL_LANGUAGE_ENTRIES', 'language_entries');
define('TBL_LANGUAGE_ENTRIES_NAME', 'language_entries_name');
define('TBL_PRODUCTS_TO_VIDEOS', 'products_to_videos');
define('TBL_TIMERS', 'timers');
define('TBL_VIDEOS', 'videos');
define('TBL_WISH_LISTS', 'wish_lists');

/*
 *  Email Templates
 */

define('EMAIL_ADMIN_FAILED_LOGIN', 'admin_failed_login_template');
define('EMAIL_MEMBER_RESET_PASSWORD','member_reset_password_template');
define('EMAIL_ADMIN_RESET_PASSWORD', 'admin_reset_password_template');
define('EMAIL_MEMBER_DOWNLOAD_ACCESS', 'member_download_access_template');

define('EMAIL_MEMBER_AFFILIATE_SEND_DOWNLINE_EMAIL', 'member_affiliate_send_downline_email');

define('EMAIL_ADMIN_ALERT_COMMENT_MODERATION', 'admin_alert_comment_moderation_template');
define('ADMIN_EMAIL_EVENT_ALERT_TEMPLATE', 'admin_email_event_alert_template');

define('EMAIL_ADMIN_FORUM_REPLY_ALERT', 'admin_forum_reply_alert_template');
define('EMAIL_ADMIN_FORUM_TOPIC_ALERT', 'admin_forum_topic_alert_template');
define('EMAIL_MEMBER_FORUM_REPLY_ALERT', 'member_forum_reply_alert_template');

define('EMAIL_ADMIN_PRODUCT_INVENTORY_ALERT', 'admin_product_inventory_alert_template');
define('EMAIL_ADMIN_PRODUCT_ATTRIBUTE_INVENTORY_ALERT', 'admin_product_attribute_inventory_alert_template');
define('EMAIL_ADMIN_ALERT_PRODUCT_REVIEW_TEMPLATE', 'admin_alert_product_review_template');

define('EMAIL_ADMIN_ALERT_CONTACT_US', 'admin_alert_contact_us_template');
define('EMAIL_ADMIN_SEND_CUSTOM_FORM', 'admin_alert_send_custom_form_template');

//checkout emails
define('EMAIL_MEMBER_ORDER_DETAILS', 'member_order_details_template');
define('EMAIL_MEMBER_PAYMENT_INVOICE', 'member_payment_invoice_template');
define('EMAIL_MEMBER_AFFILIATE_COMMISSION', 'member_affiliate_commission_template');
define('EMAIL_ADMIN_ALERT_NEW_ORDER', 'admin_alert_new_order_template');
define('EMAIL_ADMIN_AFFILIATE_COMMISSION', 'admin_affiliate_commission_template' );
define('EMAIL_ADMIN_AFFILIATE_MARKETING_ACTIVATION','admin_affiliate_marketing_activation_template');
define('EMAIL_ADMIN_ALERT_SUPPLIER', 'admin_alert_supplier_template');
define('EMAIL_MEMBER_GIFT_CERTIFICATE_DETAILS', 'member_gift_certificate_details_template');

//tickets
define('EMAIL_MEMBER_CREATE_SUPPORT_TICKET', 'member_create_support_ticket_template');
define('EMAIL_MEMBER_SUPPORT_TICKET_REPLY', 'member_support_ticket_reply_template');
define('EMAIL_ADMIN_CREATE_SUPPORT_TICKET', 'admin_create_support_ticket_template');
define('EMAIL_ADMIN_SUPPORT_TICKET_REPLY', 'admin_support_ticket_reply_template');

//registration
define('EMAIL_ADMIN_ALERT_NEW_SIGNUP', 'admin_alert_new_signup_template');
define('EMAIL_MEMBER_LOGIN_DETAILS', 'member_login_details_template');
define('EMAIL_MEMBER_EMAIL_CONFIRMATION', 'member_email_confirmation_template');
define('EMAIL_MEMBER_AFFILIATE_DOWNLINE_SIGNUP', 'member_affiliate_downline_signup_template');
define('EMAIL_MEMBER_ALERT_SIGNUP_BONUS', 'member_alert_signup_bonus_template');
define('EMAIL_MEMBER_AFFILIATE_REFERRAL_SIGNUP_BONUS', 'member_affiliate_referral_signup_bonus_template');

//search
define('ADVANCED_SEARCH_FILE_DELIMITER', ',');
define('ADVANCED_SEARCH_FILE_EXTENSION', 'csv');

//EASYPOST
//define('USE_EASYPOST_SAVED_RATE_ID', TRUE);

/*
|--------------------------------------------------------------------------
| Portable PHP password hashing framework
|--------------------------------------------------------------------------
|
| http://www.openwall.com/phpass/
|
*/
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', FALSE);

/* End of file defines.php */
/* Location: ./application/config/defines.php */