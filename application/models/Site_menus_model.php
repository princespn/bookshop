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
class Site_menus_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'menu_id';

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $link_id = 'menu_link_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $menu_id
	 * @param string $parent_id
	 * @param array $lang
	 * @return array
	 */
	public function add_link($menu_id = '', $parent_id = '', $lang = array())
	{

		$vars = array(
			'menu_id'          => $menu_id,
			'parent_id'        => $parent_id,
			'menu_link_status' => '0',
			'menu_link_type'   => $parent_id == '0' ? 'dropdown' : 'link',
			'menu_link'        => '{{site_url}}',
			'menu_options'     => '',
			'menu_sort_order'  => '1',
		);

		if (!$this->db->insert(TBL_SITE_MENUS_LINKS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add the names
		foreach ($lang as $k => $v)
		{
			$name_vars = array(
				'menu_link_id'   => $id,
				'menu_id'        => $menu_id,
				'language_id'    => $k,
				'menu_link_name' => lang('new_link'),
			);

			if (!$this->db->insert(TBL_SITE_MENUS_LINKS_NAME, $name_vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$row = array(
			'id'       => $id,
			'msg_text' => lang('record_created_successfully'),
			'success'  => TRUE,
			'row'      => $vars,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function create()
	{
		$row = $this->dbv->create(TBL_SITE_MENUS, array('menu_name' => lang('new_menu')));

		foreach (get_languages() as $k => $v)
		{
			$this->dbv->create(TBL_SITE_MENUS_NAME, array('menu_id'     => $row['id'],
			                                              'language_id' => $k));
		}

		return empty($row['success']) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function delete($id = '')
	{
		//update menus
		if (config_option('layout_design_top_menu') == $id)
		{
			$this->set->update_db_settings(array('layout_design_top_menu' => config_option('default_layout_menu')));
		}

		if (config_option('layout_design_top_menu_logged_in') == $id)
		{
			$this->set->update_db_settings(array('layout_design_top_menu_logged_in' => config_option('default_layout_menu')));
		}

		if (config_option('layout_design_checkout_menu') == $id)
		{
			$this->set->update_db_settings(array('layout_design_checkout_menu' => config_option('default_layout_menu')));
		}


		$row = $this->dbv->delete(TBL_SITE_MENUS, 'menu_id', $id);

		return empty($row['success']) ? FALSE : $row;

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function delete_link($id = '')
	{
		if (!$q = $this->db->where($this->link_id, $id)->get(TBL_SITE_MENUS_LINKS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = $q->row_array();

			//delete any links with parent id
			$row = $this->dbv->delete(TBL_SITE_MENUS_LINKS, 'parent_id', $id);

			//delete link
			$row = $this->dbv->delete(TBL_SITE_MENUS_LINKS, 'menu_link_id', $id);

			$this->update_menu_code($a['menu_id']);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param bool $logged_in
	 * @param bool $format
	 * @return bool|false|string
	 */
	public function get_menu($type = '', $logged_in = FALSE, $format = TRUE)
	{
		$m = !$logged_in ? 'layout_design_' . $type : 'layout_design_' . $type . '_logged_in';

		$lang_id = !sess('default_lang_id') ? sess('default_lang_id') : sess('default_lang_id');

		$sql = 'SELECT * 
				FROM ' . $this->db->dbprefix(TBL_SITE_MENUS) . ' p
		        LEFT JOIN ' . $this->db->dbprefix(TBL_SITE_MENUS_NAME) . ' n 
		            ON n.`language_id` = \'' . (int)$lang_id . '\' 
		            AND p.menu_id = n.menu_id
		        WHERE p.menu_id = \'' . config_option($m) . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = $q->row_array();
		}

		if (!empty($a['menu_code']))
		{
			$row = unserialize($a['menu_code']);
		}
		else
		{
			$row = $this->get_menu_links($this->config->item($m), '0', TRUE, $lang_id);

			if (!empty($row) && $format == TRUE)
			{
				$row = format_menu($row);
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return array|bool|false
	 */
	public function get_rows($form = FALSE)
	{
		if (!$q = $this->db->get(TBL_SITE_MENUS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $form == TRUE ? format_array($q->result_array(), 'menu_id', 'menu_name') : $q->result_array();
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool
	 */
	public function get_details($id = '', $lang_id = '')
	{
		$sql = 'SELECT *,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SITE_MENUS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SITE_MENUS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
				    FROM ' . $this->db->dbprefix(TBL_SITE_MENUS) . ' p
                    WHERE p.' . $this->id . '= ' . (int)$id . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
			$row['menu_links'] = $this->get_menu_links($id, '0', FALSE, $lang_id);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		foreach ($data as $k => $v)
		{
			$vars = array('menu_id'   => $k,
			              'menu_name' => $v);

			$this->dbv->update(TBL_SITE_MENUS, $this->id, $vars);
		}

		$row = array(
			'data'     => $vars,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_menu_sort_order($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (!$q = $this->db->where('menu_link_id', $v)
					->update(TBL_SITE_MENUS_LINKS, array('menu_sort_order' => $k))
				)
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update_links($id = '', $data = array())
	{
		if (!empty($data['menu']))
		{
			foreach ($data['menu'] as $k => $v)
			{
				$vars = $this->dbv->clean($v, TBL_SITE_MENUS_LINKS);

				$this->db->where($this->link_id, $k);

				if (!$this->db->update(TBL_SITE_MENUS_LINKS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		if (!empty($data['names']))
		{
			foreach ($data['names'] as $k => $v)
			{
				$this->db->where('link_name_id', $k);

				if (!$this->db->update(TBL_SITE_MENUS_LINKS_NAME, array('menu_link_name' => xss_clean($v))))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}
		}

		//update menu code
		$this->update_menu_code($id);

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';
		if (!empty($data['menu']))
		{
			foreach ($data['menu'] as $k => $v)
			{
				$this->form_validation->reset_validation();
				$this->form_validation->set_data($v);

				$required = $this->config->item(TBL_SITE_MENUS_LINKS, 'required_input_fields');

				//now get the list of fields directly from the table
				$fields = $this->db->field_data(TBL_SITE_MENUS_LINKS);

				foreach ($fields as $f)
				{
					//set the default rule
					$rule = 'trim|xss_clean';

					//if this field is a required field, let's set that
					if (is_array($required) && in_array($f->name, $required))
					{
						$rule .= '|required';
					}

					$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

					$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);

				}

				if (!$this->form_validation->run())
				{
					$error .= validation_errors();
				}
				else
				{
					$data['menu'][$k] = $this->dbv->validated($v, FALSE);
				}
			}
		}
		if (!empty($error))
		{
			//sorry! got some errors here....
			$row = array('error'    => TRUE,
			             'msg_text' => $error,
			);
		}
		else
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $data,
			);
		}

		return $row;
	}

	/**
	 * @param string $id
	 */
	protected function update_menu_code($id = '')
	{
		foreach (get_languages() as $k => $v)
		{
			$a = $this->get_menu_links($id, '0', TRUE, $k);

			$vars = array('menu_code' => serialize($a));

			$this->db->where('language_id', $k);

			$this->db->where('menu_id', $id);

			if (!$this->db->update(TBL_SITE_MENUS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

	}

	/**
	 * @param string $id
	 * @param string $parent_id
	 * @param bool $status
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	protected function get_menu_links($id = '', $parent_id = '0', $status = FALSE, $lang_id = '1')
	{
		$this->db->where($this->db->dbprefix(TBL_SITE_MENUS_LINKS) . '.' . $this->id, $id);
		$this->db->where($this->db->dbprefix(TBL_SITE_MENUS_LINKS) . '.parent_id', $parent_id);

		if ($status == TRUE)
		{
			$this->db->where($this->db->dbprefix(TBL_SITE_MENUS_LINKS) . '.menu_link_status', '1');
		}

		$this->db->order_by('menu_sort_order', 'ASC');

		if (!$q = $this->db->get(TBL_SITE_MENUS_LINKS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();

			foreach ($row as $k => $v)
			{
				$row[$k]['names'] = $this->get_menu_links_names($v['menu_link_id'], $lang_id);

				if ($v['menu_link_type'] == 'dropdown') //check sub menus
				{
					$row[$k]['sub_menu_links'] = $this->get_menu_links($id, $v['menu_link_id'], $status, $lang_id);
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool|false|string
	 */
	protected function get_menu_links_names($id = '', $lang_id = '')
	{
		$this->db->join(TBL_LANGUAGES,
			$this->db->dbprefix(TBL_LANGUAGES) . '.language_id = ' .
			$this->db->dbprefix(TBL_SITE_MENUS_LINKS_NAME) . '.language_id', 'left');

		$this->db->where($this->link_id, $id);
		if (!empty($lang_id))
		{
			$this->db->where($this->db->dbprefix(TBL_SITE_MENUS_LINKS_NAME) . '.language_id', $lang_id);
		}
		if (!$q = $this->db->get(TBL_SITE_MENUS_LINKS_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = !empty($lang_id) ? $q->row_array() : $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Site_menus_model.php */
/* Location: ./application/controllers/admin/Site_menus_model.php */