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
class Products_categories extends Admin_Controller
{
	
	/**
	 * @var array
	 */
	protected $data = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model(__CLASS__ . '_model', 'cat');
		$this->load->helper('products');
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
		$this->data[ 'page_options' ] = query_options($this->data);
		
		$this->data[ 'rows' ] = $this->cat->get_rows($this->data[ 'page_options' ], sess('default_lang_id'));
		
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

		$this->data[ 'parent_id' ] = $this->input->get('p-parent_id', TRUE);
		
		//run the page
		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_CATEGORIES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->cat->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->cat->create($row[ 'data' ]);

				$this->done(__METHOD__, $row);
				
				$this->session->set_flashdata('success', $row[ 'msg_text' ]);
				
				//set the default response
				$url = $this->input->post('redir_button') ? admin_url(CONTROLLER_CLASS . '/create/') : admin_url(CONTROLLER_CLASS . '/update/' . $row[ 'data' ][ 'category_id' ]);
				$response = array( 'type'     => 'success',
				                   'msg'      => $row[ 'msg_text' ],
				                   'redirect' => $url,
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

		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( TBL_PRODUCTS_CATEGORIES, TBL_PRODUCTS_CATEGORIES_NAME ));
		$this->data[ 'row' ][ 'lang' ] = set_default_create_data($this->data[ 'row' ], $this->language->get_languages());
		$this->data[ 'row' ][ 'sort_order' ] = 1;
		//initialize the html editor...
		$this->data[ 'meta_data' ] = html_editor('head');
		//check if were adding under parent category
		if ($this->data[ 'parent_id' ] = (int)uri(4))
		{
			$a = $this->cat->get_details($this->data['parent_id']);
			$this->data['parent_path'] = $a['category_path']['path'] . ' / ' . $a['category_name'];

		}

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_CATEGORIES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->cat->validate($this->input->post());
			
			if (!empty($row[ 'success' ]))
			{
				$row = $this->cat->update($row[ 'data' ]);

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

		$this->data[ 'row' ] = $this->cat->get_details($this->data[ 'id' ]);

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->data[ 'meta_data' ] = html_editor('head');

		//page templates
		$this->data[ 'row' ][ 'page_templates' ] = $this->tpl->get_templates('categories', '', TRUE);

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_CATEGORIES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));
		

		
		$row = $this->cat->delete($id);
		
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

			
			$row = $this->cat->mass_update($this->input->post());
			
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
		$term = $this->input->get('category_name', TRUE);
		
		$rows = $this->cat->ajax_search($term, sess('default_lang_id'), uri(5));

		if (!$rows)
		{
			$rows = array('FALSE' => 1);
		}
		echo json_encode($rows);
	}
}

/* End of file Products_categories.php */
/* Location: ./application/controllers/admin/Products_categories.php */