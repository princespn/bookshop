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
class Site_builder extends Admin_Controller
{
	/**
	 * @var array
	 */
	protected $data = array();

	public function __construct()
	{
		parent::__construct();

		$models = array(
			'site_pages' => 'page',
			'uploads' => 'up',
			'widgets' => 'w'
		);

		foreach ($models as $k => $v)
		{
			$this->load->model($k . '_model', $v);
		}

		$this->load->helper('template');

		$this->config->set_item('menu', 'content');

		if (!class_exists('DOMDocument'))
		{
			log_error('error', lang('php_domdocument_required'));
		}

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
		redirect_page(TBL_SITE_PAGES . '/view');
	}

	// ------------------------------------------------------------------------

	public function create()
	{
		if ($this->input->post())
		{
			$row = $this->page->create_site_builder_page($this->input->post(NULL, TRUE));

			if (!empty($row['success']))
			{
				$this->done(__METHOD__, $row);

				redirect_flashdata(SITE_BUILDER . '/' . $row['id'] . '?full_screen=1', $row['msg_text']);
			}
		}

		show_error(lang('could_not_create_content_builder_page'));
	}

	// ------------------------------------------------------------------------

	public function update()
	{
		$this->data['id'] = valid_id(uri(4));

		$this->data['row'] = $this->page->get_details($this->data['id'], sess('default_lang_id'));

		//run the page
		$this->load->page('content/' . TPL_ADMIN_SITE_BUILDER_UPDATE, $this->data, 'admin', FALSE);
	}

	// ------------------------------------------------------------------------

	public function reset() //to reset the site builder page to default code
	{
		$this->data['id'] = valid_id(uri(4));

		$row = $this->page->reset_site_builder($this->data['id']);

		if (!empty($row['success']))
		{
			$this->done(__METHOD__, $row);

			redirect_flashdata( strtolower(__CLASS__) . '/' . $this->data['id'] . '?full_screen=1', $row['msg_text']);
		}

		show_error(lang('could_not_reset_content_builder_page'));
	}

	function set_default() //set site page to the default home page
	{
		$this->data['id'] = valid_id(uri(4), TRUE);

		$row = $this->set->update_db_settings(array('sts_site_builder_default_home_page' => $this->data['id']));

		//set the session flash and redirect the page
		redirect_flashdata(ADMIN_ROUTE . '/site_pages/view', $row[ 'msg_text' ]);
	}

	// ------------------------------------------------------------------------

	public function layout() //direct site_builder only for full screen
	{
		$this->data['id'] = valid_id(uri(2));

		//check for post data first...
		if ($this->input->post())
		{
			//validate the POST data first....
			$row = $this->page->update_site_builder($this->data['id'], $this->input->post());

			if (!empty($row['success']))
			{

				$this->done(__METHOD__, $row);

				//set the default response
				$response = array('type'     => 'success',
				                  'msg'      => $row['msg_text'],
				                  'redirect' => admin_url(TPL_ADMIN_SITE_BUILDER_UPDATE . '/update/' . $row['id']),
				);
			}
			else
			{
				$response = array('type' => 'error',
				                  'msg'  => $row['msg_text'],
				);
			}

			ajax_response($response);
		}

		$this->data['row'] = $this->page->get_details($this->data['id'], sess('default_lang_id'));

		if (!$this->data['row'])
		{
			show_error(lang('invalid_id'));
		}

		if (!($this->data['row']['page_content']))
		{
			$this->data['row']['page_content'] = file_get_contents(APPPATH . 'views/site/site_pages/default_site_builder.tpl');
		}

		//$this->data['row']['page_content'] = $this->show->parse_tpl($this->config->config, $this->data['row']['page_content']);

		$this->data['row']['header_data'] = $this->show->display('js', 'css', $this->data, TRUE);

		$this->data['row']['footer_data'] = $this->show->display('global', 'footer', $this->data, TRUE);

		//run the page
		$this->load->page('content/' . TPL_ADMIN_SITE_BUILDER_LAYOUT, $this->data, 'admin', FALSE, FALSE, FALSE);
	}

	// ------------------------------------------------------------------------

