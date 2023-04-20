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
class Members_notes_model extends CI_Model
{

	/**
	 * @var string
	 */
	protected $table = TBL_MEMBERS_NOTES;

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $id = 'note_id';

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $member_id = 'member_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array|bool
	 */
	public function create($id = '', $data = array())
    {
        $data = $this->dbv->clean($data, $this->table, TRUE);

        if (!$this->db->insert($this->table, $data)) {
            get_error(__FILE__, __METHOD__, __LINE__);
        }

        $row = array(
            'id' => $this->db->insert_id(),
            'msg_text' => lang('record_added_successfully'),
            'success' => TRUE,
        );

        return empty($row) ? FALSE : $row;
    }

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function delete($id = '')
	{
		if (!$this->db->where($this->id, $id)->delete($this->table)) {
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row['msg_text'] = lang('record_deleted_successfully');

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $limit
	 * @return bool|false|string
	 */
	public function get_rows($id = '', $limit = TPL_AJAX_LIMIT)
	{
		if (!$q = $this->db->where($this->member_id, $id)->order_by($this->id, 'DESC')->limit($limit)->get($this->table)) {
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function update($id = '', $data = array())
    {
        if (!$q = $this->db->where($this->id, $id)->update(TBL_MEMBERS_NOTES, array('note' => strip_tags($data['value'])))) {
            get_error(__FILE__, __METHOD__, __LINE__);
        }

        return array(
            'id' => $id,
            'msg_text' => lang('system_updated_successfully'),
            'success' => TRUE,
            'row' => $data,
        );
    }
}

/* End of file Members_notes_model.php */
/* Location: ./application/models/Members_notes_model.php */