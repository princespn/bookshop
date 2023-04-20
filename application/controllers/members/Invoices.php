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
class Invoices extends Member_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        $models = array(
            'invoices' => 'invoices',
            'members' => 'mem',
            'affiliate_commissions' => 'comm',
        );

        foreach ($models as $k => $v)
        {
            $this->load->model($k . '_model', $v);
        }

        $this->data = $this->init->initialize('site');
		
		log_message('debug', __CLASS__ . ' Class Initialized');
    }

	// ------------------------------------------------------------------------

    /**
     * index file
     */
    public function index()
    {
        redirect_page($this->uri->uri_string() . '/view');
    }

	// ------------------------------------------------------------------------

    /**
     * Member invoices
     *
     * Get all invoices for the member
     */
    public function view()
    {

        //get rows
        $this->data['invoices'] = $this->invoices->get_user_invoices(sess('member_id'));

        $this->show->display(MEMBERS_ROUTE,  CONTROLLER_CLASS, $this->data);
    }

	// ------------------------------------------------------------------------

    /**
     * Invoice Details
     *
     * Show the details for specified invoice id
     */
    public function details()
    {

        $this->data['id'] = valid_id(uri(4));

	    $this->data['p'] = $this->invoices->get_details($this->data['id']);

	    //get the invoice details
	    if (!$this->data['p'])
	    {
		    log_error('error', lang('no_record_found'));
	    }

        //check for ownership
        if (!$this->sec->verify_ownership($this->data['p']['member_id']))
        {
            log_error('error', lang('invalid_id'));
        }

	    $tpl = uri(5) == 'print' ? 'invoice_details_print' : 'invoice_details';

        $this->show->display(MEMBERS_ROUTE,  $tpl, $this->data);
    }
}

/* End of file Invoices.php */
/* Location: ./application/controllers/members/Invoices.php */