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
class Main_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		//get the version number...
		require_once APPPATH . '/config/version.php';

		$this->load->config('label');
		$this->load->config('remote');

		//load libraries
		$this->load->library('user_agent');

		//autoload models
		$models = array(
			'currencies'          => 'currency',
			'countries'           => 'country',
			'regions'             => 'regions',
			'measurements'        => 'measure',
			'weight'              => 'weight',
			'settings'            => 'set',
			'site_menus'          => 'menus',
			'init'                => 'init',
			'security'            => 'sec',
			'languages'           => 'language',
			'pagination'          => 'paginate',
			'plugins'             => 'plugin',
			'email'               => 'mail',
			'templates'           => 'tpl',
			'alc'                 => 'lc',
			'promotional_rules'   => 'promo',
			'db_validation'       => 'dbv',
			'affiliate_marketing' => 'aff',
			'zones'               => 'zone',
			'tax_classes'         => 'tax',
			'rewards'             => 'rewards',
			'modules'             => 'mod',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		//load downline model if set
		if (file_exists(APPPATH . '/models/Network_marketing_model.php'))
		{
			$this->load->model('Network_marketing_model', 'downline');
		}
		else
		{
			$this->load->model('Affiliate_downline_model', 'downline');
		}

		//autoload all the helpers
		$helpers = array('cookie', 'form', 'string', 'array', 'content', 'file', 'text',
		                 'breadcrumb', 'html', 'log', 'date', 'number', 'inflector', 'typography');

		foreach ($helpers as $h)
		{
			$this->load->helper($h);
		}

		//set the default time zone
		$zone = !$this->config->item('sts_default_php_timezone') ? 'GMT' : $this->config->item('sts_default_php_timezone');
		date_default_timezone_set($zone);


		//get config registry
		$settings = $this->set->get_settings('all');

		foreach ($settings as $row)
		{
			$this->config->set_item($row['settings_key'], $row['settings_value']);
		}

		//set csrf tokens
		$this->config->set_item('csrf_token', $this->security->get_csrf_token_name());
		$this->config->set_item('csrf_value', $this->security->get_csrf_hash());

		//set version
		$this->config->set_item('app_version', APP_VERSION);
		$this->config->set_item('app_revision', APP_REVISION_NUMBER);

		//set site buiilder
		$this->config->set_item('site_builder_path', SITE_BUILDER);

		//get default address
		$add = $this->set->get_site_address($this->config->item('sts_site_default_address'), FALSE, TRUE);

		foreach ($add as $k => $v)
		{
			if ($k != 'id')
			{
				$this->config->set_item('sts_site_shipping_' . $k, $v);
			}
		}

		//set date format
		$fdate = explode(':', $this->config->item('sts_admin_date_format'));

		//set config items
		$cfg = array('menu'            => uri(),
		             'sub_menu'        => uri(),
		             'paginate'        => FALSE,
		             'meta_data'       => FALSE,
		             'curdate'         => str_replace('yyyy', 'yy', $fdate[0]),
		             'format_date'     => $fdate[0],
		             'format_date2'    => $fdate[1],
		             'format_date3'    => $fdate[2],
		             'sql_date_format' => $fdate[3],
		);

		foreach ($cfg as $c => $f)
		{
			$this->config->set_item($c, $f);
		}

		//set default page title
		$this->config->set_item('page_title', $this->config->item('sts_site_name'));

		//check IP restrictions
		if ($this->config->item('sts_sec_site_restrict_ips') && !empty($_SERVER['REMOTE_ADDR']))
		{
			$deny = explode("\n", trim($this->config->item('sts_sec_site_restrict_ips')));

			foreach ($deny as $ip)
			{
				$ip = trim($ip);
				if (!empty($ip) && preg_match("/$ip/", $_SERVER['REMOTE_ADDR']))
				{
					$this->sec->check_admin_ip_restriction();

					die('<h1>403 Forbidden</h1>');
				}
			}
		}

		//get default currency
		if ($c = $this->db->where('status', '1')->get(TBL_CURRENCIES))
		{
			if ($c->num_rows() > 0)
			{
				$this->config->set_item('currencies', $c->result_array());

				foreach (config_item('currencies') as $d)
				{
					if ($d['code'] == config_item('sts_site_default_currency'))
					{
						$this->config->set_item('currency', $d);
					}
				}
			}
		}

		//initialize variables
		$this->lc->check_network();

	}

	protected function init_module($type = '', $folder = '')
	{
		if (!is_dir(APPPATH . 'modules/' . $type . '/' . $folder))
		{
			echo $type . '/' . $folder;
			log_error('error', lang('no_module_found'));
		}

		//load module package
		$module_path = APPPATH . 'modules/' . $type . '/' . $folder;
		$this->load->add_package_path($module_path);

		//load the config file
		$this->load->config('module_config');

		foreach ($this->config->item('module_models') as $k => $v)
		{
			$this->load->model($k, $v);
		}

		//load any libraries
		if ($this->config->item('module_libraries') && is_array($this->config->item('module_libraries')))
		{
			foreach ($this->config->item('module_libraries') as $v)
			{
				$this->load->library($v);
			}
		}

		//load any helpers
		if ($this->config->item('module_helpers') && is_array($this->config->item('module_helpers')))
		{
			foreach ($this->config->item('module_helpers') as $v)
			{
				$this->load->helper($v);
			}
		}

		//load any language files
		if ($this->config->item('module_lang_files') && is_array($this->config->item('module_lang_files')))
		{
			foreach ($this->config->item('module_lang_files') as $v)
			{
				$this->lang->load($v, $this->session->default_language);
			}
		}
	}

	protected function remove_module($type = '', $folder = '')
	{
		//load module package
		$module_path = APPPATH . 'modules/' . $type . '/' . $folder;
		$this->load->remove_package_path($module_path);
	}

	protected function done($m = '', $data = array(), $level = 'info', $load_plugins = TRUE, $email = FALSE)
	{
		$f = explode('::', $m);

		//log it!
		$this->dbv->rec(array('method' => $m,
		                      'msg'    => is_var($data, 'msg_text'),
		                      'vars'   => $data,
		                      'level'  => $level,
		));

		//reset db cache
		$this->init->reset_cache($f[0], TRUE);

		//send email alert if set
		if ($email == TRUE)
		{
			$data['event'] = is_var($data, 'msg_text', FALSE, 'system_updated_successfully');
			$this->mail->send_email_events($data);
		}

		//run plugin
		if ($load_plugins == TRUE)
		{
			$this->plugin->init_plugin($m, $data);
		}
	}
}

