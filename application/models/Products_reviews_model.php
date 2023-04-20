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
class Products_reviews_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'id';

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_PRODUCTS_REVIEWS);

		//set date
		if (empty($data['date']))
		{
			$data['date'] = get_time(now(), TRUE);
		}

		if (!$q = $this->db->insert(TBL_PRODUCTS_REVIEWS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data['id'] = $this->db->insert_id();

		$this->update_product_ratings($data['product_id']);

		return sc(array('id'       => $data['id'],
		                'success'  => TRUE,
		                'data'     => $data,
		                'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete($id = '')
	{
		if ($q = $this->db->where($this->id, $id)->get(TBL_PRODUCTS_REVIEWS))
		{
			if (!$this->db->where($this->id, $id)->delete(TBL_PRODUCTS_REVIEWS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$row = $q->row_array();
			$this->update_product_ratings($row['product_id']);

			return sc(array('success'  => TRUE,
			                'id'       => $id,
			                'msg_text' => lang('record_deleted_successfully')));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $lang_id
	 * @param bool $public
	 * @return bool
	 */
	public function get_details($id = '', $lang_id = '1', $public = FALSE)
	{
		$sql = 'SELECT p.*,
						m.product_name,
						n.fname,
						n.lname,
						n.username,
						f.profile_photo,
						CONCAT(n.fname, \' \', n.lname, \' - \', n.primary_email) AS name,
					 DATE_FORMAT(p.date,\'' . $this->config->item('sql_date_format') . '\')
                        AS date_formatted';

		if ($public == FALSE)
		{
			$sql .= ',	(SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' p
	                        WHERE p.' . $this->id . ' < ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                        AS prev,
                        (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' p
	                        WHERE p.' . $this->id . ' > ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                        AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' m
			            ON m.product_id = p.product_id
			            AND m.language_id = \'' . $lang_id . '\'
		            LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' n
			            ON (n.member_id = p.member_id)
    	             LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' f
			            ON (f.member_id = p.member_id)
                    WHERE ' . $this->id . ' = \'' . (int)$id . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND status = \'1\'';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	public function get_user_reviews($id = '', $lang_id = '1', $limit = MEMBER_RECORD_LIMIT, $get_cache = TRUE)
	{
		$sort = $this->config->item(TBL_PRODUCTS_REVIEWS, 'db_sort_order');

		$sql = 'SELECT *, r.member_id AS member_id,
                    DATE_FORMAT(date,\'' . $this->config->item('sql_date_format') . '\')
                        AS formatted_date,
                    (SELECT p.product_name
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' p
                        WHERE r.product_id = p.product_id
                        AND language_id = ' . $lang_id . ')
                            AS product_name
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' m
                        ON (m.member_id = r.member_id)';

		$sql .= ' 
			       WHERE r.member_id = \'' . $id . '\'
                   ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
					        LIMIT ' . $limit;

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'  => $q->result_array(),
					'success' => TRUE,
				);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}


	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $product_id
	 * @param bool $public
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $product_id = '', $public = FALSE, $lang_id = 1)
	{
		$sort = $this->config->item(TBL_PRODUCTS_REVIEWS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *, r.member_id AS member_id,
                    DATE_FORMAT(date,\'' . $this->config->item('sql_date_format') . '\')
                        AS formatted_date,
                    (SELECT CONCAT(fname, \'|\', lname)
                        FROM ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        WHERE r.member_id = m.member_id)
                            AS username,
                    (SELECT p.product_name
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' p
                        WHERE r.product_id = p.product_id
                        AND language_id = ' . (int)$lang_id . ')
                            AS product_name
                    FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' m
                        ON (m.member_id = r.member_id)';

		if (!empty($product_id))
		{
			$options['product_id'] = $product_id;
			$sql .= ' WHERE r.product_id = \'' . $product_id . '\'';
		}

		if ($public == TRUE)
		{
			$options['public'] = $public;
			$sql .= empty($product_id) ? ' WHERE' : ' AND';
			$sql .= ' r.status = \'1\'';
		}

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_PRODUCTS_REVIEWS), $options['query']);

			$sql .= $options['and_string'];
		}

		$sql .= ' GROUP BY r.id
                    ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'  => $q->result_array(),
					'total'   => $this->get_table_totals($options),
					'success' => TRUE,
				);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return bool
	 */
	public function get_table_totals($options = '')
	{
		$sql = 'SELECT COUNT(*) AS total
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' m ON (m.member_id = r.member_id)';

		if (!empty($options['product_id']))
		{
			$sql .= ' WHERE r.product_id = \'' . $options['product_id'] . '\'';
		}

		if (!empty($options['public']))
		{
			$sql .= empty($options['product_id']) ? ' WHERE' : ' AND';
			$sql .= ' r.status = \'1\'';
		}

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_PRODUCTS_REVIEWS), $options['query']);

			$sql .= $options['and_string'];
		}

		if (!$query = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($query->num_rows() > 0)
		{
			$q = $query->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data['reviews']))
		{
			foreach ($data['reviews'] as $k => $v)
			{
				if (isset($v['id']))
				{
					if ($data['change-status'] == 'delete')
					{
						$this->delete($k);
					}
					else
					{
						$vars = array('sort_order' => $v['sort_order']);

						$vars['status'] = $data['change-status'];

						if (!$this->db->where($this->id, $k)->update(TBL_PRODUCTS_REVIEWS, $vars))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}

						$this->update_product_ratings($v['id']);
					}
				}
			}

			$row = array('success'  => TRUE,
			             'data'     => $data,
			             'msg_text' => lang('mass_update_successful'),
			);
		}

		//order the tier groups numerically
		$this->dbv->db_sort_order(TBL_BRANDS, 'brand_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$q = $this->db->where($this->id, $data[$this->id])->get(TBL_PRODUCTS_REVIEWS);
		$row = $q->row_array();

		$this->db->where('product_id', $row['product_id'])->update(TBL_PRODUCTS, array('ratings' => '0'));

		$data = $this->dbv->clean($data, TBL_PRODUCTS_REVIEWS);

		if (!$q = $this->db->where($this->id, valid_id($data[$this->id]))->update(TBL_PRODUCTS_REVIEWS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$this->update_product_ratings($data['product_id']);

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
	 * @return bool
	 */
	public function update_product_ratings($id = '')
	{
		$sql = 'SELECT AVG(ratings) AS ratings
                        FROM ' . $this->db->dbprefix(TBL_PRODUCTS_REVIEWS) . ' r
						    WHERE r.product_id = \'' . (int)$id . '\'
						    AND r.status = \'1\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$t = $q->row_array();
		}

		$t = !empty($t['ratings']) ? round($t['ratings'], 1) : '0';
		$this->db->where('product_id', (int)$id)->update(TBL_PRODUCTS, array('ratings' => $t));

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return bool|false|string
	 */
	public function validate($data = array(), $type = 'admin')
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('product_reviews', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_PRODUCTS_REVIEWS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|strip_tags|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			//check if this is an admin or member response
			switch ($f)
			{
				case 'status':

					if ($type == 'admin')
					{
						$rule .= '|required';
					}

					break;

				case 'product_id':
				case 'member_id':
				case 'sort_order':

					$rule .= '|integer';

					break;

				case 'ratings':

					$rule .= '|numeric';

					break;

				case 'date':

					if ($type == 'admin')
					{
						$rule .= '|required';
					}


					$rule .= '|date_to_sql';

					break;
			}

			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Products_reviews_model.php */
/* Location: ./application/models/Products_reviews_model.php */