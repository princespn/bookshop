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

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function check_drip_feed($data = array())
{
	//checks if the user's signup date is long enough to show the blog entry to him.
	if (!empty($data['drip_feed']))
	{
		if (sess('user_logged_in'))
		{
			$start = strtotime(sess('date'));
			$end = strtotime(get_time(now(), TRUE));

			$days_between = ceil(abs($end - $start) / 86400);

			if ($days_between > $data['drip_feed'])
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function check_blog_permissions($data = array())
{
	if (!empty($data['require_registration']))
	{
		if (sess('user_logged_in'))
		{
			if (!empty($data['restrict_group']))
			{
				if (!empty($data['blog_groups']))
				{
					foreach ($data['blog_groups'] as $v)
					{
						if (sess('blog_group') == $v['group_id'])
						{
							return TRUE;
						}
					}
				}

				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param string $file
 * @param string $type
 */
function download_file($file = '', $type = '')
{
	$CI = &get_instance();

	$CI->load->helper('download');
	$CI->load->helper('file');

	switch ($type)
	{
		case 'support':

			$path = $CI->config->slash_item('sts_support_upload_folder_path') . $file;

			break;

		case 'backup_archive':

			$path = $CI->config->slash_item('sts_backup_path') . $file;

			break;

		case 'blog':
		case 'downloads':

			//check if this is a URL redirect first
			if (preg_match("/ftp:\/\//", $file) || preg_match("/http:\/\//", $file) || preg_match("/https:\/\//", $file))
			{
				redirect_page($file, TRUE, FALSE);
			}
			else
			{
				$path = $CI->config->slash_item('sts_site_download_file_path') . $file;
			}

			break;

		case 'orders':

			$path = $CI->config->slash_item('sts_products_upload_folder_path') . $file;

			break;
	}

	if (file_exists($path))
	{
		$data = file_get_contents($path);

		force_download($file, $data);
	}
	else
	{
		$CI->show->page('404', $CI->config->config);
	}

}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_content_page($data = array(), $field = '')
{
	$CI = &get_instance();

	//check if we need to split pages and set pagination
	$body = explode('{{page_break}}', $data[$field.'body']);
	//$body = array_combine(range(1, count($body)), $body);

	//now check if there is a page set
	if (count($body) > 1)
	{
		$data[$field.'body'] = $body[0];
		if (uri(4) && isset($body[(int)uri(4)]))
		{
			$data[$field.'body'] = $body[(int)uri(4)];
		}

		$a = array(
			'uri'        => site_url() . uri(1) . '/'. uri(2) . '/' . uri(3),
			'total_rows' =>count($body),
			'per_page'   => 1,
			'segment'    => 4,
		);

		$data['paginate'] = $CI->paginate->generate($a, $data[$field.'title']);
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return array
 */
function format_comment_response($data = array(), $type = 'member')
{
	return array(
		'status'    => $data['status'],
		'blog_id'   => $data['blog_id'],
		'parent_id' => $data['parent_id'],
		'user_id'   => $data['user_id'],
		'type'      => $type,
		'date'      => $type == 'admin' ? $data['reply_date'] : $data['date'],
		'comment'   => $type == 'admin' ? $data['admin_reply'] : $data['comment'],
	);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function format_comments($data = array())
{
	//format the comments to be within each parent comment if needed
	//supports only 1 level deep

	$tree = array();
	$sub = array();

	foreach ($data as $k => $v)
	{
		if ($v['parent_id'] == '0')
		{
			$tree[$v['id']] = $v;
		}
		else
		{
			array_push($sub, $v);
		}
	}

	foreach ($tree as $k => $v)
	{
		$tree[$k]['sub'] = array();

		foreach ($sub as $s)
		{
			if ($s['parent_id'] == $k)
			{
				array_push($tree[$k]['sub'], $s);
			}
		}
	}

	return $tree;
}

// ------------------------------------------------------------------------

/**
 * @param string $tags
 * @param string $type
 * @param string $class
 * @return string
 */
function format_tags($tags = '', $class = 'badge', $type = 'blog')
{
	$a = '';
	if (!empty($tags))
	{
		$b = explode('-', $tags);
		foreach ($b as $t)
		{
			$a .= '<span>' . anchor(site_url() . $type . '/tag/' . $t, $t, 'class="' . $class . ' label-blog-tags"') . '</span> ';

		}
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return array
 */
function shuffle_tags($data = array())
{
	$max = empty($data[0]['count']) ? 0 : $data[0]['count'];

	foreach ($data as $k => $v)
	{
		$p = 0;
		if ($max > 0)
		{
			$p = floor(($v['count'] / $max) * 100);
		}

		$data[$k]['class'] = round($p, -1);
	}

	shuffle($data);

	return $data;
}

// ------------------------------------------------------------------------

function set_blog_id($id = '')
{
	$a = url_title($id);
	$c = strlen(BLOG_PREPEND_LINK);

	return  str_replace(substr($a, 0,$c + 1), '', $a);
}



/* End of file content_helper.php */
/* Location: ./application/helpers/content_helper.php */