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
class Report_archive extends Admin_Controller
{

	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'reports' => 'report',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->config->set_item('menu', 'reports');

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

		//get rows
		$this->data[ 'rows' ] = $this->dbv->get_rows($this->data[ 'page_options' ],TBL_REPORT_ARCHIVE);

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
		$this->load->page('reports/' . TPL_ADMIN_REPORTS_ARCHIVE_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function generate()
	{
		$this->data['id'] = (int)uri(4);

		$this->data['row'] = $this->dbv->get_record(TBL_REPORT_ARCHIVE, 'id', $this->data['id']);

		if (! $this->data['row'])
		{
			log_error('error', lang('no_record_found'));
		}

		//run the page
		$this->load->page('reports/' . TPL_ADMIN_REPORT_ARCHIVE_GENERATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		if ($id > 1)
		{
			$row = $this->dbv->delete(TBL_REPORT_ARCHIVE, 'id', $id);

			if (!empty($row[ 'success' ]))
			{
				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
			}
			else
			{
				log_error('error', lang('could_not_delete_record'));
			}
		}
		else
		{
			log_error('error', lang('invalid_id'));
		}
	}
}

/* End of file Report_archive.php */
/* Location: ./application/controllers/admin/Report_archive.php */