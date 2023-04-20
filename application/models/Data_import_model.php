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
class Data_import_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'module_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $options
	 * @param bool $active
	 * @return bool|false|string
	 */
	public function get_rows($options = '', $active = TRUE)
	{
		$sort = $this->config->item(TBL_MODULES, 'db_sort_order');

		$options['sort_order'] = !empty($options['query']['order']) ? $options['query']['order'] : $sort['order'];
		$options['sort_column'] = !empty($options['query']['column']) ? $options['query']['column'] : $sort['column'];

		if ($active == TRUE)
		{
			$this->db->where('module_status', '1');
		}

		$this->db->where('module_type', 'data_import');
		$this->db->order_by($options['sort_column'], $options['sort_order']);

		if (!$q = $this->db->get(TBL_MODULES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = array(
				'values'         => $q->result_array(),
				'total'          => $q->num_rows(),
				'success'        => TRUE,
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Data_import_model.php */
/* Location: ./application/models/Data_import_model.php */