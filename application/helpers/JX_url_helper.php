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
 * @package      eCommerce Suite
 * @author       JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link         https://www.jrox.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * @param string $text
 * @param string $url
 * @param string $icon
 * @param bool $return
 * @return string|void
 */
function admin_menu_link($text = '', $url = '', $icon = 'fa fa-angle-right', $return = FALSE)
{
	$CI = &get_instance();

	$lang = empty($text) ? strtolower($url) : $text;

	if ($CI->session->admin['admin_group_id'] != 1)
	{
		$vars = explode('/', $url);
		$str = count($vars) > 1 ? $vars[0] . '/' . $vars[1] : '';

		if (!empty($vars))
		{
			if (!isset($CI->session->admin['permissions'][$str]))
			{
				return;
			}
		}
	}

	if ($return == TRUE)
	{
		return '<i class="' . $icon . '"></i> ' . lang($lang);
	}

	return '<a href="' . admin_url() . $url . '" class="link"><i class="' . $icon . '"></i> ' . lang($lang) . '</a>';
}

// ------------------------------------------------------------------------

/**
 * @param string $seg
 * @return string
 */
function admin_url($seg = '')
{
	$segment = ADMIN_ROUTE . '/' . $seg;

	$url = base_url($segment);

	return $url;
}

// ------------------------------------------------------------------------

/**
 * @param string $seg
 * @return string
 */
