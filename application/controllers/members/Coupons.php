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
 * @package    eCommerce Suite
 * @author    JROX Technologies, Inc.
 * @copyright    Copyright (c) 2007 - 2020, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link    https://www.jrox.com
 * @filesource
 */
class Coupons extends Public_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $models = array(
            'coupons' => 'coupon',
            'products' => 'prod',
        );

        foreach ($models as $k => $v)
        {
            $this->load->model($k . '_model', $v);
        }

        $this->data = $this->init->initialize('site');
		
		log_message('debug', __CLASS__ . ' Class Initialized');
    }

    /**
     * index file
     */
    public function index()
    {
        redirect_page($this->uri->uri_string() . '/view');
    }

    /**
     * Coupons
     *
     * View available coupons for the user
     */
    public function view()
    {

        //get the coupons
        $this->data['coupons'] = $this->coupon->get_user_coupons(sess('member_id'));

        $this->show->display(MEMBERS_ROUTE,  CONTROLLER_CLASS, $this->data);
    }
}

/* End of file Coupons.php */
/* Location: ./application/controllers/members/Coupons.php */