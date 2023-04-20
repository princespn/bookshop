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
 * @param string $str
 * @return array
 */
function check_list_array($str = '')
{
	return array(explode(config_option('import_field_separator'), $str));
}

// ------------------------------------------------------------------------

/**
 * @param $str
 * @return array
 */
function format_curl_response($str)
{
	$data = array();
	if (!empty($str))
	{
		$row = explode('&', $str);

		foreach ($row as $v)
		{
			list($key, $value) = explode('=', $v);
			$data[$key] = urldecode($value);
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $var
 * @param bool $serialize
 * @param string $default
 * @return mixed|string
 */
function is_var($data = array(), $var = '', $serialize = FALSE, $default = '')
{
	return !empty($data[ $var ]) ? $serialize == TRUE ? serialize($data[ $var ]) : $data[ $var ] : $default;
}

// ------------------------------------------------------------------------

/**
 * @param string $data
 * @param string $encode
 * @return false|string
 */
function sc($data = '', $encode = '')
{
	switch ($encode)
	{
		case 'json':

			return json_encode($data);

			break;
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool $vars
 * @return array
 */
function query_options($data = array(), $vars = TRUE, $clean = FALSE)
{
	$CI = &get_instance();

	$options = array(
		'limit'  => empty($data['session_per_page']) ? TBL_MEMBERS_DEFAULT_TOTAL_ROWS : $data['session_per_page'],
		'offset' => empty($data['offset']) ? '0' : $data['offset'],
	);

	if ($vars == TRUE && !empty($_GET))
	{
		//generate query array
		$options['query'] = validate_get($clean);

		//set subqueries
		$i = 0;
		$options['where_string'] = '';
		$options['and_string'] = '';

		foreach ($options['query'] as $k => $v)
		{
			if (in_array($k, $CI->config->item('query_type_filter')))
			{
				continue;
			}
			$options['where_string'] .= $i == 0 ? ' WHERE ' : ' AND ';
			$options['where_string'] .= $k . '=\'' . $v . '\'';
			$options['and_string'] .= 'AND ' . $k . '=\'' . $v . '\'';
			$i++;
		}
	}

	//set the md5 for cache
	$options['md5'] = md5(base64_encode(serialize($options)));

	return $options;
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function validate_get($clean = FALSE)
{
	$CI = &get_instance();

	$CI->form_validation->set_data($_GET);

	$data = array();
	foreach ($_GET as $k => $v)
	{

		$k = str_replace('-', '.', $k);
		$data[ $k ] = $clean == TRUE ? url_title($v) : xss_clean($v);
		if ($k == 'order')
		{
			$data[$k] = $v == 'DESC' ? 'DESC' : 'ASC';
		}

	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @return string
 */
function set_delimiter($type = '')
{
	switch ($type)
	{
		case 'tab':

			return "\t";

			break;

		case 'semicolon':

			return ";";

			break;

		case 'pipe':

			return '|';

			break;

		default:

			return ',';

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function read_csv($data = array())
{
	return  str_getcsv($data, set_delimiter(config_option('module_data_import_members_delimiter')) );
}

// ------------------------------------------------------------------------

/**
 * @param array $x
 * @param string $y
 * @param string $z
 * @param bool $none
 * @param string $text
 * @return array|false
 */
function format_array($x = array(), $y = '', $z = '', $none = FALSE, $text = 'none')
{
	if (empty($x))
	{
		$array = array(lang('none'));

		return $array;
	}
	else
	{
		if ($none == TRUE)
		{
			$a = array(0);

			$b = array(lang($text));
		}
		else
		{
			$a = array();
			$b = array();
		}

		foreach ($x as $v)
		{
			array_push($a, $v[ $y ]);

			if (!empty($z))
			{
				array_push($b, lang($v[ $z ]));
			}
		}

		if (!empty($b))
		{
			$options = array_combine($a, $b);
		}
		else
		{
			$options = $a;
		}

		return $options;
	}
}

// ------------------------------------------------------------------------

/**
 * @param $data
 * @return bool
 */
function is_serialized($data)
{
	// if it isn't a string, it isn't serialized
	if (!is_string($data))
	{
		return FALSE;
	}
	$data = trim($data);
	if ('N;' == $data)
	{
		return TRUE;
	}
	if (!preg_match('/^([adObis]):/', $data, $badions))
	{
		return FALSE;
	}
	switch ($badions[1])
	{
		case 'a' :
		case 'O' :
		case 's' :
			if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
			{
				return TRUE;
			}
			break;
		case 'b' :
		case 'i' :
		case 'd' :
			if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
			{
				return TRUE;
			}
			break;
	}
	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $a
 * @param bool $exit
 */
function pr($a = '', $exit = TRUE) //todo
{
	echo '<pre>';

	foreach ($a as $k => $v)
	{
		if (is_serialized($v))
		{
			//$a[$k] = unserialize($v);
		}
	}

	print_r($a);

	if ($exit == TRUE)
	{
		//debug_print_backtrace();
		exit();
	}

	echo '</pre>';
}

// ------------------------------------------------------------------------

/**
 * @param string $arr
 * @param string $key
 * @param string $item
 * @return array
 */
function array_match_values($arr = '', $key = '', $item = '')
{
	$result = array();

	for ($i = 0; $i < count($arr); $i++)
	{
		if (strcmp($arr[ $i ][ $key ], $item) == 0)
		{
			array_push($result, $arr[ $i ]);
		}
	}

	return $result;
}

// ------------------------------------------------------------------------

/**
 * @param array $array
 * @param string $class
 * @param string $url
 * @return string
 */
function array2ul($array = array(), $class = 'tree', $url = 'products/category')
{
	$out = '<ul id="tree1" class="' . $class . '">';
	foreach ($array as $key => $elem)
	{
		if (!is_array($elem))
		{
			$u = explode('-', $elem);
			$out = $out . '<li class="icon-text"><a href="' . base_url() . $url . '/' . $u[0] . '/' . url_title(strtolower($u[1])) . '">' . lang($u[1]) . '</a></li>';
		}
		else
		{
			$u = explode('-', $key);
			$out = $out . '<li><a href="' . base_url() . $url . '/' . $u[0] . '/' . url_title(strtolower($u[1])) . '">' . lang($u[1]) . '</a>' . array2ul($elem) . '</li>';
		}
	}

	$out = $out . "</ul>";

	return $out;
}

/* End of file JX_array_helper.php */
/* Location: ./application/helpers/JX_array_helper.php */