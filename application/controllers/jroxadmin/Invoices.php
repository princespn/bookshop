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
class Invoices extends Admin_Controller
{
    /**
     * @var array
     */
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        //autoload public models
        $models = array(
            'invoices'              => 'invoices',
            'members'               => 'mem',
            'members_credits'       => 'credit',
            'affiliate_commissions' => 'comm',
            'email_mailing_lists'   => 'lists'
        );

        foreach ($models as $k => $v)
        {
            $this->load->model($k . '_model', $v);
        }

        $this->config->set_item('menu', TBL_ORDERS);

        $this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
    }

    /**
     * index file
     */
    public function index()
    {
        redirect_page($this->uri->uri_string() . '/view');
    }

	// ------------------------------------------------------------------------

	public function view()
    {
        $this->data[ 'page_options' ] = query_options($this->data);

        $this->data[ 'id' ] = !$this->input->get('member_id') ? '' : (int)$this->input->get('member_id');

        $this->data[ 'rows' ] = $this->invoices->get_rows($this->data[ 'page_options' ]);

        //check for pagination
        if (!empty($this->data[ 'rows' ][ 'total' ]))
        {
            $this->data[ 'page_options' ] = array(
                'uri'        => $this->data[ 'uri' ],
                'total_rows' => $this->data[ 'rows' ][ 'total' ],
                'per_page'   => $this->data[ 'session_per_page' ],
                'segment'    => $this->data[ 'db_segment' ],
            );

            $this->data[ 'paginate' ] = $this->paginate->generate($this->data[ 'page_options' ], CONTROLLER_CLASS, 'admin');
        }

        //run the page
        $this->load->page('orders/' . TPL_ADMIN_INVOICES_VIEW, $this->data);
    }

	// ------------------------------------------------------------------------

	public function create()
    {
        //if POST input is sent, let's validate it...
        if ($this->input->post())
        {
            //check if the form submitted is correct
            $row = $this->invoices->validate('create', $this->input->post());

            if (!empty($row[ 'success' ]))
            {
	            $row = $this->invoices->create_invoice($row['data'], 'manual');

	            $this->done(__METHOD__, $row);

                //set the default response
                $response = array( 'type'  => 'success',
                                   'redirect' => admin_url(strtolower(__CLASS__) . '/update/' . $row[ 'id' ])
                );
            }
            else
            {
                $response = array( 'type' => 'error',
                                   'msg'   => $row[ 'msg_text' ],
                );
            }

            ajax_response($response);
        }

	    //must have a valid member ID in order to create an invoice
	    $this->data[ 'id' ] = valid_id(uri(4));

	    //set the default dates
	    $this->data[ 'date' ] = display_date(get_time(), FALSE, 2, TRUE);
	    $this->data[ 'due_date' ] = display_date(default_due_date(), FALSE, 2, TRUE);

	    $this->data[ 'row' ] = $this->mem->get_details($this->data[ 'id' ], TRUE);

	    if (!$this->data[ 'row' ][ 'addresses' ])
	    {
		    log_error('error', lang('no_member_addresses_found'));
	    }

        $this->load->page('orders/' . TPL_ADMIN_INVOICES_CREATE, $this->data);
    }

	// ------------------------------------------------------------------------

	public function update()
    {
        //set the invoice ID
        $this->data[ 'id' ] = valid_id(uri(4));

        $this->data[ 'row' ] = $this->invoices->get_details($this->data[ 'id' ]);

        if (!$this->data['row'])
        {
            log_error('error', lang('no_record_found'));
        }

	    //check for credits
	    $this->data['credits'] = $this->credit->get_user_credits($this->data['row']['member_id']);

        //if POST input is sent, let's validate it...
        if ($this->input->post())
        {
            //check if the form submitted is correct
            $row = $this->invoices->validate(__FUNCTION__, $this->input->post());

            if (!empty($row[ 'success' ]))
            {
                $row = $this->invoices->update($row['data']);

	            $this->done(__METHOD__, $row);

                //set the default response
                $response = array( 'type'  => 'success',
                                   'msg' => $row['msg_text'],
                );
            }
            else
            {
                $response = array( 'type' => 'error',
                                   'msg'   => $row[ 'msg_text' ],
                );
            }

            ajax_response($response);
        }

        $this->load->page('orders/' . TPL_ADMIN_INVOICES_MANAGE, $this->data);
    }

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_INVOICES, 'invoice_id', $id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
			redirect_flashdata($url, $row['msg_text']);
		}
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('id'))
		{
			$row = $this->invoices->mass_update($this->input->post('id'), $this->input->post('change-status'));
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		$msg = !empty($row['msg_text']) ? $row['msg_text'] : '';

		redirect_flashdata($url, $msg);
	}

	// ------------------------------------------------------------------------

	public function email()
	{
		$this->data[ 'id' ] = (int)uri(4);

		$data = $this->invoices->get_details($this->data[ 'id' ]);

		$invoice = format_checkout_email('invoice_admin', $data);

		if ($this->mail->send_template(EMAIL_MEMBER_PAYMENT_INVOICE, $invoice, FALSE, sess('default_lang_id'), $invoice['customer_primary_email']))
		{
			$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
			redirect_flashdata($url, 'email_sent_successfully');
		}
		else
		{
			show_error(lang('could_not_send_email'));
		}
	}

	// ------------------------------------------------------------------------

	public function print_copy()
	{
		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->invoices->get_details($this->data[ 'id' ]);
		//run the page
		$this->load->page('orders/' . TPL_ADMIN_INVOICES_PRINT, $this->data, 'admin', FALSE, FALSE, TRUE);
	}

	// ------------------------------------------------------------------------

	public function member()
	{
		$this->init->check_ajax_security();

		$this->data[ 'page_options' ] = query_options($this->data);

		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'invoices' ] = $this->invoices->get_user_invoices($this->data['id'], ADMIN_MEMBERS_RECENT_DATA);

		//run the page
		$this->load->view('admin/members/' . TPL_AJAX_MEMBER_INVOICES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function invoice_totals()
	{
		$this->init->check_ajax_security();

		if ($this->input->post('items'))
		{
			echo calc_invoice_totals($this->input->post('items', TRUE));
		}
	}

	// ------------------------------------------------------------------------

	public function general_search()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->invoices->search($this->data['page_options'], sess('default_lang_id'));

		//check for pagination
		if (!empty($this->data['rows']['total']))
		{
			$this->data['page_options'] = array(
				'uri'        => $this->data['uri'],
				'total_rows' => $this->data['rows']['total'],
				'per_page'   => $this->data['session_per_page'],
				'segment'    => $this->data['db_segment'],
			);

			$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('orders/' . TPL_ADMIN_INVOICES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function search()
    {
        $term = $this->input->get('invoice_number', TRUE);

        $rows = $this->invoices->ajax_search(uri(5, 'invoice_number'), $term);

        echo json_encode($rows);
    }
}

/* End of file Invoices.php */
/* Location: ./application/controllers/admin/Invoices.php */