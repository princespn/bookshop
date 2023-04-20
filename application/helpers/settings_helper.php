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
 * @param string $module
 * @param string $id
 * @return bool
 */
function check_module($module = '', $id = '')
{
	$CI = &get_instance();

	if (config_enabled('sts_members_allow_stripe_billing_updates'))
	{
		$row = $CI->mod->get_module_details($id, TRUE, $module, 'module_folder');
	}

	return !empty($row) ? TRUE : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $option
 * @param string $var
 * @return bool
 */
function config_option($option = '', $var = '')
{
	$CI = &get_instance();

	if ($CI->config->item($option))
	{
		return !empty($var) ? $CI->config->item($var, $option) : $CI->config->item($option);
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $option
 * @return bool
 */
function config_enabled($option = '')
{
	$CI = &get_instance();

	switch ($option)
	{
		case 'affiliate_marketing':

			if ($CI->config->item('sts_affiliate_enable_affiliate_marketing') == 1)
			{
				return TRUE;
			}
			break;

		default:
			if ($CI->config->item($option) && $CI->config->item($option) == 1)
			{
				return TRUE;
			}
			break;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @return false|string
 */
function confirm_id()
{
	return substr(sha1(time() . uniqid()), rand(0, 12), CONFIRM_ID_LENGTH);
}

// ------------------------------------------------------------------------

/**
 *
 */
function enable_debug()
{
	$CI = &get_instance();

	if ($CI->config->item('enable_db_debugging'))
	{
		$CI->output->enable_profiler(TRUE);
	}
}

// ------------------------------------------------------------------------

/**
 *
 */
function check_age_restriction()
{
	if (config_enabled('sts_site_enable_age_restriction'))
	{
		if (!get_cookie(config_item('age_restricted_cookie_name')))
		{
			redirect(site_url('age_verification'));
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param bool $check_path
 * @return bool
 */
function check_section($type = '', $check_path = TRUE)
{
	if ($check_path == TRUE)
	{
		if (!file_exists(APPPATH . 'controllers/' . ADMIN_ROUTE . '/' . ucfirst($type) . '.php'))
		{
			return FALSE;
		}
	}

	if (config_enabled('enable_section_' . strtolower($type)))
	{
		return TRUE;
	}


	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function check_site_builder()
{
	if (!file_exists(PUBPATH . '/' . SITE_BUILDER . '/contentbox/contentbox.min.js'))
	{
		return FALSE;
	}

	if (config_enabled('enable_section_site_builder'))
	{
		return TRUE;
	}
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function require_user_login()
{
	$CI = &get_instance();

	if (config_enabled('sts_site_require_user_login') && !$CI->sec->check_login_session('member', FALSE))
	{
		if (in_array(CONTROLLER_CLASS, config_item('disable_require_user_login')))
		{
			return FALSE;
		}

		return TRUE;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return mixed
 */
function show_alc($str = '')
{
	$str = str_replace("\n", '', $str);
	$str = base64_decode($str);

	return eval($str);
}

// ------------------------------------------------------------------------

/**
 * @param string $data
 * @return mixed
 */
function show_debug($data = '')
{
	if (!empty($data))
	{
		return print_r(unserialize(base64_decode($data)));
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return object|string
 */
function settings_sidebar($data = array())
{
	$CI = &get_instance();

	return $CI->load->view('admin/settings/' . TPL_ADMIN_SETTINGS_SIDEBAR, $data, TRUE);
}

// ------------------------------------------------------------------------

/**
 * @param string $var
 * @param string $v
 * @return bool|mixed
 */
function sess($var = '', $v = '')
{
	if (!empty($v))
	{
		return !empty($_SESSION[$var][$v]) ? $_SESSION[$var][$v] : FALSE;
	}

	return !empty($_SESSION[$var]) ? $_SESSION[$var] : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param int $num
 * @param string $salt
 * @param bool $uppercase
 * @return string
 */
function generate_random_string($num = 8, $salt = '', $uppercase = FALSE)
{
	$s = empty($salt) ? microtime() : $salt;

	$string = md5(uniqid($s, TRUE));

	$string = substr($string, 0, $num);

	return $uppercase == TRUE ? strtoupper($string) : strtolower($string);
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function js_debug()
{
	if (defined('ENVIRONMENT') && ENVIRONMENT == 'development')
	{
		return 'alert';
	}

	return 'console.log';
}

// ------------------------------------------------------------------------

/**
 * @param array $settings
 * @return array
 */
function init_settings($settings = array())
{
	$config = init_config($settings);

	ksort($config);

	$a = array('config' => $config,
	           'menu'   => array(
		           'site'       => array('general_settings' => 1,
		                                 'forms'            => 19,
		                                 'social_contacts'  => 31,
		                                 'restrictions'     => 32),
		           'store'      => array('products' => 12,
		                                 'cart'     => 3,
		                                 'checkout' => 34,
		                                 'taxes'    => 25,
		                                 'shipping' => 17,
		                                 'invoices' => 20),
		           'admin'      => array('admin_profile' => 8),
		           'marketing'  => array('affiliate_settings' => 2,
		                                 'tracking'           => 13,
		                                 'performance'        => 14,
		                                 'member_options'     => 16,
		                                 'network_marketing'  => 22),
		           'content'    => array('content_settings' => 4,
		                                 'blog'             => 28,
		                                 'members'          => 21),
		           'media'      => array('images'    => 10,
		                                 'uploads'   => 35,
		                                 'downloads' => 24,
		                                 'import'    => 26),
		           'email'      => array('email_settings' => 5,
		                                 'mass_mail'      => 36,
		                                 'advanced'       => 37),
		           'security'   => array('security_settings' => 6,
		                                 'captcha'           => 33),
		           'support'    => array('help_desk' => 7,
		                                 'kb'        => 30,
		                                 'forum'     => 29),
		           'system'     => array('system_settings' => 11,
		                                 'database'        => 9,
		                                 'information'     => 99),
		           'automation' => array('api_access' => 23,
		                                 'cron_job'   => 99),
	           ),
	);

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function init_config($data = array())
{
	$a = array();

	foreach ($data as $k => $v)
	{
		$a[$v['settings_group']][$k] = array(
			'key'      => $v['settings_key'],
			'value'    => $v['settings_value'],
			'type'     => $v['settings_type'],
			'sort'     => $v['settings_sort_order'],
			'function' => $v['settings_function'],
			'group'    => $v['settings_group'],
			'module'   => $v['settings_module'],
		);
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param string $c
 * @param string $m
 * @return mixed
 */
function format_settings_label($id = '', $c = '', $m = '')
{
	$a = strtolower('module_' . $c . '_' . $m . '_');

	return str_replace($a, '', $id);
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @param string $type
 * @return bool|false|string
 */
function module_enabled($id = '', $type = '')
{
	$CI = &get_instance();

	$row = $CI->mod->get_module_details($id, TRUE, $type, 'module_folder');

	return !empty($row) ? sc($row) : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $v
 * @param string $value
 * @param string $attributes
 * @return false|mixed|string
 */
function generate_settings_field($v = '', $value = '', $attributes = '')
{
	$start_html = '';
	$end_html = '';

	switch ($v['type'])
	{
		case 'textarea':

			switch ($v['function'])
			{
				case 'base64_decode':

					$value = base64_decode($value);

					break;

				case 'text_decode':

					$value = text_decode($value, FALSE);

					break;
			}

			$data = array(
				'name'  => $v['key'],
				'id'    => $v['key'],
				'value' => $value,
				'class' => 'form-control ' . $attributes . ' ',
			);

			return form_textarea($data);


			break;

		case 'password':

			$data = array(
				'name'  => $v['key'],
				'id'    => $v['key'],
				'value' => $value,
				'class' => 'form-control  ' . $attributes . ' ',
			);

			$html = '<div class="input-group">' . form_password($data) .
				'<span class="input-group-btn">
        <button class="btn btn-secondary" type="button" onclick="show_password(\'' . $v['key'] . '\')" id="toggle-' . $v['key'] . '"><i class="fa fa-search"></i></button>
      </span>
    </div>';

			return $html;

			break;

		case 'text':
		case 'number':
		case 'readonly':

			$attributes .= str_replace('|', ' ',$v['function']);

			switch ($v['function'])
			{
				case 'text_decode':

					$value = strip_slashes($value);

					break;

				case 'image_manager':

					$start_html = '<div class="input-group">';

					$end_html = '<span class="input-group-addon">
                                    <a href="' . base_url() . 'filemanager/dialog.php?type=1&amp;akey=' . config_option('file_manager_key') . '&field_id=0"
                                       class="iframe cboxElement">' . i('fa fa-camera') . ' ' . lang('select_image') . '</a></span>
							</div>';

					$data = array(
						'name'  => $v['key'],
						'id'    => '0',
						'value' => $value,
						'class' => 'form-control  ' . $attributes . ' ',
					);

					return $start_html . form_input($data) . $end_html;

					break;

				case 'date_to_sql':
				case 'start_date_to_sql':
				case 'end_date_to_sql':

					$value = display_date($value, FALSE, 2);

					$start_html = '<div class="input-group">';

					$attributes .= '  datepicker-input ';

					$end_html = '<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>';

					break;
			}

			$data = array(
				'name'  => $v['key'],
				'id'    => $v['key'],
				'value' => $value,
				'type' => $v['type'],
				'class' => 'form-control  ' . $attributes . ' ',
			);

			if ($v['type'] == 'readonly')
			{
				$data['readonly'] = TRUE;
				$data['onclick'] = 'this.select()';
			}

			return $start_html . form_input($data) . $end_html;

			break;

		case 'dropdown':

			return form_dropdown($v['key'], options($v['function']), $value, 'class="form-control select" id="' . $v['key'] . '"');

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return int
 */
function count_modules($type = '', $data = array())
{
	$i = 0;

	foreach ($data as $v)
	{
		if ($v['module_type'] == $type)
		{
			$i++;
		}
	}

	return $i;
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function generate_social_config()
{
	$config =
		array(
			// set on "base_url" the relative url that point to HybridAuth Endpoint
			'base_url' => '/login/endpoint',

			"providers"  => array(

				"Facebook" => array(
					"enabled"    => TRUE,
					"keys"       => array("id"     => config_option('layout_design_login_facebook_login_id'),
					                      "secret" => config_option('layout_design_login_facebook_login_secret')),
					"scope"      => array('email', 'user_birthday', 'user_hometown'), // optional
					"photo_size" => 200,
				),

				"Twitter" => array(
					"enabled"      => TRUE,
					"keys"         => array("key"    => config_option('layout_design_login_twitter_login_id'),
					                        "secret" => config_option('layout_design_login_twitter_login_secret')),
					"includeEmail" => TRUE,
				),

				"Google" => array(
					"enabled" => TRUE,
					"keys"    => array("id"     => config_option('layout_design_login_google_login_id'),
					                   "secret" => config_option('layout_design_login_google_login_secret')),
				),
			),

			// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
			"debug_mode" => (ENVIRONMENT == 'development'),

			"debug_file" => APPPATH . '/logs/hybridauth.log',
		);

	return $config;
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function check_db_folder()
{
	if (file_exists(config_item('sts_backup_path')))
	{
		return TRUE;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function set_default_site_address_data()
{
	//format default data for adding a new address so no errors show up
	$CI = &get_instance();

	$vars = list_fields(array(TBL_SITE_ADDRESSES));
	$vars['regions_array'] = $CI->regions->load_country_regions(config_option('sts_site_default_country'), TRUE);
	$vars['country'] = $CI->config->item('sts_site_default_country');
	$vars['country_name'] = get_country_name(config_option('sts_site_default_country'), 'country_name');


	return $vars;
}

/* End of file settings_helper.php */
/* Location: ./application/helpers/settings_helper.php */