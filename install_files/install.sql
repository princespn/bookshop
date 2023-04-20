
INSERT INTO `jrox_products_photos` (`photo_id`, `product_id`, `photo_file_name`, `product_default`) VALUES
(1, 1, '1.jpg', '1'),
(2, 2, '2.jpg', '1'),
(3, 3, '3.jpg', '1'),
(4, 4, '4.jpg', '1');
#####
INSERT INTO `jrox_products_to_pricing` (`prod_price_id`, `product_id`, `enable`, `default_price`, `amount`, `interval_amount`, `interval_type`, `recurrence`, `enable_initial_amount`, `initial_amount`, `initial_interval`, `initial_interval_type`, `name`, `description`) VALUES
(1, 1, '0', '1', '0.00000000', 1, '0', 0, '0', '0.00000000', 0, '0', 'new payment method', ''),
(2, 2, '0', '1', '0.00000000', 1, '0', 0, '0', '0.00000000', 0, '0', 'new payment method', ''),
(3, 3, '0', '1', '0.00000000', 1, '0', 0, '0', '0.00000000', 0, '0', 'new payment method', ''),
(4, 4, '0', '1', '0.00000000', 1, '0', 0, '0', '0.00000000', 0, '0', 'new payment method', '');

INSERT INTO `jrox_slide_shows` (`slide_id`, `status`, `type`, `name`, `start_date`, `end_date`, `meta_data`, `footer_data`, `text_color`, `background_color`, `background_image`, `position`, `sort_order`) VALUES
(1, '1', 'simple', 'slide show 1', '{{current_time}}', '2050-02-01 23:59:59', '', '', '#ffffff', 'rgba(0, 0, 0, 0.7)', '{{base_url}}/images/uploads/backgrounds/bg-0001.jpg', 'center', 0),
(2, '1', 'simple', 'slide show 2', '{{current_time}}', '2050-02-01 23:59:59', '', '', '#ffffff', 'rgba(0, 0, 0, 0.3)', '{{base_url}}/images/codeigniter-bg.jpg', 'center', 0),
(3, '1', 'simple', 'slide show 3', '{{current_time}}', '2050-02-01 23:59:59', '', '', '#ffffff', 'rgba(0, 0, 0, 0.3)', '{{base_url}}/images/uploads/backgrounds/bg-00021.jpg', 'center', 0);

INSERT INTO `jrox_slide_shows_name` (`slide_name_id`, `slide_id`, `language_id`, `headline`, `slide_show`, `button_text`) VALUES
(1, 1, 1, 'Your Own Full Blown eCommerce Suite', '<p>Shopping Cart. Site Builder. Affiliates. CMS. Help Desk.</p>', 'Learn More'),
(2, 2, 1, 'Powered By Codeigniter', '<p>Developer Friendly. Easy To Customize. Bootstrap.</p>', 'Learn More'),
(3, 3, 1, 'Everything In One Place', '<p>Get Started Today. Free Download or Hosting Trial.</p>', 'Learn More');

#####

ALTER TABLE `jrox_admin_alerts`
  ADD CONSTRAINT `jrox_admin_alerts_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `jrox_admin_users` (`admin_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_affiliate_commissions`
  ADD CONSTRAINT `jrox_affiliate_commissions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_affiliate_payments`
  ADD CONSTRAINT `jrox_affiliate_payments_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_affiliate_traffic`
  ADD CONSTRAINT `jrox_affiliate_traffic_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_categories_name`
  ADD CONSTRAINT `jrox_blog_categories_name_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `jrox_blog_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_blog_categories_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_comments`
  ADD CONSTRAINT `jrox_blog_comments_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `jrox_blog_posts` (`blog_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_permissions`
  ADD CONSTRAINT `jrox_blog_permissions_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `jrox_blog_posts` (`blog_id`);

#####

