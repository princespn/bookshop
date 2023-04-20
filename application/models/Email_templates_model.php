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
class Email_templates_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'template_id';

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_EMAIL_TEMPLATES);

		//set template name
		$vars['template_name'] = empty($vars['template_name']) ? url_title($data['lang'][ config_item('sts_site_default_language') ]['subject'], 'underscore', TRUE) : url_title($vars['template_name'], 'underscore', TRUE);

		if (!$q = $this->db->insert(TBL_EMAIL_TEMPLATES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$vars['template_id'] = $this->db->insert_id();

		//now add an entry for each language
		foreach ($data['lang'] as $k => $v)
		{
			$vars = array(
				$this->id     => $vars['template_id'],
				'language_id' => $k,
				'subject'     => empty($v['subject']) ? $data['lang'][ config_item('sts_site_default_language') ]['subject'] : $v['subject'],
				'text_body'   => empty($v['text_body']) ? $data['lang'][ config_item('sts_site_default_language') ]['text_body'] : $v['text_body'],
				'html_body'   => empty($v['html_body']) ? $data['lang'][ config_item('sts_site_default_language') ]['html_body'] : $v['html_body'],
			);

			if (!$q = $this->db->insert(TBL_EMAIL_TEMPLATES_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array(
			'id'       => $vars['template_id'],
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
	public function delete($id = '')
	{
		$row = $this->get_details($id);

		if (!empty($row))
		{
			if ($row['email_type'] != 'custom')
			{
				return FALSE;
			}
		}

		return $this->dbv->delete(TBL_EMAIL_TEMPLATES, 'template_id', $id);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $id
	 * @param int $lang_id
	 * @param bool $public
	 * @return mixed
	 */
	public function get_details($id, $lang_id = 1, $public = TRUE)
	{
		$sql = 'SELECT * ';

		if ($public == FALSE)
		{
			$sql .= ', (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES) . ' p
                            WHERE p.' . $this->id . ' < ' . (int)valid_id($id) . '
                            ORDER BY `' . $this->id . '` DESC LIMIT 1)
                                AS prev,
                        (SELECT ' . $this->id . '
                            FROM ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES) . ' p
                            WHERE p.' . $this->id . ' > ' . (int)valid_id($id) . '
                            ORDER BY `' . $this->id . '` ASC LIMIT 1)
                                AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES) . ' p
		            LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES_NAME) . ' d
                        ON p.' . $this->id . ' = d.' . $this->id . '
                        AND language_id = ' . (int)$lang_id . '
                    WHERE p.' . $this->id . '= ' . (int)valid_id($id);


		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			if ($public == FALSE)
			{
				$row['lang'] = $this->dbv->get_names(TBL_EMAIL_TEMPLATES_NAME, $this->id, $id);
			}
		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param int $lang_id
	 * @param bool $format
	 * @return bool|false|string
	 */
	public function get_templates($type = '', $lang_id = 1, $format = FALSE)
	{
		$sql = 'SELECT *
		        FROM ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES) . ' p
		            LEFT JOIN ' . $this->db->dbprefix(TBL_EMAIL_TEMPLATES_NAME) . ' d
                        ON p.' . $this->id . ' = d.' . $this->id . '
                        AND language_id = ' . (int)$lang_id;

		if (!empty($type))
		{
			$sql .= ' WHERE p.email_type= \'' . $type . '\'';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $format == TRUE ? format_array($q->result_array(), $this->id, 'template_name', TRUE, 'load_template') : $q->result_array();
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
		$vars = $this->dbv->clean($data, TBL_EMAIL_TEMPLATES);

		$this->db->where($this->id, $data[ $this->id ]);

		if (!$this->db->update(TBL_EMAIL_TEMPLATES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		foreach ($data['lang'] as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_EMAIL_TEMPLATES_NAME);

			$this->db->where($this->id, $data[ $this->id ]);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_EMAIL_TEMPLATES_NAME, $vars))
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

			$required = $this->config->item(TBL_EMAIL_TEMPLATES_NAME, 'required_input_fields');

			//now get the list of fields directly from the table
			$fields = $this->db->field_data(TBL_EMAIL_TEMPLATES_NAME);

			foreach ($fields as $f)
			{
				//set the default rule
				$rule = 'trim';

				if ($k == config_item('sts_site_default_language'))
				{
					//if this field is a required field, let's set that
					if (is_array($required) && in_array($f->name, $required))
					{
						$rule .= '|required';
					}
				}

				$rule .= generate_db_rule($f->type, $f->max_length, FALSE);

				$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
			}

			if (!$this->form_validation->run())
			{

				$error .= validation_errors();
			}
			else
			{
				$data['lang'][ $k ] = $this->dbv->validated($v, FALSE);
			}
		}

		//now validate the rest...
		$this->form_validation->reset_validation();
		$this->form_validation->set_data($data);

		$required = $this->config->item(TBL_EMAIL_TEMPLATES, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_EMAIL_TEMPLATES);

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

					if ($data['email_type'] == 'custom')
					{
						$rule .= '|format_template_name|required';
					}

					break;

				case 'description':

					if ($data['email_type'] == 'custom')
					{
						$rule .= '|required';
					}

					break;

				case 'from_email':
				case 'cc':
				case 'bcc':

					if ($data[ $f->name ] != '{{site_email}}')
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

/* End of file Email_templates_model.php */
/* Location: ./application/models/Email_templates_model.php */