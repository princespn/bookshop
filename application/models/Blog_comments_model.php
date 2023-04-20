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
class Blog_comments_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'id';

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $type
	 * @return false|string
	 */
	public function create($data = array (), $type = '')
	{
		$vars = $this->dbv->clean($data, TBL_BLOG_COMMENTS);

		if ($type == 'member')
		{
			$vars['status'] = config_enabled('sts_content_require_comment_moderation') ? '0' : '1';
		}

		if (empty($vars['date']))
		{
			$vars['date'] = get_time(now(), TRUE, FALSE);
		}

		if (!$q = $this->db->insert(TBL_BLOG_COMMENTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
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
		if (!$this->db->where($this->id, $id)->delete(TBL_BLOG_COMMENTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
		                 'msg_text' => lang('record_deleted_successfully') ));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_details($id = '', $lang_id = 1)
	{
		$sql = 'SELECT p.*, k.title, t.url,
						DATE_FORMAT(p.date,\'' . $this->config->item('sql_date_format') . '\')
                        AS date_formatted,
						m.username,
						c.username AS parent_username,
						n.comment AS parent_comment,
						n.date AS parent_date,
						a.fname AS admin_fname,
						a.lname AS admin_lname,
						a.photo AS admin_photo,
                        (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' p
	                        WHERE p.' . $this->id . ' < ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                        AS prev,
                        (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' p
	                        WHERE p.' . $this->id . ' > ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                        AS next
                        FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' n
                            ON (p.parent_id = n.id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
					        ON a.admin_id =  p.user_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                            ON (p.user_id = m.member_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' c
                            ON (n.user_id = c.member_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' t
					        ON t.blog_id = p.blog_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' k
					        ON k.blog_id = p.blog_id
					    AND k.language_id = \'' . $lang_id . '\'
                        WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'';

		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'db_query'))
		{
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->row_array();
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
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
		$sort = $this->config->item(TBL_BLOG_COMMENTS, 'db_sort_order');
		
		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];
		
		$sql = 'SELECT 	p.*,
						n.*,
						c.url,
						r.profile_photo,
						m.username,
						a.username AS admin_username,
						a.fname AS admin_fname,
						a.lname AS admin_lname,
						a.photo AS admin_photo
	                FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' p
	                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
					        ON m.member_id =  p.user_id
					    LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
					        ON a.admin_id =  p.user_id
					     LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' r
					        ON r.member_id =  p.user_id
	                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' c
					        ON c.blog_id = p.blog_id
	                    LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' n
					        ON n.blog_id = p.blog_id
					    AND n.language_id = \'' . $lang_id . '\'';
		
		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_BLOG_COMMENTS ), $options[ 'query' ]);
			
			$sql .= $options[ 'where_string' ];
		}
		
		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . ' 
					LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];
		
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		
		
		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->get_table_totals($options),
				'success'        => TRUE,
			);

		}

		return !empty($row) ?  sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return mixed
	 */
	public function get_table_totals($options = '')
	{
		$sql = 'SELECT COUNT(*) as total 
					FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' p ';
		
		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_BLOG_COMMENTS ), $options[ 'query' ]);
			
			$sql .= $options[ 'where_string' ];
		}
		
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
		
		$row = $q->row();
		
		return $row->total;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function load_comments($id = '')
	{
		//load comments on public blog page

		$cache = __METHOD__ . $id;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sort = $this->config->item('public_blog_comments', 'db_sort_order');

			$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
			$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

			$sql = 'SELECT p.*, h.*,
							m.fname,
							m.lname,
							m.username,
							a.username AS admin_username,
						a.fname AS admin_fname,
						a.lname AS admin_lname,
						a.photo AS admin_photo
                        FROM ' . $this->db->dbprefix(TBL_BLOG_COMMENTS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
					        ON a.admin_id =  p.user_id
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                            ON (p.user_id = m.member_id)
                         LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' h
                            ON (p.user_id = h.member_id)
                        WHERE p.status = \'1\'
                        AND p.blog_id = \'' . $id . '\'
                        ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ];

			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				$row = format_comments($row);
			}

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
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
		if (!empty($data[ 'id' ]))
		{
			foreach ($data[ 'id' ] as $v)
			{
				if ($data[ 'change-status' ] == 'delete')
				{
					$this->delete($v);
				}
				else
				{
					switch ($data[ 'change-status' ])
					{
						case '1':
						case '0':

							$vars[ 'status' ] = $data[ 'change-status' ];

							break;
					}

					if (!$this->db->where($this->id, $v)->update(TBL_BLOG_COMMENTS, $vars))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}
			}

			$row = array( 'success'  => TRUE,
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
	public function update($data = array ())
	{
		$vars = $this->dbv->clean($data, TBL_BLOG_COMMENTS);

		if (!$q = $this->db->where($this->id, $data[ 'id' ])->update(TBL_BLOG_COMMENTS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
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
	 * @param string $type
	 * @return bool|false|string
	 */
	public function validate($data = array (), $type = 'admin')
	{
		$this->form_validation->set_data($data);

		if ($type == 'member')
		{
			$vars = array('blog_id', 'parent_id', 'email', 'comment');

			foreach ($vars as $v)
			{
				$rule = 'trim|required';

				switch ($v)
				{
					case 'user_id':

						if (config_enabled('sts_content_require_login_comment'))
						{
							$rule .= '|required';
						}

						$rule .= '|integer';

						break;

					case 'blog_id':
					case 'parent_id':

						$rule .= '|integer';

						break;

					case 'email':

						$rule .= '|strtolower|valid_email';

						break;

					case 'comment':

						$rule .= '|strip_tags|xss_clean';

						break;
				}

				$this->form_validation->set_rules($v, 'lang:' . $v, $rule);
			}
		}
		else
		{
			if (isset($data['comment']))
			{
				$vars = array('id', 'status', 'reply_date', 'comment');
				foreach ($vars as $v)
				{
					$rule = 'trim|required';

					switch ($v)
					{
						case 'id':
						case 'status':

							$rule .= '|integer';

							break;

						case 'date':

							$rule .= '|date_to_sql';

							break;

						case 'comment':

							$rule .= '|strip_tags|xss_clean';

							break;
					}

					$this->form_validation->set_rules($v, 'lang:' . $v, $rule);
				}
			}
			else
			{
				$this->form_validation->set_rules('admin_reply', 'lang:admin_reply', 'trim|required|strip_tags|xss_clean');

				$vars = array('user_id', 'reply_date');

				foreach ($vars as $v)
				{
					$rule = 'trim|required';

					switch ($v)
					{
						case 'user_id':

							$rule .= '|integer';

							break;

						case 'reply_date':

							$rule .= '|date_to_sql';

							break;
					}

					$this->form_validation->set_rules($v, 'lang:' . $v, $rule);
				}
			}
		}

		if ($this->form_validation->run())
		{
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data),
			              'msg_text' => isset($data['comment']) ? 'comment_updated_successfully' : 'comment_added_successfully',
			);

			if ($type == 'member')
			{
				//run check for recaptcha
				if (config_enabled('sts_form_enable_blog_captcha'))
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
						             'msg_text'          => validation_errors(),
						             'error_fields' => generate_error_fields($data),
						);
					}
				}
			}

		}
		else
		{
			//sorry! got some errors here....
			$row = array( 'error'    => TRUE,
			              'msg_text' => validation_errors(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Blog_comments_model.php */
/* Location: ./application/models/Blog_comments_model.php */