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

function get_kb_title($parent_id = '0')
{
	$CI = &get_instance();

	if ($parent_id > 0)
	{
		$CI->db->select('category_name');
		$CI->db->where('category_id', $parent_id);
		$CI->db->where('language_id', $CI->session->default_lang_id);

		$query = $CI->db->get(TBL_KB_CATEGORIES_NAME);

		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			return $row[ 'category_name' ];
		}
	}

	return lang('kb_categories');
}

/* End of file kb_helper.php */
/* Location: ./application/helpers/kb_helper.php */