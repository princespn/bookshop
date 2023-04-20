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
class Admin_users extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		//autoload public models
		$models = array(
			__CLASS__      => 'admins',
			'Admin_groups' => 'group',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

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

	// ------------------------------------------------------------------------

	/**
	 * View admin users in list format
	 */
	public function view()
	{
		$this->data['page_options'] = query_options($this->data);

		$this->data['rows'] = $this->dbv->get_rows($this->data['page_options'], TBL_ADMIN_USERS);

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
		$this->load->page('admin_users/' . TPL_ADMIN_ADMIN_USERS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Create a new admin user
	 */
	public function create()
	{
		//fill in default values for input fields
		$this->data['row'] = set_default_form_values(array(TBL_ADMIN_USERS));

		$this->data['admin_groups'] = $this->group->get_admin_groups(TRUE);

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->admins->validate(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row, 'security', FALSE, TRUE);

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

		$this->load->page('admin_users/' . TPL_ADMIN_ADMIN_USERS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update an admin user
	 */
	public function update()
	{
		$this->data['id'] = uri(4);

		$this->data['row'] = $this->admins->get_admin_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->data['admin_groups'] = $this->group->get_admin_groups(TRUE);

		$this->data['admin_alerts'] = $this->admins->get_alerts();

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->admins->validate(__FUNCTION__, $this->input->post());

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row, 'security', FALSE, TRUE);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		$this->load->page('admin_users/' . TPL_ADMIN_ADMIN_USERS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete an admin user
	 */
	public function delete()
	{
		$id = (int)uri(4);

		if ($id > 1)
		{
			$row = $this->admins->delete($id);

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row['msg_text']);
			}
		}
		else
		{
			log_error('error', lang('could_not_delete_record'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		$row['msg_text'] = '';

		if ($this->input->post('id') AND count($this->input->post('id')) > 0)
		{

			foreach ($this->input->post('id') as $id)
			{
				if ($id == 1)
				{
					continue;
				}

				if ($this->input->post('change-status') == 'delete')
				{
					$row = $this->admins->delete((int)$id);

					$this->hook->load_hooks(__METHOD__, $row);

					$row['msg_text'] = lang('system_updated_successfully');
				}
				else
				{
					$row = $this->admins->update_status((int)$id, $this->input->post('change-status'));
				}
			}
		}

		$this->done(__METHOD__, $row);

		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();

		redirect_flashdata($url, $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		$term = $this->input->get('username', TRUE);

		$rows = $this->admins->ajax_search($term, TRUE);

		echo json_encode($rows);
	}
}

/* End of file Admin_users.php */
/* Location: ./application/controllers/admin/Admin_users.php */