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
class Brands extends Admin_Controller
{
	/**
	 * @var string
	 */
	protected $data = '';

	/**
	 * @var string
	 */
	protected $table = TBL_BRANDS;

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'brands');
		$this->load->helper('products');
		$this->load->helper('html_editor');

		$this->config->set_item('menu', TBL_PRODUCTS);

		$this->data = $this->init->initialize();

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
	
	public function view()
	{
		$this->data[ 'page_options' ] = query_options($this->data);

		$rows = $this->brands->get_rows($this->data[ 'page_options' ], sess('default_lang_id'));

		$this->data[ 'rows' ] = $rows;

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
		$this->load->page('products/' . TPL_ADMIN_BRANDS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		$this->data[ 'id' ] = '';

		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( $this->table, TBL_BRANDS_NAME ));
		$this->data[ 'row' ][ 'lang' ] = set_default_create_data($this->data[ 'row' ], $this->language->get_languages());
		$this->data[ 'row' ][ 'sort_order' ] = 1;

		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->brands->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->brands->create($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('success', $row[ 'msg_text' ]);

				//set the default response
				$url = $this->input->post('redir_button') ? '/create' : '/update/' . $row[ 'data' ][ 'brand_id' ];
				$response = array( 'type'     => 'success',
				                   'msg'      =>  $row['msg_text'],
				                   'redirect' => admin_url(CONTROLLER_CLASS . $url),
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

		//initialize the html editor...
		$this->data[ 'meta_data' ] = html_editor('head');

		$this->load->page('products/' . TPL_ADMIN_BRANDS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		$this->data[ 'id' ] = uri(4);

		$this->data[ 'row' ] = $this->brands->get_details($this->data[ 'id' ]);

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->brands->validate($this->input->post(NULL, TRUE));

			if (!empty($row[ 'success' ]))
			{
				$row = $this->brands->update($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'msg'      => $row['msg_text']
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

		$this->data[ 'meta_data' ] = html_editor('head');

		$this->load->page('products/' . TPL_ADMIN_BRANDS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));



		$row = $this->brands->delete($id);

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

	// ------------------------------------------------------------------------

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->brands->mass_update($this->input->post(), sess('default_lang_id'));

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'   => 'success',
				                   'msg'    => $row[ 'msg_text' ],
				                   'reload' => TRUE,
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
	
	public function search()
	{
		$term = $this->input->get('brand_name', TRUE);

		$rows = $this->brands->ajax_search($term, sess('default_lang_id'));

		echo json_encode($rows);
	}
}

/* End of file Brands.php */
/* Location: ./application/controllers/admin/Brands.php */