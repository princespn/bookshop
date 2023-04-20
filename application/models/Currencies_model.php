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
class Currencies_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $table = TBL_CURRENCIES;

	// ------------------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $id = 'currency_id';

	// ------------------------------------------------------------------------

	/**
	 * @param string $a
	 * @return mixed
	 */
	public function get_default_currency($a = 'USD')
	{
		if (!$currency = $this->init->cache('currency_options', 'settings'))
		{
			$this->db->where('code', $a);
			$query = $this->db->get($this->table);

			$b = $query->row_array();
			$b['symbol_left'] = html_entity_decode($b['symbol_left'], ENT_COMPAT, $this->config->item('charset'));

			$currency = $b;

			// Save into the cache
			$this->init->save_cache('currency', 'currency_options', $currency, 'settings');
		}

		return $currency;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function convert_currencies($data = array())
	{
		//update the default first
		$new_value = array('value' => '1.00');

		$this->db->where('currency_id', $data['currency_id']);
		$this->db->update('currencies', $new_value);

		$url = config_item('currency_rate_server');

		$resp = use_curl($url);

		$pattern = "{<Cube\s*currency='(\w*)'\s*rate='([\d\.]*)'/>}is";
		preg_match_all($pattern, $resp, $xml_rates);
		array_shift($xml_rates);

		$exchange_rate['EUR'] = 1;
		for ($i = 0; $i < count($xml_rates[0]); $i++)
		{
			$exchange_rate[ $xml_rates[0][ $i ] ] = $xml_rates[1][ $i ];
		}

		if (!$q = $this->db->get(TBL_CURRENCIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $row)
			{
				$a = $row['code'];
				$b = $data['code'];

				if (!empty($exchange_rate[ $b ]) && !empty($exchange_rate[ $a ]))
				{
					$v = (1 / $exchange_rate[ $b ]) * $exchange_rate[ $a ];

					$new_value = array('value' => $v);

					$this->db->where($this->id, $row[$this->id]);
					if (!$this->db->update('currencies', $new_value))
					{
						get_error(__FILE__, __METHOD__, __LINE__);
					}
				}

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
	 * @param string $code
	 * @return bool|false|string
	 */
	public function switch_currency($code = '')
	{
		if (!$q = $this->db->where('code', url_title(uri(2)))->get(TBL_CURRENCIES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Currencies_model.php */
/* Location: ./application/models/Currencies_model.php */