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
 * @param string $key
 * @param string $value
 * @param array $data
 * @return mixed|string
 */
function check_custom_value($key = '', $value = '', $data = array())
{
	if (!empty($data[$key]))
	{
		return $data[$key];
	}

	return $value;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool $installed
 * @return array
 */
function format_lang_folders($data = array(), $installed = TRUE)
{
	$lang = array();

	if (!empty($data))
	{
		foreach ($data as $v)
		{
			if ($installed == TRUE)
			{
				if (!empty($v['values']))
				{
					array_push($lang);
				}
			}
			else
			{
				if (empty($v['values']))
				{
					$lang[$v['name']] = lang($v['name']);
				}
			}
		}
	}

	return $lang;
}

// ------------------------------------------------------------------------

/**
 * @return bool|int|mixed
 */
function set_lang_id()
{
	$CI = &get_instance();

	if ($CI->input->get('lang'))
	{
		return (int)$CI->input->get('lang');
	}

	return sess('default_lang_id');
}

// ------------------------------------------------------------------------

/**
 * @param string $line
 * @param string $for
 * @param array $attributes
 * @return mixed|string
 */
function lang($line = '', $for = '', $attributes = array())
{
	$CI =& get_instance();

	if (is_array($CI->config->item('dbi_lang_filter')))
	{
		foreach ($CI->config->item('dbi_lang_filter') as $k => $v)
		{
			$line = str_replace($k, $v, strtolower($line));
		}
	}

	$lang = ($CI->lang->line(($line)));

	if ($CI->config->item($line, 'custom_language_entries'))
	{
		$lang = $CI->config->item($line, 'custom_language_entries');

	}

	if (empty($lang))
	{
		$lang = str_replace('_', ' ', $line);
	}

	if ($for !== '')
	{
		$lang = '<label for="' . $for . '"' . _stringify_attributes($attributes) . '>' . $lang . '</label>';
	}

	return $lang;
}

// ------------------------------------------------------------------------

/**
 * @param string $line
 * @return string
 */
function check_desc($line = '')
{
	$lang = str_replace(' ', '_', $line);
	$desc = strtolower($lang);

	$lang = get_instance()->lang->line($desc);

	return empty($lang) ? $line : $lang;
}

// ------------------------------------------------------------------------

/**
 * @param bool $status
 * @param bool $form
 * @return array|bool|false
 */
function get_languages($status = FALSE, $form = TRUE)
{
	$CI =& get_instance();

	$row = $CI->language->get_languages($status);

	if (!empty($row))
	{
		return $form == TRUE ? format_array($row, 'language_id', 'name') : $row;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param string $lang_id
 */
function load_lang_files($type = '', $lang_id = '1')
{
	$CI =& get_instance();

	$lang_files = $CI->config->item('default_lang_files');

	if (!$CI->session->default_language)
	{
		$a = $CI->db->get('languages', array('language_id' => (int)config_item('sts_' . $type . '_default_language')));
		$b = $a->row_array();
		$CI->session->set_userdata('default_language', $b['name']);
		$CI->session->set_userdata('default_language_code', $b['code']);
	}

	foreach ($lang_files as $v)
	{
		$CI->lang->load($v, $CI->session->default_language);
	}

	$lang_entries = $CI->language->load_custom_entries($lang_id);

	if (!empty($lang_entries))
	{
		$a = array();

		foreach ($lang_entries as $v)
		{
			$a[$v['key']] = $v['value'];
		}

		$CI->config->set_item('custom_language_entries', $a);
	}
}

/* End of file language_helper.php */
/* Location: ./application/helpers/language_helper.php */