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
class Invoice_payments extends Admin_Controller
{
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
			'affiliate_commission_rules' => 'comm_rules',
			'email_mailing_lists'   => 'lists',
			'affiliate_groups'      => 'aff_group'
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
		$this->data['page_options'] = query_options($this->data);

		$this->data['id'] = !$this->input->get('member_id') ? '' : (int)$this->input->get('member_id');

		$this->data['rows'] = $this->invoices->get_payment_rows($this->data['page_options']);

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
		$this->load->page('orders/' . TPL_ADMIN_INVOICE_PAYMENTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data['row'] = list_fields(array(TBL_INVOICE_PAYMENTS, TBL_INVOICES));

		//get invoice data if any
		if (uri(4))
		{
			if ($invoice = $this->invoices->get_details(valid_id(uri(4)), TRUE, TRUE))
			{
				$this->data['row'] = array_merge($this->data['row'], $invoice);
			}
		}

		//set the default dates
		$this->data['row']['date_formatted'] = display_date(get_time(), FALSE, 2, TRUE);
		$this->data['row']['invoice_id'] = (int)uri(4);

		//if POST input is sent, let's validate it...
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->invoices->validate_payment(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$this->data['row']['transaction'] = $this->invoices->create_payment($row['data']);

				//process commissions if set
				if ($this->input->post('process_commissions'))
				{
					if (config_enabled('affiliate_marketing') && $this->data['row'][ 'affiliate_id' ])
					{
						$this->data['row'][ 'commissions' ] = $this->comm->generate_commissions($this->data['row'], 'admin');

						if ($this->data['row'][ 'commissions' ])
						{
							$send_email = array('alert_pending', 'alert_unpaid');

							if (in_array(config_item('sts_affiliate_new_commission'), $send_email))
							{
								foreach ($this->data['row'][ 'commissions' ] as $c)
								{
									if (!empty($c['is_affiliate']) && !empty($c['alert_new_commission']))
									{
										$comm = format_checkout_email('commission', $c, 'admin');

										$this->mail->send_template(EMAIL_MEMBER_AFFILIATE_COMMISSION, $comm, TRUE, sess('default_lang_id'), $c['primary_email']);
									}
								}
							}
						}
					}
				}

				$this->session->set_flashdata('success',  lang('record_created_successfully'));

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'redirect' => (admin_url(strtolower(__CLASS__) . '/update/' . $this->data['row']['transaction']['invoice_payment_id'])),
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => $row['msg_text'],
				);
			}

			ajax_response($response);
		}

		$this->load->page('orders/' . TPL_ADMIN_INVOICES_PAYMENTS_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//set the invoice ID
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->invoices->get_payment_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$m = array('refund', 'manual','credit');

		if (!in_array($this->data['row']['method'], $m))
		{
			$this->init_module('payment_gateways', $this->data['row']['method']);

			//use module_row array to store all module data for editing in the view
			$module = $this->config->item('module_alias');

			if (method_exists($this->$module, 'process_refund'))
			{
				$this->data['refund_module'] = TRUE;
			}
		}

		//if POST input is sent, let's validate it...
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->invoices->validate_payment(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->invoices->update_payment($row['data']);

				$this->session->set_flashdata('success',  $row['msg_text']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'redirect' => (admin_url(strtolower(__CLASS__) . '/update/' . $this->data['id'])),
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => $row['msg_text'],
				);
			}

			ajax_response($response);
		}

		$this->load->page('orders/' . TPL_ADMIN_INVOICES_PAYMENTS_MANAGE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_INVOICE_PAYMENTS, 'invoice_payment_id', $id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
	}

	// ------------------------------------------------------------------------

	public function refund()
	{
		$this->data['id']  = valid_id(uri(4));

		$this->data['row'] = $this->invoices->get_payment_details($this->data['id']);

		$this->data['row']['refund_amount'] = $this->input->post('refund_amount');

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$m = array('refund', 'manual','credit');

		if (!in_array($this->data['row']['method'], $m))
		{
			//initialize the require files for the module
			$this->init_module('payment_gateways', $this->data['row']['method']);

			//use module_row array to store all module data for editing in the view
			$module = $this->config->item('module_alias');
		}

		$type = 'manual';

		$alert = 'success';

		//try to refund through the gateway
		if ($this->input->post('refund_method') == $this->data['row']['method'])
		{
			if (method_exists($this->$module, 'process_refund'))
			{
				$row = $this->$module->process_refund($this->data['row']);

				if (!empty($row['success']))
				{
					$this->data['row'] = $row['data'];
					$type = $this->data['row']['method'];
				}
				else
				{
					$alert = 'error';
					$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text'], 'level' => 'error'));
				}
			}
		}

		if ($alert == 'success')
		{
			//add manual refund
			$row = $this->invoices->add_refund($this->data['row'], $type);

			$this->done(__METHOD__, $row);
		}

		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text'], $alert);
	}

	// ------------------------------------------------------------------------

	public function apply_credits()
	{
		//set the invoice ID
		$this->data[ 'id' ] = valid_id(uri(4));

		$invoice = $this->invoices->get_details($this->data[ 'id' ]);

		//check for credits
		$invoice['credits'] = $this->credit->get_user_credits($invoice['member_id'], TRUE);

		if (!empty($invoice['credits'] ))
		{
			$row = format_credit_payment($invoice);

			if (!empty($row['payment']))
			{
				$this->data['row']['transaction'] = $this->invoices->create_payment($row['payment']);

				//update credit
				$row = $this->credit->update($row['credits']['mcr_id'], $row['credits']);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/invoices/update/' . $this->data['id'], $row['msg_text']);
			}
		}
	}
}
/* End of file Invoice_payments.php */
/* Location: ./application/controllers/admin/Invoice_payments.php */