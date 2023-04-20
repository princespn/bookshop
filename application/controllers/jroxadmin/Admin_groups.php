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
class Admin_groups extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'group');

		$this->config->set_item('menu', 'system');

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

	/**
	 * View Admin Groups
	 */
	public function view()
	{
		//get rows
		$this->data[ 'rows' ] = $this->dbv->get_rows(array(), TBL_ADMIN_GROUPS);

		//run the page
		$this->load->page('admin_users/' . TPL_ADMIN_ADMIN_GROUPS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Create a New Admin Group
	 */
	public function create()
	{
		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( TBL_ADMIN_GROUPS ));

		$this->data[ 'permissions' ] = get_admin_permissions();

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->group->validate($this->input->post(NULL, TRUE));

			if (!empty($row[ 'success' ]))
			{
				$row = $this->group->create($row['data']);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				$page = !$this->input->post('redir_button') ? 'view' : 'create';
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/' . $page, $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		$this->load->page('admin_users/' . TPL_ADMIN_ADMIN_GROUPS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update an Admin Group
	 */
	public function update()
	{
		$this->data[ 'id' ] = valid_id(uri(4));

		if ($this->data[ 'id' ] == 1)
		{
			show_error(lang('invalid_id'));
		}

		if (!$this->data[ 'row' ] = $this->group->get_details($this->data[ 'id' ]))
		{
			log_error('error', lang('no_record_found'));
		}

		$this->data[ 'permissions' ] = get_admin_permissions($this->data[ 'row' ][ 'permissions' ]);

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

		$this->load->page('admin_users/' . TPL_ADMIN_ADMIN_GROUPS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete Admin Group
	 */
	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id > 1)
		{
			$row = $this->dbv->delete(TBL_ADMIN_GROUPS, 'admin_group_id', $id);

			if (!empty($row[ 'success' ]))
			{
				$this->dbv->reset_id(TBL_ADMIN_USERS,
					'admin_group_id',
					$id,
					config_option('default_admin_group_id')
				);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
			}
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}
}


/* End of file Admin_groups.php */
/* Location: ./application/controllers/admin/Admin_groups.php */