class Admin_Session_Controller extends Main_Controller
{
	//session controller for the admin side
	public function __construct()
	{
		parent::__construct();

		//load session for admin
		$session_vars = array('sess_cookie_name'     => $this->config->item('sess_adm_cookie_name'),
		                      'sess_expiration'      => $this->config->item('sess_adm_expiration'),
		                      'sess_expire_on_close' => TRUE,
		);

		foreach ($session_vars as $a => $b)
		{
			$this->config->set_item($a, $b);
		}

		$this->load->library('session');

		//load language files
		if (!sess('default_lang_id'))
		{
			$this->session->set_userdata('default_lang_id', $this->config->item('sts_site_default_language'));
		}
	}
}

class Admin_Controller extends Admin_Session_Controller
{
	public function __construct()
	{
		parent::__construct();

		//load language files
		load_lang_files('admin');

		//run security checks for the admin
		$this->sec->run_admin_checks();

		//set for table views
		$vars = array(
			'site_url'         => site_url(),
			'uri'              => site_url(uri(1) . '/' . uri(2) . '/' . uri(3)),
			'offset'           => uri(4),
			'session_per_page' => !$this->session->admin['rows_per_page'] ? TBL_ADMIN_DEFAULT_TOTAL_ROWS : $this->session->admin['rows_per_page'],
			'next_sort_order'  => $this->input->get('order') == 'DESC' ? 'ASC' : 'DESC',
		);

		foreach ($vars as $a => $b)
		{
			$this->config->set_item($a, $b);
		}

		//set file manager token
		$this->config->set_item('file_manager_key', sha1(config_option('file_manager_access_token')));

		check_file_manager_cookie();

		$this->config->set_item('module_type', __CLASS__);
	}
}

class Session_Controller extends Main_Controller
{
	//session controller for public side

	public function __construct()
	{
		parent::__construct();

		//load session
		$this->load->library('session');

		//load language files
		if (!sess('default_lang_id'))
		{
			$this->session->set_userdata('default_lang_id', $this->config->item('sts_site_default_language'));
		}

		//check custom currency
		if (config_enabled('sts_cart_allow_currency_conversion'))
		{
			if (sess('custom_currency'))
			{
				foreach (config_item('currencies') as $d)
				{
					if ($d['code'] == sess('custom_currency'))
					{
						$this->config->set_item('custom_currency_array', $d);
					}
				}
			}
		}
	}
}

