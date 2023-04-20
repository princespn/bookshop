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
class Subscriptions extends Admin_Controller
{
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

		$this->load->helper('products');

		$this->config->set_item('menu', 'orders');

		$this->lc->check(__CLASS__);

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		//show users
		$this->data['page_options'] = query_options($this->data);

		if ($this->input->get('member_id'))
		{
			$this->data['member'] = $this->dbv->get_record(TBL_MEMBERS, 'member_id', (int)$this->input->get('member_id'));
		}

		$this->data['rows'] = $this->sub->get_rows($this->data['page_options'], sess('default_lang_id'));

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
		$this->load->page('members/' . TPL_ADMIN_MEMBERS_SUBSCRIPTIONS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data['row'] = list_fields(array(TBL_MEMBERS_SUBSCRIPTIONS, TBL_PRODUCTS_NAME, TBL_MEMBERS));

		$this->data['row']['start_date_formatted'] = display_date(get_time() - 86400, FALSE, 2, TRUE);
		$this->data['row']['next_due_date_formatted'] = display_date(get_time() + 2592000, FALSE, 2, TRUE);

		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->sub->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->sub->create($row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(CONTROLLER_CLASS . '/update/' . $row['id']),
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

		//run the page
		$this->load->page('members/' . TPL_ADMIN_MEMBERS_SUBSCRIPTIONS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			$p = $this->att->get_product_attributes($this->input->post('product_id'), TRUE, sess('default_lang_id'), FALSE, FALSE);

			//check if the form submitted is correct
			$row = $this->sub->validate($this->input->post(), $p);

			if (!empty($row['success']))
			{
				if ($s = $this->specs->get_product_specs($this->input->post('product_id')))
				{
					$row['data']['specification_data'] = serialize($s);
				}

				$row = $this->sub->update($this->data['id'], $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => is_var($row, 'msg_text'),
				);
			}

			ajax_response($response);
		}

		$this->data['row'] = $this->sub->get_details($this->data['id'], sess('default_lang_id'));

		$this->load->page('members/' . TPL_ADMIN_MEMBERS_SUBSCRIPTIONS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_MEMBERS_SUBSCRIPTIONS, 'sub_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}
		else
		{
			log_error('error', $row['msg_text']);
		}
	}

	// ------------------------------------------------------------------------

	public function member()
	{
		$this->init->check_ajax_security();

		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->sub->get_member_subscriptions($this->data['id'], sess('default_lang_id'));

		$this->load->page('members/' . TPL_AJAX_MEMBER_SUBSCRIPTIONS_VIEW, $this->data, 'admin', FALSE, FALSE);
	}
}

/* End of file Subscriptions.php */
/* Location: ./application/controllers/admin/Subscriptions.php */