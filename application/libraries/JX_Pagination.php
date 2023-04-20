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
 * @package	eCommerce Suite
 * @author	JROX Technologies, Inc.
 * @copyright	Copyright (c) 2007 - 2019, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link	https://www.jrox.com
 * @filesource
 */

class JX_Pagination extends CI_Pagination {

    public function next_buttons($num_pages = 0)
    {
		$current_page =  (int) floor(uri($this->uri_segment) / $this->per_page) + 1;

        $a['left'] = (($current_page - 2) * $this->per_page) >= 0 ? (($current_page - 2) * $this->per_page) : '';
        $a['right'] = ($current_page * $this->per_page) < ($num_pages * $this->per_page) ? ($current_page * $this->per_page) : '';

        return $a;
    }
}