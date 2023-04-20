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
class Gift_certificates_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'cert_id';

	// ------------------------------------------------------------------------

	/**
	 * Gift_certificates_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('gift_certificates');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @param array $item
	 * @param string $type
	 * @return array
	 */
	public function add_certificate($data = array(), $item = array(), $type = '')
	{
		$certs = array();

		for ($i = 1; $i <= $item['quantity']; $i++)
		{
			//add to order gift certificate table
			$vars = $this->create(format_new_cert($data, $item, $type));

			array_push($certs, $vars['data']);
		}

		return $certs;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function apply_certificate($data = array())
	{

		//check if there is a certificate for the cart already.  if so, then override
		if (!$q = $this->db->where('cart_id', $data['totals']['cart_id'])->get(TBL_CART_TOTALS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		//delete the old one and replace
		if ($q->num_rows() > 0)
		{
			$this->db->where('cart_id', $data['totals']['cart_id']);
			$this->db->where('type', 'gift_certificate');

			if (!$q = $this->db->delete(TBL_CART_TOTALS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//add the new one
		if (!$this->db->insert(TBL_CART_TOTALS, $data['totals']))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return array('success'     => TRUE,
		             'msg_text'    => lang('certificate_applied_successfully'),
		             'certificate' => array_merge($data['certificate_data'], $data['totals']),
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return false|string
	 */
	public function create($data = array())
	{
		$data = $this->dbv->clean($data, TBL_ORDERS_GIFT_CERTIFICATES);

		if (!$q = $this->db->insert(TBL_ORDERS_GIFT_CERTIFICATES, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$data['cert_id'] = $this->db->insert_id();

		return sc(array('success'  => TRUE,
		                'data'     => $data,
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
		if (!$this->db->where($this->id, $id)->delete(TBL_ORDERS_GIFT_CERTIFICATES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return sc(array('success'  => TRUE,
		                'id'       => $id,
		                'msg_text' => lang('record_deleted_successfully')));
	}

	// ------------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function generate_serial()
	{
		do
		{
			$code = generate_serial();
		} while ($p = $this->get_details($code, 'code', FALSE));

		return $code;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param string $col
	 * @param bool $public
	 * @return bool
	 */
	public function get_details($id = '', $col = 'cert_id', $public = FALSE)
	{
		$sql = 'SELECT * ';

		if ($public == FALSE && $col == $this->id)
		{
			$sql .= ',	(SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_ORDERS_GIFT_CERTIFICATES) . ' p
	                        WHERE p.' . $this->id . ' < ' . $id . '
	                        ORDER BY `' . $this->id . '` DESC LIMIT 1)
                        AS prev,
                        (SELECT ' . $this->id . '
	                        FROM ' . $this->db->dbprefix(TBL_ORDERS_GIFT_CERTIFICATES) . ' p
	                        WHERE p.' . $this->id . ' > ' . (int)$id . '
	                        ORDER BY `' . $this->id . '` ASC LIMIT 1)
                        AS next';
		}

		$sql .= ' FROM ' . $this->db->dbprefix(TBL_ORDERS_GIFT_CERTIFICATES) . '
                    WHERE ' . $col . ' = \'' . url_title($id) . '\'';

		if ($public == TRUE)
		{
			$sql .= ' AND status = \'1\'';
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			$row['redemption'] = $this->get_redemption_history($row['cert_id']);
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return bool
	 */
	public function get_redemption_history($id = '')
	{
		$sql = 'SELECT  p.*,
						n.date_purchased,
						n.invoice_number,
					    DATE_FORMAT(n.date_purchased,\'' . $this->config->item('sql_date_format') . '\')
                            AS date_purchased_formatted
					FROM ' . $this->db->dbprefix(TBL_ORDERS_GIFT_CERTIFICATES_HISTORY) . ' p
                    LEFT JOIN ' . $this->db->dbprefix(TBL_INVOICES) . ' n
                        ON p.invoice_id = n.invoice_id
                    WHERE ' . $this->id . ' = \'' . valid_id($id) . '\'';

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? $q->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param int $limit
	 * @return bool|false|string
	 */
	public function get_user_certificates($id = '', $limit = MEMBER_RECORD_LIMIT)
	{
		$sort = $this->config->item(TBL_ORDERS_GIFT_CERTIFICATES, 'db_sort_order');

		//set the cache file
		$cache = __METHOD__ . $id . $limit;
		if (!$row = $this->init->cache($cache, 'public_db_query'))
		{
			$sql = 'SELECT p.*, d.member_id
					FROM ' . $this->db->dbprefix(TBL_ORDERS_GIFT_CERTIFICATES) . ' p
					LEFT JOIN ' . $this->db->dbprefix(TBL_ORDERS) . ' d ON p.order_id = d.order_id
					    WHERE d.member_id = \'' . (int)$id . '\'
					    ORDER BY ' . $sort['column'] . ' ' . $sort['order'] . '
					    LIMIT ' . $limit;

			//run the query
			if (!$q = $this->db->query($sql))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if ($q->num_rows() > 0)
			{
				$row = array(
					'rows'           => $q->result_array(),
					'debug_db_query' => $this->db->last_query(),
				);

				// Save into the cache
				$this->init->save_cache(__METHOD__, $cache, $row, 'public_db_query');
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

		if (!empty($data[$this->id]))
		{
			foreach ($data[$this->id] as $v)
			{
				$vars = array('status' => $data['change-status']);

				if (!$this->db->where($this->id, $v)->update(TBL_ORDERS_GIFT_CERTIFICATES, $vars))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
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
	 * @param array $data
	 * @return array
	 */
	public function remove_certificate($data = array())
	{
		$p = $this->get_details($data['totals']['gift_certificate']['code'], 'code', TRUE);

		if (!empty($p))
		{
			$this->db->where('cart_id', $data['cart_id']);
			$this->db->where('type', 'gift_certificate');

			if (!$q = $this->db->delete(TBL_CART_TOTALS))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}

			if (($p['amount'] + $p['redeemed']) > 0)
			{
				return array('success'          => TRUE,
				             'msg_text'         => lang('certificate_removed_successfully'),
				             'certificate_data' => $p,
				             'totals'           => format_certificates_data($p, $data),
				);
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
		$data = $this->dbv->clean($data, TBL_ORDERS_GIFT_CERTIFICATES);

		if (!$q = $this->db->where($this->id, valid_id($data[$this->id]))->update(TBL_ORDERS_GIFT_CERTIFICATES, $data))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
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
	 * @param string $invoice
	 * @return bool
	 */
	public function update_certificate_redemption($data = array(), $invoice = '')
	{
		$this->db->where('cert_id', $data['gift_certificate']['cert_id']);

		if (!$q = $this->db->get(TBL_ORDERS_GIFT_CERTIFICATES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();

			//set the amount of the used certificate
			$amount = check_certificate_amount($data);

			$vars = array('redeemed' => ($row['redeemed'] + $amount));

			$this->db->where('cert_id', $data['gift_certificate']['cert_id']);

			if (!$this->db->update(TBL_ORDERS_GIFT_CERTIFICATES, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		//add to certificate order history
		if (!empty($invoice))
		{
			$vars = array('cert_id'    => $row['cert_id'],
			              'invoice_id' => $invoice,
			              'amount'     => $amount,
			);

			if (!$this->db->insert(TBL_ORDERS_GIFT_CERTIFICATES_HISTORY, $vars))
			{
				get_error(__FILE__, __METHOD__, __LINE__);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate($data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('gift_certificates', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_ORDERS_GIFT_CERTIFICATES);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim|strip_tags|xss_clean';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			//check if this is an admin or member response
			switch ($f)
			{
				case 'code':

					$rule .= '|min_length[6]';

					break;

				case 'from_email':
				case 'to_email':

					$rule .= '|valid_email';

					break;

				case 'amount':

					$rule .= '|numeric';

					break;
			}

			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		if ($this->form_validation->run())
		{
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data),
			);
		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => validation_errors(),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param array $data
	 * @return array
	 */
	public function validate_certificate($id = '', $data = array())
	{
		$p = $this->get_details($id, 'code', TRUE);

		if (!empty($p))
		{
			if (($p['amount'] + $p['redeemed']) > 0)
			{
				return array('success'          => TRUE,
				             'certificate_data' => $p,
				             'totals'           => format_certificates_data($p, $data),
				);
			}
		}

		return array('type'     => 'error',
		             'msg_text' => lang('invalid_certificate'),
		);
	}
}

/* End of file Gift_certificates_model.php */
/* Location: ./application/models/Gift_certificates_model.php */