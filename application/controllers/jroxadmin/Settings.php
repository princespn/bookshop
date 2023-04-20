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
class Settings extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper(__CLASS__);

		$this->load->model('cron_model', 'cron');

		$this->data = $this->init->initialize();

		$this->config->set_item('menu', 'system');
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
		if ($this->input->post())
		{
			if ($row = $this->set->validate_settings($this->input->post()))
			{
				if (!empty($row['success']))
				{
					$row = $this->set->update_db_settings($row['data']);

					//log it!
					$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

					//set the default response
					$response = array('type' => 'success',
					                  'msg'  => $row['msg_text'],
					);
				}
				else
				{
					$response = array('type' => 'error',
					                  'msg'  => $row['msg_text'],
					);
				}
			}

			ajax_response($response);
		}

		$settings = $this->set->get_settings('settings', FALSE);

		$this->data['settings'] = init_settings($settings);

		$this->data['cron_timers'] = $this->cron->get_timers();

		//run the page
		$this->load->page('settings/' . TPL_ADMIN_SETTINGS, $this->data);
	}

	// ------------------------------------------------------------------------

	function set_debug()
	{
		$path = APPPATH . 'config/debug.php';

		if (file_exists($path))
		{
			$debug  = uri(4) == 'development' ? 'production' : 'development';

			$content = '<?php defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');
define(\'ENVIRONMENT\', \'' . $debug . '\');';

			$handle = @fopen($path, 'w+');
			if ($handle)
			{
				if (fwrite($handle, $content))
				{
					fclose($handle);
				}
				else
				{
					show_error("Could not write to " . $path);
				}
			}
		}

		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view#system-tab');
	}

	// ------------------------------------------------------------------------

	public function view_phpinfo()
	{
		$this->load->view('admin/settings/' . TPL_ADMIN_PHPINFO, $this->data);
	}
}

/* End of file Settings.php */
/* Location: ./application/controllers/admin/Settings.php */