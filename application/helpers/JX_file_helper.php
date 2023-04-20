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
 * @param string $type
 * @return bool
 */
function is_file_type($str = '', $type = '')
{
	$file_parts = pathinfo($str);

	return $file_parts['extension'] == $type ? TRUE : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param bool $invalid
 * @param string $error
 * @return bool|void
 */
function is_ajax($invalid = FALSE, $error = '')
{
	if (!empty($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && strtolower($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) == 'xmlhttprequest')
	{
		if ($error == TRUE)
		{
			$msg = !empty($text) ? $text : 'invalid_data_sent';
			$html = '<div class="alert alert-danger text-capitalize">';
			$html .= '<i class="fa fa-exclamation-circle"></i> ';
			$html .= lang($msg);
			$html .= ' ' . lang('please_contact_support');
			$html .= ' </div>';

			echo($html);
		}

		return TRUE;
	}

	return $invalid == TRUE ? show_error(lang('invalid_ajax_request')) : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @param string $type
 * @return bool
 */
function check_file($str = '', $type = 'export')
{
	//check what type of file this is....

	$str = explode('.', $str);

	switch ($type)
	{
		case 'export':

			$a = array('csv', 'txt');

			break;
	}

	return (in_array(end($str), $a)) ? TRUE : FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $delim
 * @param string $newline
 * @param string $enclosure
 * @return string
 */
function csv_from_result($data = array(), $delim = ',', $newline = "\n", $enclosure = '"')
{
	$out = '';

	if ($delim == "tab") { $delim = "\t"; $enclosure = ''; }

	// First generate the headings from the table column names
	if (!empty($data['list_fields']))
	{
		foreach ($data['list_fields'] as $name)
		{
			$out .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $name) . $enclosure . $delim;
		}

		$out = rtrim($out);
		$out .= $newline;
	}

	// Next blast through the result array and build out the rows
	foreach ($data['result_array'] as $row)
	{
		foreach ($row as $item)
		{
			$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
		}
		$out = rtrim($out);
		$out .= $newline;
	}

	return $out;
}

/* End of file JX_file_helper.php */
/* Location: ./application/helpers/JX_file_helper.php */