function base_folder_path($seg = '')
{
	$CI = &get_instance();

	if (!$path = $CI->config->slash_item('base_folder_path'))
	{
		$path = '/';
	}

	return $path . rtrim($seg, '/');
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function admin_login_url()
{
	return site_url(ADMIN_LOGIN);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function cache_url($data = array())
{
	$a = current_url();

	if (!empty($data['page_options']['md5']))
	{
		$a .= $data['page_options']['md5'];
	}

	if (!empty($data['affiliate_data']))
	{
		$a .= base64_encode(serialize($data));
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param string $mod
 * @return string
 */
function check_scroll($mod = '')
{
	$CI = &get_instance();

	switch ($mod)
	{
		case 'store':

			$uri = base_url() . $CI->uri->segment(1);
			if ($CI->config->item('layout_design_products_enable_infinite_scroll') == 1)
			{
				$uri = base_url() . 'products/ajax_home';
			}

			break;
	}

	return $uri;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return string|string[]|null
 */
function filter_stop_words($str = '')
{
	$wordlist = config_option('stop_word_filter');

	if (!empty($wordlist))
	{
		foreach ($wordlist as &$v)
		{
			$v = '/\b' . preg_quote($v, '/') . '\b/';
		}

		$str = preg_replace($wordlist, '', $str);
	}

	return $str;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return mixed|string
 */
function format_url($str = '')
{
	$a = array('{{site_url}}', '{{members_url}}', '{{cart_url}}');
	$b = array(site_url(), site_url(MEMBERS_ROUTE) . '/', site_url('cart'));

	$str = str_replace($a, $b, $str);

	return $str;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function get_cat_path($data = array())
{
	$url = '';

	if (!empty($data['path_id']))
	{
		$id = explode('/', $data['path_id']);
		$path = explode('/', $data['path']);

		for ($i = 0; $i < count($id); $i++)
		{
			if ($i > 0)
			{
				$url .= ' ' . i('fa fa-caret-right') . ' ';
			}

			$url .= ' ' . anchor(admin_url(CONTROLLER_CLASS . '/view/?p-parent_id=' . $id[$i]), trim($path[$i]), 'class="label label-info path"');
		}
	}

	$url .= !empty($url) ? ' ' . i('fa fa-caret-right') : ' ';

	return $url . ' ' . anchor(admin_url(CONTROLLER_CLASS . '/view/?p-parent_id=' . $data['category_id']), trim($data['category_name']), 'class="label label-info path"');
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @return string
 */
function generate_confirmation_url($id = '')
{
	return site_url('register/confirm/' . $id);
}

// ------------------------------------------------------------------------

/**
 * @param string $icon
 * @param string $resp
 * @return string
 */
function i($icon = '', $resp = '')
{
	return '<i class="' . $icon . ' ' . $resp . '"></i>';
}

// ------------------------------------------------------------------------

/**
 * @param string $f
 * @return bool
 */
function is_enabled($f = '')
{
	return is_disabled($f, TRUE) == 'disabled' ? FALSE : TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param string $f
 * @param bool $link
 * @return string
 */
function is_disabled($f = '', $link = FALSE)
{
	$CI = &get_instance();

	if (empty($f))
	{
		$f = CONTROLLER_FUNCTION;
	}

	return $CI->sec->check_admin_permissions(CONTROLLER_CLASS, $f, $link) == FALSE ? 'hide' : '';
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function is_secure() {
	return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
}

// ------------------------------------------------------------------------

/**
 * @param bool $encode
 * @return string
 */
function query_url($encode = FALSE)
{
	$CI = &get_instance();

	$get = $CI->input->get() ? '?' . http_build_query($CI->input->get(NULL, TRUE)) : '';

	return base64_encode(current_url() . $get);
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @param bool $rel
 * @return string
 */
function page_url($type = '', $data = array(), $rel = FALSE)
{
	$a = $rel == FALSE ? site_url() : '';

	$username = config_option('affiliate_data', 'username');

	if (sess('username'))
	{
		$username = sess('username');
	}

	if (!empty($data))
	{
		switch ($type)
		{
			case 'product':

				$a .= $type . '/' . PRODUCT_PREPEND_LINK . '/' . $data['product_id'] . '-' . url_title(strtolower($data['product_name'])) .'/' . $username;

				break;

			case 'blog':

				$a .= config_item('blog_uri') . '/' . BLOG_PREPEND_LINK . '-' . $data['url'];

				if (!empty($username))
				{
					$a .=  '?'. config_item('sts_affiliate_get_variable') . '=' . $username;
				}

				break;

			case 'forum':

				$a .= config_item('forum_uri') . '/topic/' . $data['url'];

				if (!empty($username))
				{
					$a .=  '?'. config_item('sts_affiliate_get_variable') . '=' . $username;
				}

				break;

			case 'members':

				$a .= MEMBERS_ROUTE . '/' . $data;

				break;

			default:

				$a .= $type . '/' . $data;

				if (!empty($username))
				{
					$a .=  '?'. config_item('sts_affiliate_get_variable') . '=' . $username;
				}

				break;
		}
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param string $url
 */
function redirect_page($url = '')
{
	$CI = &get_instance();

	$CI->init->db_trans('trans_complete');

	redirect($url);
}

// ------------------------------------------------------------------------

/**
 * @param string $url
 * @param string $msg
 * @param string $type
 */
function redirect_flashdata($url = '', $msg = '', $type = 'success')
{
	$CI = &get_instance();

	if (!empty($msg))
	{
		$CI->session->set_flashdata($type, lang($msg));
	}

	redirect_page($url);
}

// ------------------------------------------------------------------------

/**
 * @param string $page
 * @param array $data
 * @return mixed|string
 */
function set_landing_page($page = '', $data = array())
{
	//landing page for affiliate link redirects
	if (!empty($data))
	{
		foreach ($data as $k => $v)
		{
			$page = str_replace('{{' . $k . '}}', $v, $page);
		}
	}

	return $page;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 */
function set_headers($type = 'affiliate')
{
	$CI = &get_instance();

	switch ($type)
	{
		case 'affiliate':

			$CI->output->set_status_header('301');
			$CI->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() - 86400) . ' GMT');
			$CI->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
			$CI->output->set_header('Cache-Control: post-check=0, pre-check=0');
			$CI->output->set_header('Pragma: no-cache');

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return string
 */
function ssl_url($str = '')
{
	return config_enabled('sts_cart_ssl_on_checkout') ? site_url($str, 'https') : site_url($str);
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param string $str
 * @return int|string
 */
function valid_id($id = '', $str = '')
{
	//check if there is a valid id given, not just an empty string or value
	if (!empty($id))
	{
		return empty($str) ? (int)$id : $str == 'primary_email' ? xss_clean($id) : url_title($id);
	}

	log_error('error', $id . ' ' . lang('valid_id_required'));
}

// ------------------------------------------------------------------------

/**
 * @param string $line
 * @param string $column
 * @param bool $link
 * @param string $i
 * @param string $j
 * @return string
 */
function tb_header($line = '', $column = '', $link = TRUE, $i = '', $j = '')
{
	$CI = &get_instance();

	if ($link == FALSE)
	{
		$url = lang($line);
	}
	else
	{
		$a = current_url() . '?column=' . $column . '&order=' . $CI->config->item('next_sort_order');
		$query = $CI->input->get();

		unset($query['column']);
		unset($query['order']);

		$get = http_build_query($query);
		if (!empty($get))
		{
			$a .= '&' . $get;
		}

		$url = anchor($a, lang($line));
	}

	if (!empty($j))
	{
		$url = i($j) . ' ' . $url;
	}

	if (!empty($i))
	{
		$url = $url . ' ' . i($i);
	}

	return heading($url, 5, 'class="table-header"');
}

// ------------------------------------------------------------------------

/**
 * @param int $seg
 * @param bool $default
 * @param bool $full
 * @return string
 */
function uri($seg = 2, $default = FALSE, $full = FALSE)
{
	$CI = &get_instance();

	if ($full == TRUE)
	{
		$url = '';
		for ($i = 1; $i <= $seg; $i++)
		{
			$url .= '/' . $CI->uri->segment($i, $default);
		}

		return $url;
	}

	return $CI->uri->segment($seg, $default);
}

// ------------------------------------------------------------------------

/**
 * @param string $url
 * @param string $vars
 * @param bool $post
 * @param int $return
 * @param int $timeout
 * @param bool $ssl_verify_peer
 * @param bool $path
 * @return bool|string
 */
function use_curl($url = '', $vars = '', $post = TRUE, $return = 1, $timeout = 5, $ssl_verify_peer = FALSE, $path = FALSE)
{
	if (!empty($path))
	{
		$fp = fopen($path, 'w');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FILE, $fp);

		$data = curl_exec($ch);

		curl_close($ch);
		fclose($fp);

		return $data;
	}
	else
	{
		$ch = curl_init($url);

		// set to 0 to eliminate header info from response
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$time = !empty($timeout) ? 5 : config_item('sts_site_set_curl_timeout');

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time);
		//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);

		// Returns response data instead of TRUE(1)
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return);

		if ($post == TRUE && !empty($vars))
		{
			// use HTTP POST to send form data
			curl_setopt($ch, CURLOPT_POST, TRUE);

			$v = is_array($vars) ? http_build_query($vars) : $vars;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $v);
		}

		if ($ssl_verify_peer == TRUE)
		{
			// uncomment this line if you get no gateway response. ###
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, CURL_SSL_VERIFYPEER);
		}

		if (defined('CURL_PROXY_REQUIRED') && CURL_PROXY_REQUIRED == TRUE)
		{
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, CURL_PROXY_TUNNEL_FLAG);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
		}

		$resp = curl_exec($ch); //execute post and get results
		curl_close($ch);

		return $resp;
	}
}

/* End of file JX_url_helper.php */
/* Location: ./application/helpers/JX_url_helper.php */