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
class Orders extends Member_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        $models = array(
            'orders' => 'orders',
            'invoices' => 'invoices',
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
     * Member orders
     *
     * Get all orders for the member
     */
    public function view()
    {

        //get rows
        $this->data[ 'orders' ] = $this->orders->get_user_orders(sess('member_id'));

        $this->show->display(MEMBERS_ROUTE, CONTROLLER_CLASS, $this->data);
    }

    /**
     * Order Details
     *
     * Show the details for specified order id
     */
    public function details()
    {

        $this->data[ 'id' ] = (int)$this->uri->segment(4);

        $this->data[ 'p' ] = $this->orders->get_details($this->data[ 'id' ], TRUE);

        //get the order details
        if (!$this->data[ 'p' ])
        {
            log_error('error', lang('no_record_found'));
        }

        //check for ownership
        if (!$this->sec->verify_ownership($this->data[ 'p' ][ 'member_id' ]))
        {
            log_error('error', lang('invalid_id'));
        }

	    if (uri(5) == 'email')
	    {
		    $order = format_checkout_email('member_order_email', $this->data['p']);

		    $this->mail->send_template(EMAIL_MEMBER_ORDER_DETAILS, $order, TRUE, sess('default_lang_id'), $this->data['p']['order_primary_email']);

		    redirect_flashdata(MEMBERS_ROUTE . '/orders/details/' . $this->data['id'], 'email_sent_successfully');
	    }
	    else
	    {
		    $tpl = uri(5) == 'print' ? 'order_details_print' : 'order_details';

		    $this->show->display(MEMBERS_ROUTE, $tpl, $this->data);
	    }
    }
}

/* End of file Orders.php */
/* Location: ./application/controllers/members/Orders.php */