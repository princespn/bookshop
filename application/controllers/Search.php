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
class Search extends Public_Controller
{

	/**
	 * @var array
	 */
	public $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model('search_model', 'search');

		$this->data = $this->init->initialize('site');

		log_message('debug', __CLASS__ . ' Class Initialized');
	}

	// ------------------------------------------------------------------------

	public function site()
	{
		if ($this->input->get('search_term'))
		{
			$this->data['term'] = $this->input->get('search_term', TRUE);

			//set the custom offset
			$opt = array('offset'           => (int)uri(2, 0),
			             'session_per_page' => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
			);

			$rows = $this->search->site_search(query_options($opt), $this->data['term'], sess('default_lang_id'));

			//set the posts array
			$this->data['rows'] = !empty($rows['values']) ? $rows['values'] : FALSE;

			//check for pagination
			if (!empty($rows['total']))
			{
				$this->data['page_options'] = array(
					'uri'        => site_url('search'),
					'total_rows' => $rows['total'],
					'per_page'   => !sess('rows_per_page') ? $this->data['layout_design_products_per_page'] : sess('rows_per_page'),
					'segment'    => 2,
				);

				$this->data['paginate'] = $this->paginate->generate($this->data['page_options'], CONTROLLER_CLASS);
				$this->data['next_scroll'] = check_infinite_scroll($this->data);
			}
		}

		$this->show->display('global', 'search', $this->data);
	}

	// ------------------------------------------------------------------------

	public function search_countries()
	{
		$term = $this->input->get('country_name', TRUE);

		$all = uri(4) == 'all_regions' ? TRUE : FALSE;

		$rows = $this->country->ajax_search($term, $all, TRUE);

		echo json_encode($rows);
	}

	// ------------------------------------------------------------------------

	public function load_regions()
	{
		$term = uri(4, 'state');

		$all_regions = uri(5) == 'all_regions' ? TRUE : FALSE;

		$id = (int)$this->input->post_get('country_id');

		if (!empty($id))
		{
			if ($rows = $this->regions->load_country_regions($id, TRUE, $all_regions, TRUE))
			{
				echo form_dropdown($term, $rows, '', 'id="region_select" class="s2 select2 form-control"');
			}
		}
	}

	// ------------------------------------------------------------------------

	public function affiliates()
	{
		$term = $this->input->get('username', TRUE);

		$rows = $this->mem->ajax_search(uri(3, 'username'), url_title($term), 3, FALSE);

		echo json_encode($rows);
	}
}

/* End of file Search.php */
/* Location: ./application/controllers/Search.php */