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
class Email_templates extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'template');

		$this->load->helper('html_editor');

		$this->config->set_item('menu', 'email');

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
		$this->data['rows'] = $this->dbv->get_rows(array(), TBL_EMAIL_TEMPLATES);

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_TEMPLATES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->template->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->template->create($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'msg'      => $row[ 'msg_text' ],
				                   'redirect' => admin_url(TBL_EMAIL_TEMPLATES . '/update/' . $row[ 'id' ]),
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
		$this->data[ 'row' ] = set_default_form_values(array( TBL_EMAIL_TEMPLATES, TBL_EMAIL_TEMPLATES_NAME ));
		$this->data[ 'row' ][ 'lang' ] = set_default_create_data($this->data[ 'row' ], get_languages(FALSE, FALSE));

		//initialize the html editor...
		$this->data[ 'meta_data' ] = html_editor('head');

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_TEMPLATES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//get the ID
		$this->data[ 'id' ] = valid_id(uri(4));

		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->template->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->template->update($row[ 'data' ]);

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

		$this->data[ 'row' ] = $this->template->get_details($this->data[ 'id' ], sess('default_lang_id'), FALSE);

		//initialize the html editor...
		$this->data[ 'meta_data' ] = html_editor('head');

		//run the page
		$this->load->page('email/' . TPL_ADMIN_EMAIL_TEMPLATES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->template->delete($id);

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

	public function update_header()
	{
		$row = $this->set->update_db_settings($this->input->post());

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
}

/* End of file Email_templates.php */
/* Location: ./application/controllers/admin/Email_templates.php */