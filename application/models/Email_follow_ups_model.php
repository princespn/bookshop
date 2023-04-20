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
class Email_follow_ups_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'follow_up_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $lang
	 * @param array $data
	 * @return false|string
	 */
	public function create($id = '', $lang = array(), $data = array())
	{
		$vars = array(
			'list_id'        => $id,
			'sequence'       => '9999',
			'days_apart'     => '1',
			'follow_up_name' => lang('new_follow_up'),
			'from_name'      => config_item('sts_site_name'),
			'from_email'     => config_item('sts_site_email'),
			'email_type'     => 'html',
		);

		if (!$q = $this->db->insert(TBL_EMAIL_FOLLOW_UPS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$follow_up_id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($lang as $v)
		{
			$vars = array(
				$this->id     => $follow_up_id,
				'language_id' => $v['language_id'],
				'subject'     => lang('new_follow_up'),
				'html_body'   => lang('new_follow_up'),
			);

			if (!$q = $this->db->insert(TBL_EMAIL_FOLLOW_UPS_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		$this->sort_follow_ups($id);

		return sc(array(
			'id'       => $follow_up_id,
			'success'  => TRUE,
			'data'     => $vars,
			'msg_text' => lang('record_created_successfully'),
		));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function delete($id = '')
	{
		if (!$q = $this->db->where($this->id, $id)->get(TBL_EMAIL_FOLLOW_UPS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$data = $q->row_array();

			$this->db->where($this->id, valid_id($id));

			if (!$this->db->delete(TBL_EMAIL_FOLLOW_UPS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			$this->sort_follow_ups($data['list_id']);

			$row['success'] = TRUE;
			$row['data'] = $data;
			$row['msg_text'] = lang('record_deleted_successfully');
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return mixed
	 */
	public function get_follow_ups($id = '')
	{
		if (!empty($id))
		{
			$this->db->where('list_id', $id);
		}

		return $this->db->count_all_results(TBL_EMAIL_FOLLOW_UPS);
	}

	// ------------------------------------------------------------------------

	/**
	 * @return bool|false|string
	 */
	public function get_member_follow_ups()
	{
		$sql = 'SELECT p.*, d.*, t.*, p.list_id AS list_id, s.subject, s.html_body, s.text_body
                	FROM ' . $this->db->dbprefix(TBL_MEMBERS_EMAIL_MAILING_LIST) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' d
                        ON p.member_id = d.member_id
                     LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_FOLLOW_UPS) . ' t
                        ON p.list_id = t.list_id
                        AND p.sequence_id = t.sequence
                    LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_FOLLOW_UPS_NAME) . ' s
                        ON t.follow_up_id = s.follow_up_id 
                        AND s.language_id = p.language_id
                    WHERE p.send_date < NOW()
                    ORDER BY p.send_date ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$rows = $q->result_array();

			$q->free_result();
		}

		return empty($rows) ? FALSE : sc($rows);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_EMAIL_FOLLOW_UPS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT *
                 FROM ' . $this->db->dbprefix(TBL_EMAIL_FOLLOW_UPS) . ' p 
                 LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_FOLLOW_UPS_NAME) . ' n 
                    ON p.follow_up_id = n.follow_up_id
                    AND language_id = \'' . (int)$lang_id . '\'';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_EMAIL_FOLLOW_UPS, TBL_EMAIL_FOLLOW_UPS_NAME), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . '
                    LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}


		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'  => $q->result_array(),
				'total'   => $this->dbv->get_table_totals($options, TBL_EMAIL_FOLLOW_UPS),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $list_id
	 * @return bool|false|string
	 */
	public function mass_update($data = array(), $list_id = '1')
	{
		foreach ($data as $k => $v)
		{
			$v['follow_up_id'] = $k;

			$this->dbv->update(TBL_EMAIL_FOLLOW_UPS, $this->id, $v);
		}

		$this->sort_follow_ups($list_id);

		$row = array(
			'data'     => $data,
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 */
	public function sort_follow_ups($id = '')
	{
		$this->db->order_by('sequence', 'ASC');

		$this->db->where('list_id', $id);

		$q = $this->db->get(TBL_EMAIL_FOLLOW_UPS);

		$total = $q->num_rows();
		$i = 1;
		if ($total > 1)
		{
			foreach ($q->result_array() as $row)
			{
				$update = array('sequence' => $i);
				$this->db->where('follow_up_id', $row['follow_up_id']);

				if ($this->db->update(TBL_EMAIL_FOLLOW_UPS, $update))
				{
					//log success
					log_message('info', 'sort order changed for ' . TBL_EMAIL_FOLLOW_UPS);
				}

				$i++;
			}
		}

	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_EMAIL_FOLLOW_UPS);

		$this->db->where($this->id, $data[$this->id]);

		if (!$this->db->update(TBL_EMAIL_FOLLOW_UPS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_EMAIL_FOLLOW_UPS_NAME);

			$this->db->where($this->id, $data[$this->id]);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_EMAIL_FOLLOW_UPS_NAME, $vars))
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
	 * @param array $follow_ups
	 * @return bool
	 */
	public function update_member_list_sequence($data = array(), $follow_ups = array())
	{
		$next_sequence = $data['sequence_id'] + 1;

		$vars = array('sequence_id' => $next_sequence,
		              'send_date'   => get_time(now(), TRUE));

		//get next follow up sequence
		foreach ($follow_ups as $f)
		{
			if ($f['list_id'] == $data['list_id'] && $f['sequence'] == $next_sequence)
			{
				$vars['send_date'] = get_time(now() + (60 * 60 * 24 * $f['days_apart']), TRUE);
			}
		}

		if (!$q = $this->db->where('eml_id', $data['eml_id'])->update(TBL_MEMBERS_EMAIL_MAILING_LIST, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return TRUE;
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

			$required = $this->config->item(TBL_EMAIL_FOLLOW_UPS_NAME, 'required_input_fields');

			//now get the list of fields directly from the table
			$fields = $this->db->field_data(TBL_EMAIL_FOLLOW_UPS_NAME);

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

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$required = $this->config->item(TBL_EMAIL_FOLLOW_UPS, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_EMAIL_FOLLOW_UPS);

		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|xss_clean';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			switch ($f->name)
			{
				case 'template_name':

					if ($data['type'] == 'custom')
					{
						$rule .= '|url_title|required';
					}

					break;

				case 'description':

					if ($data['type'] == 'custom')
					{
						$rule .= '|required';
					}

					break;

				case 'from_email':
				case 'cc':
				case 'bcc':

					if ($data[$f->name] != '{{sts_site_email}}')
					{
						$rule .= '|valid_email';
					}

					break;
			}

			$rule .= generate_db_rule($f->type, $f->max_length, TRUE);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
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

}

/* End of file Email_follow_ups_model.php */
/* Location: ./application/models/Email_follow_ups_model.php */