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
class Kb_articles extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		//autoload public models
		$models = array(
			__CLASS__       => 'kb',
			'kb_categories' => 'cat',
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}


		$this->load->helper('html_editor');

		$this->config->set_item('menu', TBL_SUPPORT_TICKETS);
		$this->config->set_item('sub_menu', TBL_KB_ARTICLES);

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

		$this->data[ 'id' ] = $this->input->get('m-category_id');
		$this->data['category_name'] = '';

		//set category ID if any
		if ($this->data[ 'id' ])
		{
			if ($cat = $this->cat->get_category($this->data[ 'id' ]))
			{
				$this->data['category_name'] = $cat['category_name'];
			}
		}

		$this->data[ 'rows' ] = $this->kb->get_rows($this->data[ 'page_options' ], sess('default_lang_id'));

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
		$this->load->page('support/' . TPL_ADMIN_KB_ARTICLES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function general_search()
	{
		$this->data[ 'page_options' ] = query_options($this->data);

		$this->data[ 'id' ] = $this->input->get('m-category_id');
		$this->data['category_name'] = '';

		//set category ID if any
		if ($this->data[ 'id' ])
		{
			if ($cat = $this->cat->get_category($this->data[ 'id' ]))
			{
				$this->data['category_name'] = $cat['category_name'];
			}
		}

		$this->data[ 'rows' ] = $this->kb->search($this->data[ 'page_options' ], sess('default_lang_id'));

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
		$this->load->page('support/' . TPL_ADMIN_KB_ARTICLES_VIEW, $this->data);
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		//fill in default values for input fields
		$this->data[ 'row' ] = list_fields(array( TBL_KB_ARTICLES, TBL_KB_ARTICLES_NAME ));

		//set the default data
		$this->data[ 'row' ][ 'lang' ] = set_default_create_data($this->data[ 'row' ], get_languages(FALSE, FALSE));
		$this->data['sort_order'] = '1';

		//set category ID if any
		if (uri(4))
		{
			$cat = $this->cat->get_category(uri(4));

			if (!empty($cat))
			{
				$this->data['row']['category_id'] = uri(4);
				$this->data['row']['category_name'] = $cat['category_name'];
			}
		}

		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->kb->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->kb->create($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'msg'      =>  $row['msg_text'],
				                   'redirect' => admin_url(TBL_KB_ARTICLES . '/update/' .  $row['data'][ 'kb_id' ]),
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

		//initialize the html editor...
		$this->data[ 'meta_data' ] = html_editor('head');

		//run the page
		$this->load->page('support/' . TPL_ADMIN_KB_ARTICLES_CREATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->kb->validate($this->input->post());

			if (!empty($row[ 'success' ]))
			{
				$row = $this->kb->update($row[ 'data' ]);

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array( 'type'     => 'success',
				                   'msg'      =>  $row['msg_text']
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

		//get the ID
		$this->data[ 'id' ] = valid_id(uri(4));

		$this->data['row'] = $this->kb->get_details($this->data['id']);

		//initialize the html editor...
		$this->data[ 'meta_data' ] = html_editor('head');

		//run the page
		$this->load->page('support/' . TPL_ADMIN_KB_ARTICLES_UPDATE, $this->data);
	}

	// ------------------------------------------------------------------------

	public function delete()
	{
		$id = valid_id(uri(4));
		
		$row = $this->kb->delete($id);

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

	/**
	 * Mass update records via checkboxes
	 */
	public function mass_update()
	{
		if ($this->input->post())
		{
			$row = $this->kb->mass_update($this->input->post());

			$this->done(__METHOD__, $row);
		}

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/' . strtolower(__CLASS__) . '/view', $row[ 'msg_text' ]);
	}
}

/* End of file Kb_articles.php */
/* Location: ./application/controllers/admin/Kb_articles.php */