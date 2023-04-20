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
class Products extends Admin_Controller
{
	/**
	 * @var string
	 */
	protected $data = '';

	public function __construct()
	{
		parent::__construct();

		$m = array('products'                => 'prod',
		           'affiliate_groups'        => 'aff_group',
		           'discount_groups'         => 'disc_group',
		           'products_attributes'     => 'att',
		           'products_categories'     => 'cat',
		           'products_specifications' => 'specs',
		           'tax_classes'             => 'tax',
		);

		foreach ($m as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('string');

		$this->load->helper('html_editor');

		$this->config->set_item('menu', TBL_PRODUCTS);

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

		$this->data['rows'] = $this->prod->get_rows($this->data['page_options'], sess('default_lang_id'));

		$this->data['category_id'] = !$this->input->get('category_id') ? '' : (int)$this->input->get('category_id');

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
		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->prod->validate(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->prod->create($row['data']);

				$this->done(__METHOD__, $row);

				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row['msg_text']);
			}
			else
			{
				//show errors on form
				log_error('error', lang('could_not_create_record'));
			}
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text'], 'error');
	}

	// ------------------------------------------------------------------------

	public function clone_product()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($row = $this->prod->get_details($this->data['id'], sess('default_lang_id')))
		{
			$row = $this->prod->clone_product($row);

			$this->done(__METHOD__, $row);

			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row['msg_text']);
		}
		else
		{
			//show errors on form
			log_error('error', lang('could_not_create_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->prod->validate(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->prod->update($row['data']);

				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('success', $row['msg_text']);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
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

		if (!$row = $this->prod->get_details($this->data['id'], sess('default_lang_id')))
		{
			log_error('error', lang('no_record_found'));
		}

		//get discount groups
		$this->load->model('discount_groups_model');
		$row['dg_array'] = $this->discount_groups_model->get_discount_groups();

		//get measurements
		$row['measurements'] = $this->measure->get_measurements(TRUE);

		//get weight options
		$row['weight_options'] = $this->weight->get_weight_options(sess('default_lang_id'), TRUE);

		//page templates
		$row['page_templates'] = $this->tpl->get_templates($row['product_type'], '', TRUE);

		//get tax classes
		$row['tax_classes'] = $this->tax->get_tax_classes(TRUE);

		$this->data['row'] = $row;

		$this->data['k'] = 0;

		//set header data
		$this->data['meta_data'] = html_editor('head');

		$this->load->page('products/' . init_product_template($row['product_type']), $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post('products'))
		{
			//update status
			$row = $this->prod->mass_update($this->input->post());
		}

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, 'system_updated_successfully');
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->prod->delete($id, sess('default_lang_id'));

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
		}

		redirect_page(admin_url());
	}

	// ------------------------------------------------------------------------

	public function set_default_photo()
	{
		$id = valid_id(uri(4));

		$row = $this->prod->set_default_photo($id);

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['row']['product_id'] . '#media', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function get_product_attributes()
	{
		$this->init->check_ajax_security();

		$row = array('error'      => '',
		             'attributes' => '');

		if ($this->input->post('product_id'))
		{
			$this->data['id'] = valid_id($this->input->post('product_id'));

			$this->data['product'] = $this->prod->get_details($this->data['id']);

			$this->data['row'] = $this->att->get_product_attributes($this->data['id'], TRUE, sess('default_lang_id'), TRUE);

			//format the attributes using an HTML snippet
			$row['attributes'] = $this->load->view('admin/orders/' . TPL_AJAX_ORDERS_PRODUCT_ATTRIBUTES, $this->data, TRUE);
		}

		if (uri(4) == 'json')
		{
			echo json_encode($row);
		}
	}

	// ------------------------------------------------------------------------

	public function general_search()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->prod->search($this->data['page_options'], sess('default_lang_id'));

		$this->data['category_id'] = !$this->input->get('category_id') ? '' : (int)$this->input->get('category_id');

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
		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('product_name', TRUE);

		$rows = $this->prod->ajax_search(uri(5, 'product_name'), $term, TPL_AJAX_LIMIT, sess('default_lang_id'), FALSE, uri(6));

		echo json_encode($rows);
	}
}

/* End of file Products.php */
/* Location: ./application/controllers/admin/Products.php */