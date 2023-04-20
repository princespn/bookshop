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
class Network_marketing_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * @param string $mem
	 * @param bool $admin
	 * @param string $api
	 * @return string
	 */
	public function check_downline_details($mem = '', $admin = FALSE, $api = '')
	{
		$details = '';
		$link = 'javascript:void(0)';

		if ($admin == TRUE)
		{
			$link = admin_url('affiliate_downline/view/' . $mem['member_id']);
		}
		else
		{
			if (config_enabled('sts_affiliate_show_downline_email'))
			{
				$details .= '<br /><span class="downline-email">' . $mem['primary_email'] . '</span>';
			}
		}

		$data = '<br />' . i('fa fa-arrow-down') . '<br /><a href="' . $link . '">' . i('fa fa-user fa-4x') . '</a>';
		$data .= '<br /><a href="' . $link . '"><small>' . $this->check_show_name($mem) . '</a>' . $details . '</small>';

		return $data;

	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function check_upline($data = array())
	{
		return $this->get_upline($data['member_id']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $admin
	 * @param string $type
	 * @return mixed
	 */
	public function generate_downline($id = '0', $admin = FALSE, $type = 'table')
	{
		//set the cache file
		$cache = __METHOD__ . $id . $admin . $type;
		if (!$sdata = $this->init->cache($cache, 'downline_db_query'))
		{
			//lets get the first row of referrals first for level 1 and level 2

			$sdata['results'] = $type == 'table' ? '' : array();
			$sdata['levels'] = '';
			$total = '0';

			$sql = 'SELECT m.*,
                  c.sponsor_id
                  FROM ' . $this->db->dbprefix('members') . ' m
                  LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' c
                  ON m.member_id = c.member_id
				    WHERE sponsor_id = \'' . $id . '\'
				    AND m.is_affiliate != \'0\'';

			if ($admin == FALSE && $this->config->item('sts_affiliate_show_active_downline_users') == '1')
			{
				$sql .= ' AND m.status = \'1\'';
			}

			$first_row = $this->db->query($sql);

			if ($first_row->num_rows() > 0)
			{
				$total = $first_row->num_rows();

				if ($this->config->item('sts_affiliate_commission_levels') < 3)
				{
					$sdata['levels'] = $this->config->item('sts_affiliate_commission_levels');
				}
				else
				{
					$sdata['levels'] = $this->config->item('sts_affiliate_commission_levels_restrict_view');
				}

				//generate the table for commission levels less than 3
				if ($sdata['levels'] < 3)
				{
					$this->original_array = $first_row->result_array();

					if ($type == 'table') //return a formatted table
					{
						foreach ($first_row->result_array() as $value)
						{
							$sdata['results'] .= '<td align="center" class="downline_top"><table class="table">
										  <tr>
											<td align="center"><div class="downline-box">' .
								$this->check_downline_details($value, $admin)
								. '</div></td>
										  </tr>
										</table></td>';
						}
					}
					else //just return an array
					{
						$sdata['results'] = $first_row->result_array();
					}
				}
				else //get the entire dowline over 3 levels
				{
					//get all users first
					$this->original_array = $this->get_downline_users($id, $admin);

					if ($sdata['levels'] >= 3)
					{
						foreach ($first_row->result_array() as $value)
						{

							$query3 = $this->downline_calc($this->original_array, $value['member_id']);
							$total3 = count($query3);

							if ($type == 'table')
							{
								$sdata['results'] .= '<td align="center" class="downline_top" valign="top"><table>
									<tr class="level-div">
									<td align="center" valign="top">
										<div class="downline-box">' .
									$this->check_downline_details($value, $admin)
									. '</div></td>
									</tr>';
							}
							else
							{
								array_push($sdata['results'], $value);
							}

							if ($total3 > 0)
							{
								$total = $total + $total3;
								if ($type == 'table')
								{
									$sdata['results'] .= '<tr>
											<td align="center" valign="top"><table>
										  <tr class="level-div">';
								}
								$level_4 = array();
								foreach ($query3 as $row3)
								{
									if ($type == 'table')
									{
										$sdata['results'] .= '<td align="center" class="downline_top" valign="top">
										<div class="downline-box">' .
											$this->check_downline_details($row3, $admin)
											. '</div>
										</td>';
									}
									else
									{
										array_push($sdata['results'], $row3);
									}

									array_push($level_4, $row3['member_id']);
								}

								if ($sdata['levels'] >= 4)
								{
									if ($type == 'table')
									{
										$sdata['results'] .= '<tr>';
									}

									foreach ($level_4 as $value4)
									{
										if ($type == 'table')
										{
											$sdata['results'] .= '<td align="center" valign="top"><table>
													  <tr class="level-div">';
										}

										$query4 = $this->downline_calc($this->original_array, $value4);

										$level_5 = array();

										$total4 = count($query4);

										if ($total4 > 0)
										{
											$total = $total + $total4;

											foreach ($query4 as $row4)
											{
												if ($type == 'table')
												{
													$sdata['results'] .= '<td valign="top" align="center" class="downline_top">
													<div class="downline-box">' .
														$this->check_downline_details($row4, $admin)
														. '</div>
													</td>';
												}
												else
												{
													array_push($sdata['results'], $row4);
												}
												array_push($level_5, $row4['member_id']);
											}

											if ($sdata['levels'] >= 5)
											{
												if ($type == 'table')
												{
													$sdata['results'] .= '<tr>';
												}

												foreach ($level_5 as $value5)
												{
													if ($type == 'table')
													{
														$sdata['results'] .= '<td align="center" valign="top">
														<table>
													  	<tr class="level-div">';
													}

													$query5 = $this->downline_calc($this->original_array, $value5);

													$level_6 = array();

													$total5 = count($query5);

													if ($total5 > 0)
													{
														$total = $total + $total5;

														foreach ($query5 as $row5)
														{
															if ($type == 'table')
															{
																$sdata['results'] .= '<td valign="top" align="center" class="downline_top">
																<div class="downline-box">' .
																	$this->check_downline_details($row5, $admin)
																	. '</div>
																</td>';
															}
															else
															{
																array_push($sdata['results'], $row5);
															}

															array_push($level_6, $row5['member_id']);
														}

														if ($sdata['levels'] >= 6)
														{
															if ($type == 'table')
															{
																$sdata['results'] .= '<tr>';
															}

															foreach ($level_6 as $value6)
															{
																if ($type == 'table')
																{
																	$sdata['results'] .= '<td align="center" valign="top">
																	<table>
																  	<tr class="level-div">';
																}

																$query6 = $this->downline_calc($this->original_array, $value6);

																$level_7 = array();

																$total6 = count($query6);

																if ($total6 > 0)
																{
																	$total = $total + $total6;

																	foreach ($query6 as $row6)
																	{
																		if ($type == 'table')
																		{
																			$sdata['results'] .= '<td valign="top" align="center" class="downline_top">
																			<div class="downline-box">' .
																				$this->check_downline_details($row6, $admin)
																				. '</div>
																			</td>';
																		}
																		else
																		{
																			array_push($sdata['results'], $row6);
																		}

																		array_push($level_7, $row6['member_id']);
																	}

																	if ($sdata['levels'] >= 7)
																	{
																		if ($type == 'table')
																		{
																			$sdata['results'] .= '<tr>';
																		}

																		foreach ($level_7 as $value7)
																		{

																			if ($type == 'table')
																			{
																				$sdata['results'] .= '<td align="center" valign="top">
																				<table>
																			  	<tr class="level-div">';
																			}

																			$query7 = $this->downline_calc($this->original_array, $value7);

																			$level_8 = array();

																			$total7 = count($query7);

																			if ($total7 > 0)
																			{
																				$total = $total + $total7;
																				foreach ($query7 as $row7)
																				{
																					if ($type == 'table')
																					{
																						$sdata['results'] .= '<td valign="top" align="center" class="downline_top">
																						<div class="downline-box">' .
																							$this->check_downline_details($row7, $admin)
																							. '</div></td>';
																					}
																					else
																					{
																						array_push($sdata['results'], $row7);
																					}

																					array_push($level_8, $row7['member_id']);
																				}

																				if ($sdata['levels'] >= 8)
																				{
																					if ($type == 'table')
																					{
																						$sdata['results'] .= '<tr>';
																					}

																					foreach ($level_8 as $value8)
																					{
																						if ($type == 'table')
																						{
																							$sdata['results'] .= '<td align="center" valign="top">
																							<table>
																						  	<tr class="level-div">';
																						}

																						$query8 = $this->downline_calc($this->original_array, $value8);

																						$level_9 = array();

																						$total8 = count($query8);

																						if ($total8 > 0)
																						{
																							$total = $total + $total8;
																							foreach ($query8 as $row8)
																							{
																								if ($type == 'table')
																								{
																									$sdata['results'] .= '<td valign="top" align="center" class="downline_top">
																									<div class="downline-box">' .
																										$this->check_downline_details($row8, $admin)
																										. '</div></td>';
																								}
																								else
																								{
																									array_push($sdata['results'], $row8);
																								}

																								array_push($level_9, $row8['member_id']);
																							}

																							if ($sdata['levels'] >= 9)
																							{
																								if ($type == 'table')
																								{
																									$sdata['results'] .= '<tr>';
																								}

																								foreach ($level_9 as $value9)
																								{
																									if ($type == 'table')
																									{
																										$sdata['results'] .= '<td align="center" valign="top"><table>
																									 	<tr class="level-div">';
																									}
																									$query9 = $this->downline_calc($this->original_array, $value9);

																									$level_10 = array();
																									$total9 = count($query9);

																									if ($total9 > 0)
																									{
																										$total = $total + $total9;
																										foreach ($query9 as $row9)
																										{
																											if ($type == 'table')
																											{
																												$sdata['results'] .= '<td valign="top" align="center" class="downline_top"><div class="downline-box">' .
																													$this->check_downline_details($row9, $admin)
																													. '</div></td>';
																											}
																											else
																											{
																												array_push($sdata['results'], $row9);
																											}

																											array_push($level_10, $row9['member_id']);
																										}

																										if ($sdata['levels'] == 10)
																										{
																											if ($type == 'table')
																											{
																												$sdata['results'] .= '<tr>';
																											}

																											foreach ($level_10 as $value10)
																											{
																												if ($type == 'table')
																												{
																													$sdata['results'] .= '<td align="center" valign="top"><table>
																												  <tr class="level-div">';
																												}

																												$query10 = $this->downline_calc($this->original_array, $value10);

																												$total10 = count($query10);

																												if ($total10 > 0)
																												{
																													$total = $total + $total10;
																													foreach ($query10 as $row10)
																													{
																														if ($type == 'table')
																														{
																															$sdata['results'] .= '<td valign="top" align="center" class="downline_top"><div class="downline-box">' .
																																$this->check_downline_details($row10, $admin)
																																. '</div></td>';
																														}
																														else
																														{
																															array_push($sdata['results'], $row10);
																														}
																													}
																												}
																												else
																												{
																													if ($type == 'table')
																													{
																														$sdata['results'] .= '<td valign="top" align="center">&nbsp;</td>';
																													}
																												}

																												if ($type == 'table')
																												{
																													$sdata['results'] .= '</tr>
																															</table></td>';
																												}
																											}
																										}
																									}
																									else
																									{
																										if ($type == 'table')
																										{
																											$sdata['results'] .= '<td valign="top" align="center">&nbsp;</td>';
																										}
																									}

																									if ($type == 'table')
																									{
																										$sdata['results'] .= '</tr>
																												</table></td>';
																									}
																								}
																							}
																						}
																						else
																						{
																							if ($type == 'table')
																							{
																								$sdata['results'] .= '<td valign="top" align="center">&nbsp;</td>';
																							}
																						}

																						if ($type == 'table')
																						{
																							$sdata['results'] .= '</tr>
																									</table></td>';
																						}
																					}
																				}
																			}
																			else
																			{
																				if ($type == 'table')
																				{
																					$sdata['results'] .= '<td valign="top" align="center">&nbsp;</td>';
																				}
																			}

																			if ($type == 'table')
																			{
																				$sdata['results'] .= '</tr>
																						</table></td>';
																			}
																		}
																	}
																}
																else
																{
																	if ($type == 'table')
																	{
																		$sdata['results'] .= '<td valign="top" align="center">&nbsp;</td>';
																	}
																}
																if ($type == 'table')
																{
																	$sdata['results'] .= '</tr>
																	</table></td>';
																}
															}
														}
													}
													else
													{
														if ($type == 'table')
														{
															$sdata['results'] .= '<td valign="top" align="center">&nbsp;</td>';
														}
													}
													if ($type == 'table')
													{
														$sdata['results'] .= '</tr>
														</table></td>';
													}
												}
											}
										}
										else
										{
											if ($type == 'table')
											{
												$sdata['results'] .= '<td valign="top" align="center">&nbsp;</td>';
											}
										}

										if ($type == 'table')
										{
											$sdata['results'] .= '</tr>
														</table></td>';
										}
									}

								}
								if ($type == 'table')
								{
									$sdata['results'] .= '</tr></table></td>
										  </tr>';
								}
							}

							if ($type == 'table')
							{
								$sdata['results'] .= '</table></td>';
							}
						}
					}
				}

			}
			else
			{
				if ($type == 'table')
				{
					$sdata['results'] = '<td align="center">' . $this->lang->line('no_downline_members_found') . '</td>';
				}
			}

			$sdata['total_users'] = $total;

			// Save into the cache
			$this->init->save_cache(__METHOD__, $cache, $sdata, 'downline_db_query');
		}

		return $sdata;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $sponsor
	 * @return string
	 */
	public function get_downline_sponsor($sponsor = '')
	{
		if (config_enabled('sts_affiliate_enable_mlm_forced_matrix'))
		{
			$sponsor = $this->calc_sponsor_matrix($sponsor, '1');
		}

		return $sponsor;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $sponsor
	 * @return array
	 */
	public function get_direct_downline($sponsor = '')
	{
		$this->db->select('member_id as mid');
		$this->db->where('sponsor_id', $sponsor);
		/*
		$this->db->join(TBL_MEMBERS,
			$this->db->dbprefix(TBL_MEMBERS) . '.member_id = ' .
			$this->db->dbprefix(TBL_MEMBERS_SPONSORS) . '.member_id', 'left');
		*/
		if (config_enabled('sts_affiliate_enable_mlm_forced_matrix'))
		{
			$this->db->limit(config_item('sts_affiliate_mlm_matrix_width'));
		}

		if (!$q = $this->db->get(TBL_MEMBERS_SPONSORS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			return $q->result_array();
		}

		return array();
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate_email($data = array())
	{
		//now check for form flood
		if ($this->sec->check_flood_control('downline_email', 'check', sess('member_id')))
		{
			$this->form_validation->set_data($data);

			$this->form_validation->set_rules('message', 'lang:message', 'trim|strip_tags|required|xss_clean|min_length[25]|max_length[500]');

			if ($this->form_validation->run())
			{
				$row = array('success' => TRUE,
				             'data'    => $this->dbv->validated($data, FALSE),
				);

				//run check for recaptcha
				if (config_enabled('sts_form_enable_captcha'))
				{
					$this->form_validation->reset_validation();

					$this->form_validation->set_data($data);

					$this->form_validation->set_rules(
						CAPTCHA_FIELD, 'lang:captcha',
						array(
							'required',
							array('check_captcha', array($this->dbv, 'check_captcha')),
						)
					);

					$this->form_validation->set_message('check_captcha', lang('invalid_security_captcha'));

					if (!$this->form_validation->run())
					{
						//sorry! got some errors here....
						$row = array('error'        => TRUE,
						             'msg_text'     => validation_errors(),
						             'error_fields' => generate_error_fields($data),
						);
					}
				}
			}
			else
			{
				//sorry! got some errors here....
				$row = array('error'    => TRUE,
				             'msg_text' => validation_errors(),
				);
			}

		}
		else
		{
			$row = array('error'    => TRUE,
			             'msg_text' => lang('maximum_form_submission_reached') . '. ' . lang('please_wait'),
			);

		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $mem_array
	 * @return mixed|string
	 */
	protected function check_show_name($mem_array = array())
	{
		if ($this->config->item('show_view_downline_usernames'))
		{
			$id = $this->config->item('show_view_downline_usernames');

			return $mem_array[$id];
		}

		return $mem_array['fname'] . ' ' . $mem_array['lname'];
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @param bool $admin
	 * @return bool
	 */
	protected function get_downline_users($id = '', $admin = FALSE)
	{

		$sql = 'SELECT m.*,
                  c.sponsor_id
                  FROM ' . $this->db->dbprefix('members') . ' m
                  LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' c
                  ON m.member_id = c.member_id
                    WHERE m.member_id != \'' . $id . '\'
                    AND c.sponsor_id > \'0\'
				    AND m.is_affiliate != \'0\'';

		if ($admin == FALSE && $this->config->item('sts_affiliate_show_active_downline_users') == '1')
		{
			$sql .= ' AND status = \'1\'';
		}

		$query = $this->db->query($sql);

		return $query->num_rows() > 0 ? $query->result_array() : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $arr
	 * @param string $item
	 * @return array
	 */
	protected function downline_calc($arr = '', $item = '')
	{
		$result = array();

		for ($i = 0; $i <= count($arr); $i++)
		{
			if (!empty($arr[$i]))
			{
				if (strcmp($arr[$i]['sponsor_id'], $item) == 0)
				{
					array_push($result, $arr[$i]);
				}
			}
		}

		return ($result);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $id
	 * @return array
	 */
	private function get_upline($id = '')
	{
		$rows = array();
		$current_sponsor = $id;

		$levels = config_option('max_commission_levels') == 10 ? config_option('sts_affiliate_commission_levels') : '1';

		for ($i = 1; $i <= $levels; $i++)
		{
			if ($current_sponsor != '0')
			{
				$sql = 'SELECT *, m.member_id AS member_id
					 	FROM ' . $this->db->dbprefix(TBL_MEMBERS_SPONSORS) . ' m
						LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_AFFILIATE_GROUPS) . ' a
						    ON m.member_id = a.member_id
						LEFT JOIN ' . $this->db->dbprefix(TBL_AFFILIATE_GROUPS) . ' g
						    ON a.group_id = g.group_id
						LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS) . ' p
						    ON m.member_id = p.member_id
						LEFT JOIN ' . $this->db->dbprefix(TBL_MEMBERS_ALERTS) . ' q
						    ON m.member_id = q.member_id
						WHERE m.member_id = \'' . $current_sponsor . '\'
							AND p.is_affiliate = \'1\'
						GROUP BY m.member_id';


				if (!$q = $this->db->query($sql))
				{
					get_error(__FILE__, __METHOD__, __LINE__);
				}

				if ($q->num_rows() > 0)
				{
					$row = $q->row_array();

					//add the member data to the array
					$rows[$i] = $row;

					//set the new affiliate
					$current_sponsor = $row['sponsor_id'];
				}
			}
		}

		return $rows;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $sponsor
	 * @param int $level
	 * @return string
	 */
	private function calc_sponsor_matrix($sponsor = '', $level = 1)
	{
		$width = $this->config->item('sts_affiliate_mlm_matrix_width');
		$levels = $this->config->item('sts_affiliate_commission_levels');

		$array = array();
		while ($level <= $levels)
		{
			$a = $this->get_direct_downline($sponsor);

			if (count($a) >= $width)
			{
				$array[2] = array();
				foreach ($a as $row)
				{
					array_push($array[2], $row);
				} //2
				$level++;  //2
				if ($level <= $levels)
				{
					$array[3] = array();
					foreach ($array[2] as $row2)
					{
						$b = $this->get_direct_downline($row2['mid']);
						if (count($b) < $width)
						{
							return $row2['mid'];
						}
						foreach ($b as $row)
						{
							array_push($array[3], $row);
						} //3
					}
					$level++;
					if ($level <= $levels)
					{
						$array[4] = array();
						foreach ($array[3] as $row3)
						{
							$c = $this->get_direct_downline($row3['mid']);
							if (count($c) < $width)
							{
								return $row3['mid'];
							}
							foreach ($c as $row)
							{
								array_push($array[4], $row);
							} //4
						}
						$level++;
						if ($level <= $levels)
						{
							$array[5] = array();
							foreach ($array[4] as $row4)
							{
								$d = $this->get_direct_downline($row4['mid']);
								if (count($d) < $width)
								{
									return $row4['mid'];
								}
								foreach ($d as $row)
								{
									array_push($array[5], $row);
								} //5
							}
							$level++;
							if ($level <= $levels)
							{
								$array[6] = array();
								foreach ($array[5] as $row5)
								{
									$e = $this->get_direct_downline($row5['mid']);
									if (count($e) < $width)
									{
										return $row5['mid'];
									}
									foreach ($e as $row)
									{
										array_push($array[6], $row);
									} //6
								}
								$level++;
								if ($level <= $levels)
								{
									$array[7] = array();
									foreach ($array[6] as $row6)
									{
										$f = $this->get_direct_downline($row6['mid']);
										if (count($f) < $width)
										{
											return $row6['mid'];
										}
										foreach ($f as $row)
										{
											array_push($array[7], $row);
										} //7
									}
									$level++;
									if ($level <= $levels)
									{
										$array[8] = array();
										foreach ($array[7] as $row7)
										{
											$g = $this->get_direct_downline($row7['mid']);
											if (count($g) < $width)
											{
												return $row7['mid'];
											}
											foreach ($g as $row)
											{
												array_push($array[8], $row);
											} //8
										}
										$level++;
										if ($level <= $levels)
										{
											$array[9] = array();
											foreach ($array[8] as $row8)
											{
												$h = $this->get_direct_downline($row8['mid']);
												if (count($h) < $width)
												{
													return $row8['mid'];
												}
												foreach ($h as $row)
												{
													array_push($array[9], $row);
												} //9
											}
											$level++;
											if ($level <= $levels)
											{
												$array[10] = array();
												foreach ($array[9] as $row9)
												{
													$i = $this->get_direct_downline($row9['mid']);
													if (count($i) < $width)
													{
														return $row9['mid'];
													}
													foreach ($i as $row)
													{
														array_push($array[9], $row);
													} //10
												}
												$level++;
											}
											else
											{
												continue;
											}
										}
										else
										{
											continue;
										}
									}
									else
									{
										continue;
									}
								}
								else
								{
									continue;
								}
							}
							else
							{
								continue;
							}
						}
						else
						{
							continue;
						}
					}
					else
					{
						continue;
					}
				}
				else
				{
					continue;
				}
			}
			else
			{
				return $sponsor;
			}
		}

		return $sponsor;
	}

}
/* End of file Network_marketing_model.php */
/* Location: ./application/models/Network_marketing_model.php */