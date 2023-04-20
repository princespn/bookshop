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

class Themes extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$m = array(__CLASS__ => 'themes',
		           'uploads' => 'up',
		);

		$this->load->config('palettes');
		$this->load->helper('file');

		foreach ($m as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

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
		redirect_page($this->uri->uri_string() . '/view_themes');
	}

	// ------------------------------------------------------------------------

	public function set_theme()
	{
		$row = $this->set->update_db_settings(array('layout_design_site_theme' => uri(4),
		                                            'layout_design_custom_css' => ''));

		//check to load palette if aany
		$row = $this->themes->load_palette(uri(4));

		$row = $this->themes->save_palette($row['data']);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view_themes', $row['msg_text']);
		}
		else
		{
			//show errors on form
			$this->data['error'] = validation_errors();
		}
	}

	// ------------------------------------------------------------------------

	public function view_themes()
	{
		if ($this->input->post())
		{
			$row = $this->set->update_db_settings($this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$this->themes->save_palette($this->input->post(NULL, TRUE));

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view_themes', $row['msg_text']);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		$this->data['rows'] = $this->themes->get_themes();

		$this->data['current_css'] = $this->themes->get_css($this->data['layout_design_site_theme']);

		//get colors if any
		$this->data['colors'] = $this->themes->get_css_colors($this->data['layout_design_site_theme']);

		$this->data['meta_data'] = link_tag('themes/admin/' . $this->data['sts_admin_layout_theme'] . '/third/codemirror/codemirror.css');

		$this->data['meta_data'] .= link_tag('js/colorpicker/spectrum.css');

		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/' . $this->data['sts_admin_layout_theme'] . '/third/codemirror/codemirror.js') . '"></script>';
		$this->data['meta_data'] .= '<script src="' . base_url('themes/admin/' . $this->data['sts_admin_layout_theme'] . '/third/codemirror/mode/xml/xml.js') . '"></script>';

		$this->data['palettes'] = config_item('palettes');

		//run the page
		$this->load->page('design/' . TPL_ADMIN_THEMES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function load_palette()
	{
		$row = $this->themes->load_palette(uri(4));

		$row = $this->themes->save_palette($row['data']);

		$this->done(__METHOD__, $row);

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view_themes#palette', $row['msg_text']);
	}

	// ------------------------------------------------------------------------

	public function unzip()
	{
		$msg = '';

		if ($this->input->post('zip_file'))
		{
			$row = $this->up->unzip($this->input->post('zip_file'), PUBPATH . '/themes/site/');

			if (!empty($row['success']))
			{
				$msg = lang('file_unzipped_successfully');
			}
			else
			{
				show_error(lang($row['msg_text']));
			}
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view_themes', $msg);
	}

	// ------------------------------------------------------------------------

	public function upload()
	{

		//check for file uploads
		$files = $this->up->validate_uploads('themes');

		if (!empty($files['success']))
		{
			//set json response
			$response = array('type'      => 'success',
			                  'file_name' => $this->config->slash_item('sts_data_import_folder') . $files['file_data']['file_name'],
			                  'msg'       => lang('file_uploaded_successfully') . ' . ' . lang('please_proceed'),
			);

			//log it!
			$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('file_uploaded_successfully')));
		}
		else
		{
			//error!
			$response = array('type' => 'error',
			                  'msg'  => $files['msg'],
			);
		}

		//send the response via ajax
		ajax_response($response);

	}
}

/* End of file Themes.php */
/* Location: ./application/controllers/admin/Themes.php */