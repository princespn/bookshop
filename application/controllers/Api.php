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
class Api extends Public_Controller
{
	protected $data = array();

	protected $table = '';

	// ------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->config('api');

		//this will automatically initialize the api model and run the security checks for api key, token and ip address
		$this->load->model('api_model', 'api');

		$this->table = valid_id(uri(3), TRUE);

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function get()
	{
		//sample http://www.domain.com/api/get/members/0/25; 0 is the offset; 25 is the db limit

		if ($this->input->post_get('api_command') && !empty($this->data['api_class_alias'][ $this->table ]))
		{
			//load models
			$this->load_models();

			$m = $this->data['api_class_alias'][ $this->table ];
			$c = $this->input->post_get('api_command');
			$lang_id = !$this->input->post_get('lang_id') ? 1 : $this->input->post_get('lang_id');

			if (method_exists($this->table . '_model', $c))
			{
				$row = $this->$m->$c(query_options(array('session_per_page' => (int)(uri(5, API_DEFAULT_NUMBER_ROWS)),
				                                         'limit'            => (int)(uri(4, 0))), FALSE),
					$lang_id);
			}
		}

		if (empty($row))
		{
			$row = array('msg_text' => 'no_data_found');
		}

		$this->api->show($row);
	}

	// ------------------------------------------------------------------------

