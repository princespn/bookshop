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
 * @param string $file
 * @param string $method
 * @param string $line
 * @param array $error
 */
function get_error($file = '', $method = '', $line = '', $error = array())
{
	$CI = &get_instance();

	$msg = '';
	if (empty($error))
	{
		$error = $CI->db->error();
	}

	if (!empty($error[ 'message' ]))
	{
		$msg .= '<p>Sorry, but there was an error processing your request...</p>';
	}

	if (ENVIRONMENT == 'development')
	{
		$msg .= '<p><strong>Error Message:</strong> ' . $error[ 'message' ] . '</p>';
		$msg .= '<p><strong>File:</strong>' . $file;
		$msg .= '</p><p><strong>Method:</strong>' . $method;
		$msg .= '</p><p><strong>Line:</strong> ' . $line;

		if ($CI->db->last_query())
		{
			$msg .= '</p><p><strong>Last SQL Query:</strong><br />' . $CI->db->last_query();
		}
		$msg .= '</p>';
	}

	log_error('error', $msg);
}

// ------------------------------------------------------------------------

/**
 * @param string $level
 * @param string $msg
 */
function log_error($level = 'error', $msg = '')
{
	log_message($level, $msg);
	show_error($msg);
}

/* End of file log_helper.php */
/* Location: ./application/helpers/log_helper.php */