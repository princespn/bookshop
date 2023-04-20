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
class Products_downloads_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'download_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $code
	 * @return bool|false|string
	 */
	public function check_download($code = '')
	{
		$sql = 'SELECT * 
				FROM ' . $this->db->dbprefix(TBL_MEMBERS_DOWNLOADS) . '
				WHERE code = \'' . $code . '\'';

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			$this->dbv->update_count(array('table' => TBL_MEMBERS_DOWNLOADS,
			                               'key'   => 'd_id',
			                               'id'    => $row['d_id'],
			                               'field' => 'downloads'));

			return sc($row);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = array('file_name' => 'file.zip');

		if (!$q = $this->db->insert(TBL_PRODUCTS_DOWNLOADS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars['download_id'] = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data as $v)
		{
			$vars = array(
				$this->id       => $vars['download_id'],
				'language_id'   => $v['language_id'],
				'download_name' => lang('new_download'),
				'description'   => lang('new_download'),
			);

			if (!$q = $this->db->insert(TBL_PRODUCTS_DOWNLOADS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array(
			'id'       => $vars['download_id'],
			'success'  => TRUE,
			'data'     => $vars,
			'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_details($id = '')
	{
		if ($row = $this->dbv->get_record(TBL_PRODUCTS_DOWNLOADS, $this->id, $id))
		{
			//get names
			$row['lang'] = $this->dbv->get_names(TBL_PRODUCTS_DOWNLOADS_NAME, $this->id, $id);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param int $limit
	 * @return bool|string
	 */
	public function get_user_downloads($id = '', $lang_id = 1, $limit = MEMBER_RECORD_LIMIT)
	{
		$sort = $this->config->item(TBL_MEMBERS_DOWNLOADS, 'db_sort_order');

		//set the cache file
		$cache = __METHOD__ . $id . $limit;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT p.*, c.*, d.max_downloads_user
					FROM ' . $this->db->dbprefix(TBL_MEMBERS_DOWNLOADS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' d ON p.product_id = d.product_id
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' c ON p.product_id = c.product_id
					     AND c.language_id = ' . (int)$lang_id . '
					    WHERE member_id = \'' . (int)$id . '\'
					    ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
					    LIMIT ' . $limit;

			//run the query
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'rows'    => $q->result_array(),
					'success' => TRUE,
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get download
	 *
	 * Get the details for the downloadable file
	 *
	 * @param string $id
	 * @param string $member_id
	 * @return bool|string
	 */
	public function get_download_details($id = '', $public = FALSE)
	{
		$sql = 'SELECT *
					FROM ' . $this->db->dbprefix(TBL_MEMBERS_DOWNLOADS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS) . ' b ON b.product_id = p.product_id
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_TO_DOWNLOADS) . ' c ON p.product_id = c.product_id
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS) . ' d ON c.download_id = d.download_id
					    WHERE d_id = \'' . (int)$id . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND expires > CURDATE()';
		}

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = $q->row_array();

			//check for max download attempts
			if (!empty($a['max_downloads_user']))
			{
				if ($a['downloads'] >= config_option('sts_site_days_download_expires'))
				{
					return FALSE;
				}
			}

			$row = array(
				'row'     => $a = $q->row_array(),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);

	}

	// ------------------------------------------------------------------------

	/**
	 * Update download limits
	 *
	 * Update the amount of downloads generated
	 * by the member
	 *
	 * @param string $id
	 * @return bool
	 */
	function update_limits($id = '')
	{
		$sql = 'UPDATE  ' . $this->db->dbprefix(TBL_MEMBERS_DOWNLOADS) . '
                SET downloads = downloads + 1
                WHERE d_id = \'' . (int)$id . '\'';

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return array|bool
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_PRODUCTS_DOWNLOADS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS_NAME) . ' c
				    ON (p.' . $this->id . ' = c.' . $this->id . '
				    AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_PRODUCTS_DOWNLOADS), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$query = $this->db->query($sql);


		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'  => $query->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_PRODUCTS_DOWNLOADS),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function ajax_search($term = '', $lang_id = 1)
	{
		$this->db->like('download_name', $term);
		$this->db->where('language_id', $lang_id);
		$this->db->select('download_id, download_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_PRODUCTS_DOWNLOADS_NAME))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @param string $lang_id
	 * @return array|bool|mixed
	 */
	public function generate_user_downloads($id = '', $data = array(), $lang_id = '1')
	{
		//add downloads for user access
		$data['downloads'] = array();

		if ($files = $this->get_product_downloads($id, $lang_id))
		{
			foreach ($files as $v)
			{
				$d = $this->add_user_download($v, $data);

				$d['download_name'] = $v['download_name'];

				array_push($data['downloads'], $d);
			}

			//send out emails
			$this->mail->send_download_access_emails($data, $lang_id, $data['primary_email']);
		}

		return !empty($data['downloads']) ? $data['downloads'] : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $file
	 * @param string $data
	 * @return array
	 */
	public function add_user_download($file = '', $data = '')
	{
		$vars = format_add_user_download($file, $data);

		if (!$this->db->insert(TBL_MEMBERS_DOWNLOADS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $vars;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @return bool
	 */
	public function get_product_downloads($id = '', $lang_id = '1')
	{
		$sql = 'SELECT *
                  FROM ' . $this->db->dbprefix(TBL_PRODUCTS_TO_DOWNLOADS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS) . ' s
                        ON p.download_id = s.download_id
                    LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_DOWNLOADS_NAME) . ' n
                        ON p.download_id = s.download_id
                        AND language_id = \'' . (int)$lang_id . '\'
                  WHERE p.product_id = \'' . (int)$id . '\'
                    GROUP BY p.download_id';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_PRODUCTS_DOWNLOADS);

		$this->db->where($this->id, $data[$this->id]);

		if (!$this->db->update(TBL_PRODUCTS_DOWNLOADS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_PRODUCTS_DOWNLOADS_NAME);

			$this->db->where($this->id, $data[$this->id]);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_PRODUCTS_DOWNLOADS_NAME, $vars))
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
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);
			$this->form_validation->set_rules('description', 'lang:description', 'trim|strip_tags|xss_clean',
				array('required' => $v['language'] . ' ' . lang('description')));
			$this->form_validation->set_rules('download_name', 'lang:download_name', 'trim|required|strip_tags|xss_clean',
				array('required' => $v['language'] . ' ' . lang('download_name_required')));

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}
			else
			{
				$data['lang'][$k] = $this->dbv->validated($v);
			}
		}

		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('file_name', 'lang:file_name', 'trim|required|strip_tags|xss_clean');

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
			             'data'    => $data,
			);
		}

		return $row;
	}
}

/* End of file Products_downloads_model.php */
/* Location: ./application/models/Products_downloads_model.php */