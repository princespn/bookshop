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
class Site_map_model extends CI_Model
{
	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $lang_id
	 * @param int $limit
	 * @param int $offset
	 * @return bool|false|string
	 */
	public function get_site_records($type = '', $lang_id = '1', $limit = SITE_MAP_LIMIT, $offset = 0)
	{
		switch ($type)
		{
			case 'product':

				$sql = 'SELECT p.product_id, d.product_name, modified 
							FROM ' . $this->db->dbprefix(TBL_PRODUCTS) . ' p
							LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_NAME) . ' d
		                        ON p.product_id = d.product_id
		                        AND d.language_id = \'' . $lang_id . '\'
							WHERE product_status = \'1\' 
								AND hidden_product = \'0\'
							LIMIT ' . $offset . ', ' . $limit;

				break;

			case 'product_categories':

				$sql = 'SELECT p.category_id, d.category_name, modified 
							FROM ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES) . ' p
							LEFT JOIN ' . $this->db->dbprefix(TBL_PRODUCTS_CATEGORIES_NAME) . ' d
		                        ON p.category_id = d.category_id
		                        AND d.language_id = \'' . $lang_id . '\'
							WHERE category_status = \'1\'
							LIMIT ' . $offset . ', ' . $limit;

				break;

			case 'blog':

				$sql = 'SELECT p.blog_id, p.category_id, p.url, p.date_published
						FROM ' . $this->db->dbprefix(TBL_BLOG_POSTS) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_BLOG_POSTS_NAME) . ' d
		                        ON p.blog_id = d.blog_id
		                        AND d.language_id = \'' . $lang_id . '\'
							WHERE status = \'1\'
							 AND  p.date_published < NOW()
							LIMIT ' . $offset . ', ' . $limit;


				break;

			case 'pages':

				$sql = 'SELECT p.page_id, p.title, p.url, p.date_modified
						FROM ' . $this->db->dbprefix(TBL_SITE_PAGES) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_SITE_PAGES_NAME) . ' d
		                        ON p.page_id = d.page_id
		                        AND d.language_id = \'' . $lang_id . '\'
							WHERE status = \'1\'
							LIMIT ' . $offset . ', ' . $limit;


				break;

			case 'kb':

				$sql = 'SELECT p.kb_id, p.category_id, p.url, p.date_modified
						FROM ' . $this->db->dbprefix(TBL_KB_ARTICLES) . ' p
						LEFT JOIN ' . $this->db->dbprefix(TBL_KB_ARTICLES_NAME) . ' d
		                        ON p.kb_id = d.kb_id
		                        AND d.language_id = \'' . $lang_id . '\'
							WHERE status = \'1\'
							LIMIT ' . $offset . ', ' . $limit;

				break;
		}

		if (!$q = $this->db->query($sql))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? sc($q->result_array()) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @param string $lang_id
	 * @return array
	 */
	public function generate_site_links($type = '', $lang_id = '1')
	{
		$links = array();

		if ($data = $this->get_site_records($type, $lang_id))
		{
			foreach ($data as $k => $v)
			{
				switch ($type)
				{
					case 'product':

						$opt['link'] = site_url() . $type . '/details/' . $v['product_id'] . '-' . strtolower(url_title($v['product_name']));
						$opt['date_modified'] = date('Y-m-d', strtotime($v['modified']));
						array_push($links, $opt);

						break;

					case 'product_categories':

						$opt['link'] = site_url() . 'product/category/' . $v['category_id'] . '-' . strtolower(url_title($v['category_name']));
						$opt['date_modified'] = date('Y-m-d');
						array_push($links, $opt);

						break;

					case 'blog':

						$opt['link'] = site_url() . 'blog/post/' . $v['url'];
						$opt['date_modified'] = date('Y-m-d', strtotime($v['date_published']));
						array_push($links, $opt);

						break;

					case 'pages':

						$opt['link'] = site_url() . 'page/' . $v['url'];
						$opt['date_modified'] = date('Y-m-d', strtotime($v['date_modified']));
						array_push($links, $opt);

						break;


					case 'kb':

						$opt['link'] = site_url() . 'kb/article/' . $v['url'];
						$opt['date_modified'] = date('Y-m-d', strtotime($v['date_modified']));
						array_push($links, $opt);

						break;
				}
			}
		}

		return $links;
	}
}

/* End of file Site_map_model.php */
/* Location: ./application/models/Site_map_model.php */