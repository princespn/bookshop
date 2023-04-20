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

class Plugins_model extends CI_Model {

	/**
	 * Plugins_model constructor.
	 */
	public function __construct()
    {
        parent::__construct();
    }

	// ------------------------------------------------------------------------

	/**
	 * @param string $method
	 * @param string $data
	 * @return mixed
	 */
	public function init_plugin($method = '', $data = '')
    {
        $func = strtolower(str_replace('::','_', $method));

        return do_action($func, $data);
    }
}
