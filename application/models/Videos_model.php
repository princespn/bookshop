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
class Videos_model extends CI_Model
{

	/**
	 * @var string
	 */
	protected $id = 'video_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return array|bool
	 */
	public function ajax_search($term = '')
	{
		$row = array();

		if (!empty($term))
		{
			$this->db->like('video_name', $term);
			$this->db->select('video_id, video_name');
			$this->db->limit(TPL_AJAX_LIMIT);

			if (!$q = $this->db->get(TBL_VIDEOS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = $q->result_array();
			}
		}

		array_push($row, array('video_id'       => '0',
		                       'video_name' => lang('none')));

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 */
	public function delete($id = '')
	{
		//update product group IDs
		$this->dbv->reset_id(TBL_PRODUCTS,
			'video_as_default',
			$id
		);

		//update product group IDs
		$this->dbv->reset_id(TBL_BLOG_POSTS,
			'video_id',
			$id
		);

		//update product specific groups
		if (!$this->db->where('video_id', $id)->delete(TBL_VIDEOS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array('success'  => TRUE,
		             'data'     => $id,
		             'msg_text' => lang('record_deleted_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @return array|bool
	 */
	public function get_rows($options = '')
	{
		$sort = $this->config->item(TBL_VIDEOS, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		$sql = 'SELECT * FROM ' . $this->db->dbprefix(TBL_VIDEOS) . ' p';

		if (!empty($options['query']))
		{
			$this->dbv->validate_columns(array(TBL_VIDEOS), $options['query']);

			$sql .= $options['where_string'];
		}

		$sql .= ' ORDER BY ' . $options['sort_column'] . ' ' . $options['sort_order'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$query = $this->db->query($sql);
		

		if ($query->num_rows() > 0)
		{
			$row = array(
				'values'         => $query->result_array(),
				'total'          => $this->dbv->get_table_totals($options, TBL_VIDEOS),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function validate($data = array())
	{
		//get the list of fields required for this
		$required = $this->config->item(TBL_VIDEOS, 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_VIDEOS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim';

			//if this field is a required field, let's set that
			if (is_array($required) && in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			$rule .= generate_db_rule($f->type, $f->max_length, TRUE, $f->name);

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, false),
			);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
			);
		}

		return $row;
	}
}

/* End of file Videos_model.php */
/* Location: ./application/models/Videos_model.php */