<?php
add_action('latest_products', 'get_latest_products', 10);

function get_latest_products($data = array())
{
	$CI = &get_instance();

	$lang_id = !$CI->session->default_lang_id ? '1' : $CI->session->default_lang_id;

	$CI->load->model('products_model', 'prod');

	$opt = array( 'offset'           => '0',
	              'session_per_page' => config_option('layout_design_products_per_home_page')
	);
	$rows = $CI->prod->load_home_products(query_options($opt), $lang_id);

	return !empty($rows) ? $rows : FALSE;
}