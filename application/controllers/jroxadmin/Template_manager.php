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
class Template_manager extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->config->set_item('menu', 'design');
		$this->config->set_item('sub_menu', 'layout');

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
		$this->data['rows'] = get_templates();

		$this->data['module_rows'] = get_module_templates();

		//check for pagination
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
		$this->load->page('design/' . TPL_ADMIN_TEMPLATE_MANAGER_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['category'] = uri(4);
		$this->data['file'] = uri(5);
		$this->data['sub_folder'] = uri(6);
		$this->data['type'] = uri(7);
		$this->data['template'] = $this->data['category']  . '/' . $this->data['file'] ;

		$this->data['row'] = $this->tpl->get_details($this->data['category'], $this->data['file'], $this->data['sub_folder']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		if ($this->input->post())
		{
			$row = $this->tpl->validate_custom_template($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->tpl->update_custom_template($row['data']);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata($this->uri->uri_string(), $row['msg_text']);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		$this->data['meta_data'] = link_tag('themes/admin/' . $this->data['sts_admin_layout_theme'] . '/third/codemirror/codemirror.css');

		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/' . $this->data['sts_admin_layout_theme'] . '/third/codemirror/codemirror.js') . '"></script>';
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/' . $this->data['sts_admin_layout_theme'] . '/third/codemirror/mode/xml/xml.js') . '"></script>';

		$this->load->page('design/' . TPL_ADMIN_TEMPLATE_MANAGER_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function reset_template()
	{
		$this->data['id'] = uri(4);

		$row = $this->dbv->delete(TBL_PAGE_TEMPLATES, 'template_id', $this->data['id']);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		$url = !$this->agent->referrer() ? ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view' : $this->agent->referrer();
		redirect_flashdata($url, lang('system_updated_successfully'));
	}
}

/* End of file Template_manager.php */
/* Location: ./application/controllers/admin/Template_manager.php */