ALTER TABLE `jrox_blog_posts_name`
  ADD CONSTRAINT `jrox_blog_posts_name_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `jrox_blog_posts` (`blog_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_blog_posts_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_posts_revisions`
  ADD CONSTRAINT `jrox_blog_posts_revisions_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `jrox_blog_posts` (`blog_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_posts_revisions_name`
  ADD CONSTRAINT `jrox_blog_posts_revisions_name_ibfk_1` FOREIGN KEY (`revision_id`) REFERENCES `jrox_blog_posts_revisions` (`revision_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_blog_posts_revisions_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_to_downloads`
  ADD CONSTRAINT `jrox_blog_to_downloads_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `jrox_blog_posts` (`blog_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_blog_to_downloads_ibfk_2` FOREIGN KEY (`download_id`) REFERENCES `jrox_products_downloads` (`download_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_to_groups`
  ADD CONSTRAINT `jrox_blog_to_groups_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `jrox_blog_posts` (`blog_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_blog_to_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `jrox_blog_groups` (`group_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_blog_to_tags`
  ADD CONSTRAINT `jrox_blog_to_tags_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `jrox_blog_tags` (`tag_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_blog_to_tags_ibfk_2` FOREIGN KEY (`blog_id`) REFERENCES `jrox_blog_posts` (`blog_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_brands_name`
  ADD CONSTRAINT `jrox_brands_name_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `jrox_brands` (`brand_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_brands_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_cart_items`
  ADD CONSTRAINT `jrox_cart_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_cart_items_ibfk_2` FOREIGN KEY (`cart_id`) REFERENCES `jrox_cart` (`cart_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_cart_totals`
  ADD CONSTRAINT `jrox_cart_totals_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `jrox_cart` (`cart_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_coupons_products`
  ADD CONSTRAINT `jrox_coupons_products_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `jrox_coupons` (`coupon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_coupons_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_email_follow_ups_name`
  ADD CONSTRAINT `jrox_email_follow_ups_name_ibfk_1` FOREIGN KEY (`follow_up_id`) REFERENCES `jrox_email_follow_ups` (`follow_up_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_email_follow_ups_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_email_templates_name`
  ADD CONSTRAINT `jrox_email_templates_name_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `jrox_email_templates` (`template_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_email_templates_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_faq_name`
  ADD CONSTRAINT `jrox_faq_name_ibfk_1` FOREIGN KEY (`faq_id`) REFERENCES `jrox_faq` (`faq_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_faq_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_form_fields`
  ADD CONSTRAINT `jrox_form_fields_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `jrox_forms` (`form_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_form_fields_name`
  ADD CONSTRAINT `jrox_form_fields_name_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `jrox_form_fields` (`field_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_form_fields_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_forum_categories_name`
  ADD CONSTRAINT `jrox_forum_categories_name_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `jrox_forum_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_forum_categories_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_forum_topics_replies`
  ADD CONSTRAINT `jrox_forum_topics_replies_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `jrox_forum_topics` (`topic_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_invoice_items`
  ADD CONSTRAINT `jrox_invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `jrox_invoices` (`invoice_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_invoice_payments`
  ADD CONSTRAINT `jrox_invoice_payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `jrox_invoices` (`invoice_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_invoice_totals`
  ADD CONSTRAINT `jrox_invoice_totals_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `jrox_invoices` (`invoice_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_kb_articles_name`
  ADD CONSTRAINT `jrox_kb_articles_name_ibfk_1` FOREIGN KEY (`kb_id`) REFERENCES `jrox_kb_articles` (`kb_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_kb_articles_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_kb_categories_name`
  ADD CONSTRAINT `jrox_kb_categories_name_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `jrox_kb_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_kb_categories_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_kb_to_videos`
  ADD CONSTRAINT `jrox_kb_to_videos_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `jrox_videos` (`video_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_language_entries`
  ADD CONSTRAINT `jrox_language_entries_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_language_entries_name`
  ADD CONSTRAINT `jrox_language_entries_name_ibfk_1` FOREIGN KEY (`entry_id`) REFERENCES `jrox_language_entries` (`entry_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_language_entries_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_addresses`
  ADD CONSTRAINT `jrox_members_addresses_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_affiliate_groups`
  ADD CONSTRAINT `jrox_members_affiliate_groups_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_affiliate_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `jrox_affiliate_groups` (`group_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_alerts`
  ADD CONSTRAINT `jrox_members_alerts_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_blog_groups`
  ADD CONSTRAINT `jrox_members_blog_groups_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_blog_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `jrox_blog_groups` (`group_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_custom_fields`
  ADD CONSTRAINT `jrox_members_custom_fields_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `jrox_forms` (`form_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_custom_fields_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `jrox_form_fields` (`field_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_discount_groups`
  ADD CONSTRAINT `jrox_members_discount_groups_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_discount_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `jrox_discount_groups` (`group_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_email_mailing_list`
  ADD CONSTRAINT `jrox_members_email_mailing_list_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `jrox_email_mailing_lists` (`list_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_notes`
  ADD CONSTRAINT `jrox_members_notes_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_passwords`
  ADD CONSTRAINT `jrox_members_passwords_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_permissions`
  ADD CONSTRAINT `jrox_members_permissions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_profiles`
    ADD CONSTRAINT `jrox_members_profiles_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_sponsors`
  ADD CONSTRAINT `jrox_members_sponsors_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_subscriptions`
  ADD CONSTRAINT `jrox_members_subscriptions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_subscriptions_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_subscriptions_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `jrox_orders` (`order_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_subscriptions_history`
  ADD CONSTRAINT `jrox_members_subscriptions_history_ibfk_1` FOREIGN KEY (`sub_id`) REFERENCES `jrox_members_subscriptions` (`sub_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_subscriptions_history_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `jrox_orders` (`order_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_members_to_custom_fields`
  ADD CONSTRAINT `jrox_members_to_custom_fields_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_members_to_custom_fields_ibfk_2` FOREIGN KEY (`custom_field_id`) REFERENCES `jrox_members_custom_fields` (`custom_field_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_module_affiliate_marketing_affiliate_stores`
  ADD CONSTRAINT `jrox_module_affiliate_marketing_affiliate_stores_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_module_affiliate_marketing_affiliate_stores_products`
  ADD CONSTRAINT `jrox_module_affiliate_marketing_affiliate_stores_products_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_module_affiliate_marketing_affiliate_stores_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;
#####

ALTER TABLE `jrox_module_affiliate_marketing_affiliate_stores_products`
  ADD CONSTRAINT `jrox_module_affiliate_marketing_affiliate_stores_products_ibfk_3` FOREIGN KEY (`member_id`) REFERENCES `jrox_module_affiliate_marketing_affiliate_stores` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_orders_gift_certificates_history`
  ADD CONSTRAINT `jrox_orders_gift_certificates_history_ibfk_1` FOREIGN KEY (`cert_id`) REFERENCES `jrox_orders_gift_certificates` (`cert_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_orders_gift_certificates_history_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `jrox_invoices` (`invoice_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_orders_items`
  ADD CONSTRAINT `jrox_orders_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `jrox_orders` (`order_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_orders_shipping`
  ADD CONSTRAINT `jrox_orders_shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `jrox_orders` (`order_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_attributes_name`
  ADD CONSTRAINT `jrox_products_attributes_name_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `jrox_products_attributes` (`attribute_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_attributes_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_filters_values`
    ADD CONSTRAINT `jrox_products_filters_values_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `jrox_products_filters` (`filter_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_attribute_options`
  ADD CONSTRAINT `jrox_products_attribute_options_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `jrox_products_attributes` (`attribute_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_attribute_options_name`
  ADD CONSTRAINT `jrox_products_attribute_options_name_ibfk_1` FOREIGN KEY (`option_id`) REFERENCES `jrox_products_attribute_options` (`option_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_attribute_options_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_categories_name`
  ADD CONSTRAINT `jrox_products_categories_name_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `jrox_products_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_categories_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_cross_sells`
  ADD CONSTRAINT `jrox_products_cross_sells_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_cross_sells_ibfk_2` FOREIGN KEY (`product_cross_sell_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_downloads_name`
  ADD CONSTRAINT `jrox_products_downloads_name_ibfk_1` FOREIGN KEY (`download_id`) REFERENCES `jrox_products_downloads` (`download_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_downloads_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_name`
  ADD CONSTRAINT `jrox_products_name_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_photos`
  ADD CONSTRAINT `jrox_products_photos_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_specifications_name`
  ADD CONSTRAINT `jrox_products_specifications_name_ibfk_1` FOREIGN KEY (`spec_id`) REFERENCES `jrox_products_specifications` (`spec_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_specifications_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_aff_groups`
  ADD CONSTRAINT `jrox_products_to_aff_groups_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_aff_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `jrox_affiliate_groups` (`group_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_attributes`
  ADD CONSTRAINT `jrox_products_to_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `jrox_products_attributes` (`attribute_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_attributes_values`
  ADD CONSTRAINT `jrox_products_to_attributes_values_ibfk_1` FOREIGN KEY (`prod_att_id`) REFERENCES `jrox_products_to_attributes` (`prod_att_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_attributes_values_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `jrox_products_attribute_options` (`option_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_categories`
  ADD CONSTRAINT `jrox_products_to_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `jrox_products_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_categories_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_disc_groups`
  ADD CONSTRAINT `jrox_products_to_disc_groups_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_disc_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `jrox_discount_groups` (`group_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_downloads`
  ADD CONSTRAINT `jrox_products_to_downloads_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_pricing`
  ADD CONSTRAINT `jrox_products_to_pricing_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_specifications_name`
  ADD CONSTRAINT `jrox_products_to_specifications_name_ibfk_1` FOREIGN KEY (`spec_id`) REFERENCES `jrox_products_specifications` (`spec_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_specifications_name_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_specifications_name_ibfk_3` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_tags`
  ADD CONSTRAINT `jrox_products_to_tags_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `jrox_products_tags` (`tag_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_tags_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_videos`
  ADD CONSTRAINT `jrox_products_to_videos_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `jrox_videos` (`video_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_videos_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_products_to_wish_lists`
  ADD CONSTRAINT `jrox_products_to_wish_lists_ibfk_1` FOREIGN KEY (`wish_list_id`) REFERENCES `jrox_wish_lists` (`wish_list_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_products_to_wish_lists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `jrox_products` (`product_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_promotional_items`
  ADD CONSTRAINT `jrox_promotional_items_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `jrox_promotional_rules` (`rule_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_regions`
  ADD CONSTRAINT `jrox_regions_ibfk_1` FOREIGN KEY (`region_country_id`) REFERENCES `jrox_countries` (`country_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_regions_to_zones`
  ADD CONSTRAINT `jrox_regions_to_zones_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `jrox_zones` (`zone_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_rewards_history`
  ADD CONSTRAINT `jrox_rewards_history_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_site_menus_links`
  ADD CONSTRAINT `jrox_site_menus_links_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `jrox_site_menus` (`menu_id`);

#####

ALTER TABLE `jrox_site_menus_links_name`
  ADD CONSTRAINT `jrox_site_menus_links_name_ibfk_1` FOREIGN KEY (`menu_link_id`) REFERENCES `jrox_site_menus_links` (`menu_link_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_site_menus_links_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_site_menus_name`
  ADD CONSTRAINT `jrox_site_menus_name_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `jrox_site_menus` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_site_menus_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_site_pages_name`
  ADD CONSTRAINT `jrox_site_pages_name_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `jrox_site_pages` (`page_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_site_pages_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_slide_shows_name`
  ADD CONSTRAINT `jrox_slide_shows_name_ibfk_1` FOREIGN KEY (`slide_id`) REFERENCES `jrox_slide_shows` (`slide_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_slide_shows_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_support_categories_name`
  ADD CONSTRAINT `jrox_support_categories_name_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `jrox_support_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_support_categories_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_support_tickets`
  ADD CONSTRAINT `jrox_support_tickets_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_support_tickets_notes`
  ADD CONSTRAINT `jrox_support_tickets_notes_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `jrox_support_tickets` (`ticket_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_support_tickets_replies`
  ADD CONSTRAINT `jrox_support_tickets_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `jrox_support_tickets` (`ticket_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_system_pages_name`
  ADD CONSTRAINT `jrox_system_pages_name_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `jrox_system_pages` (`page_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_system_pages_name_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `jrox_languages` (`language_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_tax_rates`
  ADD CONSTRAINT `jrox_tax_rates_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `jrox_zones` (`zone_id`) ON DELETE CASCADE;

/*
 *
 * Copyright (c) 2007 - 2021, JROX Technologies, Inc.
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
 * @package      eCommerce Suite
 * @author       JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2021, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link         https://www.jrox.com
 * @filesource
 */

#####

ALTER TABLE `jrox_tax_rate_rules`
  ADD CONSTRAINT `jrox_tax_rate_rules_ibfk_1` FOREIGN KEY (`tax_class_id`) REFERENCES `jrox_tax_classes` (`tax_class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jrox_tax_rate_rules_ibfk_2` FOREIGN KEY (`tax_rate_id`) REFERENCES `jrox_tax_rates` (`tax_rate_id`) ON DELETE CASCADE;

/*
 *
 * Copyright (c) 2007 - 2021, JROX Technologies, Inc.
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
 * @package      eCommerce Suite
 * @author       JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2021, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link         https://www.jrox.com
 * @filesource
 */

#####

ALTER TABLE `jrox_tracking_referrals`
  ADD CONSTRAINT `jrox_tracking_referrals_ibfk_1` FOREIGN KEY (`tracking_id`) REFERENCES `jrox_tracking` (`tracking_id`) ON DELETE CASCADE;

#####

ALTER TABLE `jrox_wish_lists`
  ADD CONSTRAINT `jrox_wish_lists_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `jrox_members` (`member_id`) ON DELETE CASCADE;