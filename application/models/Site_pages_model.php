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
class Site_pages_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'page_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool
	 */
	public function check_url($str = '')
	{
		$str = empty($str) ? $this->input->post('url') : $str;

		if (!empty($str))
		{
			$this->db->where('url', $str);

			if ($this->input->post($this->id))
			{
				$this->db->where($this->id . ' !=', (int)$this->input->post($this->id));
			}

			if (!$q = $this->db->get(TBL_SITE_PAGES))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_SITE_PAGES);

		//finally... generate a unique URL if empty
		if (empty($vars['url']))
		{
			$vars['url'] = $this->generate_post_url($data['title']);
		}

		if (!$q = $this->db->insert(TBL_SITE_PAGES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data['lang'] as $k => $v)
		{
			$vars = array(
				$this->id          => $id,
				'language_id'      => $k,
				'page_content'     => empty($v['page_content']) ? $data['lang'][config_item('sts_site_default_language')]['page_content'] : $v['page_content'],
				'meta_title'       => empty($v['meta_title']) ? $data['lang'][config_item('sts_site_default_language')]['meta_title'] : $v['meta_title'],
				'meta_description' => empty($v['meta_description']) ? $data['lang'][config_item('sts_site_default_language')]['meta_description'] : $v['meta_description'],
				'meta_keywords'    => empty($v['meta_keywords']) ? $data['lang'][config_item('sts_site_default_language')]['meta_keywords'] : $v['meta_keywords'],
			);

			if (!$q = $this->db->insert(TBL_SITE_PAGES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'data'     => $data,
		                'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create_site_builder_page($data = array())
	{
		//create a default site page first
		$title = empty($data['title']) ? lang('content_builder_page') : $data['title'];
		$row = $this->dbv->create(TBL_SITE_PAGES, array('type'  => 'builder',
		                                                'title' => $title,
		                                                'url'   => $this->generate_post_url($title))
		);
		foreach (get_languages() as $k => $v)
		{
			//check for a custom home page
			$tpl = APPPATH . 'views/site/site_pages/default_site_builder.tpl';
			if (file_exists(PUBPATH . '/themes/site/' . config_item('layout_design_site_theme') . '/custom_templates/site_pages/default_site_builder.tpl' ))
			{
				$tpl = PUBPATH . '/themes/site/' . config_item('layout_design_site_theme') . '/custom_templates/site_pages/default_site_builder.tpl';
			}

			$t = file_get_contents($tpl);

			$t = $this->show->parse_tpl($this->config->config, $t);
			$this->dbv->create(TBL_SITE_PAGES_NAME, array('page_id'          => $row['id'],
			                                              'language_id'      => $k,
			                                              'page_content'     => $t,
			                                              'meta_title'       => $title,
			                                              'meta_keywords'    => $title,
			                                              'meta_description' => is_var($data, 'meta_description'))
			);
		}


		return sc(array('id'       => $row['id'],
		                'success'  => TRUE,
		                'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_SITE_PAGES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_SITE_PAGES_NAME) . ' c ON (p.' . $this->id . ' = c.' . $this->id . '
				AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_SITE_PAGES, TBL_SITE_PAGES_NAME), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'  => $query->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_SITE_PAGES),
				'success' => TRUE,
			);
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @param string $id
	 * @return string
	 */
	public function generate_post_url($str = '', $id = '')
	{
		$str = url_title(filter_stop_words(strtolower($str)));
		$url = empty($str) ? random_string('alpha', 8) : $str;

		while ($this->check_unique($url, $id))
		{
			$url .= '-' . rand(100, 100000);
		}

		return $url;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param bool $public
	 * @param string $col
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = 1, $public = FALSE, $col = 'page_id')
	{
		$sql = 'SELECT p.*, c.* ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next';
		}

		$sql .= '  FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_SITE_PAGES_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\')
                    WHERE p.' . $col . ' = \'' . valid_id($id, TRUE) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND p.status = \'1\'';
		}

		$cache = __METHOD__ . md5($sql);
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';
		if (!$row = $this->init->cache($cache, $cache_type))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				if ($public == FALSE)
				{
					$row['lang'] = $this->dbv->get_names(TBL_SITE_PAGES_NAME, $this->id, $id);
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data['page']))
		{
			foreach ($data['page'] as $k => $v)
			{
				if ($data['change-status'] == 'delete')
				{
					if (!empty($v['update']))
					{
						if ($k > 1)
						{
							$this->dbv->delete(TBL_SITE_PAGES, $this->id, $k);
						}
					}
				}
				else
				{
					$v[$this->id] = $k;
					$v['url'] = $this->generate_post_url($v['url'], $k);

					$vars = $this->dbv->clean($v, TBL_SITE_PAGES);

					if (!empty($v['update']))
					{
						switch ($data['change-status'])
						{
							case '1':
							case '0':

								$vars['status'] = $data['change-status'];

								break;
						}
					}

					$this->dbv->update(TBL_SITE_PAGES, $this->id, $vars);

					//update name
					$vars = $this->dbv->clean($v, TBL_SITE_PAGES_NAME);

					$this->db->where('language_id', sess('default_lang_id'));
					if (!$this->db->where($this->id, $v[$this->id])->update(TBL_SITE_PAGES_NAME, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}

			$row = array('success'  => TRUE,
			             'data'     => $data,
			             'msg_text' => lang('mass_update_successful'),
			);
		}

		//order the tier groups numerically
		$this->dbv->db_sort_order(TBL_SITE_PAGES, 'page_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function reset_site_builder($id = '')
	{
		$row = $this->get_details($id, sess('default_lang_id'));

		foreach ($row['lang'] as $k => $v)
		{
			$t = file_get_contents(APPPATH . 'views/site/site_pages/default_site_builder.tpl');
			$t = $this->show->parse_tpl($this->config->config, $t);
			if (!$this->db->where('name_page_id', $v['name_page_id'])->update(TBL_SITE_PAGES_NAME, array('page_content' => $t)))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

		}

		return sc(array('id'       => $id,
		                'success'  => TRUE,
		                'msg_text' => 'system_updated_successfully',
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function search($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_SITE_PAGES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$count = 'SELECT COUNT(*) AS total
					FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_SITE_PAGES_NAME) . ' c 
						ON (p.' . $this->id . ' = c.' . $this->id . '
					AND c.language_id = \'' . $lang_id . '\')';

		$sql = 'SELECT * 
					FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_SITE_PAGES_NAME) . ' c 
						ON (p.' . $this->id . ' = c.' . $this->id . '
					AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = $this->db->list_fields(TBL_SITE_PAGES);

				$i = 1;
				foreach ($columns as $f)
				{
					if ($i == 1)
					{
						$sql .= ' WHERE p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= ' WHERE p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}
					else
					{
						$sql .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
						$count .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}

					$i++;
				}

				$columns = $this->db->list_fields(TBL_SITE_PAGES_NAME);

				foreach ($columns as $f)
				{
					$sql .= ' OR c.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					$count .= ' OR c.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
				}
			}
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$query = $this->db->query($sql);


		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'  => $query->result_array(),
				'total'   => $this->dbv->get_query_total($count),
				'success' => TRUE,
			);
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_SITE_PAGES);

		if (!$q = $this->db->where($this->id, $data[$this->id])->update(TBL_SITE_PAGES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!empty($data['lang']))
		{
			foreach ($data['lang'] as $k => $v)
			{
				$v = $this->dbv->clean($v, TBL_SITE_PAGES_NAME);

				$vars = array(
					'page_content'     => empty($v['page_content']) ? $data['lang'][config_item('sts_site_default_language')]['page_content'] : $v['page_content'],
					'meta_title'       => empty($v['meta_title']) ? $data['lang'][config_item('sts_site_default_language')]['meta_title'] : $v['meta_title'],
					'meta_description' => empty($v['meta_description']) ? $data['lang'][config_item('sts_site_default_language')]['meta_description'] : $v['meta_description'],
					'meta_keywords'    => empty($v['meta_keywords']) ? $data['lang'][config_item('sts_site_default_language')]['meta_keywords'] : $v['meta_keywords'],
				);

				$this->db->where($this->id, $data['page_id']);
				$this->db->where('language_id', $k);

				if (!$this->db->update(TBL_SITE_PAGES_NAME, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
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

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return false|string
	 */
	public function update_site_builder($id = '', $data = array())
	{
		$data['meta_data'] = '';

		if (!empty($data['main_css']))
		{
			$data['meta_data'] .= $data['main_css'];
		}

		if (!empty($data['section_css']))
		{
			$data['meta_data'] .= $data['section_css'];
		}

		$vars = $this->dbv->clean($data, TBL_SITE_PAGES);

		$this->db->where($this->id, $data['page_id']);

		if (!$this->db->update(TBL_SITE_PAGES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars = $this->dbv->clean($data, TBL_SITE_PAGES_NAME);
		$vars['page_content'] = $data['page_content'];

		$this->db->where($this->id, $data['page_id']);

		if (!$this->db->update(TBL_SITE_PAGES_NAME, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('id'       => $id,
		                'success'  => TRUE,
		                'msg_text' => lang('system_updated_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		if (!empty($data['lang']))
		{
			foreach ($data['lang'] as $k => $v)
			{
				$this->form_validation->reset_validation();
				$this->form_validation->set_data($v);

				$required = $this->config->item('site_pages', 'required_input_fields');

				//now get the list of fields directly from the table
				$fields = $this->db->field_data(TBL_SITE_PAGES_NAME);

				foreach ($fields as $f)
				{
					//set the default rule
					$rule = 'trim|xss_clean';

					if ($k == config_item('sts_site_default_language'))
					{
						//if this field is a required field, let's set that
						if (is_array($required) && in_array($f->name, $required))
						{
							$rule .= '|required';
						}
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
					$data['lang'][$k] = $this->dbv->validated($v, FALSE);
				}
			}
		}

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('title', 'lang:title', 'trim|required|xss_clean|strip_tags');

		if (CONTROLLER_FUNCTION == 'create')
		{
			$this->form_validation->set_rules(
				'url', 'lang:url',
				'trim|strtolower|url_title|is_unique[' . TBL_SITE_PAGES . '.url]',
				array(
					'is_unique' => '%s ' . lang('already_in_use'),
				)
			);
		}
		else
		{
			$this->form_validation->set_rules(
				'url', 'lang:url',
				array(
					'trim', 'required', 'strtolower', 'url_title',
					array('check_url', array($this->page, 'check_url')),
				)
			);

			$this->form_validation->set_message('check_url', '%s ' . lang('already_in_use'));
		}

		if (!$this->form_validation->run())
		{
			$error .= validation_errors();
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
			             'data'    => $this->dbv->validated($data),
			);
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $url
	 * @param string $id
	 * @return bool
	 */
	private function check_unique($url, $id = '')
	{
		if (!empty($id))
		{
			$this->db->where('page_id !=', $id);
		}

		if (!$q = $this->db->where('url', $url)->get(TBL_SITE_PAGES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? TRUE : FALSE;
	}
}

/* End of file Site_pages_model.php */
/* Location: ./application/models/Site_pages_model.php */