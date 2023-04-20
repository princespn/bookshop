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
class Search extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('search_model', 's');
		$this->load->helper('download');
		$this->load->helper('file');

		$this->data = $this->init->initialize();

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function general()
	{
		redirect(admin_url($this->input->post('table') . '/general_search?search_term=' . $this->input->post('search_term')));
	}

	// ------------------------------------------------------------------------

	public function select()
	{
		//for searching specific rows via select2 dropdowns
		$term = $this->input->get('term', TRUE);

		$rows = $this->s->select_search($term, uri(4), uri(5), uri(6), uri(7)); //table, column, key, status

		echo json_encode($rows);
	}

	// ------------------------------------------------------------------------

	public function advanced()
	{
		if ($this->input->get())
		{
			//show the results
			$this->data['rows'] = $this->s->advanced_search($this->input->get(NULL, TRUE));

			$this->data['table'] = $this->input->get('table');

			if (!$this->data['rows']['values'])
			{
				show_error(lang('no_records_found'));
			}

			switch ($this->input->get('output'))
			{
				case 'csv_file':

					$row = $this->s->generate_download($this->data['rows']['values']);

					force_download($row['file_name'], $row['data']);

					break;

				default:

					//show the advanced search form
					$this->load->page('search/' . TPL_ADMIN_ADVANCED_SEARCH_RESULTS, $this->data);

					break;
			}
		}
		else
		{
			$this->data['default_table'] = $this->db->dbprefix(TBL_MEMBERS);

			$c = $this->db->field_data($this->data['default_table']);

			$this->data['default_columns']  = array();

			foreach ($c as $v)
			{
				$f = $v->type . '-' . $v->name;
				$this->data['default_columns'][$f] = $v->name;
			}

			//show the advanced search form
			$this->load->page('search/' . TPL_ADMIN_ADVANCED_SEARCH, $this->data);
		}
	}

	// ------------------------------------------------------------------------

	public function load_columns()
	{
		$c = $this->db->field_data($this->input->get('table', TRUE));

		if (!empty($c))
		{
			foreach ($c as $v)
			{
				$f = $v->type . '-' . $v->name;
				$rows[$f] = $v->name;
			}

			echo form_dropdown(uri(4, 'column'), $rows, '', 'id="column" class="s2 select2 form-control required"');
		}
	}
}

/* End of file Search.php */
/* Location: ./application/controllers/admin/Search.php */