	public function assets() //direct
	{
		$this->data['id'] = valid_id(uri(4));

		switch ($this->data['id'])
		{
			case 'basicjs';

				$t = TPL_ADMIN_SITE_BUILDER_BASIC_JS;

				break;

			case 'examplesjs':

				$t = TPL_ADMIN_SITE_BUILDER_EXAMPLES_JS;

				break;

			case 'contentjs':

				$t = TPL_ADMIN_SITE_BUILDER_CONTENT_JS;

				break;

			case 'ideashtml':

				$t = TPL_ADMIN_SITE_BUILDER_IDEAS_HTML;

				break;

		}

		//get widgets
		$this->data['widgets'] = $this->w->get_widgets($this->data);

		if (!empty($t))
		{
			//run the page
			$js = $this->load->page('content/' . $t, $this->data, 'admin', FALSE, FALSE, FALSE, TRUE);

			if ($this->data['id'] != 'ideashtml')
			{
				header('Content-Type: application/x-javascript');
			}

			echo $js;
		}
	}

	// ------------------------------------------------------------------------

	public function save_cover() //for saving the backgrounds
	{
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');

		//check for file uploads
		$files = $this->up->validate_uploads('content', 'fileCover');

		if (!empty($files['success']))
		{
			//set json response
			$row = array('type'      => 'success',
			             'file_name' => $files['file_data']['file_name'],
			             'msg_text'       => lang('file_uploaded_successfully'),
			);

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text'], 'vars' => $row));

			//Replace image src with the new saved file
			echo '<html><body onload ="parent.applyBoxImage(\'' . base_folder_path() . DEFAULT_CONTENT_SITE__BUILDER_PATH . $files['file_data']['file_name'] . '\')"></body></html>';
			exit();
		}
		else
		{
			//error!
			$row = array('type' => 'error',
			             'msg_text'  => files['msg'],
			);

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text'], 'vars' => $row, 'level' => 'error'));

			echo "<html><body onload=\"alert('" . $files['msg']. "')\"></body></html>";
			exit();
		}


	}

	// ------------------------------------------------------------------------

	public function save_image_module()
	{
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');

		//check for file uploads
		$files = $this->up->validate_uploads('content', 'fileImage');

		if (!empty($files['success']))
		{
			//set json response
			$row = array('type'      => 'success',
			                  'file_name' => $files['file_data']['file_name'],
			                  'msg_text'       => lang('file_uploaded_successfully'),
			);

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text'], 'vars' => $row));

			//Replace image src with the new saved file
			echo '<html><body onload ="parent.sliderImageSaved(\'' . base_folder_path() . DEFAULT_CONTENT_SITE__BUILDER_PATH . $files['file_data']['file_name'] . '\')"></body></html>';
			exit();
		}
		else
		{
			//error!
			$row = array('type' => 'error',
			             'msg_text'  => files['msg'],
			);

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text'], 'vars' => $row, 'level' => 'error'));

			echo "<html><body onload=\"alert('" . $files['msg']. "')\"></body></html>";
			exit();
		}
	}

	// ------------------------------------------------------------------------

	public function save_images()
	{
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');

		$count = (int)$this->input->get('count');
		$b64str = $this->input->post('hidimg-' . $count);
		$imgname = url_title($this->input->post('hidname-' . $count));
		$imgtype = $this->input->post('hidtype-' . $count)  == 'png' ? 'png' : 'jpg';

		$image = $imgname . '-' . base_convert(rand(), 10, 36) . '.' . $imgtype;

		if (file_put_contents(DEFAULT_CONTENT_UPLOAD_PATH . $image, base64_decode($b64str)))
		{
			//set json response
			$row = array('type'      => 'success',
			             'file_name' => $image,
			             'msg_text'       => lang('file_uploaded_successfully'),
			);

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text'], 'vars' => $row));

			//Replace image src with the new saved file
			echo "<html><body onload=\"parent.document.getElementById('img-" . $count . "').setAttribute('src','" .  base_folder_path() . DEFAULT_CONTENT_SITE__BUILDER_PATH . $image . "');  parent.document.getElementById('img-" . $count . "').removeAttribute('id') \"></body></html>";
		}
		else
		{
			//error!
			$row = array('type' => 'error',
			             'msg_text'  => lang('check_permission_on_folder') . " - " . DEFAULT_CONTENT_UPLOAD_PATH,
			);

			$this->dbv->rec(array('method' => __METHOD__, 'msg' => $row['msg_text'], 'vars' => $row, 'level' => 'error'));

			echo "<html><body onload=\"alert('" . $row['msg_text']  . "')\"></body></html>";
		}
	}
}

/* End of file Site_builder.php */
/* Location: ./application/controllers/admin/Site_builder.php */