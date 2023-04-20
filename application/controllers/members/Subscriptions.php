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
class Subscriptions extends Member_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        $models = array(
	        'members'                 => 'mem',
	        'subscriptions'           => 'sub',
	        'products'                => 'prod',
	        'affiliate_groups'        => 'aff_group',
	        'discount_groups'         => 'disc_group',
	        'products_categories'     => 'cat',
	        'products_specifications' => 'specs',
	        'products_attributes'     => 'att',
	        'cart'                    => 'cart',
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
     * Member subscriptions
     *
     * Get all subscriptions for the member
     */
    public function view()
    {

        //get rows
        $this->data['subscriptions'] = $this->sub->get_user_subscriptions(sess('member_id'), sess('default_lang_id'));

        $this->show->display(MEMBERS_ROUTE,  CONTROLLER_CLASS, $this->data);
    }

    /**
     * Subscription Details
     *
     * Show the details for specified subscription id
     */
    public function details()
    {

        $this->data['id'] = valid_id(uri(4));

        $this->data['p'] = $this->sub->get_details($this->data['id'], sess('default_lang_id'), TRUE);

        //get the commission details
        if (!$this->data['p'])
            log_error('error', lang('no_record_found'));

        //check for ownership
        if (!$this->sec->verify_ownership($this->data['p']['member_id']))
        {
            log_error('error', lang('invalid_id'));
        }

        $this->show->display(MEMBERS_ROUTE,  'subscription_details', $this->data);
    }

	// ------------------------------------------------------------------------

	public function cancel()
    {

	    $this->data['id'] = valid_id(uri(4));

	    if (config_enabled('sts_products_enable_subscription_cancellations'))
	    {
		    //get the commission details
		    if (!$p = $this->sub->get_details($this->data['id'], sess('default_lang_id'), TRUE))
		    {
			    log_error('error', lang('no_record_found'));
		    }

		    //check for ownership
		    if (!$this->sec->verify_ownership($p['member_id']))
		    {
			    log_error('error', lang('invalid_id'));
		    }

		    if ($this->sub->cancel($this->data['id']))
		    {
			    //set the session flash and redirect the page
			    $type = 'success';
			    $msg = lang('id') . ' ' . $this->data['id'] . ' ' . lang('subscription_cancelled_successfully');

			    //log it!
			    $this->dbv->rec(array('method' => __METHOD__, 'msg' => $msg, 'vars' => $p, 'email' => TRUE));
		    }
		    else
		    {
			    $type = 'error';
			    $msg = lang('could_not_cancel_subscription');
		    }

		    redirect_flashdata(MEMBERS_ROUTE . '/' . strtolower(__CLASS__) . '/view', $msg, $type);
	    }
	    else
	    {
	    	log_error(lang('subscription_cancellation_not_allowed'));
	    }
    }
}

/* End of file Subscriptions.php */
/* Location: ./application/controllers/members/Subscriptions.php */