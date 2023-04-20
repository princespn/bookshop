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
class Init_model extends CI_Model
{
	/**
	 * Init_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		//load default cache driver
		$this->load->driver('cache', array('adapter' => $this->config->item('cache_driver_type'),
		                                   'backup'  => 'file'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $ajax
	 * @return mixed
	 */
	public function initialize($type = 'admin', $ajax = '')
	{
		//set controllers
		$class = $this->router->fetch_class();
		$method = $this->router->fetch_method();

		define('CONTROLLER_METHOD', ucfirst($class) . '::' . $method);
		define('CONTROLLER_CLASS', str_replace('custom_', '', strtolower($class)));
		define('CONTROLLER_FUNCTION', empty($method) ? '' : $method);
		define('INIT_TYPE', $type);

		//set default page title
		$title = $method == 'index' ? $class : $class . '_' . $method;
		$this->config->set_item('page_title', lang($title));

		//default page ID
		$this->config->set_item('page_id', str_replace('::', '-', strtolower(CONTROLLER_METHOD)));

		//set fonts
		if (in_array(config_item('layout_design_theme_header_font'), config_item('google_fonts')))
		{
			$this->config->set_item('load_google_fonts', TRUE);
		}

		if (in_array(config_item('layout_design_theme_base_font'), config_item('google_fonts')))
		{
			$this->config->set_item('load_google_fonts', TRUE);
		}

		if ($type != 'admin' && $type != 'system' && $type != 'external')
		{
			$this->init_site($type);
		}

		if (defined('INIT_TYPE') && INIT_TYPE != 'external')
		{
			//run innodb transactions first...
			$this->init->db_trans('trans_start');
		}

		return $this->config->config;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if page is accessed via ajax
	 */
	public function check_ajax_security()
	{
		if (config_enabled('enable_ajax_security'))
		{
			is_ajax(TRUE);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * initial measurement options
	 */
	public function init_measurements()
	{
		//get weight options first
		$a = $this->weight->get_weight_options();

		$this->config->set_item('weight_options', $a);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $start
	 */
	public function db_trans($start = 'trans_start')
	{
		if ($this->config->item('enable_innodb_transactions') == TRUE)
		{
			$this->db->$start();
		}

		if ($start == 'trans_complete')
		{
			if ($this->db->trans_status() === FALSE)
			{
				log_error('debug', 'transaction failure');
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $file
	 * @param string $type
	 *
	 * @return bool
	 */
	public function cache($file = '', $type = 'site')
	{
		//enable site wide database cache
		if (config_enabled('enable_database_cache'))
		{
			//this returns all the settings from the settings table
			if ($type == 'settings')
			{

				return $this->config->item('cache_' . $type . '_data') == TRUE ? $this->cache->get(sha1($file)) : FALSE;
			}

			//adds any query strings to the URL if there are any
			if ($this->input->get())
			{
				$file .= http_build_query($this->input->get(NULL, TRUE));
			}

			$type = 'cache_' . $type . '_data';

			return $this->config->item($type) == TRUE ? $this->cache->get(sha1($file)) : FALSE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $m
	 * @param string $key
	 * @param string $data
	 * @param int $type
	 */
	public function save_cache($m = '', $key = '', $data = '', $type = '')
	{
		if (config_enabled('enable_database_cache'))
		{
			if ($this->config->item('cache_' . $type . '_data') == FALSE)
			{
				return;
			}

			//check the disable cache array first to see if a specific item is not meant to be cached
			if ($this->check_disable_cache($m))
			{
				return;
			}

			$time = !$this->config->item($type . '_cache_limit') ? CACHE_TIME_LIMIT : $this->config->item($type . '_cache_limit');

			//adds any query strings to the URL if there are any
			if ($this->input->get())
			{
				$key .= http_build_query($this->input->get(NULL, TRUE));
			}

			$file = sha1($key); //set the cache file name from Class::method

			if ($type == 'db_query') //save the file to the database for indexing and purge on updates.
			{
				$c = explode('::', $m); //set to specific class if db_query

				$this->db->replace(TBL_CACHE, array('cache_type' => md5(strtolower($c[0])), 'cache_file' => $file));
			}

			$this->cache->save($file, $data, $time);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $file
	 * @param bool $all
	 */
	public function reset_cache($type = '', $file = '')
	{
		if (config_enabled('enable_database_cache'))
		{
			switch ($type)
			{
				case 'file': //delete a specific cache file
					$this->cache->delete(sha1($file));
					break;

				case 'module': //get all cache file entries from cache table
					$this->clear_module_cache($file);
					break;

				case 'all': //clean everything
					$this->db->truncate(TBL_CACHE);
					$this->cache->clean();
					break;
			}

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => lang('cache_reset') . ' ' . $type));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public function clear_module_cache($type = '')
	{
		foreach ($type as $t)
		{
			if (!$q = $this->db->where('cache_type', md5(strtolower($t)))->get(TBL_CACHE))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				foreach ($q->result_array() as $v)
				{
					$this->cache->delete($v['cache_file']);
				}
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 *  checks the config item for specific pages that
	 * should be disable from using the cache
	 *
	 * @param string $m
	 *
	 * @return bool
	 */
	public function check_disable_cache($m = '')
	{
		if (in_array($m, $this->config->item('admin', 'db_disable_cache_pages')))
		{
			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 */
	protected function init_site($type = '')
	{
		//check maintenance mode
		if ($type == 'site')
		{
			$this->check_maintenance_mode();
		}

		//get languages
		$this->init_languages($type);

		//set affiliate info
		$this->init_affiliate();

		//check if user is logged in
		$this->init->user_login();

		//init tax zones
		$this->init->tax_zones();

		//setup site links
		$this->init_urls();

		//get menus
		$this->init_menus($type);

		//initialize weight and measurements
		if ($type == 'cart')
		{
			$this->init_measurements();
		}

		//init custom templates
		if ($type != 'admin')
		{
			$this->get_db_templates();
		}

		//check for cart data
		$this->get_cart($type);
	}

	// ------------------------------------------------------------------------

	/**
	 * get database templates
	 */
	protected function get_db_templates()
	{
		if (!$templates = $this->init->cache('db_templates', 'settings'))
		{
			if (!$q = $this->db->get(TBL_PAGE_TEMPLATES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$templates = $q->result_array();

				// Save into the cache
				$this->init->save_cache('db_templates', 'db_templates', $templates, 'settings');
			}
		}

		if (!empty($templates))
		{
			$this->config->set_item('db_templates', $templates);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * enable or disable maintenance mode
	 */
	protected function check_maintenance_mode()
	{
		if (CONTROLLER_FUNCTION != 'offline')
		{
			if (config_enabled('sts_site_enable_offline_mode'))
			{
				if (!get_cookie(config_item('sess_adm_cookie_name')))
				{
					redirect('offline');
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 */
	protected function init_menus($type = '')
	{
		if (!is_ajax()) //no need for menus when calling ajax
		{
			switch ($type)
			{
				case 'cart':

					//get menu system
					$this->config->set_item('top_menu', $this->menus->get_menu('checkout_menu', FALSE, FALSE));

					break;

				default:

					//get menu system
					$this->config->set_item('top_menu', $this->menus->get_menu('top_menu', $this->config->item('member_logged_in'), FALSE));

					break;

			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * initialize languages
	 */
	protected function init_languages($type = 'site')
	{
		$lang = get_languages(TRUE, FALSE);

		foreach ($lang as $v)
		{
			if ($type == 'site')
			{
				$a = !sess('default_language') ? config_item('default_language') : sess('default_language');

				if ($v['name'] == $a)
				{
					$this->config->set_item('default_language', $v['name']);
					$this->config->set_item('default_lang_code', $v['code']);
					$this->config->set_item('default_lang_image', $v['image']);
				}
			}
			else
			{
				if ($v['language_id'] == config_item('sts_admin_default_language'))
				{
					$this->config->set_item('sts_admin_default_language_name', $v['name']);
					$this->config->set_item('sts_admin_default_language_code', $v['code']);
					$this->config->set_item('sts_admin_default_language_image', $v['image']);
				}
			}
		}

		$this->config->set_item('site_languages', $lang);
	}

	// ------------------------------------------------------------------------

	/**
	 * initialize affiliate marketing system
	 */
	protected function init_affiliate()
	{
		if (config_enabled('affiliate_marketing'))
		{
			//check if there is a tracking session already, if not check and set it
			if (!sess('tracking_data') || config_enabled('sts_affiliate_overwrite_existing_cookie'))
			{
				if ($aff_data = $this->aff->check_coupon_affiliate())
				{
					$this->session->set_userdata('tracking_data', $aff_data);
				}
				elseif ($aff_data = $this->aff->set_tracking_data())
				{
					$this->session->set_userdata('tracking_data', $aff_data);
				}
			}

			//set lifetime sponsor if set
			if (!sess('lifetime_tracking_data'))
			{
				if (sess('user_logged_in') && sess('sponsor_id'))
				{
					if ($aff_data = $this->aff->set_lifetime_sponsor(sess('sponsor_id')))
					{
						$this->session->set_userdata('lifetime_tracking_data', $aff_data);
					}
				}
			}

			//check if we're setting lifetime or not
			$a = !sess('lifetime_tracking_data') ? '' : 'lifetime_';

			//if there is affiliate data available, set it as part of the config array
			$this->config->set_item('affiliate_data', sess($a . 'tracking_data'));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * set system URLs
	 */
	protected function init_urls()
	{
		$a = $this->config->slash_item('base_url');
		$this->config->set_item('base_url', $a);

		if ($this->config->item('index_page'))
		{
			$a .= $this->config->slash_item('index_page');
		}
		$this->config->set_item('site_url', $a);

		//set default breadcrumbs
		$this->config->set_item('breadcrumb', set_breadcrumb());
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 */
	protected function get_cart($type = '')
	{
		switch ($type)
		{
			case 'cart':

				if (!sess('cart_details', 'items'))
				{
					redirect_page('cart');
				}

				$this->config->set_item('cart', $this->cart->get_cart());

				break;

			default:

				$this->config->set_item('cart', sess('cart_details'));

				break;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * load tax zones
	 */
	protected function tax_zones()
	{
		if ($this->config->item('sts_tax_enable_tax_calculations') == 1)
		{
			$this->zone->load_zones();
		}
	}

	// ------------------------------------------------------------------------

	/**
	 *  check login session
	 */
	protected function user_login()
	{
		//check if the user is logged in
		if ($this->sec->check_login_session('member', FALSE))
		{
			$this->config->set_item('member_logged_in', TRUE);
		}
	}
}

/* End of file Init_model.php */
/* Location: ./application/models/Init_model.php */