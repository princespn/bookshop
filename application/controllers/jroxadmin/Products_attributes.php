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
class Products_attributes extends Admin_Controller
{

	/**
	 * @var string
	 */
	protected $data = '';

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'att');
		$this->load->model('products_model', 'prod');
		$this->load->helper('products');

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
		$this->data[ 'page_options' ] = query_options($this->data);

		$this->data[ 'rows' ] = $this->att->get_rows($this->data[ 'page_options' ], sess('default_lang_id'));

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
		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_ATTRIBUTES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{


			//check if the form submitted is correct
			$row = $this->att->validate(__FUNCTION__, $this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->att->create($row['data']);

				$this->done(__METHOD__, $row);

				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row[ 'id' ], $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ], 'error');
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->att->validate(__FUNCTION__, $this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->att->update($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type' => 'success',
				                   'msg'  => $row[ 'msg_text' ]
				);
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => $row[ 'msg_text' ],
				);
			}

			ajax_response($response);
		}

		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->att->get_details($this->data[ 'id' ], sess('default_lang_id'));

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		//get languages
		$this->data[ 'languages' ] = get_languages(FALSE, FALSE);

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_ATTRIBUTES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));



		$row = $this->att->delete($id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);

		}
		else
		{
			log_error('error', $row[ 'msg_text' ]);
		}
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->att->mass_update($this->input->post(), sess('default_lang_id'));

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'   => 'success',
				                   'msg'    => $row[ 'msg_text' ],
				);
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => $row[ 'msg_text' ],
				);
			}
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------

	public function update_product_attributes()
	{
		$this->data[ 'id' ] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->att->update_product_attributes($this->data[ 'id' ], $this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . TBL_PRODUCTS . '/update/' . $this->data[ 'id' ] . '#attributes', $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				log_error('error', __CLASS__ . ': ' . lang('invalid_data'));
			}
		}

		$this->data[ 'attributes' ] = $this->att->get_product_attributes($this->data[ 'id' ]);

		$this->data[ 'meta_data' ] = link_tag('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/css/iframe.css');
		$this->data[ 'meta_data' ] .= '<script src="' . base_url('js/select2/select2.min.js') . '"></script>';

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_ASSIGN_ATTRIBUTES, $this->data, 'admin', FALSE, FALSE);
	}

	// ------------------------------------------------------------------------

	public function get_product_attributes()
	{
		//for setting attribues on the update order page
		$this->init->check_ajax_security();

		$row = array( 'error'      => '',
		              'attributes' => '' );

		if ($this->input->post('product_id'))
		{
			$this->data[ 'id' ] = (int)$this->input->post('product_id');

			//get pricing options if any
			$this->data['product'] = $this->prod->get_product_pricing($this->data['id']);

			$this->data[ 'row' ] = $this->att->get_product_attributes($this->data[ 'id' ], TRUE, sess('default_lang_id'), TRUE);

			if (!empty($this->data[ 'row' ]))
			{
				//format the attributes using an HTML snippet
				$row[ 'attributes' ] = $this->load->view('admin/orders/' . TPL_AJAX_ORDERS_PRODUCT_ATTRIBUTES, $this->data, TRUE);
			}
		}

		if (uri(4) == 'json')
			{
				echo json_encode($row);
		}
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('attribute_name', TRUE);

		$rows = $this->att->ajax_search($term, sess('default_lang_id'));

		echo json_encode($rows);
	}
}

/* End of file Product_attributes.php */
/* Location: ./application/controllers/admin/Product_attributes.php */