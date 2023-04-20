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
 * @param array $data
 * @param bool $moderator_only
 * @return bool
 */
function check_moderation($data = array(), $moderator_only = FALSE)
{
	$CI = &get_instance();

	if ($moderator_only == FALSE)
	{
		if ($CI->sec->verify_ownership($data['member_id'], sess('member_id'), FALSE))
		{
			return TRUE;
		}
	}

	//check for moderation
	if (sess('allow_forum_moderation') == '1')
	{
		return TRUE;
	}

	return FALSE;
}

/* End of file forum_helper.php */
/* Location: ./application/helpers/forum_helper.php */