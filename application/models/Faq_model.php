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
class Faq_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'faq_id';

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $lang
	 * @return false|string
	 */
	public function create($data = array(), $lang = array())
	{
		if (!$q = $this->db->insert(TBL_FAQ, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		//now add an entry for each language
		foreach ($lang as $v)
		{
			$vars = array(
				$this->id      => $id,
				'language_id' => $v[ 'language_id' ],
				'question'    => lang('new_faq_question'),
				'answer'      => lang('new_faq_answer'),
			);

			if (!$q = $this->db->insert(TBL_FAQ_NAME, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
		                 'data' => $data,
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
		$tables = array( TBL_FAQ_NAME, TBL_FAQ);

		foreach ($tables as $v)
		{
			if (!$this->db->where($this->id, $id)->delete($v))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
		                 'msg_text' => lang('record_deleted_successfully') ));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_details($id = '')
	{
		if ($row = $this->dbv->get_record(TBL_FAQ, $this->id, $id))
		{
			//get names
			$row[ 'lang' ] = $this->dbv->get_names(TBL_FAQ_NAME, $this->id, $id);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $lang_id = 1)
	{
		$sort = $this->config->item(TBL_FAQ, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_FAQ) . ' p
				LEFT JOIN ' . $this->db->dbprefix(TBL_FAQ_NAME) . ' c ON (p.' . $this->id . ' = c.' . $this->id . '
				AND c.language_id = \'' . $lang_id . '\')';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_FAQ ), $options[ 'query' ]);

			$sql .= $options[ 'where_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ] . ' LIMIT ' . $options[ 'offset' ] . ', ' . $options[ 'limit' ];

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_FAQ),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param int $lang_id
	 * @return bool|false|string
	 */
	public function load_faqs($lang_id = 1)
	{
		$sql = 'SELECT *
                    FROM ' . $this->db->dbprefix(TBL_FAQ) . ' p
				    LEFT JOIN ' . $this->db->dbprefix(TBL_FAQ_NAME) . ' c
				        ON (p.' . $this->id . ' = c.' . $this->id . '
				        AND c.language_id = \'' . (int)$lang_id . '\')
				    WHERE p.status = \'1\'
                    ORDER BY sort_order ASC';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update($id = '', $data = array())
	{
		foreach ($data as $k => $v)
		{
			$vars = $this->dbv->clean($v, TBL_FAQ_NAME);

			$this->db->where($this->id, $id);
			$this->db->where('language_id', $k);

			if (!$this->db->update(TBL_FAQ_NAME, $vars))
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

		foreach ($data as $k => $v)
		{
			$this->form_validation->reset_validation();
			$this->form_validation->set_data($v);
			$this->form_validation->set_rules('question', 'lang:question', 'trim|required|xss_clean',
				array( 'required' => $v[ 'language' ] . ' ' . lang('question_required') ));

			$this->form_validation->set_rules('answer', 'lang:answer', 'trim|required|xss_clean',
				array( 'required' => $v[ 'language' ] . ' ' . lang('answer_required') ));

			if (!$this->form_validation->run())
			{
				$error .= validation_errors();
			}
		}

		if (!empty($error))
		{
			//sorry! got some errors here....
			$row = array( 'error'    => TRUE,
			              'msg_text' => $error,
			);
		}
		else
		{
			//cool! no errors...
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data),
			);
		}

		return $row;
	}
}

/* End of file Faq_model.php */
/* Location: ./application/models/Faq_model.php */