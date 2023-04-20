<?php
/**
 * Plugin Name: Latest Products
 * Version: 1.0
 * Description: Latest Products shown on Home Page
 */

add_action('latest_blog_posts', 'get_blog_posts', 10);

function get_blog_posts()
{
	$CI = &get_instance();

	$lang_id = !$CI->session->default_lang_id ? '1' : $CI->session->default_lang_id;

	$CI->load->model('blog_posts_model', 'blog');

	$opt = array( 'offset'           => '0',
	              'session_per_page' => config_option('layout_design_blogs_per_home_page')
	);

	$rows = $CI->blog->load_blog_posts(query_options($opt), $lang_id);

	return !empty($rows) ? $rows : FALSE;
}
?>