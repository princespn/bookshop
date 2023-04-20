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
class Forum_topics_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'topic_id';

	// ------------------------------------------------------------------------

	/**
	 * Forum_topics_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('forum');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function approve_topic($id = '')
	{
		if (!$this->db->where($this->id, (int)$id)->update(TBL_FORUM_TOPICS, array('status' => '1')))
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
	 * @param string $id
	 * @return false|string
	 */
	public function approve_reply($id = '')
	{
		if (!$this->db->where('reply_id', (int)$id)->update(TBL_FORUM_TOPICS_REPLIES, array('status' => '1')))
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
	 * @return false|string
	 */
	public function add_topic($data = array())
	{
		$data['status'] = config_enabled('sts_forum_require_topic_moderation') ? '0' : '1';

		return $this->create($data);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function add_user_reply($data = array())
	{
		$status = config_enabled('sts_forum_require_topic_moderation') ? '0' : '1';

		return $this->add_topic_reply($data, $status);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $status
	 * @return bool|false|string
	 */
	public function add_topic_reply($data = array(), $status = '0')
	{
		$vars = $this->dbv->clean($data, TBL_FORUM_TOPICS_REPLIES);

		$vars['status'] = $status;

		//set the ip
		$vars['ip_address'] = $this->input->ip_address();

		if (!$q = $this->db->insert(TBL_FORUM_TOPICS_REPLIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars['reply_id'] = $this->db->insert_id();

		$row = array(
			'id'       => $data[$this->id],
			'msg_text' => 'reply_added_successfully',
			'success'  => TRUE,
			'data'     => $vars,
		);

		//update the parent ticket timestamp as well
		$this->update_timestamp($data[$this->id]);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_FORUM_TOPICS);

		$vars['url'] = $this->dbv->generate_permalink($vars['title'], TBL_FORUM_TOPICS, 'url');
		$vars['date_added'] = get_time('', TRUE);
		$vars['ip_address'] = $this->input->ip_address();

		if (!$q = $this->db->insert(TBL_FORUM_TOPICS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//set the topic id
		$vars['topic_id'] = $this->db->insert_id();

		return sc(array('id'       => $vars['topic_id'],
		                'success'  => TRUE,
		                'data'     => $vars,
		                'msg_text' => 'record_created_successfully',
		));

	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $str
	 * @return bool
	 */
	public function check_url($str = '')
	{
		$str = empty($str) ? $this->input->topic('url') : $str;

		if (!empty($str))
		{
			$this->db->where('url', $str);

			if ($this->input->post($this->id))
			{
				$this->db->where($this->id . ' !=', (int)$this->input->topic($this->id));
			}

			if (!$q = $this->db->get(TBL_FORUM_TOPICS))
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
	 * @param string $id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function delete($id = '', $public = FALSE)
	{
		if ($public == TRUE)
		{
			if ($row = $this->get_details($id))
			{
				if (!empty($row['admin_id']))
				{
					return FALSE;
				}
			}
		}

		if (!$this->db->where($this->id, $id)->delete(TBL_FORUM_TOPICS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'msg_text' => lang('record_deleted_successfully')));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function delete_reply($id = '', $public = FALSE)
	{
		if ($public == TRUE)
		{
			if ($row = $this->dbv->get_record(TBL_FORUM_TOPICS_REPLIES, 'reply_id', $id))
			{
				if (!empty($row['admin_id']))
				{
					return FALSE;
				}
			}
		}

		if (!$this->db->where('reply_id', $id)->delete(TBL_FORUM_TOPICS_REPLIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'msg_text' => lang('record_deleted_successfully')));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = 1, $public = FALSE)
	{
		$cache = __METHOD__ . $id . $lang_id . $public;
		$cache_type = $public == TRUE ? 'public_db_query' : 'db_query';
		if (!$row = $this->init->cache($cache, $cache_type))
		{
			$sql = 'SELECT  p.*,
                            c.category_name,
                            n.category_url,
                            m.fname AS member_fname,
	                        m.lname AS member_lname,
	                        m.primary_email,
	                        m.username AS member_username,
	                        r.*,
	                        a.fname AS  admin_fname,
	                        a.lname AS admin_lname,
	                        a.photo AS admin_photo,
	                        a.username AS admin_username,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' p
				        WHERE p.' . $this->id . ' < ' . (int)$id . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' p
				        WHERE p.' . $this->id . ' > ' . (int)$id . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
                        FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                            ON (p.member_id = m.member_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' r
                            ON (p.member_id = r.member_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
                            ON (p.admin_id = a.admin_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES) . ' n
                            ON (p.category_id = n.category_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' c
                            ON (p.category_id = c.category_id
                            AND c.language_id = \'' . $lang_id . '\')';

			if ($public == TRUE)
			{
				$sql .= ' WHERE p.url = \'' . $id . '\'';
			}
			else
			{
				$sql .= ' WHERE p.' . $this->id . ' = \'' . $id . '\' ';
			}

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();

				$row['topic_replies'] = $this->get_topic_replies($row['topic_id']);
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
		$sort = $this->config->item(TBL_FORUM_TOPICS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT  p.*,
						m.username,
						f.*,
						a.username AS admin_username,
						a.fname AS admin_fname,
                        a.lname AS admin_lname,
						a.photo AS admin_photo,
                        c.category_id as category_id,
                        c.category_name';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(*) FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS_REPLIES) . ' r
			    WHERE r.' . $this->id . ' = p.' . $this->id . ') AS replies ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' c
				        ON (p.category_id = c.category_id
				        AND c.language_id = \'' . (int)$lang_id . '\')
				    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
					    ON (p.member_id = m.member_id)
					 LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
					    ON (a.admin_id = p.admin_id)    
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' f
					    ON (p.member_id = f.member_id)    ';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_FORUM_TOPICS, TBL_FORUM_CATEGORIES_NAME), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' GROUP BY p.' . $this->id . ' 
					ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

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

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $options
	 * @param bool $pinned
	 * @param bool $latest
	 * @return bool|false|string
	 */
	public function get_topics($id = '', $options = '', $pinned = FALSE, $latest = FALSE)
	{
		$sort = $this->config->item('forum_home_page', 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$count = 'SELECT COUNT(p.topic_id) AS total 
					FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' p
					WHERE  p.category_id = \'' . valid_id($id) . '\'';

		if ($pinned == TRUE)
		{
			$count .= ' AND p.pinned = \'1\'';
		}
		elseif ($latest == FALSE)
		{
			$count .= ' AND p.pinned = \'0\'';
		}
		else
		{
			if ($latest == TRUE)
			{
				$count .= 'AND p.status = \'1\'';
			}
		}

		$sql = 'SELECT  p.*,
						m.username,
						m.fname AS member_fname,
						m.lname AS member_lname,
						f.*,
						a.username AS admin_username,
						a.fname AS admin_fname,
                        a.lname AS admin_lname,
						a.photo AS admin_photo';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(*) FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS_REPLIES) . ' r
			    WHERE r.' . $this->id . ' = p.' . $this->id . '
			     AND r.status = \'1\') AS replies ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' p
				   
				    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
					    ON (p.member_id = m.member_id)
					 LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
					    ON (a.admin_id = p.admin_id)    
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' f
					    ON (p.member_id = f.member_id)   
					WHERE p.category_id = \'' . valid_id($id) . '\'';

		if ($pinned == TRUE)
		{
			$sql .= ' AND p.pinned = \'1\'';
		}
		elseif ($latest == FALSE)
		{
			$sql .= ' AND p.pinned = \'0\'';
		}
		else
		{
			if ($latest == TRUE)
			{
				$sql .= 'AND p.status = \'1\'';
			}
		}

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_FORUM_TOPICS, TBL_FORUM_CATEGORIES_NAME), $options['query']);

			$sql .= $options['and_string'];
		}

		$sql .= ' GROUP BY p.' . $this->id . ' 
					ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'];

		if ($pinned == FALSE)
		{
			$sql .= ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		}

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'values'  => $q->result_array(),
					'total'   => $this->dbv->get_query_total($count),
					'success' => TRUE,
				);
			}
			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param string $type
	 * @return bool
	 */
	public function get_table_totals($options = '', $type = '')
	{
		switch ($type)
		{
			case 'category':

				$sql = 'SELECT  COUNT(*) as total
                    FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' p
                    WHERE p.status = \'1\'';

				if (!empty($options['category_id']))
				{
					$sql .= ' AND p.category_id = \'' . $options['category_id'] . '\' ';
				}

				break;

			default:

				$sql = 'SELECT COUNT(*) as total FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' m ';

				if (!empty($options['query']))
				{
					$this->dbv->validate_columns(array(TBL_FORUM_TOPICS), $options['query']);

					$sql .= $options['where_string'];
				}

				break;
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$q = $q->row();

			return $q->total;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param array $data
	 * @return array
	 */
	public function load_forum($options = '', $data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				$topics = $this->get_topics($v['category_id'], $options, FALSE, TRUE);

				if (!empty($topics['values']))
				{
					$data[$k]['latest_topics'] = $topics['values'];
				}
			}
		}

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function mass_update($data = array())
	{
		if (!empty($data['topic_id']))
		{
			foreach ($data['topic_id'] as $v)
			{
				$vars = array('category_id' => $data['category_id']);
				if (!$this->db->where($this->id, $v)->update(TBL_FORUM_TOPICS, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}
			}

			$row = array(
				'data'     => $data,
				'msg_text' => lang('system_updated_successfully'),
				'success'  => TRUE,
			);
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @param bool $public
	 * @return bool|false|string
	 */
	public function search($options = '', $lang_id = 1, $public = FALSE)
	{
		$sort = $this->config->item(TBL_FORUM_TOPICS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT 	p.*,
						f.*,
						m.*,
						m.fname AS member_fname,
						m.lname AS member_lname,
						r.*,
						a.username AS admin_username,
						a.fname AS admin_fname,
                        a.lname AS admin_lname,
						p.member_id AS member_id,
						c.category_name';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(*) FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS_REPLIES) . ' d
			    WHERE r.' . $this->id . ' = d.' . $this->id . ') AS replies ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS_REPLIES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_TOPICS) . ' r
					    ON (p.topic_id = r.topic_id)    
					LEFT JOIN ' . $this->db->dbprefix(TBL_FORUM_CATEGORIES_NAME) . ' c
					    ON (r.category_id = c.category_id
					    AND c.language_id = \'' . $lang_id . '\')
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
					    ON (r.member_id = m.member_id)
					LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
					    ON (a.admin_id = p.admin_id)       
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' f
					    ON (r.member_id = f.member_id)';

		if (!empty($options['query']))
		{
			foreach ($options['query'] as $k => $v)
			{
				//remove any fields not needed in the query
				if (in_array($k, $this->config->item('query_type_filter')))
				{
					continue;
				}

				$columns = array('p.reply_content', 'r.title', 'r.topic');

				$i = 1;
				foreach ($columns as $f)
				{
					if ($i == 1)
					{
						$sql .= ' WHERE ( ' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}
					else
					{
						$sql .= 'OR  ' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}

					$i++;
				}
			}

			$sql .= ' ) ';
		}

		if ($public == TRUE)
		{
			$sql .= ' AND p.status = \'1\'';
		}

		$sql .= ' GROUP BY p.topic_id ';


		$order = '	ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];


		if (!$q = $this->db->query($sql . $order))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_query_total($sql, '', TRUE),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 */
	public function update_timestamp($id = '')
	{
		$data = array(
			'table' => TBL_FORUM_TOPICS,
			'key'   => $this->id,
			'value' => $id,
			'field' => 'date_modified',
		);

		update_timestamp($data);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update_reply($id = '', $data = array())
	{
		if (!empty($data['reply-content-' . $id]))
		{
			$data['reply_content'] = $data['reply-content-' . $id];
		}

		$vars = $this->dbv->clean($data, TBL_FORUM_TOPICS_REPLIES);

		if (!$q = $this->db->where('reply_id', valid_id($id))
			->update(TBL_FORUM_TOPICS_REPLIES, $vars)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		return array('success'  => TRUE,
		             'data'     => nl2br_except_pre(html_escape($data['reply_content'])),
		             'msg_text' => lang('system_updated_successfully'),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @param array $data
	 * @return array
	 */
	public function update_topic($id, $data = array())
	{
		$data = $this->dbv->clean($data, TBL_FORUM_TOPICS);

		if (!$q = $this->db->where('topic_id', valid_id($id))
			->update(TBL_FORUM_TOPICS, $data)
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//reformat the data for showing on the screen automatically..
		$data['title'] = parse_codes(nl2br_except_pre(html_escape($data['title'])));
		$data['topic'] = parse_codes(nl2br_except_pre(html_escape($data['topic'])));

		return array('success'  => TRUE,
		             'data'     => $data,
		             'msg_text' => lang('system_updated_successfully'),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return bool|false|string
	 */
	public function validate($data = array(), $type = 'create')
	{
		$this->form_validation->set_data($data);

		$required = $type == 'create' ? $this->config->item('forum_topics_create', 'required_input_fields') : $this->config->item('forum_topics_replies', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_FORUM_TOPICS_REPLIES);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			//check if this is an admin or member response
			switch ($f)
			{
				case 'member_id':

					$rule .= $data['reply_type'] == 'member' ? '|required' : '';

					break;

				case 'admin_id':

					$rule .= $data['reply_type'] == 'admin' ? '|required' : '';

					break;
			}

			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		//validate title
		if ($type == 'create')
		{
			$this->form_validation->set_rules('title', 'lang:title', 'trim|xss_clean|required');
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, FALSE));
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors());
		}


		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate_user_topic($type = 'create', $data = array())
	{
		//now check for form flood
		if ($this->sec->check_flood_control('forum_post', 'check', sess('member_id')))
		{
			$this->form_validation->set_data($data);

			$required = $this->config->item('forum_' . $type, 'required_input_fields');

			foreach ($required as $v)
			{
				$rule = 'trim|required';

				switch ($v)
				{
					case 'member_id':

						$rule .= '|integer';

						break;

					case 'topic':
					case 'title':
					case 'reply_content':

						if (config_item('sts_forum_enable_bbcode') == '0')
						{
							$rule .= '|strip_bbcode';
						}

						$rule .= defined('DISABLE_CODE_ON_FORUM_POSTS') ? '|strip_tags|xss_clean' : '|htmlentities';

						break;
				}

				$this->form_validation->set_rules($v, 'lang:' . $v, $rule);
			}

			if ($this->form_validation->run())
			{
				$row = array('success' => TRUE,
				             'data'    => $this->dbv->validated($data, FALSE),
				);

				//run check for recaptcha
				if (config_enabled('sts_form_enable_forum_captcha'))
				{
					$this->form_validation->reset_validation();

					$this->form_validation->set_data($data);

					$this->form_validation->set_rules(
						CAPTCHA_FIELD, 'lang:captcha',
						array(
							'required',
							array('check_captcha', array($this->dbv, 'check_captcha')),
						)
					);

					$this->form_validation->set_message('check_captcha', lang('invalid_security_captcha'));

					if (!$this->form_validation->run())
					{
						//sorry! got some errors here....
						$row = array('error'        => TRUE,
						             'msg_text'     => validation_errors(),
						             'error_fields' => generate_error_fields($data),
						);
					}
				}
			}
			else
			{
				//sorry! got some errors here....
				$row = array('error'    => TRUE,
				             'msg_text' => validation_errors(),
				);
			}
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => lang('maximum_form_submission_reached') . '. ' . lang('please_wait'),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	protected function get_topic_replies($id = '')
	{
		$sort = $this->config->item(TBL_FORUM_TOPICS_REPLIES, 'db_sort_order');

		$sql = 'SELECT  p.*, n.*,
                        a.photo AS admin_photo,
                        a.username AS admin_username,
                        a.fname AS admin_fname,
                        a.lname AS admin_lname,
                        m.username,
                        m.fname AS member_fname,
                        m.lname AS member_lname,
                         p.member_id AS member_id
                  FROM ' . $this->db->dbprefix(TBL_FORUM_TOPICS_REPLIES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
                        ON (p.admin_id = a.admin_id)
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        ON (p.member_id = m.member_id)
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' n
                        ON (p.member_id = n.member_id)
                    WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'
                        ORDER BY ' . $sort['column'] . ' ' . $sort['order'];

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}
}
/* End of file Forum_topics_model.php */
/* Location: ./application/models/Forum_topics_model.php */