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
class Support_categories extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var string
	 */
	protected $table = TBL_SUPPORT_CATEGORIES;

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'cat');


		$this->config->set_item('menu', TBL_SUPPORT_TICKETS);
		$this->config->set_item('sub_menu', TBL_SUPPORT_TICKETS);

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

		//get data
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

		//add jquery UI..
		$this->data[ 'meta_data' ] .= '<script src="' . base_url('themes/admin/default/js/jquery-ui.js') . '"></script>';

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_CATEGORIES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//get language files

		$row = $this->cat->create($this->language->get_languages());

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row[ 'id' ], $row[ 'msg_text' ]);
		}

		show_error(lang('could_not_create_record'));
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//get the id..
		$this->data[ 'id' ] = (int)uri(4);

		//fill in default values for input fields
		$this->data[ 'row' ] = $this->cat->get_details($this->data[ 'id' ], sess('default_lang_id'));

		//check for post data first...
		if ($this->input->post('lang'))
		{


			//validate the POST data first....
			$row = $this->cat->validate($this->input->post('lang'));

			if (!empty($row[ 'success' ]))
			{
				$row = $this->cat->update($this->data[ 'id' ], $this->input->post('lang', TRUE));

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type' => 'success',
				                   'msg'  => lang('system_updated_successfully')
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

		//run the page
		$this->load->page('support/' . TPL_ADMIN_SUPPORT_CATEGORIES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = (int)uri(4);

		$row = $this->cat->delete($id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);

		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	public function update_order()
	{
		$this->dbv->update_sort_order(TBL_SUPPORT_CATEGORIES, $this->input->get('catid', TRUE), 'category_id');
	}

}

/* End of file Support_categories.php */
/* Location: ./application/controllers/admin/Support_categories.php */