class Public_Controller extends Session_Controller
{
	public function __construct()
	{
		parent::__construct();

		//load language files for the user
		load_lang_files('site', sess('default_lang_id'));

		//autoload public models
		$models = array(
			'members'    => 'mem',
			'cart'       => 'cart',
			'products'   => 'prod',
			'blog_posts' => 'blog',
			'brands'     => 'brand',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		//set the default offset and per page config
		$vars = array(
			'uri'              => site_url(uri(1) . '/' . uri(2) . '/' . uri(3)),
			'offset'           => uri(1) == MEMBERS_ROUTE ? uri(4) : uri(3),
			'session_per_page' => !$this->session->rows_per_page ? TBL_MEMBERS_DEFAULT_TOTAL_ROWS : $this->session->rows_per_page,
			'next_sort_order'  => $this->input->get('order') == 'DESC' ? 'ASC' : 'DESC',
			'module_type'      => __CLASS__,
		);

		foreach ($vars as $a => $b)
		{
			$this->config->set_item($a, $b);
		}
	}

	protected function update_list($type = '', $list = '', $email = '', $data = array(), $lang_id = '1')
	{
		//check if we are using a third party mailing list
		if (config_option('sts_email_mailing_list_module'))
		{
			//add the user to the internal list first...
			$this->lists->$type($list, $email, $data, $lang_id);

			// check if we're using a third party module..
			if (config_option('sts_email_mailing_list_module') != 'internal')
			{
				$this->init_module('mailing_lists', config_option('sts_email_mailing_list_module'));

				//run the add_user/remove_user function from the module
				$module = $this->config->item('module_alias');
				$func = $this->config->item('module_' . $type);

				//run only if the method is available
				if (method_exists($this->$module, $func))
				{
					$row = $this->$module->$func($list, $email, $data);

					//mailing list updated
					if (!empty($row['success']))
					{
						$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));
					}
				}
			}

			//reset module
			$this->remove_module('mailing_lists', config_option('sts_email_mailing_list_module'));
		}
	}
}

class Product_Controller extends Public_Controller
{
	public function __construct()
	{
		parent::__construct();

		$models = array('products'                => 'prod',
		                'affiliate_groups'        => 'aff_group',
		                'discount_groups'         => 'disc_group',
		                'blog_groups'             => 'blog_group',
		                'products_attributes'     => 'att',
		                'products_categories'     => 'cat',
		                'products_specifications' => 'specs',
		                'products_categories'     => 'cat',
		                'products_downloads'      => 'dw',
		                'products_reviews'        => 'rev',
		                'brands'                  => 'brands',
		                'gift_certificates'       => 'gift',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$helpers = array('products');

		foreach ($helpers as $h)
		{
			$this->load->helper($h);
		}

		if (!config_enabled('sts_store_enable'))
		{
			redirect();
		}

		$this->config->set_item('module_type', __CLASS__);
	}
}

class Member_Controller extends Public_Controller
{
	public function __construct()
	{
		parent::__construct();

		//load required models
		$models = array(
			'forms' => 'form',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->sec->check_login_session('member');

		$this->config->set_item('module_type', __CLASS__);
	}
}

class Cart_Controller extends Product_Controller
{
	public function __construct()
	{
		parent::__construct();

		//load required models
		$models = array(
			'uploads'  => 'uploads',
			'coupons'  => 'coupon',
			'checkout' => 'checkout',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->config->set_item('module_type', __CLASS__);
	}
}

class Checkout_Controller extends Cart_Controller
{
	public function __construct()
	{
		parent::__construct();

		//load required models
		$models = array(
			'email_mailing_lists'        => 'lists',
			'members_credits'            => 'credit',
			'forms'                      => 'form',
			'shipping'                   => 'ship',
			'orders'                     => 'orders',
			'invoices'                   => 'invoices',
			'affiliate_commissions'      => 'comm',
			'affiliate_commission_rules' => 'comm_rules',
			'subscriptions'              => 'sub',
			'payment_gateways'           => 'pay',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('download');

		if (config_enabled('sts_cart_ssl_on_checkout'))
		{
			if (!is_secure())
			{
				redirect(ssl_url($this->uri->uri_string()));
			}
		}

		//update the session first with latest data
		$this->cart->update_session_cart();

		$this->config->set_item('module_type', __CLASS__);
	}
}

class Cron_Controller extends Session_Controller
{
	public function __construct()
	{
		parent::__construct();

		$models = array(
			'products'                   => 'prod',
			'members'                    => 'mem',
			'affiliate_groups'           => 'aff_group',
			'discount_groups'            => 'disc_group',
			'blog_groups'                => 'blog_group',
			'email_mailing_lists'        => 'lists',
			'email_follow_ups'           => 'follow_up',
			'subscriptions'              => 'sub',
			'forms'                      => 'form',
			'shipping'                   => 'ship',
			'orders'                     => 'orders',
			'invoices'                   => 'invoices',
			'affiliate_commissions'      => 'comm',
			'affiliate_commission_rules' => 'comm_rules',
			'subscriptions'              => 'sub',
			'payment_gateways'           => 'pay',
			'reports'                    => 'report',
			'support_tickets'            => 'support',
			'backup'                     => 'backup',
			'cron'                       => 'cron',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->config('cron');

		$this->cron->check_security();

		//load language files for the user
		load_lang_files('site', config_item('sts_site_default_language'));
	}
}

/* End of file JX_Controller.php */
/* Location: ./application/core/JX_Controller.php */
