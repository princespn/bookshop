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
class System_pages_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'page_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param bool $public
	 * @param string $col
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = '1', $public = FALSE, $col = 'page_id')
	{
		$sql = 'SELECT p.*, c.* ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SYSTEM_PAGES) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SYSTEM_PAGES) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next';
		}

		$sql .= '  FROM ' . $this->db->dbprefix(TBL_SYSTEM_PAGES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_SYSTEM_PAGES_NAME) . ' c
                        ON (p.' . $this->id . ' = c.' . $this->id . '
                        AND c.language_id = \'' . $lang_id . '\')
                    WHERE p.' . $col . ' = \'' . valid_id($id, TRUE) . '\'';


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
					$row['lang'] = $this->dbv->get_names(TBL_SYSTEM_PAGES_NAME, $this->id, $id);
				}
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, $cache_type);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_SYSTEM_PAGES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_SYSTEM_PAGES) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_SYSTEM_PAGES_NAME) . ' c ON (p.' . $this->id . ' = c.' . $this->id . '
				AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_SYSTEM_PAGES, TBL_SYSTEM_PAGES_NAME), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$query = $this->db->query($sql);
		

		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'         => $query->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_SYSTEM_PAGES),
				'success'        => TRUE,
			);

			return sc($row);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_SYSTEM_PAGES);

		if (!$q = $this->db->where($this->id, $data[ $this->id ])->update(TBL_SYSTEM_PAGES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data['lang'] as $k => $v)
		{
			$v = $this->dbv->clean($v, TBL_SYSTEM_PAGES_NAME);

			$vars = array(
				'page_content'     => empty($v['page_content']) ? $data['lang'][ config_item('sts_site_default_language') ]['page_content'] : $v['page_content'],
				'title'       => empty($v['title']) ? $data['lang'][ config_item('sts_site_default_language') ]['title'] : $v['title'],
			);

			$this->db->where($this->id, $data['page_id']);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_SYSTEM_PAGES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
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
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		$error = '';

		foreach ($data['lang'] as $k => $v)
		{
			$a = $this->dbv->validate(TBL_SYSTEM_PAGES_NAME, 'system_pages', $v, FALSE);

			if (!empty($a['success']))
			{
				$data['lang'][ $k ] = $a['data'];
			}
			else
			{
				$row['error'] = TRUE;
				$row['msg_text'] .= $a['msg_text'];
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
}

/* End of file System_pages_model.php */
/* Location: ./application/models/System_pages_model.php */