<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
Module Name: WP Plugin Template
Module URI: http://fyaconiello.github.com/wp-plugin-template
Module Description: A simple WordPress plugin template
Version: 1.0
Author: Francis Yaconiello
Author URI: http://www.yaconiello.com
License: GPL2
 */

class Modules extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$m = array(__CLASS__          => 'mod',
		           'shipping'         => 'ship',
		           'payment_gateways' => 'pay',
		           'data_import'      => 'import',
		           'data_export'      => 'export',
		           'reports'          => 'reports',
		           'uploads'          => 'up',
		           'affiliate_payments' => 'aff_pay',
		           'email_templates'  => 'email_template',
		);

		foreach ($m as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('settings');

		$this->config->set_item('menu', 'system');

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
		$this->data['page_options'] = query_options($this->data);

		if (!$this->input->get('module_type'))
		{
			redirect_page($this->uri->uri_string() . '?module_type=admin_reporting');
		}
		else
		{
			$this->data['module_type'] = $this->input->get('module_type', TRUE);
		}

		$this->data['rows'] = $this->mod->get_rows($this->data['page_options'], $this->data['module_type']);

		$this->data['mod_folders'] = $this->mod->get_module_folders(FALSE, TRUE, $this->data['rows']);

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
		$this->load->page('settings/' . TPL_ADMIN_MODULES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function install()
	{
		//load the module
		$this->init_module(uri(4), uri(5));

		$vars = $this->mod->add(uri(4), uri(5));
		$msg = '';

		//set model and function alias for calling methods
		$module = !config_item('module_alias') ? 'module' : config_item('module_alias');
		if (method_exists($this->$module, 'install'))
		{
			$row = $this->$module->install($vars['id']);
		}

		if (!empty($row['success']))
		{
			$msg = $row['msg_text'];
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/?module_type=' . uri(4), $msg);

	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->mod->get_module_details($this->data['id']);

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->mod->validate($this->input->post());

			if (!empty($row['success']))
			{
				$row = $this->mod->update($row['data']);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $this->data['id'], $row['msg_text']);
			}
			else
			{
				//show errors on form
				$this->data['error'] = validation_errors();
			}
		}

		$this->load->page('settings/' . TPL_ADMIN_MODULES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$this->data['id'] = valid_id(uri(4));
		$msg = '';

		if (!$mod = $this->mod->get_module_details($this->data['id']))
		{
			log_error('error', lang('no_record_found'));
		}

		//load the module
		$this->init_module($mod['module']['module_type'], $mod['module']['module_folder']);

		//set model and function alias for calling methods
		$module = !config_item('module_alias') ? 'module' : config_item('module_alias');
		if (method_exists($this->$module, 'uninstall'))
		{
			$row = $this->$module->uninstall($this->data['id']);
		}
		else
		{
			//remove settings from database
			$this->mod->remove_config($this->data['id'], 'payment_gateways');
		}

		if (!empty($row['success']))
		{
			$msg = $row['msg_text'];
			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/?module_type=' . $mod['module']['module_type'], $msg);
	}

	// ------------------------------------------------------------------------

	public function delete_all()
	{
		if (!$row = $this->mod->get_modules())
		{
			log_error('error', lang('no_record_found'));
		}

		foreach ($row as $mod)
		{
			//load the module
			$this->init_module($mod['module_type'], $mod['module_folder']);

			//set model and function alias for calling methods
			$module = !config_item('module_alias') ? 'module' : config_item('module_alias');

			if (method_exists($this->$module, 'uninstall'))
			{
				$row = $this->$module->uninstall($mod['module_id']);
			}
			else
			{
				//remove settings from database
				$this->mod->remove_config($mod['module_id'], $mod['module_folder']);
			}

			//reset modules
			$this->remove_module($mod['module_type'], $mod['module_folder']);

			if (!empty($row['success']))
			{
				$msg = $row['msg_text'];

			}
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view/', '');
	}

	// ------------------------------------------------------------------------

	public function external()
	{
		//load the module
		$this->init_module(uri(4), uri(5));

		if ($this->config->item('module_external_url'))
		{
			redirect($this->config->item('module_external_url'));
		}
		else
		{
			redirect(ADMIN_ROUTE . '/payment_gateways');
		}
	}

	// ------------------------------------------------------------------------

	public function unzip()
	{

		$msg = '';

		if ($this->input->post())
		{
			$row = $this->up->unzip($this->input->post('zip_file'), APPPATH . 'modules/' . $this->input->post('module_type'));

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
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view?module_type=' . $this->input->post('module_type'), $msg);
	}

	// ------------------------------------------------------------------------

	public function upload()
	{

		//check for file uploads
		$files = $this->up->validate_uploads('modules');

		if (!empty($files['success']))
		{
			//set json response
			$response = array('type'      => 'success',
			                  'file_name' => $this->config->slash_item('sts_data_import_folder') . $files['file_data']['file_name'],
			                  'msg'       => lang('file_uploaded_successfully') . ' . ' . lang('please_proceed'),
			);

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

/* End of file Modules.php */
/* Location: ./application/controllers/admin/Modules.php */