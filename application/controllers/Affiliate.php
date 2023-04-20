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
class Affiliate extends Public_Controller
{
	protected $data;

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		//autoload public models
		$models = array(
			'affiliate_marketing' => 'aff',
			'members'             => 'mem',
			'affiliate_groups'    => 'aff_group',
			'discount_groups'     => 'disc_group',
			'blog_groups'         => 'blog_group',
			'email_mailing_lists' => 'lists',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function id()
	{
		if (config_enabled('sts_affiliate_enable_affiliate_marketing'))
		{
			//set default page redirect
			$page = config_option('sts_affiliate_default_landing_page');

			if (!sess('tracking_data') || config_enabled('sts_affiliate_overwrite_existing_cookie'))
			{
				$tool_id = '';

				//verify username
				if (uri(DEFAULT_AFFILIATE_USERNAME_URI))
				{
					$user = url_title(uri(DEFAULT_AFFILIATE_USERNAME_URI));

					//set if it's a tool id
					if (uri(DEFAULT_AFFILIATE_TOOL_MODULE_URI))
					{
						$tool_id = valid_id(uri(DEFAULT_AFFILIATE_TOOL_ID_URI));

						//get tool details
						$this->init_module('affiliate_marketing', uri(DEFAULT_AFFILIATE_TOOL_MODULE_URI));

						$module = $this->config->item('module_alias');

						if (method_exists($this->$module, 'get_record_details'))
						{
							if (!$tool = $this->$module->get_record_details($tool_id))
							{
								show_error(lang('invalid_data'));
							}

							if (!empty($tool['enable_redirect']) && !empty($tool['redirect_custom_url']))
							{
								$page = $tool['redirect_custom_url'];
							}
						}
					}

					$overwrite = TRUE;

					if (sess('tracking_data'))
					{
						$row = sess('tracking_data');

						$overwrite = config_enabled('sts_affiliate_overwrite_existing_cookie') ? TRUE : FALSE;

					}

					if ($overwrite == TRUE)
					{
						//set cookie
						$row = $this->aff->set_tracking_data($user, TRUE, uri(DEFAULT_AFFILIATE_TOOL_MODULE_URI), $tool_id);
					}

					if (!empty($row))
					{
						$this->session->set_userdata('tracking_data', $row);

						//set default page redirect
						$page = set_landing_page($page, $row);
					}
				}

				//set output headers
				set_headers('affiliate');
			}

			if (empty($tool))
			{
				//redirect to the affiliate store if enabled
				$page = $this->aff->check_affiliate_stores($page, $user);
			}

			//set 301 redirect
			redirect_page($page);
		}
		else
		{
			redirect_page(site_url());
		}
	}
}

/* End of file Affiliate.php */
/* Location: ./application/controllers/Affiliate.php */