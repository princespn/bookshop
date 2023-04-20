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
class Dashboard extends Admin_Controller
{
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		if (uri(3) == 'view')
		{
			$this->config->set_item('menu', 'design');
			$this->config->set_item('sub_menu', 'layout');
		}
		
		$this->data = $this->init->initialize();

		$this->load->model('dashboard_model', 'dash');
		$this->load->model('reports_model', 'report');
	}

	// ------------------------------------------------------------------------

	public function index()
	{
		$this->dash->check_homepage();

		foreach (config_item('dashboard_latest_data') as $v)
		{
			$this->data[$v] = $this->dash->get_widgets($v);
		}

		$this->data['meta_data'] .= '<link rel="stylesheet" href="' . base_url('js/morris/morris.css') . '">
	<script src="' . base_url('js/morris/raphael-min.js') . '"></script>
	<script src="' . base_url('js/morris/morris.min.js') . '"></script>';

		//run the page
		$this->load->page('system/' . TPL_ADMIN_DASHBOARD_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	//for the members dashboard area
	public function view()
	{
		$this->data['icons'] = $this->dbv->get_all(TBL_MEMBERS_DASHBOARD, 'sort_order', 'ASC');

		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/js/jquery-ui.js') . '"></script>';

		//run the page
		$this->load->page('design/' . TPL_ADMIN_LAYOUT_DASHBOARD_ICONS, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_MEMBERS_DASHBOARD, 'dashboard', $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->create(TBL_MEMBERS_DASHBOARD, $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url('dashboard/update/' . $row['id']),
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

		//fill in default values for input fields
		$this->data['row'] = set_default_form_values(array(TBL_MEMBERS_DASHBOARD));

		$this->data['row']['icon'] = 'fa-file-text-o';

		$this->data['meta_data'] = link_tag('themes/admin/default/third/font-awesome/css/fontawesome-iconpicker.css');
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/third/font-awesome/js/fontawesome-iconpicker.js') . '"></script>';

		$this->load->page('design/' . TPL_ADMIN_LAYOUT_DASHBOARD_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->dbv->validate(TBL_MEMBERS_DASHBOARD, 'dashboard', $this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->dbv->update(TBL_MEMBERS_DASHBOARD, 'dash_id', $row['data']);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type' => 'success',
				                  'msg'  => $row['msg_text'],
				                  'data' => $row['data'],
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

		$this->data['row'] = $this->dbv->get_record(TBL_MEMBERS_DASHBOARD, 'dash_id', $this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->data['meta_data'] = link_tag('themes/admin/default/third/font-awesome/css/fontawesome-iconpicker.css');
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/default/third/font-awesome/js/fontawesome-iconpicker.js') . '"></script>';

		$this->load->page('design/' . TPL_ADMIN_LAYOUT_DASHBOARD_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_MEMBERS_DASHBOARD, 'dash_id', $id);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/dashboard/view', $row['msg_text']);
		}
		else
		{
			show_error(lang('could_not_delete'));
		}
	}

	// ------------------------------------------------------------------------

	public function update_order()
	{
		$this->dash->update_sort_order($this->input->get('id', TRUE));
	}

	// ------------------------------------------------------------------------

	public function getting_started()
	{
		$this->set->update_db_settings(array('sts_admin_show_getting_started_widget' => '0'));

		header('Location:' . admin_url());
	}

	// ------------------------------------------------------------------------

	public function get_data()
	{
		$this->init->check_ajax_security();

		$id = valid_id(uri(4, TRUE));

		$row = $this->dash->get_widgets($id, (int)(uri(5)));

		$response = array('type'  => 'success',
		                  'title' => lang('dashboard_' . $id),
		                  'data'  => $row,
		);

		ajax_response($response);
	}
}