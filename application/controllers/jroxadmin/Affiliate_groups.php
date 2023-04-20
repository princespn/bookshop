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
class Affiliate_groups extends Admin_Controller
{

	protected $data = array();

	// ------------------------------------------------------------------------
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'group');

		$this->config->set_item('menu', 'affiliates');

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
		$this->data[ 'page_options' ] = query_options($this->data);

		//get rows
		$this->data[ 'rows' ] = $this->group->get_rows($this->data[ 'page_options' ]);

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

		$this->data[ 'meta_data' ] .= '<script src="' . base_url('js/select2/select2.min.js') . '"></script>';

		//run the page
		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_GROUPS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function create()
	{
		$this->lc->check(__CLASS__);

		$this->data[ 'id' ] = '';

		if (config_enabled('enable_multi_affiliate_groups'))
		{
			//fill in default values for input fields
			$this->data['row'] = list_fields(array(TBL_AFFILIATE_GROUPS));

			if ($this->input->post())
			{
				//check if the form submitted is correct
				$row = $this->group->validate($this->input->post(NULL, TRUE));

				if (!empty($row['success']))
				{
					$row = $this->group->create($row['data']);

					$this->done(__METHOD__, $row);

					//set the session flash and redirect the page
					$page = !$this->input->post('redir_button') ? 'view' : 'create';
					redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/' . $page, $row['msg_text']);
				}
				else
				{
					//show errors on form
					$this->data['error'] = validation_errors();
				}
			}

			$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_GROUP_CREATE, $this->data);
		}
		else
		{
			show_error(lang('invalid_license'));
		}
	}

	// ------------------------------------------------------------------------
	
	public function update()
	{
		$this->data[ 'id' ] = (int)$this->uri->segment(4);

		$this->data[ 'row' ] = $this->group->get_details($this->data[ 'id' ]);

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->group->validate($this->input->post(NULL, TRUE));

			if (!empty($row[ 'success' ]))
			{
				$row = $this->group->update($row['data']);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);

			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		$this->load->page('affiliate/' . TPL_ADMIN_AFFILIATE_GROUP_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------
	
	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->group->delete($id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);

		}
		else
		{
			log_error('error', $row['msg_text']);
		}
	}

	// ------------------------------------------------------------------------
	
	public function update_product_affiliate_groups()
	{
		$this->data[ 'id' ] = (int)$this->uri->segment(4); //product id

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->group->update_product_affiliate_groups($this->data[ 'id' ], $this->input->post(NULL, TRUE));

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . TBL_PRODUCTS . '/update/' . $this->data[ 'id' ] . '#affiliate_marketing', $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				log_error('error', __CLASS__ . ': ' . lang('invalid_data'));
			}
		}

		$this->data[ 'groups' ] = $this->group->get_product_affiliate_groups($this->data[ 'id' ]);

		$this->data[ 'meta_data' ] = link_tag('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/css/iframe.css');
		$this->data[ 'meta_data' ] .= '<script src="' . base_url('js/select2/select2.min.js') . '"></script>';

		$this->load->page('products/' . TPL_ADMIN_PRODUCTS_ASSIGN_AFFILIATE_GROUPS, $this->data, 'admin', FALSE, FALSE);
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->group->update_groups($this->input->post());

			if (!empty($row[ 'success' ]))
			{
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
		}

		ajax_response($response);
	}

	// ------------------------------------------------------------------------
	
	public function search()
	{
		$term = $this->input->get('aff_group_name', TRUE);

		$rows = $this->group->ajax_search($term, uri(5));

		echo json_encode($rows);
	}
}

/* End of file Affiliate_groups.php */
/* Location: ./application/controllers/Affiliate_groups.php */