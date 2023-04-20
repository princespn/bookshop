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
class Admin_groups_model extends CI_Model
{
	/**
	 *  Default ID for the admin_groups table
	 *
	 * @var string
	 */
	protected $id = 'admin_group_id';

	/**
	 * Create Admin Group
	 *
	 * Create a new admin group
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function create($data = array())
	{
		$vars = array( 'group_name'  => $data[ 'group_name' ],
		               'permissions' => !empty($data[ 'permissions' ]) ? serialize($data[ 'permissions' ]) : '' );

		if (!$this->db->insert(TBL_ADMIN_GROUPS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $this->db->insert_id(),
			'data' => $vars,
			'msg_text' => $data[ 'group_name' ] . ' ' . lang('record_created_successfully'),
			'success'  => TRUE,
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Admin Groups
	 *
	 * Query the table for all the admin groups
	 *
	 * @param bool $form
	 * @return array|bool|string
	 */
	public function get_admin_groups($form = FALSE)
	{
		if (!$q = $this->db->get('admin_groups'))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			return $form == TRUE ?  format_array($q->result_array(), 'admin_group_id', 'group_name') : sc($q->result_array());
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Admin Group Details
	 *
	 * Get admin group details
	 *
	 * @param string $id
	 * @return bool|string
	 */
	public function get_details($id = '')
	{
		$this->db->where($this->id, $id);

		if (!$q = $this->db->get(TBL_ADMIN_GROUPS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			$row[ 'permissions' ] = unserialize(($row[ 'permissions' ]));
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Update Admin Group
	 *
	 * Update the details for the specified admin group ID
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function update($data = array())
	{
		$vars = array( 'group_name'  => $data[ 'group_name' ],
		               'permissions' => !empty($data[ 'permissions' ]) ? serialize($data[ 'permissions' ]) : '' );

		$this->db->where($this->id, $data[ $this->id ]);

		if (!$this->db->update(TBL_ADMIN_GROUPS, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'id'       => $data[ $this->id ],
			'data' => $vars,
			'msg_text' => $data[ 'group_name' ] . ' ' . lang('record_updated_successfully'),
			'success'  => TRUE, 1
		);

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate data
	 *
	 * Validate the permissions data submitted by the form
	 *
	 * @param array $data
	 * @return bool|string
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('group_name', 'lang:group_name', 'trim|required|min_length[2]|max_length[255]');

		$perms = array('view','create', 'update', 'delete');

		foreach ($perms as $p)
		{
			if (!empty($data[ 'permissions'][$p] ))
			{
				foreach ($data[ 'permissions'][$p] as $v)
				{
					$this->form_validation->set_rules($v, 'lang:permissions', 'trim|xss_clean');
				}
			}
		}

		if ($this->form_validation->run())
		{
			$row = array( 'success' => TRUE,
			              'data'    => $this->dbv->validated($data),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Admin_groups_model.php */
/* Location: ./application/models/Admin_groups_model.php */