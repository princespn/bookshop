<?php
/**
 * Plugin Name: Show Brands
 * Version: 1.0
 * Description: Show Brands shown on Home Page
 */

add_action('show_brands', 'show_brands', 10);

function show_brands()
{
	$CI = &get_instance();

	$lang_id = !$CI->session->default_lang_id ? '1' : $CI->session->default_lang_id;

	$CI->load->model('brands_model', 'brand');

	$opt = array( 'offset'           => '0',
	              'session_per_page' => config_option('layout_design_brands_per_home_page')
	);

	$row = array('values' => $CI->brand->load_brands(query_options($opt), $lang_id));

	return !empty($row) ? $row : FALSE;
}
?>