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

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return array
 */
function format_rss($type = '', $data = array())
{
	if (!empty($data))
	{
		$vars = array('name'         => config_item('sts_site_name') . ' ' . lang($type),
		              'email'        => config_item('sts_site_email'),
		              'content_type' => $type,
		);

		if ($type == 'products')
		{
			$vars['url'] = site_url('store');
			$vars['description'] = lang('rss_feed_for_products');
			$vars['location'] = site_url('rss/' . $type);

			foreach ($data as $v)
			{
				$vars['data'][] = array('title'       => $v['product_name'],
				                        'description' => $v['product_overview'],
				                        'url'         => site_url() . 'product/details/' . $v['product_id'] . '-' . strtolower(url_title($v['product_name'])),
				                        'date'        => date('r', strtotime($v['modified'])),
				                        'image'       => image('products', $v['photo_file_name'], $v['product_name'], '', TRUE, '75', '75'),
				);
			}
		}
		else
		{
			$vars['url'] = site_url('blog');
			$vars['description'] = lang('rss_feed_for_blog');
			$vars['location'] = site_url('rss/' . $type);

			foreach ($data as $v)
			{
				$vars['data'][] = array('title'       => $v['title'],
				                        'description' => $v['overview'],
				                        'url'         => site_url() . 'blog/' . $v['url'],
				                        'date'        => date('r', strtotime($v['date_published'])),
				                        'image'       => image('blog', $v['overview_image'], 'image', '', TRUE, '75', '75'),
				);
			}
		}
	}

	return $vars;
}


/* End of file rss_helper.php */
/* Location: ./application/helpers/rss_helper.php */