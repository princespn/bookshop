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

class Update_session extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->init->reset_cache(__CLASS__, true);
    }

	// ------------------------------------------------------------------------

	public function index()
    {
        redirect_page();
    }

	// ------------------------------------------------------------------------

	public function rows()
    {
	    $sess['admin'] = $this->session->admin;
        $sess['admin']['rows_per_page'] = $this->uri->segment(4, $this->session->userdata('per_page'));

        $this->session->set_userdata($sess);

        $ret = base64_decode($this->uri->segment(5), true);

        if (!$ret) show_error('invalid redirect url');

        header("Location:" . urldecode($ret));
    }

}