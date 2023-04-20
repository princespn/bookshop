<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="input-group text-capitalize">
			<?= generate_sub_headline(lang('site_layout'), 'fa-pencil', '', FALSE) ?>
        </div>
    </div>
    <div class="col-md-4 text-right">
        <a href="<?= admin_url('dashboard/view') ?>" class="btn btn-primary"><?= i('fa fa-search') ?> <span
                    class="hidden-xs"><?= lang('view_dashboard_icons') ?></span></a>
    </div>
</div>
<hr/>
<div class="box-info">
	<?= form_open('', 'id="form" class="form-horizontal"') ?>
    <ul class="resp-tabs nav nav-tabs responsive text-capitalize">
        <li class="active"><a href="#global" data-toggle="tab"><?= lang('global') ?></a></li>
        <li><a href="#home" data-toggle="tab"><?= lang('home_page') ?></a></li>
        <li><a href="#product" data-toggle="tab"><?= lang('product') ?></a></li>
        <li><a href="#blog" data-toggle="tab"><?= lang('blog') ?></a></li>
        <li><a href="#page" data-toggle="tab"><?= lang('site_pages') ?></a></li>
		<?php if (config_enabled('enable_section_kb_articles')): ?>
            <li><a href="#support" data-toggle="tab"><?= lang('support') ?></a></li>
		<?php endif; ?>
        <li><a href="#dashboard" data-toggle="tab"><?= lang('dashboard') ?></a></li>
        <li><a href="#login" data-toggle="tab"><?= lang('login') ?></a></li>
        <li><a href="#contact" data-toggle="tab"><?= lang('contact_us') ?></a></li>
        <li><a href="#meta" data-toggle="tab"><?= lang('meta_data') ?></a></li>
    </ul>
    <div class="tab-content responsive">
        <div id="global" class="tab-pane active">
            <h3 class="text-capitalize"><?= lang('global_layout') ?></h3>
            <span class="text-capitalize"><?= lang('global_layout_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('site_name', 'layout_design_site_name', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_input('layout_design_site_name', set_value('layout_design_site_name', config_option('layout_design_site_name')), ' class="form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('default_meta_keywords', 'layout_design_default_meta_keywords', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_input('layout_design_default_meta_keywords', set_value('layout_design_default_meta_keywords', config_option('layout_design_default_meta_keywords')), ' class="form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('default_meta_description', 'layout_design_default_meta_description', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_input('layout_design_default_meta_description', set_value('layout_design_default_meta_description', config_option('layout_design_default_meta_description')), ' class="form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('top_menu', 'layout_design_top_menu', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_top_menu', options('menus'), config_option('layout_design_top_menu'), 'class="form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('logged_in_top_menu', 'layout_design_top_menu_logged_in', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_top_menu_logged_in', options('menus'), config_option('layout_design_top_menu_logged_in'), 'class="form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('checkout_menu', 'layout_design_checkout_menu', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_checkout_menu', options('menus'), config_option('layout_design_checkout_menu'), 'class="form-control required"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('show_footer', 'layout_design_global_show_footer', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_global_show_footer', options('yes_no'), config_option('layout_design_global_show_footer'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('show_search_form', 'layout_design_show_search_form', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_show_search_form', options('yes_no'), config_option('layout_design_show_search_form'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div id="show-timed-modal">
                <div class="form-group">
			        <?= lang('show_timed_modal', 'layout_design_modal_enable_timer', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
				        <?= form_dropdown('layout_design_modal_enable_timer', options('yes_no'), config_option('layout_design_modal_enable_timer'), 'id="layout_design_modal_enable_timer" class="form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div id="modal-seconds">
                    <div class="form-group">
				        <?= lang('modal_delay_in_seconds', 'layout_design_modal_timer_seconds', array('class' => 'col-md-3 control-label')) ?>
                        <div class="r col-md-5">
                            <?=form_input(array('name' => 'layout_design_modal_timer_seconds', 'type' => 'number','value' => set_value('layout_design_modal_timer_seconds', config_option('layout_design_modal_timer_seconds')), 'class' => 'form-control required digits' ))?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
		                <?= lang('modal_cookie_expires_in_days', 'layout_design_modal_cookie_expires', array('class' => 'col-md-3 control-label')) ?>
                        <div class="r col-md-5">
	                        <?=form_input(array('name' => 'layout_design_modal_cookie_expires', 'type' => 'number','value' => set_value('layout_design_modal_cookie_expires', config_option('layout_design_modal_cookie_expires')), 'class' => 'form-control required digits' ))?>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
		                <?= lang('modal_message', 'layout_design_modal_timer_text', 'class="col-md-3 control-label"') ?>

                        <div class="r col-md-5">
	                        <?= form_textarea('layout_design_modal_timer_text', set_value('layout_design_modal_timer_text', config_option('layout_design_modal_timer_text'), FALSE), ' class="editor form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                </div>
            </div>
        </div>
        <div id="home" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('home_page_layout') ?></h3>
            <span class="text-capitalize"><?= lang('home_page_layout_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('home_page_layout', 'layout_design_home_page_content_layout', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_home_page_content_layout', options('content_layout'), config_option('layout_design_home_page_content_layout'), 'id="content_layout" class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div id="sb-autosave">
                <div class="form-group">
			        <?= lang('auto_save_site_builder_pages', 'layout_design_enable_sb_auto_save', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
				        <?= form_dropdown('layout_design_enable_sb_auto_save', options('yes_no'), config_option('layout_design_enable_sb_auto_save'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
            </div>
            <div id="show-slide-shows">
                <div class="form-group">
					<?= lang('show_slideshows', 'layout_design_home_page_show_slideshows', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_dropdown('layout_design_home_page_show_slideshows', options('yes_no'), config_option('layout_design_home_page_show_slideshows'), 'id="show_slideshows" class="form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div id="slide-shows">
                <div class="form-group">
					<?= lang('number_of_slideshows', 'layout_design_home_page_total_slideshows', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
	                    <?= form_dropdown('layout_design_home_page_total_slideshows', options('numbers_10'), config_option('layout_design_home_page_total_slideshows'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
                </div>
            </div>

            <div id="show-products">
                <div class="form-group">
					<?= lang('show_featured_products', 'layout_design_home_page_show_featured_products', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_dropdown('layout_design_home_page_show_featured_products', options('yes_no'), config_option('layout_design_home_page_show_featured_products'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('show_latest_products', 'layout_design_home_page_show_latest_products', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_dropdown('layout_design_home_page_show_latest_products', options('yes_no'), config_option('layout_design_home_page_show_latest_products'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
            </div>
            <div id="per-products">
                <div class="form-group">
					<?= lang('products_per_home_page', 'layout_design_products_per_home_page', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
                        <input type="number" name="layout_design_products_per_home_page"
                               value="<?=set_value('layout_design_products_per_home_page', config_option('layout_design_products_per_home_page'))?>" class="form-control required digits" />
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('brands_per_home_page', 'layout_design_brands_per_home_page', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
                        <input type="number" name="layout_design_brands_per_home_page"
                               value="<?=set_value('layout_design_brands_per_home_page', config_option('layout_design_brands_per_home_page'))?>" class="form-control required digits" />
                    </div>
                </div>
                <hr/>
            </div>
            <div id="blog-posts">
                <div class="form-group">
					<?= lang('blog_posts_per_home_page', 'layout_design_blogs_per_home_page', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
                        <input type="number" name="layout_design_blogs_per_home_page"
                               value="<?=set_value('layout_design_blogs_per_home_page', config_option('layout_design_blogs_per_home_page'))?>" class="form-control required digits" />
                    </div>
                </div>
                <hr/>
            </div>
        </div>
        <div id="product" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('product_page_layout') ?></h3>
            <span class="text-capitalize"><?= lang('product_page_layout_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('products_per_store_page', 'layout_design_products_per_page', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
                    <input type="number" name="layout_design_products_per_page"
                           value="<?=set_value('layout_design_products_per_page', config_option('layout_design_products_per_page'))?>" class="form-control required digits" />
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('store_item_layout', 'layout_design_product_page_layout', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_product_page_layout', options('grid_list'), config_option('layout_design_product_page_layout'), 'id="store_layout" class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('store_sidebar', 'layout_design_product_page_sidebar', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
			        <?= form_dropdown('layout_design_product_page_sidebar', options('sidebar'), config_option('layout_design_product_page_sidebar'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div id="store-sidebar">
                <div class="form-group">
		            <?= lang('enable_infinite_scroll', 'layout_design_products_enable_infinite_scroll', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
			            <?= form_dropdown('layout_design_products_enable_infinite_scroll', options('yes_no'), config_option('layout_design_products_enable_infinite_scroll'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
            </div>
            <div class="form-group">
				<?= lang('cross_sell_layout', 'layout_design_cross_sell_layout', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_cross_sell_layout', options('grid_list'), config_option('layout_design_cross_sell_layout'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('cart_layout', 'layout_design_cart_layout', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_cart_layout', options('full_column'), config_option('layout_design_cart_layout'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('shopping_cart_bag_basket', 'layout_design_shopping_cart_or_bag', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
			        <?= form_dropdown('layout_design_shopping_cart_or_bag', options('cart_bag'), config_option('layout_design_shopping_cart_or_bag'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('enable_product_tag_cloud', 'layout_design_product_enable_tag_cloud', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_product_enable_tag_cloud', options('yes_no'), config_option('layout_design_product_enable_tag_cloud'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('product_zoom', 'layout_design_enable_product_image_zoom', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_enable_product_image_zoom', options('yes_no'), config_option('layout_design_enable_product_image_zoom'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <div id="blog" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('blog_page_layout') ?></h3>
            <span class="text-capitalize"><?= lang('blog_page_layout_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('blog_layout', 'layout_design_blog_page_layout', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_blog_page_layout', options('grid_list'), config_option('layout_design_blog_page_layout'), 'id="blog_layout" class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('posts_per_page', 'layout_design_blogs_per_page', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
                    <input type="number" name="layout_design_blogs_per_page"
                           value="<?=set_value('layout_design_blogs_per_page', config_option('layout_design_blogs_per_page'))?>" class="form-control required digits" />
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('enable_related_articles', 'layout_design_blog_related_articles', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_blog_related_articles', options('yes_no'), config_option('layout_design_blog_related_articles'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div id="blog-sidebar">
                <div class="form-group">
					<?= lang('sidebar', 'layout_design_blog_page_sidebar', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_dropdown('layout_design_blog_page_sidebar', options('sidebar'), config_option('layout_design_blog_page_sidebar'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
            </div>
            <div class="form-group">
				<?= lang('enable_tag_cloud', 'layout_design_blogs_enable_tag_cloud', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_blogs_enable_tag_cloud', options('yes_no'), config_option('layout_design_blogs_enable_tag_cloud'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('enable_continue_reading_button', 'layout_design_continue_reading_button', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
			        <?= form_dropdown('layout_design_continue_reading_button', options('yes_no'), config_option('layout_design_continue_reading_button'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <div id="page" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('site_page_layout') ?></h3>
            <span class="text-capitalize"><?= lang('site_page_layout_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('sidebar_on_basic_pages', 'layout_design_site_page_sidebar', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_site_page_sidebar', options('sidebar'), config_option('layout_design_site_page_sidebar'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
		<?php if (config_enabled('enable_section_kb_articles')): ?>
            <div id="support" class="tab-pane">
                <h3 class="text-capitalize"><?= lang('kb_forum_page_layout') ?></h3>
                <span class="text-capitalize"><?= lang('kb_forum_page_layout_description') ?></span>
                <hr/>
                <div class="form-group">
					<?= lang('kb_articles_per_page', 'layout_design_kb_per_page', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_input('layout_design_kb_per_page', set_value('layout_design_kb_per_page', config_option('layout_design_kb_per_page')), ' class="form-control required digits"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('knowledge_sidebar', 'layout_design_kb_sidebar', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_dropdown('layout_design_kb_sidebar', options('sidebar'), config_option('layout_design_kb_sidebar'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('forum_posts_per_page', 'layout_design_forum_posts_per_page', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_input('layout_design_forum_posts_per_page', set_value('layout_design_forum_posts_per_page', config_option('layout_design_forum_posts_per_page')), ' class="form-control required digits"') ?>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
					<?= lang('forum_sidebar', 'layout_design_forum_sidebar', array('class' => 'col-md-3 control-label')) ?>
                    <div class="r col-md-5">
						<?= form_dropdown('layout_design_forum_sidebar', options('sidebar'), config_option('layout_design_forum_sidebar'), 'class="form-control"') ?>
                    </div>
                </div>
                <hr/>
            </div>
		<?php endif; ?>
        <div id="dashboard" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('members_dashboard') ?></h3>
            <span class="text-capitalize"><?= lang('members_dashboard_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('dashboard_template', 'layout_members_dashboard_template', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_members_dashboard_template', options('dashboard_templates'), config_option('layout_members_dashboard_template'), 'class="form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <div id="login" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('login_page') ?></h3>
            <span class="text-capitalize"><?= lang('login_page_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('enable_social_logins', 'layout_design_login_enable_social_login', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_login_enable_social_login', options('yes_no'), config_option('layout_design_login_enable_social_login'), 'id="social-login" class="form-control"') ?>
                </div>
            </div>
            <hr/>
			<?php $logins = array('facebook', 'twitter', 'google') ?>
            <div id="social-ids">
				<?php foreach ($logins as $v): ?>
                    <div class="form-group">
						<?= lang('enable_' . $v . '_login', 'layout_design_login_enable_' . $v . '_login', array('class' => 'col-md-3 control-label')) ?>
                        <div class="r col-md-5">
							<?= form_dropdown('layout_design_login_enable_' . $v . '_login', options('yes_no'), config_option('layout_design_login_enable_' . $v . '_login'), 'id="' . $v . '-login" class="form-control"') ?>
                        </div>
                    </div>
                    <hr/>
                    <div id="<?= $v ?>-ids">
                        <div class="form-group">
							<?= lang($v . '_id', 'layout_design_login_' . $v . '_login_id', array('class' => 'col-md-3 control-label')) ?>
                            <div class="r col-md-5">
								<?= form_input('layout_design_login_' . $v . '_login_id', set_value('layout_design_login_' . $v . '_login_id', config_option('layout_design_login_' . $v . '_login_id')), 'id="layout_design_login_' . $v . '_login_id"  class="form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
							<?= lang($v . '_secret', 'layout_design_login_' . $v . '_login_secret', array('class' => 'col-md-3 control-label')) ?>
                            <div class="r col-md-5">
								<?= form_input('layout_design_login_' . $v . '_login_secret', set_value('layout_design_login_' . $v . '_login_secret', config_option('layout_design_login_' . $v . '_login_secret')), 'id="layout_design_login_' . $v . '_login_secret"  class="form-control"') ?>
                            </div>
                        </div>
                        <hr/>
                    </div>
				<?php endforeach; ?>
            </div>
        </div>
        <div id="contact" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('contact_us_page') ?></h3>
            <span class="text-capitalize"><?= lang('contact_us_page_description') ?></span>
            <hr/>
            <div class="form-group">
				<?= lang('show_phone_number', 'layout_design_contact_show_phone', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_contact_show_phone', options('yes_no'), config_option('layout_design_contact_show_phone'), 'id="contact-show-phone" class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('show_mailing_address', 'layout_design_contact_show_mailing_address', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_contact_show_mailing_address', options('yes_no'), config_option('layout_design_contact_show_mailing_address'), 'id="contact-show-address" class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('show_social_icons', 'layout_design_contact_show_social_icons', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_contact_show_social_icons', options('yes_no'), config_option('layout_design_contact_show_social_icons'), 'id="contact-show-social-icons" class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
				<?= lang('show_map', 'layout_design_contact_show_map', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
					<?= form_dropdown('layout_design_contact_show_map', options('yes_no'), config_option('layout_design_contact_show_map'), 'id="contact-show-map" class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('show_locations', 'layout_design_contact_show_locations_link', array('class' => 'col-md-3 control-label')) ?>
                <div class="r col-md-5">
			        <?= form_dropdown('layout_design_contact_show_locations_link', options('yes_no'), config_option('layout_design_contact_show_locations_link'), 'id="contact-show-locations" class="form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <div id="meta" class="tab-pane">
            <h3 class="text-capitalize"><?= lang('meta_data_layout') ?></h3>
            <span class="text-capitalize"><?= lang('meta_data_layout_description') ?></span>
            <hr/>
            <div class="form-group">
		        <?= lang('extra_header_data', 'layout_design_meta_header_info', 'class="col-md-3 control-label"') ?>

                <div class="r col-md-5">
			        <?= form_textarea('layout_design_meta_header_info', set_value('layout_design_meta_header_info', config_option('layout_design_meta_header_info'), FALSE), ' class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('extra_footer_data', 'layout_design_meta_footer_info', 'class="col-md-3 control-label"') ?>

                <div class="r col-md-5">
			        <?= form_textarea('layout_design_meta_footer_info', set_value('layout_design_meta_footer_info', config_option('layout_design_meta_footer_info'), FALSE), ' class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('checkout_header_info', 'layout_design_meta_checkout_info', 'class="col-md-3 control-label"') ?>

                <div class="r col-md-5">
			        <?= form_textarea('layout_design_meta_checkout_info', set_value('layout_design_meta_checkout_info', config_option('layout_design_meta_checkout_info'), FALSE), ' class="form-control"') ?>
                </div>
            </div>
            <hr/>
            <div class="form-group">
		        <?= lang('thank_you_header_info', 'layout_design_meta_thank_you_info', 'class="col-md-3 control-label"') ?>

                <div class="r col-md-5">
			        <?= form_textarea('layout_design_meta_thank_you_info', set_value('layout_design_meta_thank_you_info', config_option('layout_design_meta_thank_you_info'), FALSE), ' class="form-control"') ?>
                </div>
            </div>
            <hr/>
        </div>
        <nav class="navbar navbar-fixed-bottom  save-changes">
            <div class="container text-right">
                <div class="row">
                    <div class="col-md-12">
                        <button id="save-changes"
                                class="btn btn-info navbar-btn block-phone" <?= is_disabled('update', TRUE) ?>
                                type="submit"><?= i('fa fa-refresh') ?> <?= lang('save_changes') ?></button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
	<?= form_close() ?>
</div>
<script>
	<?=html_editor('init', 'html')?>

    $("select#content_layout").change(function () {
        $("select#content_layout option:selected").each(function () {
            if ($(this).attr("value") == "builder") {
                $("#show-products").hide(100);
                $("#per-products").show(100);
                $("#sb-autosave").show(100);
                $("#show-slide-shows").hide(100);
            }
            else {

                if ($(this).attr("value") == "blog_grid") {
                    $("#per-products").hide(100);
                    $("#show-products").hide(100);
                }
                else if ($(this).attr("value") == "blog_list") {
                    $("#per-products").hide(100);
                    $("#show-products").hide(100);
                }
                else {
                    $("#per-products").show(100);
                    $("#show-products").show(100);
                }

                $("#show-slide-shows").show(100);
                $("#sb-autosave").hide(100);
            }
        });
    }).change();

    $("select#store_layout").change(function () {
        $("select#store_layout option:selected").each(function () {
            if ($(this).attr("value") == "list") {
                $("#store-sidebar").show(100);
            }
            else {
                $("#store-sidebar").hide(100);
            }
        });
    }).change();

    $("select#layout_design_modal_enable_timer").change(function () {
        $("select#layout_design_modal_enable_timer option:selected").each(function () {
            if ($(this).attr("value") == "1") {
                $("#modal-seconds").show(100);
            }
            else {
                $("#modal-seconds").hide(100);
            }
        });
    }).change();

    $("select#blog_layout").change(function () {
        $("select#blog_layout option:selected").each(function () {
            if ($(this).attr("value") == "list") {
                $("#blog-sidebar").show(100);
            }
            else {
                $("#blog-sidebar").hide(100);
            }
        });
    }).change();

    $("select#show_slideshows").change(function () {
        $("select#show_slideshows option:selected").each(function () {
            if ($(this).attr("value") == "1") {
                $("#slide-shows").show(100);
            }
            else {
                $("#slide-shows").hide(100);
            }
        });
    }).change();

    $("select#social-login").change(function () {
        $("select#social-login option:selected").each(function () {
            if ($(this).attr("value") == "1") {
                $("#social-ids").show(100);
                $("#layout_design_login_social_login_id").addClass('required');
                $("#layout_design_login_social_login_secret").addClass('required');
            }
            else {
                $("#social-ids").hide(100);
                $("#layout_design_login_social_login_id").removeClass('required');
                $("#layout_design_login_social_login_secret").removeClass('required');

				<?php foreach ($logins as $v): ?>
                $("#layout_design_login_<?=$v?>_login_id").removeClass('required');
                $("#layout_design_login_<?=$v?>_login_secret").removeClass('required');
				<?php endforeach; ?>
            }
        });
    }).change();

	<?php foreach ($logins as $v): ?>
    $("select#<?=$v?>-login").change(function () {
        $("select#<?=$v?>-login option:selected").each(function () {
            if ($(this).attr("value") == "1") {
                $("#<?=$v?>-ids").show(100);
                $("#layout_design_login_<?=$v?>_login_id").addClass('required');
                $("#layout_design_login_<?=$v?>_login_secret").addClass('required');
            }
            else {
                $("#<?=$v?>-ids").hide(100);
                $("#layout_design_login_<?=$v?>_login_id").removeClass('required');
                $("#layout_design_login_<?=$v?>_login_secret").removeClass('required');
            }
        });
    }).change();
	<?php endforeach; ?>



    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            tinyMCE.triggerSave();
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