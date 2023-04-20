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
class Affiliate_marketing extends Member_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'modules' => 'mod',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->data = $this->init->initialize('site');
		
		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	/**
	 * Affiliate marketing
	 *
	 * View available affiliate marketing tools
	 */
	public function view()
	{

		//get the reports
		$this->data['tools'] = $this->mod->get_modules('affiliate_marketing', TRUE);

		$this->show->display(MEMBERS_ROUTE, CONTROLLER_CLASS, $this->data);
	}

	/**
	 * Affiliate tools
	 *
	 * List the tools for the affiliate marketing
	 */
	public function module()
	{

		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->mod->get_module_details($this->data['id'], TRUE, 'affiliate_marketing');

		if (!$this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//initialize the require files for the module
		$this->init_module('affiliate_marketing', $this->data['row']['module']['module_folder']);

		//get the reports
		$module = $this->config->item('module_alias');


		switch(uri(5))
		{
			case 'activate':

				if (method_exists($this->$module, 'member_activate'))
				{
					if ($this->input->post())
					{
						$row = $this->$module->member_activate($this->input->post(), 'validate');

						if (!empty($row['success']))
						{
							$row = $this->$module->member_activate($row['data'], 'activate');
						}

						redirect_page($row['redirect_url']);
					}

					$this->$module->member_activate();
				}

				break;

			case 'create':

				if (method_exists($this->$module, 'member_create'))
				{
					if ($this->input->post())
					{
						//run validation
						$row = $this->$module->member_create($this->input->post(), 'validate');

						if (!empty($row['success']))
						{
							if (!empty($row['success']))
							{
								$row = $this->$module->member_create($row['data'], 'update');
							}
						}

						redirect_page($row['redirect_url']);
					}

					$this->$module->member_create();
				}

				break;

			case 'update':

				if (method_exists($this->$module, 'member_update'))
				{
					if ($this->input->post())
					{
						$row = $this->$module->member_update($this->input->post(), 'validate');

						if (!empty($row['success']))
						{
							$row = $this->$module->member_update($row['data'], 'update');
						}

						$url = !$this->agent->referrer() ? $row['redirect_url'] : $this->agent->referrer();

						redirect_flashdata($url, $row['msg_text']);
					}
				}

				break;

			case 'delete':

				if (method_exists($this->$module, 'member_delete'))
				{
					if ($this->input->post())
					{
						$row = $this->$module->member_delete($this->input->post(), 'validate');

						if (!empty($row['success']))
						{
							$this->$module->member_delete($row, 'update');
						}
					}

					$this->$module->member_delete();
				}

				break;

			default: //view

				$this->data['page_options'] = query_options($this->data);

				$this->data['p'] = $this->$module->generate_module($this->data, sess('member_id'));

				$this->show->display(MEMBERS_ROUTE, $this->data['p']['template'], $this->data, FALSE, config_option('template_full_path'));
				 
				break;
		}
	}
}

/* End of file Affiliate_marketing.php */
/* Location: ./application/controllers/members/Affiliate_marketing.php */