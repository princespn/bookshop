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
class Updates_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * @param int $decimal
	 * @return array
	 */
	public function change_decimal_types($decimal = 2)
	{
		//table => column
		$tables = array('affiliate_commission_rules'       => array('sale_amount', 'bonus_amount'),
		                'cart_items'                       => array('unit_price', 'discount_amount'),
		                'cart_totals'                      => array('amount'),
		                'invoice_items'                    => array('unit_price'),
		                'invoice_payments'                 => array('amount', 'fee'),
		                'invoice_totals'                   => array('amount'),
		                'members_subscriptions'            => array('product_price'),
		                'module_shipping_flat_rate'        => array('amount'),
		                'module_shipping_free_shipping'    => array('amount'),
		                'module_shipping_percentage'       => array('amount'),
		                'module_shipping_per_item'         => array('amount'),
		                'module_shipping_unit_based'       => array('min_amount', 'max_amount', 'amount'),
		                'orders_gift_certificates_history' => array('amount'),
		                'orders_gift_certificates'         => array('amount', 'redeemed'),
		                'orders_items'                     => array('unit_price', 'discount_amount'),
		                'orders_shipping'                  => array('rate'),
		                'products_to_aff_groups'           => array('commission_level_1',
		                                                            'commission_level_2',
		                                                            'commission_level_3',
		                                                            'commission_level_4',
		                                                            'commission_level_5',
		                                                            'commission_level_6',
		                                                            'commission_level_7',
		                                                            'commission_level_8',
		                                                            'commission_level_9',
		                                                            'commission_level10'),
		                'products_to_attributes_values'    => array('price'),
		                'products_to_pricing'              => array('amount', 'initial_amount'),
		                'promotional_items'                => array('promo_amount'),
		                'tax_rates'                        => array('tax_amount'),
		                'affiliate_commissions'            => array('commission_amount', 'sale_amount', 'fee'),
		                'affiliate_groups'                 => array('fee_amount'),
		                'coupons'                          => array('coupon_amount', 'minimum_order'),
		                'affiliate_payments'               => array('payment_amount'),
		                'invoices'                         => array('total'),
		                'orders'                           => array('order_total'),
		                'products'                         => array('product_price', 'product_sale_price', 'shipping_cost'),
		);

		foreach ($tables as $t => $c)
		{
			//go through each table as check the column
			$t = 'jrox_' . $t;
			if ($this->db->table_exists($t))
			{
				$fields = $this->db->list_fields($t);

				foreach ($fields as $f)
				{
					if (in_array($f, $c))
					{
						$default = number_format('0', $decimal);
						$sql = "ALTER TABLE `" . $t . "` CHANGE `" . $f . "` `" . $f . "` DECIMAL(" . DEFAULT_COLUMN_DECIMAL_LENGTH . ", " . $decimal . ") NOT NULL DEFAULT '" . $default . "';";

						if (!$q = $this->db->query($sql))
						{
							get_error(__FILE__, __METHOD__, __LINE__);
						}
					}
				}
			}
		}

		return array('success' => TRUE, 'msg_text' => lang('system_updated_successfully'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $path
	 * @return mixed|string
	 */
	public function get_updates($path = '')
	{
		if (!config_enabled('sts_update_use_server_path'))
		{
			$dest = config_item('sts_update_file_path');
		}
		else
		{
			//download updates from server
			$file_name = config_item('jrox_update_file_name') . '.zip';
			$url = $this->config->slash_item('jrox_update_server') .  $file_name;

			$dest = rtrim($path, '/') . '/jrox_' . $file_name;

			//try downloading it from the server first
			$data = use_curl($url, '', FALSE);

			if ($data)
			{
				if ($file = fopen($dest, "w+"))
				{
					fputs($file, $data);
					fclose($file);
				}
			}
		}

		return $dest;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function run_db_updates($data = array())
	{
		//backup data first...
		if (config_enabled('backup_db_during_update'))
		{
			$row = $this->backup->backup_db();

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text']));

		}

		$files = directory_map('./application/updates/sql/');

		$row = array('msg_text'     => '',
		             'total_number' => 0);

		if (!empty($files))
		{
			$languages = get_languages(FALSE, FALSE);

			foreach ($files as $file)
			{
				if (!is_file_type($file, 'php'))
				{
					continue;
				}

				if (file_exists(APPPATH . '/updates/sql/' . $file))
				{
					@require_once APPPATH . '/updates/sql/' . $file;

					if (!empty($updates) && is_array($updates))
					{
						foreach ($updates as $v)
						{
							switch ($v['update_type'])
							{
								case 'INSERT':

									//run the check first
									$sql = $v['check_sql'];

									if (!$check = $this->db->query($sql))
									{
										get_error(__FILE__, __METHOD__, __LINE__);
									}

									if ($check->num_rows() < 1)
									{
										//run the update
										$sql_insert = $v['update_sql'];

										foreach ($this->data as $a => $b)
										{
											if (!is_array($v))
											{
												$sql_insert = str_replace('{{' . $b . '}}', addslashes($b), $sql_insert);
											}
										}

										$q = $this->db->query($sql_insert);

										if ($q)
										{
											$insert_id = $this->db->insert_id();

											//if there is other sql queries needed with the ID, run it now.
											if (!empty($v['supporting_sql']))
											{
												$second_insert = $v['supporting_sql'];

												if (!empty($v['set_lang']))
												{
													foreach ($languages as $lang)
													{
														foreach ($this->data as $a => $b)
														{
															if (!is_array($b))
															{
																$second_insert = str_replace('{{mysql_insert_id}}', $insert_id, $second_insert);
																$second_insert = str_replace('{{language_id}}', $lang['language_id'], $second_insert);
																$second_insert = str_replace('{{' . $b . '}}', addslashes($b), $second_insert);
															}
														}

														$this->db->query($second_insert);
													}
												}
												else
												{
													foreach ($this->data as $a => $b)
													{
														if (!is_array($b))
														{
															$sql_insert = str_replace('{{mysql_insert_id}}', $insert_id, $second_insert);
															$sql_insert = str_replace('{{' . $b . '}}', addslashes($b), $second_insert);
														}
													}

													$this->db->query($second_insert);
												}
											}

											$row['success'] = TRUE;
											$row['total_number']++;
										}
									}

									break;

								case 'TABLE':

									$run = TRUE;

									if ($this->db->table_exists($v['table_name']))
									{
										$run = FALSE;
									}

									if ($run == TRUE)
									{
										//run the update
										$sql_insert = $v['update_sql'];

										if ($this->db->query($sql_insert))
										{
											$row['success'] = TRUE;
											$row['total_number']++;
										}
									}

									break;

								case 'COLUMN':

									$run = TRUE;

									if ($this->db->field_exists($v['column_name'], $v['table_name']))
									{
										$run = FALSE;
									}

									if ($run == TRUE)
									{
										//run the update
										$sql_insert = $v['update_sql'];

										if ($this->db->query($sql_insert))
										{
											$row['success'] = TRUE;
											$row['total_number']++;
										}
									}

									break;

								case 'COLUMN_TYPE':

									//run the check first
									$sql = $v['check_sql'];

									$check = $this->db->query($sql);
									$run = TRUE;

									foreach ($check->result_array() as $row)
									{
										//echo '<pre>'; print_r($row);
										foreach ($row as $k => $value)
										{
											//echo $k . ' - ' . $value . '<br />';
											if ($k == 'Field')
											{
												if ($value == $v['column_name'])
												{
													if ($v['column_type'] == $row['Type'])
													{
														$run = FALSE;
													}
												}
											}
										}
									}

									if ($run == TRUE)
									{
										//run the update
										$sql_insert = $v['update_sql'];

										if ($this->db->query($sql_insert))
										{
											$row['success'] = TRUE;
											$row['total_number']++;
										}
									}

									break;

								case 'CONFIG':

									$this->db->where('settings_key', $v['config_key']);
									$q = $this->db->get('settings');

									if ($q->num_rows() < 1)
									{
										$q = !empty($v['update_sql']) ?  $this->db->query($v['update_sql']) : $this->db->insert('settings', $v['update_vars']);

										if ($q)
										{
											$row['success'] = TRUE;
										}
									}

									break;

								case 'MODULE':

									$this->db->where('settings_key', $v['config_key']);
									$q = $this->db->get('settings');

									if ($q->num_rows() < 1)
									{
										//get the id
										$this->db->where('module_type', $v['module_type']);
										$this->db->where('module_file_name', $v['module_file_name']);
										$q = $this->db->get('modules');
										if ($q->num_rows() > 0)
										{
											$mod_info = $q->row_array();

											$update_sql = str_replace('{{module_id}}', $mod_info['module_id'], $v['update_sql']);

											if ($this->db->query($update_sql))
											{
												$row['success'] = TRUE;
												$row['total_number']++;
											}
										}
									}

									break;

								case 'WIDGET':

									if (!empty($v['update_data']) && is_array($v['update_data']))
									{
										$vars = $v['update_data'];

										//check if widget is alrady installed
										if (!$q = $this->db->where('widget_name', $vars['widget_name'])->get(TBL_WIDGETS))
										{
											get_error(__FILE__, __METHOD__, __LINE__);
										}

										if ($q->num_rows() < 1)
										{
											$vars['widget_type'] = 'section';
											$vars['thumbnail'] = empty($vars['thumbnail']) ? '//placehold.it/300x130?text=' . urlencode($vars['widget_name']) : $vars['thumbnail'];

											$this->db->insert(TBL_WIDGETS, $vars);
										}
									}

									break;

								default:

									//run the update
									$sql_update = $v['update_sql'];

									if ($this->db->query($sql_update))
									{
										$row['success'] = TRUE;
										$row['total_number']++;
									}

									break;
							}
						}

						//lets try and delete the file
						$row['msg_text'] .= $file . ' ' . lang('update file ran successfully');

						if (defined('DELETE_UPDATE_FILE_AFTER_INSTALL'))
						{
							if (@unlink(DEFAULT_FILE_UPDATES_UPLOAD_PATH . '/sql/' . $file) == FALSE)
							{
								$row['msg_text'] = lang('could_not_delete_file') . ' ' . $file;
							}
						}

						$row['success'] = TRUE;
					}
				}
			}
		}

		return empty($row['success']) ? FALSE : sc($row);
	}
}

/* End of file Updates_model.php */
/* Location: ./application/models/Updates_model.php */