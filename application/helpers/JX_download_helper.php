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
 * @param array $file
 * @param array $data
 * @return array
 */
function format_add_user_download($file = array(), $data = array())
{
    $vars = array(
        'product_id' => is_var($file, 'product_id'),
        'member_id'  => is_var($data, 'member_id'),
        'filename'       => $file[ 'file_name' ],
        'downloads'  => 0,
        'code'       => generate_random_string(15),
        'expires'    => get_time(now() + (60 * 60 * 24 * config_option('sts_site_days_download_expires')), TRUE),
    );

    return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function check_download_limits($data = array())
{
	if (!empty($data['max_downloads_user']))
	{
		if ($data['downloads'] >= $data['max_downloads_user'])
		{
			return TRUE;
		}
	}

	return FALSE;
}

/* End of file JX_download_helper.php */
/* Location: ./application/helpers/JX_download_helper.php */