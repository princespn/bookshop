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
class Support_tickets_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'ticket_id';

	// ------------------------------------------------------------------------

	/**
	 * Support_tickets_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('support');
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function auto_close_tickets()
	{
		$sql = 'UPDATE ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) .
			' SET closed = \'1\' 
			WHERE date_modified < (CURDATE() - INTERVAL ' . config_item('sts_support_auto_close_interval') . ' DAY);';

		if (!$this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$rows = $this->db->affected_rows();

		if (!empty($rows))
		{
			$row = array(
				'msg_text' => $rows . ' ' . lang('tickets_auto_closed_successfully'),
				'success'  => TRUE,
			);
		}

		return !empty($row) ? sc($row) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return array
	 */
	public function add_note($id = '')
	{
		//update the note...
		if (!$q = $this->db->insert(TBL_SUPPORT_TICKETS_NOTES, array('ticket_id' => $id,
		                                                             'note'      => ''))
		)
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('system_updated_successfully'),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Add ticket
	 *
	 * Add a new support ticket to the db
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function add_ticket($data = array())
{
	$data = $this->dbv->clean($data, TBL_SUPPORT_TICKETS, TRUE);

	$data['date_added'] = empty($data['date_added']) ? get_time('', TRUE) : $data['date_added'];

	if (!$q = $this->db->insert(TBL_SUPPORT_TICKETS, $data))
	{
		get_error(__FILE__, __METHOD__, __LINE__);
	}

	//set the ticket id
	return $this->db->insert_id();
}

	// ------------------------------------------------------------------------

	/**
	 * Add ticket reply
	 *
	 * Add a reply to the parent ticket
	 *
	 * @param array $data
	 *
	 * @return bool|string
	 */
	public function add_ticket_reply($data = array(), $merge = array())
	{
		$vars = $this->dbv->clean($data, TBL_SUPPORT_TICKETS_REPLIES);

		//set the ip
		$vars['ip_address'] = $this->input->ip_address();

		//set merge fields
		if (!empty($merge))
		{
			$vars['reply_content'] = merge_predefined_fields($merge, $vars['reply_content']);
		}

		if (!$q = $this->db->insert(TBL_SUPPORT_TICKETS_REPLIES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data['ticket_id'],
			'msg_text' => 'ticket_reply_added_successfully',
			'success'  => TRUE,
			'data'     => array_merge($data, $vars, $merge),
		);

		//update the parent ticket timestamp as well
		$this->update_timestamp($data['ticket_id']);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if support tickets are enabled
	 */
	public function check_enabled()
	{
		if (config_enabled('sts_support_enable'))
		{
			return;
		}
		else
		{
			if (config_item('sts_support_url_redirect'))
			{
				redirect(config_item('sts_support_url_redirect'));
			}
			else
			{
				show_error(lang('support_desk_disabled'));
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return false|string
	 */
	public function delete_ticket($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete(TBL_SUPPORT_TICKETS_REPLIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if (!$this->db->where($this->id, $id)->delete(TBL_SUPPORT_TICKETS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array('success'  => TRUE,
		             'msg_text' => lang('record_deleted_successfully'));

		return sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get ticket rows
	 *
	 * Get support tickets
	 *
	 * @param string $options
	 * @param int $lang_id
	 *
	 * @return bool|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_SUPPORT_TICKETS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT 	p.*,
						f.*,
						d.username AS admin_username,
						m.*,
						p.member_id AS member_id,
						c.category_name';
		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(*) FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS_REPLIES) . ' r
			    WHERE r.' . $this->id . ' = p.' . $this->id . ') AS replies ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES_NAME) . ' c
					    ON (p.category_id = c.category_id
					    AND c.language_id = \'' . $lang_id . '\')
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
					    ON (p.member_id = m.member_id)
					 LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' d
					    ON (p.admin_id = d.admin_id)   
					 LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' f
					    ON (p.member_id = f.member_id)';

		if (!empty(sess('admin', 'show_assigned_tickets_only')))
		{
			$sql .= ' WHERE d.admin_id = \'' . sess('admin', 'admin_id') . '\' ';
		}

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_SUPPORT_TICKETS), $options['query']);

			$sql .=  !empty(sess('admin', 'show_assigned_tickets_only')) ? $options['and_string'] : $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		//set the unique cache file
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
					'total'   => $this->dbv->get_table_totals($options, TBL_SUPPORT_TICKETS),
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
	 * Get user tickets
	 *
	 * Get support tickets for specified user
	 *
	 * @param string $id
	 * @param int $closed
	 * @param int $lang_id
	 * @param int $limit
	 *
	 * @return bool|string
	 */
	public function get_user_tickets($id = '', $closed = 0, $lang_id = 1, $limit = MEMBER_RECORD_LIMIT)
	{
		$sort = $this->config->item(TBL_SUPPORT_TICKETS, 'db_sort_order');

		$sql = 'SELECT p.*, c.category_name
                  FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES_NAME) . ' c
                        ON (p.category_id = c.category_id
                        AND c.language_id = \'' . $lang_id . '\')
                    WHERE p.member_id = \'' . $id . '\'
                    AND closed = \'' . $closed . '\'
                    ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
                    LIMIT ' . $limit;

		//set the cache file
		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			//run the query
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_details($id, $lang_id = 1)
	{
		//get parent first
		$sql = 'SELECT  p.*,
                        m.fname,
                        m.lname,
                        m.primary_email,
                        m.username,
                        c.category_name,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) . ' p
				        WHERE p.' . $this->id . ' < ' . valid_id($id) . '
				        ORDER BY `' . $this->id . '` DESC LIMIT 1)
				        AS prev,
				    (SELECT ' . $this->id . '
				        FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) . ' p
				        WHERE p.' . $this->id . ' > ' . valid_id($id) . '
				        ORDER BY `' . $this->id . '` ASC LIMIT 1)
				        AS next
                      FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) . ' p
                        LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                            ON (p.member_id = m.member_id)
                        LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES_NAME) . ' c
                            ON (p.category_id = c.category_id
                            AND c.language_id = \'' . $lang_id . '\')
                        WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'';

		//run the query
		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			//set ticket parent array
			$row = $q->row_array();

			//now get the replies
			$row['replies'] = $this->get_ticket_replies($id);

			$row['notes'] = $this->get_ticket_notes($id);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function get_predefined_reply($id = '')
	{
		if (!$q = $this->db->where('id', $id)->get(TBL_SUPPORT_PREDEFINED_REPLIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return bool|false|string
	 */
	public function get_predefined_replies($form = FALSE)
	{
		if (!$q = $this->db->get(TBL_SUPPORT_PREDEFINED_REPLIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			//set the replies array
			$row = $q->result_array();

			if ($form == TRUE)
			{
				$row = format_array($row, 'id', 'title', TRUE, 'load_predefined_reply');
			}
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
		if (!empty($data['ticket_id']))
		{
			foreach ($data['ticket_id'] as $v)
			{
				if ($data['change-status'] == 'delete')
				{
					$this->dbv->delete(TBL_SUPPORT_TICKETS, $this->id, $v);
				}
				else
				{
					switch ($data['change-status'])
					{
						case 'admin_id':

							$vars =  array('admin_id' => $data['admin_id']);

							break;

						case '1':
						case '0':

							$vars = array('closed' => $data['change-status']);

							break;

						default:

							$vars = array('priority' => $data['change-status']);

							break;
					}

					if (!$this->db->where($this->id, $v)->update(TBL_SUPPORT_TICKETS, $vars))
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
		$this->dbv->db_sort_order(TBL_BRANDS, 'brand_id', 'sort_order');

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function search($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_SUPPORT_TICKETS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT 	p.*,
						f.*,
						m.*,
						r.*,
						p.member_id AS member_id,
						c.category_name';

		if (!$this->config->item('disable_sql_category_count'))
		{
			$sql .= ', (SELECT COUNT(*) FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS_REPLIES) . ' d
			    WHERE r.' . $this->id . ' = d.' . $this->id . ') AS replies ';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS_REPLIES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS) . ' r
					    ON (p.ticket_id = r.ticket_id)    
					LEFT JOIN ' . $this->db->dbprefix(TBL_SUPPORT_CATEGORIES_NAME) . ' c
					    ON (r.category_id = c.category_id
					    AND c.language_id = \'' . $lang_id . '\')
					LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
					    ON (r.member_id = m.member_id)
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

				$columns = $this->db->list_fields(TBL_SUPPORT_TICKETS_REPLIES);

				$i = 1;
				foreach ($columns as $f)
				{
					if ($i == 1)
					{
						$sql .= ' WHERE p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}
					else
					{
						$sql .= 'OR p.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
					}

					$i++;
				}

				$columns = $this->db->list_fields(TBL_SUPPORT_TICKETS);

				foreach ($columns as $f)
				{
					$sql .= ' OR r.' . $f . ' LIKE \'%' . $v . '%\' ESCAPE \'!\'';
				}
			}
		}

		$sql .= ' GROUP BY p.ticket_id';
		$order = ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
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
	 * Ticket reply details
	 *
	 * Get the row details for specific reply
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	public function ticket_reply_details($id = '')
	{
		if (!$q = $this->db->where('reply_id', (int)$id)->get(TBL_SUPPORT_TICKETS_REPLIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : '';
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update_reply($id = '', $data = array())
	{
		$vars['reply_content'] = $data['reply-content-' . $id];

		$data = $this->dbv->clean($vars, TBL_SUPPORT_TICKETS_REPLIES);

		if (!$q = $this->db->where('reply_id', valid_id($id))
			->update(TBL_SUPPORT_TICKETS_REPLIES, $data)
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
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update_note($id = '', $data = array())
	{
		if ($row = $this->get_ticket_notes($id))
		{
			//update the note...
			if (!$q = $this->db->where('note_id', valid_id($row['note_id']))
				->update(TBL_SUPPORT_TICKETS_NOTES, array('note' => $data['note']))
			)
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}
		else
		{
			//insert the note
			if (!$q = $this->db->insert(TBL_SUPPORT_TICKETS_NOTES, array('ticket_id'  => valid_id($id),
			                                                             'admin_user' => sess('admin', 'admin_id'),
			                                                             'note'       => $data['note']))
			)
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return array('success'  => TRUE,
		             'msg_text' => lang('system_updated_successfully'),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update timestamp
	 *
	 * Update the date modified for the specific ticket id
	 *
	 * @param string $id
	 */
	public function update_timestamp($id = '')
	{
		$data = array(
			'table' => TBL_SUPPORT_TICKETS,
			'key'   => 'ticket_id',
			'value' => $id,
			'field' => 'date_modified',
		);

		update_timestamp($data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update status
	 *
	 * Update the ticket to closed / open status
	 *
	 * @param string $id
	 * @param string $status
	 *
	 * @return bool
	 */
	public function update_status($id = '', $status = '1')
	{
		if (!$q = $this->db->where('ticket_id', $id)->update(TBL_SUPPORT_TICKETS, array('closed' => $status)))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 */
	public function update_ticket_status($data = array())
	{
		$data = $this->dbv->clean($data, TBL_SUPPORT_TICKETS);

		if (!$q = $this->db->where('ticket_id', $data['ticket_id'])->update(TBL_SUPPORT_TICKETS, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate ticket
	 *
	 * Validate ticket data first before adding
	 *
	 * @param array $data
	 * @param string $files
	 *
	 * @return bool|string
	 */
	public function validate_ticket($data = array(), $files = '', $merge = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('support_ticket_create', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_SUPPORT_TICKETS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

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

		if ($this->form_validation->run())
		{
			$data = $this->dbv->validated($data, FALSE); //don't escape HTML for this one...

			//add the parent ticket first
			$data['ticket_id'] = $this->add_ticket($data);

			//check for attachments
			$data['attachments'] = !empty($files) ? save_attachments($files) : '';

			$row = $this->add_ticket_reply($data, $merge);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate ticket reply
	 *
	 * Validate replies to parent ticket
	 *
	 * @param array $data
	 * @param array $files
	 *
	 * @return bool|string
	 */
	public function validate_ticket_reply($data = array(), $files = array(), $merge = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('support_ticket_reply', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_SUPPORT_TICKETS_REPLIES);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

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

		if ($this->form_validation->run())
		{
			$data = $this->dbv->validated($data, FALSE); //don't escape HTML for this one...

			//check for attachments
			$data['attachments'] = !empty($files) ? save_attachments($files) : '';

			$row = $this->add_ticket_reply($data, $merge);

			//update ticket status
			$this->update_ticket_status($data);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	protected function get_ticket_replies($id = '')
	{
		$sort = $this->config->item(TBL_SUPPORT_TICKETS_REPLIES, 'db_sort_order');

		$sql = 'SELECT  p.*, n.*,
                        a.photo AS admin_photo,
                        a.username AS admin_username,
                        a.fname AS admin_fname,
                        a.lname AS admin_lname,
                        m.username,
                        m.fname AS member_fname,
                        m.lname AS member_lname,
                         p.member_id AS member_id
                  FROM ' . $this->db->dbprefix(TBL_SUPPORT_TICKETS_REPLIES) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_ADMIN_USERS) . ' a
                        ON (p.admin_id = a.admin_id)
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' m
                        ON (p.member_id = m.member_id)
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_PROFILES) . ' n
                        ON (p.member_id = n.member_id)
                    WHERE p.' . $this->id . ' = \'' . valid_id($id) . '\'
                    ORDER BY ' . $sort['column'] . ' ' . $sort['order'];

		//set the cache file
		$cache = __METHOD__ . md5($sql);
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			//run the query
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
			}
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $col
	 * @return bool|false|string
	 */
	protected function get_ticket_notes($id = '', $col = 'ticket_id')
	{
		if (!$q = $this->db->where($col, $id)->get(TBL_SUPPORT_TICKETS_NOTES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->row_array()) : FALSE;
	}
}

/* End of file Support_tickets_model.php */
/* Location: ./application/models/Support_tickets_model.php */