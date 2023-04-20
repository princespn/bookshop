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
 * @package	eCommerce Suite
 * @author	JROX Technologies, Inc.
 * @copyright	Copyright (c) 2007 - 2019, JROX Technologies, Inc. (https://www.jrox.com/)
 * @link	https://www.jrox.com
 * @filesource
 */

class Measurements_model extends CI_Model {

	/**
	 * @var string
	 */
	protected $table = TBL_MEASUREMENTS;

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $id = 'measure_id';

	// ------------------------------------------------------------------------

	/**
	 * @param bool $form
	 * @return array|bool|false
	 */
	public function get_measurements($form = FALSE)
    {
        $cache  = __METHOD__.$form;

        if ( ! $row = $this->init->cache($cache, 'db_query'))
        {
            if (!$q = $this->db->order_by('sort_order', 'ASC')->get(TBL_MEASUREMENTS))
                get_error(__FILE__,__METHOD__, __LINE__);

            if ($q->num_rows() > 0)
            {
                $row = $form == TRUE ? format_array($q->result_array(), 'measure_id', 'name') : $q->result_array();

                // Save into the cache
                $this->init->save_cache(__METHOD__, $cache, $row, 'db_query');
            }
        }

        return empty($row) ? FALSE : $row;
    }

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $current
	 * @return bool|false|string
	 */
	public function update($data = array(), $current = array())
	{
		$a = array();

		//check if the list is already in the table
		if (!empty($data['measurements']))
		{
			foreach ($data['measurements'] as $v)
			{
				if (!empty($v[$this->id]))
				{
					$row = $this->dbv->update(TBL_MEASUREMENTS, $this->id, $v);

					array_push($a, $v[$this->id]);
				}
				else
				{
					$row = $this->dbv->create(TBL_MEASUREMENTS, $v);
				}
			}
		}

		//let's delete all the attributes not in the current one
		if (!empty($current))
		{
			foreach ($current as $v)
			{
				if (!in_array($v[$this->id], $a))
				{
					$this->dbv->delete(TBL_MEASUREMENTS, $this->id, $v[$this->id]);
				}
			}
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Measurements_model.php */
/* Location: ./application/models/Measurements_model.php */