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
class Templates_model extends CI_Model
{
	/**
	 * @var string
	 */
	protected $id = 'template_id';

	// ------------------------------------------------------------------------

	/**
	 * Templates_model constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('template');
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tpl
	 * @param array $data
	 * @param string $type
	 * @return array|string
	 */
	public function format_template_data($tpl = '', $data = array(), $type = 'preview')
	{
		if (!empty($tpl))
		{
			$tpl = str_replace('&nbsp;', '', $tpl);
			$meta_data = '';
			$footer_data = '';
			$a = array();
			$doc = new DOMDocument();
			$doc->validateOnParse = TRUE;
			libxml_use_internal_errors(TRUE);
			$doc->loadHTML($tpl);
			libxml_use_internal_errors(FALSE);
			$doc->preserveWhiteSpace = FALSE;
			$selector = new DOMXPath($doc);

			$result = $selector->query('//div[@data-type="' . $type . '"]');

			if ($type == 'preview')
			{
				$i = 1;

				foreach ($result as $node)
				{
					//get the id for the widget
					$id = $node->getAttribute('data-id');

					if (!empty($id) && $id != 'static')
					{
						if ($row = $this->w->get_details($id))
						{
							$meta_data .= $row['meta_data'];
							$footer_data .= $row['footer_data'];
							//update the template data with the template code from the widget
							$div = $doc->createElement('div', $row['template_code']);
							$div->setAttribute('id', 'block-' . $i);
							$node->parentNode->replaceChild($div, $node);
						}
					}
					else
					{
						$div = $doc->createElement('div', $node->c14n());
						$div->setAttribute('id', 'block-' . $i);
						$node->parentNode->replaceChild($div, $node);
					}

					$i++;
				}

				$tpl = html_decode(urldecode($doc->saveHTML()));

				$data['template_string'] = $footer_data;
				$a['footer_data'] = $this->show->display('js', 'string', $data, TRUE);
				$a['template_data'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $tpl);
				$a['meta_data'] = parse_string($meta_data, $data);
			}
			else //replace the dynamic data on the public content page (home page)
			{
				foreach ($result as $node)
				{
					$a = $node->getAttribute('data-function');

					if (!empty($a) && empty($data[$a]))
					{
						$plugin = $this->plugin->init_plugin($a, $data);

						$data[$a] = !empty($plugin['values']) ? $plugin['values'] : '';

						if (!empty($plugin['meta_data']))
						{
							$meta_data .= $plugin['meta_data'];

						}
						if (!empty($plugin['footer_data']))
						{
							$footer_data .= $plugin['footer_data'];
						}
					}
				}

				$tpl = html_decode(urldecode($doc->saveHTML()));

				$data['template_string'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $tpl);

				$a = $meta_data;
				$a .= $this->show->display('js', 'string', $data, TRUE);
				$a .= $footer_data;
			}

			return $a;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tpl
	 * @param string $column
	 * @return bool|false|string
	 */
	public function get_custom_template_details($tpl = '', $column = 'template_name')
	{
		$this->db->where($column, valid_id($tpl, TRUE));
		if (!$q = $this->db->get(TBL_PAGE_TEMPLATES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $q->row_array();
			$row['template_data'] = htmlentities($row['template_data']);
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param bool $format
	 * @return bool|false|string
	 */
	public function get_custom_templates($type = '', $format = FALSE)
	{
		$this->db->where('template_category', $type);

		if (!$q = $this->db->get(TBL_PAGE_TEMPLATES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$row = $format == TRUE ? format_array($q->result_array(), 'template_name', 'template_data') : $q->result_array();
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $cat
	 * @param string $file
	 * @param string $sub
	 * @return bool|false|string
	 */
	public function get_details($cat = '', $file = '', $sub = '')
	{
		//first, let's check if there is a db version of it...
		if ($row = $this->get_custom_template_details($file))
		{
			return $row;
		}

		$modules = array('affiliate_marketing', 'member_reporting', 'payment_gateways');

		if (in_array($cat, $modules))
		{
			$folder = $cat == 'payment_gateways' ? 'checkout' : 'members';

			if (file_exists(APPPATH . 'modules/' . $cat . '/' . $sub . '/views/' . $folder . '/' . $file))
			{
				$tpl = APPPATH . 'modules/' . $cat . '/' . $sub . '/views/' . $folder . '/' . $file;

				$a['template_data'] = htmlentities(@file_get_contents($tpl));
			}

		}
		else
		{
			$tpl_dir = APPPATH . 'views/site/';

			if (file_exists(PUBPATH . '/themes/site/' . $this->config->item('layout_design_site_theme') . '/custom_templates/' . $cat . '/' . $file))
			{
				$tpl_dir = PUBPATH . '/themes/site/' . $this->config->item('layout_design_site_theme') . '/custom_templates/';
			}

			$a['template_data'] = htmlentities(@file_get_contents($tpl_dir . $cat . '/' . $file));
		}

		return !empty($a) ? $a : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tpl
	 * @return bool|false|string
	 */
	public function get_template($tpl = '')
	{
		if (!$q = $this->db->where('tpl_file_name', $tpl)->get(TBL_PAGE_TEMPLATES))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		if ($q->num_rows() > 0)
		{
			$a = $q->row_array();

			$row = $a['tpl_data'];
		}

		return empty($row) ? FALSE : sc($row);
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return array|bool
	 */
	public function get_templates($type = '')
	{
		$map = directory_map('./application/views/site/product/');

		$row = array();

		foreach ($map as $k => $v)
		{
			if (preg_match("/^product_" . $type . "/", $v))
			{
				$v = str_replace('.tpl', '', $v);
				$row[$v] = $v;
			}
		}

		asort($row);

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $tpl
	 * @param array $data
	 * @return array|bool
	 */
	public function load_template($tpl = '', $data = array())
	{
		//loading the template for the homepage.
		$row = array('template' => $tpl);

		//get slideshows if enabled
		if (config_enabled('layout_design_home_page_show_slideshows') && $tpl != 'builder')
		{
			$t = $this->slide->get_slideshows(sess('default_lang_id'), $data);
			$row['slide_shows'] = $t['slide_shows'];
			$row['meta_data'] = $t['meta_data'];
			$row['footer_data'] = $t['footer_data'];
		}

		switch ($tpl)
		{
			case 'store_grid':
			case 'store_list':

				$row['products'] = $this->prod->load_store(sess('default_lang_id'));

				break;

			case 'blog_grid':
			case 'blog_list':

				$row['blogs'] = $this->blog->load_blog(sess('default_lang_id'));

				break;

			case 'default':

				$row['products'] = $this->prod->load_store(sess('default_lang_id'));
				$row['blogs'] = $this->blog->load_blog(sess('default_lang_id'));
				$row['brands'] = $this->brand->load_brands(query_options(), sess('default_lang_id'));

				break;

			case 'builder':

				$row = $this->page->get_details(config_item('sts_site_builder_default_home_page'), 1);

				if (empty($row['status']))
				{
					redirect('offline/home');

				}
				else
				{
					$t = $this->tpl->format_template_data($row['page_content'] , $this->data);

					$row['meta_data'] .= $t['meta_data'];
					$row['footer_data'] = $t['footer_data'];

					$row['template_data'] = $this->tpl->format_template_data($t['template_data'], $this->data, 'widget');
					$row['template'] = 'site_builder';
				}

				break;
		}

		return empty($row) ? FALSE : $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function update_custom_template($data = array())
	{
		//check if there is a template already in the db...
		if ($q = $this->get_custom_template_details($data['template_name']))
		{
			//if there is update it.
			$row = $this->dbv->update(TBL_PAGE_TEMPLATES, 'template_name', $data);
		}
		else
		{
			//if not, add it.
			$row = $this->dbv->create(TBL_PAGE_TEMPLATES, $data);

		}

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function update($data = array())
	{
		$id = $data['id'];

		$vars = $this->dbv->clean($data, TBL_PAGE_TEMPLATES);

		//set the template code
		$tpl = $this->format_template_data($vars['preview_data'], $this->config->config);

		$vars = array_merge($vars, $tpl);

		$this->db->where($this->id, $id);

		if (!$this->db->update(TBL_PAGE_TEMPLATES, $vars))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		$row = array(
			'msg_text' => lang('system_updated_successfully'),
			'success'  => TRUE,
			'id'       => $id,
			'data'     => $data,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return bool|false|string
	 */
	public function validate_custom_template($data = array())
	{
		$this->form_validation->set_data($data);

		//get the list of fields required for this
		$required = $this->config->item('page_templates', 'required_input_fields');

		//now get the list of fields directly from the table
		$fields = $this->db->list_fields(TBL_PAGE_TEMPLATES);

		//go through each field and
		foreach ($fields as $f)
		{
			//set the default rule
			$rule = 'trim';

			//if this field is a required field, let's set that
			if (in_array($f, $required))
			{
				$rule .= '|required';
			}

			$this->form_validation->set_rules($f, 'lang:' . $f, $rule);
		}

		if ($this->form_validation->run())
		{
			//cool! no errors...
			$row = array('success' => TRUE,
			             'data'    => $this->dbv->validated($data, FALSE),
			);
		}

		return empty($row) ? FALSE : sc($row);
	}
}

/* End of file Templates_model.php */
/* Location: ./application/models/Templates_model.php */