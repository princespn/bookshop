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
class Reports extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'reports' => 'report',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->config->set_item('menu', 'reports');

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
		//get the reports
		$this->data[ 'rows' ] = $this->mod->get_modules('admin_reporting', TRUE);

		//run the page
		$this->load->page('reports/' . TPL_ADMIN_REPORTS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function generate()
	{
		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->mod->get_module_details($this->data['id'], TRUE, 'admin_reporting');

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('admin_reporting', $this->data['row']['module']['module_folder']);

		//get the reports
		$model = $this->config->item('module_model_alias');
		$func = $this->config->item('module_generate_function'); //generate_module()
		$this->data['report'] = $this->$model->$func($this->data);
		$this->data['no_archive'] = TRUE;

		//run the page
		if (uri(5) == 'archive')
		{
			$html = $this->load->page('reports/' . $this->config->item('module_admin_view_template'), $this->data, 'admin', FALSE, FALSE, FALSE, TRUE);

			$row = $this->report->archive_report($this->data['row']['module']['module_name'], $html);

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . TBL_REPORT_ARCHIVE . '/view', $row[ 'msg_text' ]);
			}
		}
		else
		{
			$this->load->page('reports/' . $this->config->item('module_admin_view_template'), $this->data);
		}
	}
}

/* End of file Reports.php */
/* Location: ./application/controllers/admin/Reports.php */