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
class Widgets_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'widget_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $term
	 * @return bool|false|string
	 */
	public function ajax_search($term = '')
	{
		$this->db->like('category_name', $term);
		$this->db->select('category_id, category_name');
		$this->db->limit(TPL_AJAX_LIMIT);

		if (!$q = $this->db->get(TBL_WIDGETS_CATEGORIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$rows = $q->result_array();
		}

		return empty($rows) ? FALSE : sc($rows);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool|false|string
	 */
	public function clone_widget($id = '')
	{
		if ($vars = $this->get_details($id))
		{
			//remove the id
			unset($vars['widget_id']);

			//set the new thumb preview
			$vars['widget_name'] .= ' ' . lang('clone');
			$vars['widget_type'] = 'section';
			$vars['image'] = 'module-custom.png';

			$row = $this->create($vars);

			$row = array(
				'msg_text' => lang('record_created_successfully'),
				'success'  => TRUE,
				'id'       => $row['id'],
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_WIDGETS);

		if (empty($vars[ 'preview_code' ]))
		{
			$vars[ 'preview_code' ] = $vars[ 'template_code' ];
		}
		
		if (!$q = $this->db->insert(TBL_WIDGETS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$id = $this->db->insert_id();

		return sc(array( 'success'  => TRUE,
		                 'id'       => $id,
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
		$sql = 'SELECT *,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_WIDGETS) . ' p
                        WHERE p.' . $this->id . ' < ' . (int)$id . '
                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                    AS prev,
                    (SELECT ' . $this->id . '
                        FROM ' . $this->db->dbprefix(TBL_WIDGETS) . ' p
                        WHERE p.' . $this->id . ' > ' . (int)$id . '
                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                    AS next
				    FROM ' . $this->db->dbprefix(TBL_WIDGETS) . ' p
                    WHERE p.' . $this->id . '= ' . (int)$id . '';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->row_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param int $cat_id
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $cat_id = 1)
	{
		$sort = $this->config->item(TBL_WIDGETS, 'db_sort_order');

		$options[ 'sort_order' ] = !empty($options[ 'query' ][ 'order' ]) ? $options[ 'query' ][ 'order' ] : $sort[ 'order' ];
		$options[ 'sort_column' ] = !empty($options[ 'query' ][ 'column' ]) ? $options[ 'query' ][ 'column' ] : $sort[ 'column' ];

		$sql = 'SELECT *
					FROM ' . $this->db->dbprefix(TBL_WIDGETS) . '
					WHERE widget_category = \'' . valid_id($cat_id) . '\' ';

		if (!empty($options[ 'query' ]))
		{
			$this->dbv->validate_columns(array( TBL_WIDGETS), $options[ 'query' ]);

			$sql .= $options[ 'and_string' ];
		}

		$sql .= ' ORDER BY ' . $options[ 'sort_column' ] . ' ' . $options[ 'sort_order' ];

		$q = $this->db->query($sql);

		if ($q->num_rows() > 0)
		{
			$rows = $q->result_array();

			foreach ($rows as $k => $v)
			{
				$rows[ $k ][ 'thumbnail'] = str_replace('{{base_url}}', base_url(), $v['thumbnail']);
			}

			$row = array(
				'values'         => $rows,
				'total'          => count($q->result_array()),
				'success' => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param string $id
	 * @param bool $not
	 * @return bool|false|string
	 */
	public function get_widgets($data = array(), $id = '', $not = FALSE)
	{
		if (!empty($id))
		{
			if ($not == TRUE)
			{
				$this->db->where('widget_category !=', $id);
			}
			else
			{
				$this->db->where('widget_category', $id);
			}
		}

		if (!$q = $this->db->get(TBL_WIDGETS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$rows = $q->result_array();

			foreach ($rows as $k => $v)
			{
				$data['widget_id'] = $v['widget_id'];
				$data[ 'template_string' ] = $v[ 'preview_code' ];
				$rows[ $k ][ 'preview_code' ] = $this->show->display('js', 'string', $data, TRUE);
				$rows[ $k ][ 'thumbnail'] = str_replace('{{base_url}}', base_url(), $v['thumbnail']);
			}
		}

		return empty($rows) ? FALSE : sc($rows);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$vars = $this->dbv->clean($data, TBL_WIDGETS);

		if (empty($vars[ 'preview_code' ]))
		{
			$vars[ 'preview_code' ] = $vars[ 'template_code' ];
		}

		$this->db->where($this->id, $data[ $this->id ]);

		if (!$this->db->update(TBL_WIDGETS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'id'       => $data[ 'widget_id' ],
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
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('widgets', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->field_data(TBL_WIDGETS);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim';

			//if this field is a required field, let's set that
			if (in_array($f->name, $required))
			{
				$rule .= '|required';
			}

			switch ($f->name)
			{
				case 'image':

					$rule .= $data[ 'widget_type' ] == 'custom' ? '|required' : '';

					break;
			}

			$this->form_validation->set_rules($f->name, 'lang:' . $f->name, $rule);
		}

		if ($this->form_validation->run())
		{
			//cool! no errors...
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data, FALSE),
			);
		}
		else
		{
			//sorry! got some errors here....
			$row = array( 'error'    => TRUE,
			              'msg_text' => validation_errors(),
			);
		}

		return $row;
	}
}

/* End of file Widgets_model.php */
/* Location: ./application/models/Widgets_model.php */