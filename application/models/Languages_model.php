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
class Languages_model extends CI_Model
{

	/**
	 * @var string
	 */
	protected $id = 'language_id';

	// ------------------------------------------------------------------------

	/**
	 * get the language files in array
	 *
	 *
	 * @param string $lang
	 * @return array|string
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('language');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function create($data = array())
	{
		$row = $this->dbv->create(TBL_LANGUAGES, $data);

		$tables = $this->config->item('name_tables');

		foreach ($tables as $t)
		{
			$table = $t . '_name';

			$fields = $this->db->list_fields($table);

			if (!$q = $this->db->where($this->id, config_item('sts_site_default_language'))->get($table))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				foreach ($q->result_array() as $v)
				{
					$primary = $this->config->item('name_ids');

					foreach ($fields as $f)
					{
						if (in_array($f, $primary))
						{
							$id = $v[$f];
							unset($v[$f]);
						}
					}

					$v['language_id'] = $row['id'];

					if ($table == 'products_to_specifications_name')
					{
						$this->db->where('language_id', $v['language_id']);
						$a = $this->db->where('spec_id', $v['spec_id'])->get(TBL_PRODUCTS_SPECIFICATIONS_NAME);

						if ($a->num_rows() > 0)
						{
							$b = $a->row_array();
							$v['spec_name_id'] = $b['spec_name_id'];
						}
					}

					if (!$q = $this->db->insert($table, $v))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function create_key($str = '', $lang_id = '1')
	{
		if (!$q = $this->db->where('key', url_title($str))->get(TBL_LANGUAGE_ENTRIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//add/update entry key
		if ($q->num_rows() < 1)
		{
			$vars = array('language_id' => $lang_id,
			              'type'        => 'custom',
			              'key'         => $str);

			$row = $this->dbv->create(TBL_LANGUAGE_ENTRIES, $vars);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $status
	 * @return bool|false|string
	 */
	public function get_languages($status = FALSE)
	{
		$cache = __METHOD__;
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if ($status == TRUE)
			{
				$this->db->where('status', '1');
			}

			if (!$q = $this->db->get(TBL_LANGUAGES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @return array
	 */
	public function get_rows()
	{
		$f = $this->get_folders();
		$lang = array();
		foreach ($f as $v)
		{
			$lang[$v]['name'] = $v;
			if (!$q = $this->db->where('name', trim($v))->get(TBL_LANGUAGES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$lang[$v]['values'] = $q->row_array();
			}
		}

		return $lang;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $path
	 * @return array
	 */
	public function get_folders($path = './application/language/')
	{
		$a = array();
		$map = directory_map($path, 1);

		foreach ($map as $v)
		{
			if ($v == 'index.html')
			{
				continue;
			}
			$v = substr($v, 0, -1);

			array_push($a, $v);
		}

		sort($a);

		return $a;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $lang
	 * @return array|string
	 */
	public function get_files($lang = '')
	{
		$map = directory_map(APPPATH . 'language/' . $lang);

		$lang = array();

		foreach ($map as $v)
		{
			if ($v != 'index.html')
			{
				array_push($lang, $v);
			}
		}
		asort($lang);

		return $lang;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	public function load_custom_entries($lang_id = '')
	{
		if (!$row = $this->init->cache('custom_lang_entries', 'settings'))
		{
			$row = $this->get_custom_entries($lang_id, FALSE);

			if (!empty($row))
			{
				// Save into the cache
				$this->init->save_cache(__METHOD__, 'custom_lang_entries', $row, 'settings');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $lang_id
	 * @param bool $form
	 * @param string $type
	 * @return bool|false|string
	 */
	public function get_custom_entries($lang_id = '', $form = FALSE, $type = '')
	{
		$this->db->join(TBL_LANGUAGE_ENTRIES_NAME,
			$this->db->dbprefix(TBL_LANGUAGE_ENTRIES_NAME) . '.entry_id = ' .
			$this->db->dbprefix(TBL_LANGUAGE_ENTRIES) . '.entry_id', 'left');

		if (!empty($type))
		{
			$this->db->where('type', $type);
		}

		$this->db->where($this->db->dbprefix(TBL_LANGUAGE_ENTRIES . '.language_id'), $lang_id);

		if (!$q = $this->db->get(TBL_LANGUAGE_ENTRIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();

			if ($form == TRUE)
			{
				$row = format_array($row, 'key', 'value');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $language
	 * @param string $file
	 * @return array
	 */
	public function get_language_entries($language = '', $file = '')
	{
		@include(APPPATH . 'language/' . $language . '/' . $file);

		if (!empty($lang) && is_array($lang))
		{
			asort($lang);

			return $lang;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @return false|string
	 */
	public function reset_db()
	{
		$this->db->where('type', 'system');
		if (!$q = $this->db->delete(TBL_LANGUAGE_ENTRIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $language
	 * @param string $str
	 * @return bool|false|string
	 */
	public function search($language = '', $str = '')
	{
		$row = array();

		$files = $this->get_files($language);

		foreach ($files as $v)
		{
			$entries = $this->get_language_entries($language, $v);

			foreach ($entries as $a => $b)
			{
				if (strpos(strtolower($b), strtolower($str)) !== FALSE)
				{
					$row[$a] = $b;
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return false|string
	 */
	public function mass_update($data = array(), $type = '')
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				$v[$this->id] = $k;

				if (!$this->dbv->update(TBL_LANGUAGES, $this->id, $v))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function map_custom_entries($id = '')
	{
		$lang = $this->get_languages();

		if (!$q = $this->db->get(TBL_LANGUAGE_ENTRIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//check if each entry has one for the language file
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $b)
			{
				//check language files
				foreach ($lang as $v)
				{
					$this->db->where('language_id', $v['language_id']);
					$c = $this->db->where('key', $b['key'])->get(TBL_LANGUAGE_ENTRIES);

					if ($c->num_rows() > 0)
					{
						continue;
					}
					else
					{
						//insert entry
						$vars = array('language_id' => $v['language_id'],
						              'type'        => 'custom',
						              'key'         => $b['key']);

						$row = $this->dbv->create(TBL_LANGUAGE_ENTRIES, $vars);
					}
				}
			}
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $language
	 * @param string $file
	 * @return array
	 */
	public function update_entries($data = array(), $language = '', $file = '')
	{
		if (!empty($file))
		{
			$this->lang->load($file, $language);
		}

		if (!empty($data['lang']))
		{
			foreach ($data['lang'] as $k => $v)
			{
				//check if the current entry is the same as the one in the language file first
				if ($v == lang($k))
				{
					//now delete the entry if its the same as the one in the language file
					$this->db->where('language_id', (int)($data['id']));
					if (!$this->db->where('key', xss_clean($k))->delete(TBL_LANGUAGE_ENTRIES))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
					continue;
				}

				$this->db->where('language_id', (int)($data['id']));
				if (!$q = $this->db->where('key', xss_clean($k))->get(TBL_LANGUAGE_ENTRIES))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				//add/update entry key
				if ($q->num_rows() > 0)
				{
					$vars = $q->row_array();
				}
				else
				{
					//insert key
					$vars = array('language_id' => (int)$data['id'],
					              'type'        => $data['file'] != 'custom' ? 'system' : 'custom',
					              'key'         => xss_clean($k));

					$row = $this->dbv->create(TBL_LANGUAGE_ENTRIES, $vars);
					$vars['entry_id'] = $row['id'];
				}

				//now add/update the entry value
				$this->db->where('language_id', (int)($data['id']));
				if (!$q = $this->db->where('entry_id', (int)$vars['entry_id'])->get(TBL_LANGUAGE_ENTRIES_NAME))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				//set the value
				$a = array('value'       => $v,
				           'entry_id'    => $vars['entry_id'],
				           'language_id' => (int)$data['id']);

				//add/update entry key
				if ($q->num_rows() > 0)
				{
					$this->db->where('entry_id', (int)$vars['entry_id']);
					$this->db->where('language_id', (int)($data['id']));
					if (!$this->db->update(TBL_LANGUAGE_ENTRIES_NAME, $a))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
				else
				{
					//insert a new entry
					$row = $this->dbv->create(TBL_LANGUAGE_ENTRIES_NAME, $a);
				}
			}
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}
}


/* End of file Languages_model.php */
/* Location: ./application/models/Languages_model.php */