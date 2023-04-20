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
class Widgets extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->model(__CLASS__ . '_model', 'w');

		$this->config->set_item('menu', 'design');

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

		$this->data[ 'widget_category' ] = !$this->input->get('widget_category') ? '51' : (int)$this->input->get('widget_category');

		$this->data[ 'rows' ] = $this->w->get_rows($this->data[ 'page_options' ], $this->data[ 'widget_category' ]);

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
		$this->load->page('design/' . TPL_ADMIN_WIDGETS_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->w->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->w->create($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( TBL_WIDGETS ));
		$this->data['row']['widget_type'] = 'custom';

		//add required meta data...
		$this->data[ 'meta_data' ] = link_tag('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/third/codemirror/codemirror.css');

		$this->data[ 'meta_data' ] .= '<script src="' . base_url('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/third/codemirror/codemirror.js') . '"></script>';
		$this->data[ 'meta_data' ] .= '<script src="' . base_url('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/third/codemirror/mode/xml/xml.js') . '"></script>';

		$this->load->page('design/' . TPL_ADMIN_WIDGETS_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->w->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->w->update($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the session flash and redirect the page
				redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row[ 'msg_text' ]);
			}
			else
			{
				//show errors on form
				$this->data[ 'error' ] = validation_errors();
			}
		}

		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data[ 'row' ] = $this->w->get_details($this->data[ 'id' ]);

		//add required meta data...
		$this->data[ 'meta_data' ] = link_tag('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/third/codemirror/codemirror.css');

		$this->data[ 'meta_data' ] .= '<script src="' . base_url('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/third/codemirror/codemirror.js') . '"></script>';
		$this->data[ 'meta_data' ] .= '<script src="' . base_url('themes/admin/' . $this->data[ 'sts_admin_layout_theme' ] . '/third/codemirror/mode/xml/xml.js') . '"></script>';

		$this->load->page('design/' . TPL_ADMIN_WIDGETS_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));

		$row = $this->dbv->delete(TBL_WIDGETS, 'widget_id', $id);

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

	// ------------------------------------------------------------------------

	public function clone_widget()
	{
		$this->data[ 'id' ] = valid_id(uri(4));

		$row = $this->w->clone_widget($this->data[ 'id' ]);

		if (!empty($row[ 'success' ]))
		{
			$this->done(__METHOD__, $row);

			//set the session flash and redirect the page
			redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/update/' . $row['id'], $row[ 'msg_text' ]);
		}
		else
		{
			log_error(lang('could_not_clone_widget'));
		}
	}

	// ------------------------------------------------------------------------

	public function search()
	{
		if ($this->input->get('category_name'))
		{
			$term = $this->input->get('category_name', TRUE);

			$rows = $this->w->ajax_search($term);

			echo json_encode($rows);
		}
	}
}

/* End of file Widgets.php */
/* Location: ./application/controllers/admin/Widgets.php */