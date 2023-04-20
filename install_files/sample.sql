INSERT INTO `jrox_blog_categories` (`category_id`, `status`, `sort_order`) VALUES
(1, '1', 1),
(2, '1', 0);

#####

INSERT INTO `jrox_blog_categories_name` (`category_id`, `language_id`, `category_name`, `description`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(1, 1, 'news', '', 'news', 'news', 'news'),
(2, 1, 'updates', 'updates', 'updates', 'updates', 'updates');

#####

INSERT INTO `jrox_blog_posts` (`blog_id`, `category_id`, `status`, `date_published`, `url`, `author`, `likes`, `enable_comments`, `views`, `overview_image`, `blog_header`, `require_registration`, `drip_feed`, `restrict_group`, `notes`, `sort_order`) VALUES
(1, 0, '1', '{{current_time}}', 'first-blog-post', 'Site Admin', 0, '1', 0, '{{base_url}}/images/uploads/backgrounds/bg-00014.jpg', '{{base_url}}/images/uploads/backgrounds/bg-00019.jpg', '0', 0, '0', '', 1),
(2, 1, '1', '{{current_time}}', 'another-sample-blog-post', 'Site Admin', 0, '1', 0, '{{base_url}}/images/uploads/backgrounds/bg-00018.jpg', '{{base_url}}/images/uploads/backgrounds/bg-00018.jpg', '0', 0, '0', '', 1),
(3, 0, '1', '{{current_time}}', 'third-final-blog-post', 'Site Admin', 0, '0', 0, '{{base_url}}/images/uploads/backgrounds/bg-0003.jpg', '{{base_url}}/images/uploads/backgrounds/bg-0003.jpg', '0', 0, '0', '', 1);

#####

INSERT INTO `jrox_blog_posts_name` (`blog_id`, `language_id`, `title`, `overview`, `body`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(1, 1, 'This is a sample blog post', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam ullamcorper risus ut quam eleifend pharetra. Aenean porta nibh sed lacus mollis finibus. Integer sit amet porttitor dui. Nulla vitae augue posuere, scelerisque orci sit amet, fermentum urna. Integer fringilla, lectus eu egestas auctor, nibh nunc vehicula nisi, eu cursus justo lorem vitae lacus. Donec at convallis orci.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam ullamcorper risus ut quam eleifend pharetra. Aenean porta nibh sed lacus mollis finibus. Integer sit amet porttitor dui. Nulla vitae augue posuere, scelerisque orci sit amet, fermentum urna. Integer fringilla, lectus eu egestas auctor, nibh nunc vehicula nisi, eu cursus justo lorem vitae lacus. Donec at convallis orci. In lobortis rutrum arcu vulputate pharetra. Nam arcu mi, molestie quis erat id, vulputate accumsan ipsum. Nullam posuere risus a nisi tristique, a gravida nisl lacinia. Ut lacus ipsum, varius at erat at, consequat commodo urna. Nam eget leo et magna rutrum porta. Nam et condimentum leo, varius commodo mi. Etiam consequat, sem quis luctus imperdiet, turpis nisi finibus metus, id semper nisi nisl suscipit tellus. Sed mattis tempus mi sed laoreet.</p>\r\n<p>Nullam vel blandit erat, in egestas elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. Cras consectetur arcu et fringilla elementum. Phasellus eu massa vel nibh cursus malesuada. Cras ultricies diam non lacus consectetur porttitor. Etiam arcu tortor, tristique congue justo sit amet, dapibus semper tortor. Morbi neque diam, gravida vel lectus id, hendrerit tincidunt diam. Proin non sapien id elit congue iaculis non vel metus. Proin at tempus lorem. Sed nec laoreet leo. Cras mattis sem eu sem vehicula, eu sodales neque euismod. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam maximus neque id porta condimentum. Nulla ex nisi, ultrices id sem vitae, placerat faucibus risus.</p>\r\n<p>Nunc fringilla bibendum risus, non viverra purus luctus ut. Vestibulum sit amet ante ac ante feugiat ullamcorper gravida quis lacus. Vivamus tincidunt aliquam consequat. Cras fermentum euismod tellus, eu porta lectus semper vel. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Quisque posuere lacus dui, in tincidunt urna luctus a. Sed scelerisque interdum felis, posuere accumsan tellus dignissim vitae. Nam ullamcorper non risus et aliquam. Donec consequat leo sed enim congue, sit amet viverra neque tincidunt. Fusce ultrices dui nec sapien rutrum condimentum. Nullam accumsan lacinia dui, vitae blandit dui interdum in. Maecenas ac mauris eu dolor venenatis maximus quis non sem. Quisque ac neque id orci lobortis pulvinar. Morbi ornare ligula nec feugiat convallis. Maecenas consectetur metus et dui laoreet tristique. Nulla scelerisque tellus nec libero vestibulum congue.</p>\r\n<p>Donec at ex eros. Vivamus lobortis nisl quis pretium faucibus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur fermentum ex mi, nec efficitur nunc aliquet vitae. Vestibulum tempus dolor quis ultricies varius. Nam quis lobortis orci. Maecenas at porttitor massa. Donec faucibus neque eu sapien imperdiet euismod.</p>\r\n<p>Proin dictum, mi ut condimentum tristique, nunc purus eleifend sem, fringilla convallis quam neque quis justo. Etiam nec ultricies lacus. Morbi pulvinar aliquet turpis, vitae convallis orci ullamcorper sed. Vivamus rhoncus libero nec augue pulvinar convallis. Nullam lacinia congue nibh at aliquam. In pharetra, lectus vel vulputate laoreet, turpis ante tincidunt odio, vel tincidunt ipsum enim eget nulla. Ut sit amet dignissim tortor. Integer justo risus, luctus ac finibus nec, placerat quis nulla. Duis ac massa vitae mi fermentum fringilla. Pellentesque nunc dolor, euismod a aliquam sit amet, fermentum at ipsum. Etiam ultricies dolor lectus, non mattis turpis gravida eget. Suspendisse potenti. Pellentesque consequat metus sem, eget pulvinar mauris blandit in. Donec ornare quis odio quis commodo.</p>', 'This is a sample blog post', 'This is a sample blog post', 'This is a sample blog post'),
(2, 1, 'Another sample blog post', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam ullamcorper risus ut quam eleifend pharetra. Aenean porta nibh sed lacus mollis finibus. Integer sit amet porttitor dui. Nulla vitae augue posuere, scelerisque orci sit amet, fermentum urna. Integer fringilla, lectus eu egestas auctor, nibh nunc vehicula nisi, eu cursus justo lorem vitae lacus. Donec at convallis orci.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam ullamcorper risus ut quam eleifend pharetra. Aenean porta nibh sed lacus mollis finibus. Integer sit amet porttitor dui. Nulla vitae augue posuere, scelerisque orci sit amet, fermentum urna. Integer fringilla, lectus eu egestas auctor, nibh nunc vehicula nisi, eu cursus justo lorem vitae lacus. Donec at convallis orci. In lobortis rutrum arcu vulputate pharetra. Nam arcu mi, molestie quis erat id, vulputate accumsan ipsum. Nullam posuere risus a nisi tristique, a gravida nisl lacinia. Ut lacus ipsum, varius at erat at, consequat commodo urna. Nam eget leo et magna rutrum porta. Nam et condimentum leo, varius commodo mi. Etiam consequat, sem quis luctus imperdiet, turpis nisi finibus metus, id semper nisi nisl suscipit tellus. Sed mattis tempus mi sed laoreet.</p>\r\n<p>Nullam vel blandit erat, in egestas elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. Cras consectetur arcu et fringilla elementum. Phasellus eu massa vel nibh cursus malesuada. Cras ultricies diam non lacus consectetur porttitor. Etiam arcu tortor, tristique congue justo sit amet, dapibus semper tortor. Morbi neque diam, gravida vel lectus id, hendrerit tincidunt diam. Proin non sapien id elit congue iaculis non vel metus. Proin at tempus lorem. Sed nec laoreet leo. Cras mattis sem eu sem vehicula, eu sodales neque euismod. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam maximus neque id porta condimentum. Nulla ex nisi, ultrices id sem vitae, placerat faucibus risus.</p>\r\n<p>Nunc fringilla bibendum risus, non viverra purus luctus ut. Vestibulum sit amet ante ac ante feugiat ullamcorper gravida quis lacus. Vivamus tincidunt aliquam consequat. Cras fermentum euismod tellus, eu porta lectus semper vel. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Quisque posuere lacus dui, in tincidunt urna luctus a. Sed scelerisque interdum felis, posuere accumsan tellus dignissim vitae. Nam ullamcorper non risus et aliquam. Donec consequat leo sed enim congue, sit amet viverra neque tincidunt. Fusce ultrices dui nec sapien rutrum condimentum. Nullam accumsan lacinia dui, vitae blandit dui interdum in. Maecenas ac mauris eu dolor venenatis maximus quis non sem. Quisque ac neque id orci lobortis pulvinar. Morbi ornare ligula nec feugiat convallis. Maecenas consectetur metus et dui laoreet tristique. Nulla scelerisque tellus nec libero vestibulum congue.</p>\r\n<p>Donec at ex eros. Vivamus lobortis nisl quis pretium faucibus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur fermentum ex mi, nec efficitur nunc aliquet vitae. Vestibulum tempus dolor quis ultricies varius. Nam quis lobortis orci. Maecenas at porttitor massa. Donec faucibus neque eu sapien imperdiet euismod.</p>\r\n<p>Proin dictum, mi ut condimentum tristique, nunc purus eleifend sem, fringilla convallis quam neque quis justo. Etiam nec ultricies lacus. Morbi pulvinar aliquet turpis, vitae convallis orci ullamcorper sed. Vivamus rhoncus libero nec augue pulvinar convallis. Nullam lacinia congue nibh at aliquam. In pharetra, lectus vel vulputate laoreet, turpis ante tincidunt odio, vel tincidunt ipsum enim eget nulla. Ut sit amet dignissim tortor. Integer justo risus, luctus ac finibus nec, placerat quis nulla. Duis ac massa vitae mi fermentum fringilla. Pellentesque nunc dolor, euismod a aliquam sit amet, fermentum at ipsum. Etiam ultricies dolor lectus, non mattis turpis gravida eget. Suspendisse potenti. Pellentesque consequat metus sem, eget pulvinar mauris blandit in. Donec ornare quis odio quis commodo.</p>', 'Another sample blog post', 'Another sample blog post', 'Another sample blog post'),
(3, 1, 'Third and Final Blog Post', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam ullamcorper risus ut quam eleifend pharetra. Aenean porta nibh sed lacus mollis finibus. Integer sit amet porttitor dui. Nulla vitae augue posuere, scelerisque orci sit amet, fermentum urna. Integer fringilla, lectus eu egestas auctor, nibh nunc vehicula nisi, eu cursus justo lorem vitae lacus. Donec at convallis orci', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam ullamcorper risus ut quam eleifend pharetra. Aenean porta nibh sed lacus mollis finibus. Integer sit amet porttitor dui. Nulla vitae augue posuere, scelerisque orci sit amet, fermentum urna. Integer fringilla, lectus eu egestas auctor, nibh nunc vehicula nisi, eu cursus justo lorem vitae lacus. Donec at convallis orci. In lobortis rutrum arcu vulputate pharetra. Nam arcu mi, molestie quis erat id, vulputate accumsan ipsum. Nullam posuere risus a nisi tristique, a gravida nisl lacinia. Ut lacus ipsum, varius at erat at, consequat commodo urna. Nam eget leo et magna rutrum porta. Nam et condimentum leo, varius commodo mi. Etiam consequat, sem quis luctus imperdiet, turpis nisi finibus metus, id semper nisi nisl suscipit tellus. Sed mattis tempus mi sed laoreet.</p>\r\n<p>Nullam vel blandit erat, in egestas elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. Cras consectetur arcu et fringilla elementum. Phasellus eu massa vel nibh cursus malesuada. Cras ultricies diam non lacus consectetur porttitor. Etiam arcu tortor, tristique congue justo sit amet, dapibus semper tortor. Morbi neque diam, gravida vel lectus id, hendrerit tincidunt diam. Proin non sapien id elit congue iaculis non vel metus. Proin at tempus lorem. Sed nec laoreet leo. Cras mattis sem eu sem vehicula, eu sodales neque euismod. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam maximus neque id porta condimentum. Nulla ex nisi, ultrices id sem vitae, placerat faucibus risus.</p>\r\n<p>Nunc fringilla bibendum risus, non viverra purus luctus ut. Vestibulum sit amet ante ac ante feugiat ullamcorper gravida quis lacus. Vivamus tincidunt aliquam consequat. Cras fermentum euismod tellus, eu porta lectus semper vel. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Quisque posuere lacus dui, in tincidunt urna luctus a. Sed scelerisque interdum felis, posuere accumsan tellus dignissim vitae. Nam ullamcorper non risus et aliquam. Donec consequat leo sed enim congue, sit amet viverra neque tincidunt. Fusce ultrices dui nec sapien rutrum condimentum. Nullam accumsan lacinia dui, vitae blandit dui interdum in. Maecenas ac mauris eu dolor venenatis maximus quis non sem. Quisque ac neque id orci lobortis pulvinar. Morbi ornare ligula nec feugiat convallis. Maecenas consectetur metus et dui laoreet tristique. Nulla scelerisque tellus nec libero vestibulum congue.</p>\r\n<p>Donec at ex eros. Vivamus lobortis nisl quis pretium faucibus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur fermentum ex mi, nec efficitur nunc aliquet vitae. Vestibulum tempus dolor quis ultricies varius. Nam quis lobortis orci. Maecenas at porttitor massa. Donec faucibus neque eu sapien imperdiet euismod.</p>\r\n<p>Proin dictum, mi ut condimentum tristique, nunc purus eleifend sem, fringilla convallis quam neque quis justo. Etiam nec ultricies lacus. Morbi pulvinar aliquet turpis, vitae convallis orci ullamcorper sed. Vivamus rhoncus libero nec augue pulvinar convallis. Nullam lacinia congue nibh at aliquam. In pharetra, lectus vel vulputate laoreet, turpis ante tincidunt odio, vel tincidunt ipsum enim eget nulla. Ut sit amet dignissim tortor. Integer justo risus, luctus ac finibus nec, placerat quis nulla. Duis ac massa vitae mi fermentum fringilla. Pellentesque nunc dolor, euismod a aliquam sit amet, fermentum at ipsum. Etiam ultricies dolor lectus, non mattis turpis gravida eget. Suspendisse potenti. Pellentesque consequat metus sem, eget pulvinar mauris blandit in. Donec ornare quis odio quis commodo.</p>', 'Third and Final Blog Post', 'Third and Final Blog Post', 'Third and Final Blog Post');

#####
INSERT INTO `jrox_blog_tags` (`tag_id`, `tag`, `count`) VALUES
(1, 'example', 0),
(2, 'sample', 0),
(3, 'news', 0);
#####
INSERT INTO `jrox_brands` (`brand_id`, `brand_banner`, `brand_image`, `brand_status`, `brand_notes`, `sort_order`, `modified`) VALUES
(1, '', '{{base_url}}/images/uploads/brands/php.png', '1', '', 1, '{{current_time}}'),
(2, '', '{{base_url}}/images/uploads/brands/centos.png', '1', '', 2, '{{current_time}}'),
(3, '', '{{base_url}}/images/uploads/brands/codeigniter.png', '1', '', 3, '{{current_time}}'),
(4, '', '{{base_url}}/images/uploads/brands/mysql.png', '1', '', 4, '{{current_time}}'),
(5, '', '{{base_url}}/images/uploads/brands/cpanel.png', '1', '', 5, '{{current_time}}');
#####
INSERT INTO `jrox_brands_name` (`brand_name_id`, `brand_id`, `language_id`, `brand_name`, `description`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(1, 1, 1, 'PHP', 'PHP', 'default', 'default', 'default'),
(2, 2, 1, 'CentOS', 'CentOS', 'CentOS', 'CentOS', 'CentOS'),
(3, 3, 1, 'Codeigniter', 'Codeigniter', 'Codeigniter', 'Codeigniter', 'Codeigniter'),
(4, 4, 1, 'MySQL', 'MySQL', 'MySQL', 'MySQL', 'MySQL'),
(5, 5, 1, 'cPanel', 'cPanel', 'cPanel', 'cPanel', 'cPanel');
#####
INSERT INTO `jrox_faq` (`faq_id`, `status`, `sort_order`) VALUES
(1, '1', 0),
(2, '1', 0),
(3, '1', 0);
#####
INSERT INTO `jrox_faq_name` (`faq_id`, `language_id`, `question`, `answer`) VALUES
(1, 1, 'How Do I Register for An Account?', 'Just click on the Register link on the top right side of our site and fill in your details.  Once you have submitted your form, you will be issued login details to the Client Area.'),
(2, 1, 'What Payment Methods Do You Accept?', 'We accept Visa, Mastercard, American Express and Discover card as well as Paypal and Check Payments.'),
(3, 1, 'Where is Your Terms of Service and Return Policy?', 'You can view our Terms of Service and Return Policies at the bottom of this page, by clicking on the link that says ''terms of service''');
#####
INSERT INTO `jrox_products` (`product_id`, `product_type`, `product_sku`, `product_upc`, `product_ean`, `product_jan`, `product_isbn`, `product_mpn`, `product_location`, `product_status`, `product_featured`, `enable_up_sell`, `hidden_product`, `product_price`, `product_sale_price`, `date_added`, `date_expires`, `modified`, `date_available`, `brand_id`, `supplier_id`, `affiliate_group`, `discount_group`, `blog_group`, `product_views`, `enable_inventory`, `inventory_amount`, `length`, `width`, `height`, `weight`, `length_type`, `weight_type`, `login_for_price`, `min_quantity_required`, `max_quantity_allowed`, `enable_custom_commissions`, `disable_commissions`, `sort_order`, `tax_class_id`, `add_mailing_list`, `remove_mailing_list`, `product_page_template`, `points`, `charge_shipping`, `shipping_cost`, `affiliate_redirect`, `video_as_default`, `youtube_playlist`, `max_downloads_user`) VALUES
(1, 'general', 'WWEASFD', '', '', '', '', '', '', '1', '1', '0', '0', '39.990000', '0.000000', '{{current_time}}', '{{future_time}}', '{{current_time}}', '{{current_time}}', 0, 0, 0, 0, 0, 9, '0', 0, '0.0000', '0.0000', '0.0000', '0.0000', 1, 1, '0', 0, 0, '0', '0', 2, 0, 0, 0, 'product_general_details_default', 0, '0', '0.00', '', 0, '', 0),
(2, 'general', 'DESSZC', '', '', '', '', '', '', '1', '1', '0', '0', '19.990000', '0.000000', '{{current_time}}', '{{future_time}}', '{{current_time}}', '{{current_time}}', 0, 0, 0, 0, 0, 9, '0', 0, '0.0000', '0.0000', '0.0000', '0.0000', 1, 1, '0', 0, 0, '0', '0', 3, 0, 0, 0, 'product_general_details_default', 0, '0', '0.00', '', 0, '', 0),
(3, 'general', 'SDFAFS', '', '', '', '', '', '', '1', '1', '0', '0', '29.990000', '0.000000', '{{current_time}}', '{{future_time}}', '{{current_time}}', '{{current_time}}', 0, 0, 0, 0, 0, 6, '0', 0, '0.0000', '0.0000', '0.0000', '0.0000', 1, 1, '0', 0, 0, '0', '0', 4, 0, 0, 0, 'product_general_details_default', 0, '0', '0.00', '', 0, '', 0),
(4, 'general', '0XH4JV', '', '', '', '', '', '', '1', '1', '0', '0', '49.990000', '0.000000', '{{current_time}}', '{{future_time}}', '{{current_time}}', '{{current_time}}', 0, 0, 0, 0, 0, 4, '0', 0, '0.0000', '0.0000', '0.0000', '0.0000', 1, 1, '0', 0, 0, '0', '0', 1, 0, 0, 0, 'product_general_details_default', 0, '0', '0.00', '', 0, '', 0);
#####
INSERT INTO `jrox_products_name` (`product_name_id`, `product_id`, `language_id`, `product_name`, `product_overview`, `product_description`, `meta_title`, `meta_description`, `meta_keywords`) VALUES
(1, 1, 1, 'White Blouse', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse interdum facilisis pulvinar. Praesent vehicula nibh eget dignissim egestas.', '<p>new product</p>', 'new product', 'new product', 'new product'),
(2, 2, 1, 'White Shirt', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse interdum facilisis pulvinar. Praesent vehicula nibh eget dignissim egestas.', '<p>new product</p>', 'new product', 'new product', 'new product'),
(3, 3, 1, 'White Dress', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse interdum facilisis pulvinar. Praesent vehicula nibh eget dignissim egestas.', '<p>new product</p>', 'new product', 'new product', 'new product'),
(4, 4, 1, 'Brown Jacket', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse interdum facilisis pulvinar. Praesent vehicula nibh eget dignissim egestas.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse interdum facilisis pulvinar. Praesent vehicula nibh eget dignissim egestas. Pellentesque metus lorem, feugiat nec ligula ac, tempor pretium est. Vestibulum turpis nisl, dictum ut pharetra vel, feugiat quis turpis. Maecenas laoreet est ac libero bibendum, vel faucibus ex egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec ac metus ac massa malesuada facilisis a at urna. Vestibulum dictum luctus ornare. Ut scelerisque dapibus aliquam. Fusce vel elit condimentum, porta ante quis, semper neque. Aliquam dignissim ac augue eget placerat. Quisque mi justo, viverra auctor porta id, porttitor et metus. Pellentesque mattis rutrum mauris convallis ullamcorper.</p>\r\n<p>Aenean fringilla est dolor, id venenatis sem pellentesque vitae. Donec fringilla velit mi, in porta massa rhoncus at. Proin mollis aliquet nibh rhoncus dictum. Donec ultricies mauris a dui suscipit euismod. Cras arcu sem, pretium a commodo placerat, porttitor vitae sapien. Phasellus viverra eros urna, eget ornare mi accumsan id. Donec quis dictum nunc, et lacinia ipsum. Etiam a nisi sed odio rhoncus consectetur. Curabitur eu pellentesque leo, eu consectetur eros.</p>\r\n<p>Donec congue mi vel dolor bibendum, eget mattis ligula congue. Praesent tincidunt ullamcorper quam non tincidunt. Donec volutpat dui eu blandit consectetur. Aliquam lobortis leo urna, sed maximus orci sollicitudin viverra. Nam fermentum, lectus sit amet hendrerit viverra, tortor ligula sagittis nulla, sed vehicula enim sem non felis. Vivamus auctor mauris dui, et rutrum nisl interdum at. Aliquam erat volutpat. Aenean fringilla libero tortor, quis mattis tellus malesuada in.</p>\r\n<p>Integer rhoncus lectus orci, vitae tincidunt orci suscipit a. Morbi ac lectus nec dolor tincidunt placerat non convallis metus. Aenean ut ullamcorper magna. Aliquam rutrum scelerisque odio. Vivamus facilisis sollicitudin tortor, sed ullamcorper massa porttitor a. Nulla gravida orci nisl, ac sollicitudin justo tempor nec. Sed maximus libero elit, mollis lacinia nunc tempor ac. Donec fermentum accumsan est quis varius.</p>\r\n<p>Donec sapien sem, feugiat imperdiet massa at, consectetur pulvinar mauris. Pellentesque scelerisque consectetur molestie. Donec ornare iaculis urna id egestas. Sed rhoncus tortor ut risus lobortis, quis interdum lacus aliquam. Vivamus rhoncus dui eu odio eleifend, sit amet aliquet ligula rutrum. Vestibulum consequat nisi ornare quam pulvinar feugiat. Aliquam vel libero nulla. Cras consequat pretium dolor non varius. Donec vitae tincidunt sapien, quis aliquet mauris. Vivamus quis convallis turpis.</p>', 'Default Product 4', 'Default Product 4', 'Default Product 4');
#####
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
#####
INSERT INTO `jrox_slide_shows` (`slide_id`, `status`, `type`, `name`, `start_date`, `end_date`, `meta_data`, `footer_data`, `text_color`, `background_color`, `background_image`, `position`, `sort_order`) VALUES
(1, '1', 'simple', 'slide show 1', '{{current_time}}', '2050-02-01 23:59:59', '', '', '', '', '{{base_url}}/images/uploads/backgrounds/bg-0001.jpg', 'left', 0),
(2, '1', 'simple', 'slide show 2', '{{current_time}}', '2050-02-01 23:59:59', '', '', '', '', '{{base_url}}/images/uploads/backgrounds/bg-0003.jpg', 'left', 0),
(3, '1', 'simple', 'slide show 3', '{{current_time}}', '2050-02-01 23:59:59', '', '', '', '', '{{base_url}}/images/uploads/backgrounds/bg-00013.jpg', 'left', 0);
#####
INSERT INTO `jrox_slide_shows_name` (`slide_name_id`, `slide_id`, `language_id`, `headline`, `slide_show`, `button_text`) VALUES
(1, 1, 1, 'Your Own Full Blown eCommerce Suite', '<div class="container">\r\n                <div class="row">\r\n                    <div class="col-md-10 col-xs-12">\r\n                                          <div class="list">\r\n                            <ul>\r\n                                <li class="animated slideInLeft first delay"><span><i class="fa fa fa-code"></i> Built with Bootstrap.</span>\r\n                                </li>\r\n                                <li class="animated slideInLeft second delay"><span><i class="fa fa-cogs"></i> Easy to Customize.</span>\r\n                                </li>\r\n                                <li class="animated slideInLeft third delay"><span><i class="fa fa-tablet"></i> Fully Responsive.</span>\r\n                                </li>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                    <div class="col-md-6 hidden-sm hidden-xs">\r\n                       \r\n                    </div>\r\n                </div>\r\n            </div>', 'Learn More'),
(2, 2, 1, 'Powered By Codeigniter', '<div class="container">\r\n                <div class="row">\r\n                    <div class="col-md-8 col-xs-12">\r\n                                                <div class="list">\r\n                            <ul>\r\n                                <li class="animated slideInLeft first delay"><span><i class="fa fa fa-code"></i>Fast and Easy Framework</span>\r\n                                </li>\r\n                                <li class="animated slideInLeft second delay"><span><i class="fa fa-cogs"></i> Developer Friendly</span>\r\n                                </li>\r\n                                <li class="animated slideInLeft third delay"><span><i class="fa fa-tablet"></i> Open Source and Fully Documented.</span>\r\n                                </li>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                    <div class="col-md-6 hidden-sm hidden-xs">\r\n                       \r\n                    </div>\r\n                </div>\r\n            </div>', 'Learn More'),
(3, 3, 1, 'Everything In One Place', '<div class="container">\r\n                <div class="row">\r\n                    <div class="col-md-8 col-xs-12">\r\n                                                <div class="list">\r\n                            <ul>\r\n                                <li class="animated slideInLeft first delay"><span><i class="fa fa fa-code"></i>Robust Shopping Cart</span>\r\n                                </li>\r\n                                <li class="animated slideInLeft second delay"><span><i class="fa fa-cogs"></i> Integrated Marketing Tools</span>\r\n                                </li>\r\n                                <li class="animated slideInLeft third delay"><span><i class="fa fa-tablet"></i> Full Content Management System</span>\r\n                                </li>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                    <div class="col-md-6 hidden-sm hidden-xs">\r\n                       \r\n                    </div>\r\n                </div>\r\n            </div>', 'Learn More');