	public function post()
	{
		//www.domain.com/api/post/members/

		if ($this->input->post_get('api_command') && !empty($this->data['api_class_alias'][ $this->table ]))
		{
			//load models
			$this->load_models();

			$m = $this->data['api_class_alias'][ $this->table ];
			$c = $this->input->post_get('api_command');
			$lang_id = !$this->input->post_get('lang_id') ? 1 : $this->input->post_get('lang_id');

			if (method_exists($this->table . '_model', $c))
			{
				switch ($this->table)
				{
					case 'members':

						//get form field data
						$this->data['fields'] = $this->form->init_form(2, $lang_id, '');

						switch ($c)
						{
							case 'create':

								//run validation first
								$row = $this->mem->validate('update_api', $this->input->post(), $this->data['fields']['values']);

								if (!empty($row['success']))
								{
									$row = $this->mem->init_new_member(generic_user($row['data']));

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;

							case 'update':

								//set the ID
								$this->data['id'] = (int)$this->input->post_get('member_id');

								$this->data['custom_fields'] = $this->form->get_member_custom_fields($this->data['id'], $lang_id);

								//run validation first
								$row = $this->mem->validate('update_api', $this->input->post(), $this->data['fields']['values']);

								if (!empty($row['success']))
								{
									$row = $this->mem->update($row['data'], $this->data['custom_fields']);

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}

								break;

							case 'add_address':

								//set the ID
								$this->data['id'] = (int)$this->input->post_get('member_id');

								//check if the form submitted is correct
								$row = $this->mem->validate_address($c, $this->input->post(NULL, TRUE));

								if (!empty($row['success']))
								{
									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;

							case 'update_address':

								//set the ID
								$this->data['id'] = (int)$this->input->post_get('id');

								//check if the form submitted is correct
								$row = $this->mem->validate_address($c, $this->input->post(NULL, TRUE));

								if (!empty($row['success']))
								{
									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;
						}

						break;

					case 'products':

						switch ($c)
						{
							case 'create':

								//check if the form submitted is correct
								$row = $this->prod->validate($c, $this->input->post());

								if (!empty($row['success']))
								{
									$row = $this->prod->create($row['data']);

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;

							case 'update':

								//set the ID
								$this->data['id'] = (int)$this->input->post_get('product_id');

								//check if the form submitted is correct
								$row = $this->prod->validate($c, $this->input->post());

								if (!empty($row['success']))
								{
									$row = $this->prod->update($row['data']);

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;
						}

						break;

					case 'kb':

						switch ($c)
						{
							case 'create':

								//check if the form submitted is correct
								$row = $this->kb->validate($this->input->post());

								if (!empty($row['success']))
								{
									$row = $this->kb->create($row['data']);

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;

							case 'update':

								//set the ID
								$this->data['id'] = (int)$this->input->post_get('kb_id');

								//check if the form submitted is correct
								$row = $this->kb->validate($this->input->post());

								if (!empty($row['success']))
								{
									$row = $this->kb->update($row['data']);

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;
						}

						break;

					case 'blog':

						switch ($c)
						{
							case 'create':

								//check if the form submitted is correct
								$row = $this->blog->validate($this->input->post());

								if (!empty($row['success']))
								{
									$row = $this->blog->create($row['data']);

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;

							case 'update':

								//set the ID
								$this->data['id'] = (int)$this->input->post_get('blog_id');

								//check if the form submitted is correct
								$row = $this->blog->validate($this->input->post());

								if (!empty($row['success']))
								{
									$row = $this->blog->update($row['data']);

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));
								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;
						}

						break;

					case 'affiliate_commissions':

						switch ($c)
						{
							case 'create':

								//generate a commission via API
								$row = $this->comm->validate($c, $this->input->post());

								if (!empty($row['success']))
								{
									//check if we're generating using group amounts
									if ($row['data']['use_group_amounts'] == 1)
									{
										//get upline
										$upline = $this->downline->check_upline($row['data']);

										//check if we're crediting the upline
										if ($row['data']['generate_upline'] == 1 && check_upline_config())
										{
											//go through each member in the downline
											$line = array_reverse($upline, TRUE);

											$comms = array();
											foreach ($line as $k => $v)
											{
												//generate single commission
												$a = $this->comm->create_commission(format_commission_data($k,
													$row['data']['sale_amount'],
													$v,
													$row['data']));

												array_push($comms, $a);
											}

											$row = array('comms' => $comms,
											             'success' => TRUE,
											             'msg_text' => lang('commissions_generated_successfully'));
										}
										else
										{
											//generate single commission
											$row = $this->comm->create_commission(format_commission_data(1,
												$row['data']['sale_amount'],
												$upline[1],
												$row['data']));
										}
									}
									else
									{
										$row = $this->comm->create($row['data']);
									}

									//log it!
									$this->dbv->rec(array('method' => __METHOD__,
									                      'msg' => lang('api') . ' - ' . $this->table . '/' . $c . ' - ' . $row['msg_text']));

								}
								else
								{
									$row = array('error'    => TRUE,
									             'msg_text' => validation_errors());
								}

								break;
						}

						break;
				}
			}
		}

		if (empty($row))
		{
			$row = array('msg_text' => lang('invalid_api_access'));
		}

		$this->api->show($row);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		//www.domain.com/api/delete/members/member_id/25

		//load models
		$this->load_models();

		if (method_exists($this->table . '_model', 'delete'))
		{
			$this->data['id'] = valid_id(uri(5));

			switch ($this->table)
			{
				case 'members':

					$row = $this->mem->delete($this->data['id']);

					break;

				case 'members_address':

					$row = $this->mem->delete_address($this->data['id']);

					break;

				case 'products':

					$row = $this->prod->delete($this->data['id']);

					break;

				case 'kb':

					$row = $this->kb->delete($this->data['id']);

					break;

				case 'blog':

					$row = $this->blog->delete($this->data['id']);

					break;
			}

			if (!empty($row['success']))
			{
				//log it!
				$this->dbv->rec(array('method' => __METHOD__,
				                      'msg' => lang('api') . ' - ' . $this->table . '/' . CONTROLLER_FUNCTION . ' - ' . $row['msg_text']));
			}
			else
			{
				$row = array('error'    => TRUE,
				             'msg_text' => lang('could_not_delete_data'));
			}
		}


		if (empty($row))
		{
			$row = array('msg_text' => 'invalid_api_access');
		}

		$this->api->show($row);
	}

	// ------------------------------------------------------------------------

	private function load_models()
	{
		//load models
		if ($this->data['api_load_models'][ $this->table ])
		{
			foreach ($this->data['api_load_models'][ $this->table ] as $k => $v)
			{
				$this->load->model($k . '_model', $v);
			}
		}

		//load helpers
		if (!empty($this->data['api_load_helpers'][ $this->table ]))
		{
			foreach ($this->data['api_load_helpers'][ $this->table ] as $v)
			{
				$this->load->helper($v);
			}
		}
	}

}

/* End of file Api.php */
/* Location: ./application/controllers/Api.php */