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
class Gift_certificates extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->model(__CLASS__ . '_model', 'gift');

		$this->config->set_item('menu', TBL_PRODUCTS);

		$this->lc->check(__CLASS__);

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	/**
	 * index file
	 */
	public function index()
	{
		redirect_page($this->uri->uri_string() . '/view');
	}

	// ------------------------------------------------------------------------

	public function view()
	{
		$this->data[ 'page_options' ] = query_options($this->data);

		$this->data[ 'rows' ] = $this->dbv->get_rows($this->data[ 'page_options' ], TBL_ORDERS_GIFT_CERTIFICATES);

		//check for pagination
		if (!empty($this->data[ 'rows' ][ 'total' ]))
		{
			$this->data[ 'page_options' ] = array(
				'uri'        => $this->data[ 'uri' ],
				'total_rows' => $this->data[ 'rows' ][ 'total' ],
				'per_page'   => $this->data[ 'session_per_page' ],
				'segment'    => $this->data[ 'db_segment' ],
			);

			$this->data[ 'paginate' ] = $this->paginate->generate($this->data[ 'page_options' ], CONTROLLER_CLASS, 'admin');
		}

		//run the page
		$this->load->page('orders/' . TPL_ADMIN_ORDERS_GIFT_CERTIFICATES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->gift->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->gift->create($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				$this->session->set_flashdata('success', $row[ 'msg_text' ]);

				//set the default response
				$url = $this->input->post('redir_button') ? admin_url(CONTROLLER_CLASS . '/create/') : admin_url(CONTROLLER_CLASS . '/update/' . $row[ 'data' ][ 'cert_id' ]);
				$response = array( 'type'     => 'success',
				                   'msg'      => $row[ 'msg_text' ],
				                   'redirect' => $url,
				);
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => $row[ 'msg_text' ],
				);
			}

			ajax_response($response);
		}

		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( TBL_ORDERS_GIFT_CERTIFICATES ));

		//autogenerate gift certificate code
		$this->data[ 'row' ][ 'code' ] = generate_serial();

		$this->load->page('orders/' . TPL_ADMIN_ORDERS_GIFT_CERTIFICATES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		if ($this->input->post())
		{
			//check if the form submitted is correct
			$row = $this->gift->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{

				$row = $this->gift->update($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type' => 'success',
				                   'msg'  => $row[ 'msg_text' ]
				);
			}
			else
			{
				$response = array( 'type' => 'error',
				                   'msg'  => $row[ 'msg_text' ],
				);
			}

			ajax_response($response);
		}

		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->gift->get_details($this->data[ 'id' ], 'cert_id');

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->load->page('orders/' . TPL_ADMIN_ORDERS_GIFT_CERTIFICATES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->gift->delete($id);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
		}
		else
		{
			log_error('error', $row[ 'msg_text' ]);
		}
	}

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->gift->mass_update($this->input->post());

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	public function email()
	{
		//send the gift certificate to the user who it is meant for.

		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->gift->get_details($this->data[ 'id' ], 'cert_id');

		if (!$this->data[ 'row' ])
		{
			log_error('error', lang('no_record_found'));
		}

		$this->mail->send_template(EMAIL_MEMBER_GIFT_CERTIFICATE_DETAILS, $this->data['row'], FALSE, sess('default_lang_id'), $this->data[ 'row' ]['to_email']);

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', 'email_sent_successfully');
	}

	// ------------------------------------------------------------------------

	public function generate_serial()
	{
		$this->init->check_ajax_security();

		$response = array( 'type' => 'success',
		                   'code' => $this->gift->generate_serial(),
		);

		ajax_response($response);
	}
}

/* End of file Gift_certificates.php */
/* Location: ./application/controllers/admin/Gift_certificates.php */