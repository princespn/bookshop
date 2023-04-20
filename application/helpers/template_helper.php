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
 * @param $src
 * @param $dst
 * @param $width
 * @param $height
 * @param int $crop
 * @return bool|string
 */
function image_resize($src, $dst, $width, $height, $crop = 0)
{

	if (!list($w, $h) = getimagesize($src))
	{
		return "Unsupported picture type!";
	}

	$type = strtolower(substr(strrchr($src, "."), 1));
	if ($type == 'jpeg')
	{
		$type = 'jpg';
	}
	switch ($type)
	{
		case 'bmp':
			$img = imagecreatefromwbmp($src);
			break;
		case 'gif':
			$img = imagecreatefromgif($src);
			break;
		case 'jpg':
			$img = imagecreatefromjpeg($src);
			break;
		case 'png':
			$img = imagecreatefrompng($src);
			break;
		default :
			return "Unsupported picture type!";
	}
	if ($w < $width or $h < $height)
	{
		$width = 1629;
		$height = 850;
	}
	if ($w < $width or $h < $height)
	{
		$width = 1533;
		$height = 800;
	}
	if ($w < $width or $h < $height)
	{
		$width = 1438;
		$height = 750;
	}
	if ($w < $width or $h < $height)
	{
		$width = 1380;
		$height = 720;
	}
	if ($w < $width or $h < $height)
	{
		$width = 1342;
		$height = 700;
	}
	if ($w < $width or $h < $height)
	{
		$width = 1246;
		$height = 650;
	}
	if ($w < $width or $h < $height)
	{
		$width = 1150;
		$height = 600;
	}
	if ($w < $width or $h < $height)
	{
		$width = 1054;
		$height = 550;
	}
	if ($w < $width or $h < $height)
	{
		$width = 958;
		$height = 500;
	}
	if ($w < $width or $h < $height)
	{
		$width = 863;
		$height = 450;
	}
	if ($w < $width or $h < $height)
	{
		$width = 767;
		$height = 400;
	}
	if ($w < $width or $h < $height)
	{
		$width = 671;
		$height = 350;
	}
	if ($w < $width or $h < $height)
	{
		$width = 575;
		$height = 300;
	}
	if ($w < $width or $h < $height)
	{
		return "Picture is too small. Minimum dimension: 575 x 350 pixels.";
	}

	// resize
	if ($crop)
	{
		$ratio = max($width / $w, $height / $h);
		$h = $height / $ratio;
		$x = ($w - $width / $ratio) / 2;
		$w = $width / $ratio;
	}
	else
	{
		$ratio = min($width / $w, $height / $h);
		$width = $w * $ratio;
		$height = $h * $ratio;
		$x = 0;
	}

	$new = imagecreatetruecolor($width, $height);

	// preserve transparency
	if ($type == "gif" or $type == "png")
	{
		imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
		imagealphablending($new, FALSE);
		imagesavealpha($new, TRUE);
	}

	imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

	switch ($type)
	{
		case 'bmp':
			imagewbmp($new, $dst);
			break;
		case 'gif':
			imagegif($new, $dst);
			break;
		case 'jpg':
			imagejpeg($new, $dst);
			break;
		case 'png':
			imagepng($new, $dst);
			break;
	}

	return TRUE;
}

// ------------------------------------------------------------------------

function filter_c($a = '', $v = '', $data = array())
{
	switch ($a)
	{
		case 'sort':

			$c  = explode('-', $v);
			$column = $c[0];
			$order = $c[1];

			if (!empty($data['sort']['column']) && !empty($data['sort']['order']))
			{
				if ($data['sort']['column'] == $column && $data['sort']['order'] == $order)
				{
					return TRUE;
				}
			}

			break;

		case 'brands':
		case 'categories':
		case 'price':
		case 'ratings':
		case 'tags':

		if (!empty($data[$a]))
		{
			foreach ($data[$a] as $g)
			{
				if ($g == $v)
				{
					return 'checked';
				}
			}
		}

			break;
	}
}

// ------------------------------------------------------------------------

function format_name($n = '', $delimiter = '|')
{
	$a = explode($delimiter, $n);

	return $a[0]  . ' ' . substr($a[1],0,1) .'.';
}

// ------------------------------------------------------------------------

function format_ratings($n = '')
{
	$stars = '';
	if ($n >= '0' AND $n <= '1')
	{
		if ($n <= '0.50')
		{
			$stars .= i('fa fa-star-half-empty');
		}
		else
		{
			$stars .= i('fa fa-star');
		}

		for ($i = 1; $i <= 4; $i++)
		{
			$stars .= i ( 'fa fa-star-o');
		}
	}

	if ($n > '1' AND $n <= '2')
	{
		$stars .= i('fa fa-star');

		if ($n <= '1.50')
		{
			$stars .= i('fa fa-star-half-empty');
		}
		else
		{
			$stars .= i('fa fa-star');
		}

		for ($i = 1; $i <= 3; $i++)
		{
			$stars .= i ( 'fa fa-star-o');
		}
	}

	if ($n > '2' AND $n <= '3')
	{
		$stars .= i('fa fa-star');
		$stars .= i('fa fa-star');

		if ($n <= '2.50')
		{
			$stars .= i('fa fa-star-half-empty');
		}
		else
		{
			$stars .= i('fa fa-star');
		}

		for ($i = 1; $i <= 2; $i++)
		{
			$stars .= i ( 'fa fa-star-o');
		}
	}

	if ($n > '3' AND $n <= '4')
	{
		$stars .= i('fa fa-star');
		$stars .= i('fa fa-star');
		$stars .= i('fa fa-star');

		if ($n <= '3.50')
		{
			$stars .= i('fa fa-star-half-empty');
		}
		else
		{
			$stars .= i('fa fa-star');
		}

		for ($i = 1; $i <= 1; $i++)
		{
			$stars .= i ( 'fa fa-star-o');
		}
	}

	if ($n > '4' AND $n <= '5')
	{
		$stars .= i('fa fa-star');
		$stars .= i('fa fa-star');
		$stars .= i('fa fa-star');
		$stars .= i('fa fa-star');
		if ($n <= '4.50')
		{
			$stars .= i('fa fa-star-half-empty');
		}
		else
		{
			$stars .= i('fa fa-star');
		}
	}



	return $stars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function format_attribute_data($data = array())
{
	$html = '<ul class="attribute-list list-unstyled mb-1">';

	if (!empty($data['attribute_data']))
	{
		$a = unserialize($data['attribute_data']);

		foreach ($a as $v)
		{
			if (!empty($v['attribute_name']))
			{
				$html .= '<li><small><strong>' . $v['attribute_name'] . '</strong> - ';

				if ($v['attribute_type'] == 'file')
				{
					$html .= $v['file_name'];
				}
				elseif ($v['attribute_type'] == 'image')
				{
					$html .= img(array('src'   => $v['path'],
					                   'class' => 'img-cart')
					);
				}
				else
				{
					$html .= $v['value'];
				}

				$html .= '</small></li>';
			}
		}
	}

	$html .= '</ul>';

	return $html;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @return array
 */
function get_chart_colors($type = 'admin')
{
	$CI = &get_instance();

	if (file_exists($CI->config->slash_item('base_physical_path') . '/themes/site/' . $CI->config->item('layout_design_site_theme') . '/theme_info.php'))
	{
		$theme = file_get_contents($CI->config->slash_item('base_physical_path') . '/themes/site/' . $CI->config->item('layout_design_site_theme') . '/theme_info.php');

		preg_match('|chart-bg-color:(.*)$|mi', $theme, $cbg_color);
		preg_match('|chart-grid-color:(.*)$|mi', $theme, $cg_color);
		preg_match('|chart-grid-color2:(.*)$|mi', $theme, $cg_color2);
		preg_match('|chart-width:(.*)$|mi', $theme, $cwidth);
		preg_match('|chart-height:(.*)$|mi', $theme, $cheight);
	}

	$row = array(
		'bg_color'     => !empty($cbg_color[1]) ? trim($cbg_color[1]) : $CI->config->item('chart_bg_color'),
		'grid_color'   => !empty($cg_color[1]) ? trim($cg_color[1]) : random_element($CI->config->item('chart_graph_colors')),
		'grid_color2'  => !empty($cg_color2[1]) ? trim($cg_color2[1]) : random_element($CI->config->item('chart_graph_colors2')),
		'chart_width'  => !empty($cwidth[1]) ? trim($cwidth[1]) : $CI->config->item('chart_graph_width'),
		'chart_height' => !empty($cheight[1]) ? trim($cheight[1]) : $CI->config->item('chart_graph_height'),
	);

	return $row;
}

// ------------------------------------------------------------------------

/**
 * @param string $string
 * @return mixed|string|string[]|null
 */
function minify($string = '')
{
	$string = preg_replace("/\s{2,}/", " ", $string);
	$string = str_replace("\n", "", $string);
	$string = str_replace('@CHARSET "UTF-8";', "", $string);
	$string = str_replace(', ', ",", $string);
	$string = preg_replace("/(\/\*[\w\'\s\r\n\*\+\,\"\-\.]*\*\/)/", "", $string);

	return $string;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @return mixed
 */
function session_tags($type = '', $data = array())
{
	$row = $_SESSION;

	$atts = !config_option('enable_no_follow_links') ? 'rel="sponsored"' : 'rel="nofollow"';

	$row['affiliate_url'] = anchor(aff_tools_url($type), '', $atts);
	$row['affiliate_url_text'] = aff_tools_url($type);
	$row['unsubscribe_url_text'] = site_url('email/subscriptions/' . md5(config_Item('sts_system_domain_key')) . '/' . $row['primary_email']);
	$row['unsubscribe_url'] = anchor($row['unsubscribe_url_text']);

	return $row;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @param array $data
 * @return mixed|string
 */
function parse_string($str = '', $data = array())
{
	foreach ($data as $k => $v)
	{
		if (!is_array($v))
		{
			$str = str_replace('{{ ' . $k . ' }}', $v, $str);
			$str = str_replace('{{' . $k . '}}', $v, $str);
		}
	}

	return $str;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $cat
 * @return mixed
 */
function render_template($data = array(), $cat = '')
{
	$CI = &get_instance();
	$c = !empty($cat) ? $cat : $CI->config->item('module_view_path');

	return html_snippet($c, $data, $CI->config->item('module_html_data'));
}

// ------------------------------------------------------------------------

/**
 * @param string $folder
 * @return array
 */
function get_images($folder = 'backgrounds')
{
	$map = directory_map('./images/uploads/' . $folder);

	$images = array();

	foreach ($map as $v)
	{
		$a = explode('.', $v);
		if (in_array(end($a), config_item('default_image_types')))
		{
			$images[] = $v;
		}
	}

	return $images;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return mixed
 */
function parse_text($str = '')
{
	$CI = &get_instance();

	return $CI->show->parse_tpl($CI->config->config, $str);
}

// ------------------------------------------------------------------------

/**
 * @param string $cat
 * @param array $data
 * @param string $tpl
 * @return mixed
 */
function html_snippet($cat = '', $data = array(), $tpl = '')
{
	$CI = &get_instance();

	//set the path based on the module data type and folder name
	$path = $data['row']['module']['module_type'] . '/' . $data['row']['module']['module_folder'] . '/views/';

	return $CI->show->display($cat, $tpl, $data, TRUE, APPPATH . 'modules/' . $path);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @param string $type
 * @return string
 */
function photo_path($str = '', $type = 'products')
{
	if (file_exists(PUBPATH . '/images/uploads/' . $type . '/' . $str))
	{
		$str = base_url('images/uploads/' . $type . '/' . $str);
	}

	return $str;

}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 */
function ajax_response($data = array(), $type = 'Content-Type: application/json')
{
	//sends form responses back to the browser via ajax
	$CI = &get_instance();

	$CI->output->set_header($type);

	if (empty($data))
	{
		$data = array('type' => 'error',
		              'msg'  => lang('invalid_ajax_request'),
		);
	}

	//set flashdata if we're redirecting
	if (!empty($data['redirect']) && $data['type'] == 'success' && !empty($data['msg']))
	{
		$CI->session->set_flashdata('success', lang($data['msg']));
	}

	echo json_encode($data);

	if (defined('INIT_TYPE') && INIT_TYPE != 'external')
	{
		//complete innodb transactions...
		$CI->init->db_trans('trans_complete');
	}

	exit;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool|mixed
 */
function check_infinite_scroll($data = array())
{
	if (!empty($data['paginate']['prev_next']['right']))
	{
		return $data['paginate']['prev_next']['right'];
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return bool
 */
function image_manager($str = '')
{
	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param array $menu
 * @param string $class
 * @param bool $sidebar
 * @return string
 */
function format_menu($menu = array(), $class = 'class = "nav"', $sidebar = FALSE)
{
	$a = '<ul ' . $class . '>';

	if (!empty($menu))
	{
		$c = $sidebar == TRUE ? 'list-group-item' : 'nav-item';
		$d = '';
		$e = $sidebar == TRUE ? i('fa fa-caret-right') : '';
		$i = 1;
		foreach ($menu as $v)
		{
			if ($v['menu_link_type'] == 'dropdown')
			{
				$a .= '<li class="' . $c . ' dropdown"> ' . $d;

				if ($sidebar == TRUE)
				{
					$f = '';
					$g = 'data-toggle="collapse" data-target="#sub-level-' . $i . '" aria-expanded="false"';
				}
				else
				{
					$f = 'nav-link';
					$g = 'data-toggle="dropdown"  role="button" aria-haspopup="true" aria-expanded="false"';
				}

				$a .= $d . ' ' . anchor('#', $e . ' ' . $v['names']['menu_link_name'], 'class="' . $f . '" ' . $g);


				if (!empty($v['sub_menu_links']))
				{
					$a .= $sidebar == TRUE ? '<ul class="submenu list-group collapse" id ="sub-level-' . $i . '">' : '<div class="dropdown-menu">';

					foreach ($v['sub_menu_links'] as $s)
					{
						$a .= $sidebar == TRUE ? '<li class="list-unstyled">' : '';
						$a .= anchor(format_url($s['menu_link']), $s['names']['menu_link_name'], 'class="dropdown-item ' . $s['menu_options'] . '"');
						$a .= $sidebar == TRUE ? '</li>' : '';
					}

					$a .= $sidebar == TRUE ? '</ul>' : '</div>';
				}
			}
			elseif ($v['menu_link_type'] == 'mega')
			{
				$a .= '<li class="' . $c . ' dropdown"> ' . $d;

				if ($sidebar == TRUE)
				{
					$f = '';
					$g = 'data-toggle="collapse" data-target="#sub-level-' . $i . '" aria-expanded="false" aria-controls="sub-level-' . $i . '"';
				}
				else
				{
					$f = 'nav-link';
					$g = 'dropdown-toggle" data-toggle="dropdown"  role="button" aria-haspopup="true" aria-expanded="false"';
				}

				$a .= $d . ' ' . anchor('#', $e . ' ' . $v['names']['menu_link_name'], 'class="' . $f . '" ' . $g);

				$a .= $sidebar == TRUE ? '<ul class="submenu list-group collapse" id ="sub-level-' . $i . '">' : '<div class="dropdown-menu">';

				$a .= $v['menu_code'];

				$a .= $sidebar == TRUE ? '</ul>' : '</div>';
			}
			else
			{
				$c = $sidebar == TRUE ? 'list-group-item' : 'nav-item';
				$f = $sidebar == TRUE ? '' : 'class="nav-link" ';

				$a .= '<li class="' . $c . '">' . $d . ' ' . anchor(format_url($v['menu_link']), $v['names']['menu_link_name'], $f . $v['menu_options']);
			}

			$a .= '</li>';
			$i++;
		}
	}

	$a .= '</ul>';

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function get_templates()
{
	$map = directory_map('./application/views/site/');

	$dir = array();
	foreach ($map as $k => $d)
	{
		$k = str_replace('/', '', $k);
		asort($d);
		$dir[$k] = $d;
	}

	ksort($dir);

	return $dir;
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function get_module_templates()
{
	$data = array();

	$folders = array('affiliate_marketing', 'member_reporting', 'payment_gateways');

	foreach ($folders as $f)
	{
		$map = directory_map('./application/modules/' . $f . '/');

		$data['name'][$f] = array();
		$data['path'][$f] = array();

		foreach ($map as $k => $v)
		{
			$folder = $f == 'payment_gateways' ? 'checkout' : 'members';

			$t = directory_map('./application/modules/' . $f . '/' . $k . 'views/' . $folder);

			if (!empty($t))
			{
				foreach ($t as $y)
				{
					array_push($data['name'][$f], $k . $y);
					array_push($data['path'][$f], $y . '/' . rtrim($k, '/') . '/' . $folder);
				}
			}
		}
	}

	foreach ($folders as $f)
	{
		asort($data['path'][$f]);
		asort($data['name'][$f]);
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param string $m
 * @return mixed|string
 */
function confirm_deletion($m = '')
{
	$msg = 'are_you_sure_you_want_to_do_this';

	switch ($m)
	{
		case 'Members::view':

			$msg = 'are_you_sure_you_want_to_delete_this_user';

			break;

		case 'Products::view':
		case 'Products::update':

			$msg = 'are_you_sure_you_want_to_delete_this_product';

			break;
	}

	return lang($msg);
}

// ------------------------------------------------------------------------

/**
 * @param string $text
 * @return string
 */
function format_tag($text = '')
{
	$CI = &get_instance();

	return $CI->config->item('template_tag_left') . $text . $CI->config->item('template_tag_right');
}

// ------------------------------------------------------------------------

/**
 * @param string $text
 * @param bool $sanitize
 * @return string
 */
function text_decode($text = '', $sanitize = TRUE)
{
	return html_decode($text, $sanitize);
}

// ------------------------------------------------------------------------

/**
 * @param string $text
 * @return string
 */
function text_encode($text = '')
{
	return addslashes(htmlentities($text));
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @param bool $sanitize
 * @return string
 */
function html_decode($str = '', $sanitize = TRUE)
{
	$CI = &get_instance();

	$a = html_entity_decode($str, ENT_QUOTES, $CI->config->item('charset'));

	$a = stripslashes($a);

	return $sanitize == TRUE ? $CI->dbv->input_clean($a) : $a;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param string $method
 * @param string $id
 * @param string $module_id
 * @return string
 */
function prev_next_item($type = 'left', $method = '', $id = '', $module_id = '')
{
	$m = strtolower(str_replace('::', '/', $method));
	$disabled = !empty($id) ? '' : 'disabled';

	$url = admin_url($m . '/' . $id . '/' . $module_id);

	return anchor($url, i('fa ' . TPL_ADMIN_CHEVRON . '-' . $type), 'class="btn btn-primary ' . $disabled . '"');
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $options
 * @return string
 */
function next_page($type = 'left', $options = array())
{
	$CI = &get_instance();

	if ($options['num_pages'] > 1)
	{
		if (isset($options['prev_next'][$type]))
		{
			$url = admin_url() . $CI->uri->slash_segment(2) . $CI->uri->slash_segment(3);
			$url .= $options['prev_next'][$type] . '/';

			for ($i = 5; $i <= 7; $i++)
			{
				if ($CI->uri->segment($i))
				{
					$url .= $CI->uri->slash_segment($i);
				}
			}

			if (!empty($_GET))
			{
				$url .= '?' . http_build_query($_GET);
			}

			$disabled = is_numeric($options['prev_next'][$type]) ? '' : 'disabled';
			$url = anchor($url, i('fa ' . TPL_ADMIN_CHEVRON . '-' . $type), 'class="btn btn-primary ' . $disabled . '"');

			return $url;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $url
 * @return bool
 */
function check_login($url = 'login')
{
	$CI = &get_instance();

	if (!$CI->sec->check_login_session('member', FALSE))
	{
		redirect($url);
	}

	return TRUE;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param string $img
 * @param string $alt
 * @param string $class
 * @param bool $thumb
 * @param string $width
 * @param string $height
 * @return string
 */
function image($type = '', $img = '', $alt = '', $class = 'img-responsive', $thumb = FALSE, $width = '', $height = '')
{
	//for showing public side images
	switch ($type)
	{
		case 'products':

			$src = !empty($img) ? $img : '';

			if (!empty($src))
			{
				if (config_enabled('sts_image_' . $type . '_use_thumbnails') && $thumb == TRUE)
				{
					if (file_exists(PUBPATH . '/images/thumbs/' . $type . '/' . $src))
					{
						$src = base_url('images/thumbs/' . $type . '/' . $src);
					}
				}
				else
				{
					$src = photo_path($src);
				}
			}

			break;

		case 'home_page_brands':

			$src = empty($img) ? 'images/uploads/brands/default_brand_logo.png' : photo_path($img, 'brands');

			break;

		case 'members':

			$src = empty($img) ? 'images/profile.png' : $img;

			break;

		case 'forum':

			if (!empty($img))
			{
				$src = $img;
			}
			else
			{
				return '<div class="ltr-avatar">
						  <span class="ltr">' . substr($alt, 0, 1) . '
						  </span>
						</div>';
			}


			break;

		default:

			$src = $img;

			break;
	}

	$a = array(
		'src'   => empty($src) ? 'images/no-photo.jpg' : $src,
		'alt'   => empty($alt) ? lang('no_photo') : $alt,
		'class' => $class,
	);

	if (!empty($width))
	{
		$a['width'] = $width;
	}
	if (!empty($height))
	{
		$a['height'] = $height;
	}

	return config_enabled('lazy_load_images') ? lazy_img($a) : img($a);
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return string
 */
function lazy_img($data = array())
{
	return '<img data-src="' . ($data['src']) . '" src="' . base_url('images/no-photo.jpg') . '" alt="' . $data['alt'] . '" class="lazy ' . $data['class'] . '" />';

	return img($data);
}

// ------------------------------------------------------------------------

/**
 * @param string $img
 * @return string
 */
function profile_photo($img = '')
{
	if (file_exists(PUBPATH . '/images/thumbs/backgrounds/' . $img))
	{
		return base_url('images/thumbs/backgrounds/' . $img);
	}

	return base_url('images/uploads/backgrounds/' . $img);
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function generate_internal_links()
{
	$vars = array('1' => array('blog', 'brands', 'cart', 'register'),
	              '2' => array('faq', 'contact', 'gallery', 'members'),
	              '3' => array('kb', 'store', 'forum', 'login'));

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param array $data
 * @param string $class
 * @param bool $image
 * @param string $id
 * @return string
 */
function photo($type = 'user', $data = array(), $class = '', $image = FALSE, $id = '0')
{
	//for showing admin side images
	$img = TPL_DEFAULT_PRODUCT_PHOTO;
	$folder_img = 'images/folder.png';

	switch ($type)
	{
		case 'Gallery::update':

			if (!empty($data['gallery_photo']))
			{
				$img = $data['gallery_photo'];
			}

			return img($img, $img, array('id' => $id, 'class' => $class));

			break;

		case 'Widgets::create':
		case 'Widgets::update':

			$img = SITE_BUILDER . '/assets/designs/preview/';

			if (!empty($data['thumbnail']))
			{
				$img .= $data['thumbnail'];
			}
			else
			{
				$img .= 'module-custom.png';
			}

			return img($img, $data['thumbnail'], array('id' => $id, 'class' => $class));

			break;

		case 'Suppliers::update':

			$img = 'images/no-photo.jpg';

			if (!empty($data['image']))
			{
				$img = str_replace('{{base_url}}', base_url(), $data['image']);
			}

			return img($img, $data['image'], array('id' => $id, 'class' => $class));

			break;

		case 'Admin_users::update':
		case 'Admin_users::view':

			$img = TPL_DEFAULT_ADMIN_PHOTO;

			if (!empty($data['photo']))
			{
				$img = $data['photo'];
			}

			return img($img, $data['username'], array('id' => $id, 'class' => $class));

			break;

		case 'Blog_comments::view':

			$img = TPL_DEFAULT_ADMIN_PHOTO;

			if ($data['type'] == 'admin')
			{
				if (!empty($data['admin_photo']))
				{
					$img = $data['admin_photo'];
				}
				$alt = $data['admin_username'];
			}
			else
			{
				if (!empty($data['profile_photo']))
				{
					$img = $data['profile_photo'];
				}
				$alt = $data['username'];
			}

			return img($img, $alt, array('id' => $id, 'class' => $class));

			break;

		case 'Members::view':
		case 'Members::update':
		case 'Support_tickets::view':
		case 'Members::general_search':
		case 'Support_tickets::general_search':

			$img = TPL_DEFAULT_ADMIN_PHOTO;

			if (!empty($data['profile_photo']))
			{
				$img = $data['profile_photo'];
			}

			return img($img, $data['username'], array('id' => $id, 'class' => $class));

			break;

		case 'Forum_topics::view':
		case 'Forum_topics::general_search':

			$img = TPL_DEFAULT_ADMIN_PHOTO;

			if (!empty($data['profile_photo']))
			{
				$img = $data['profile_photo'];
			}
			elseif (!empty($data['admin_photo']))
			{
				$img = $data['admin_photo'];
			}

			return img($img, $img, array('id' => $id, 'class' => $class));

			break;

		case 'Support_tickets::update':
		case 'Forum_topics::update':

			if (!empty($data['member_id']))
			{
				if (!empty($data['profile_photo']))
				{
					$img = $data['profile_photo'];
				}
			}
			else
			{
				if (!empty($data['admin_photo']))
				{
					$img = $data['admin_photo'];
				}
			}

			return img($img, $img, array('id' => $id, 'class' => $class));

			break;

		case 'Products_reviews::view':

			$img = !empty($data['profile_photo']) ? $data['profile_photo'] : TPL_DEFAULT_ADMIN_PHOTO;

			return img($img, $data['username'], array('id' => $id, 'class' => $class));

			break;

		case 'Products::view':

			if (!empty($data['photo_file_name']) && file_exists(PUBPATH . '/images/uploads/products/' . $data['photo_file_name']))
			{
				return img('images/uploads/products/' . $data['photo_file_name'], $data['product_name'], array('class' => $class));
			}

			return $image == TRUE ? img($folder_img, $data['product_name'], array('class' => $class)) : '<div class="text-center dash-photo">' . i('fa fa-camera-retro') . '</div>';

			break;

		case 'Products_categories::view':
		case 'Products_categories::update':

			if (!empty($data['category_image']))
			{
				return img($data['category_image'], $data['category_name'], array('class' => $class,
				                                                                  'id'    => $id));
			}

			return $image == TRUE ? img($folder_img, $data['category_name'], array('class' => $class,
			                                                                       'id'    => $id)) : '<div class="text-center dash-photo">' . i('fa fa-camera-retro') . '</div>';

			break;

		case 'Brands::view':
		case 'Brands::update':

			if (!empty($data['brand_image']))
			{
				return img($data['brand_image'], $data['brand_image'], array('id' => $id, 'class' => $class));
			}

			return $image == TRUE ? img($folder_img, $data['brand_image'], array('class' => $class,
			                                                                     'id'    => $id)) : '<div class="text-center dash-photo">' . i('fa fa-camera-retro') . '</div>';

			break;

		case 'Products_groups::view':
		case 'Products_groups::update':

			if (!empty($data['image']))
			{
				return img($data['image'], $data['image'], array('id' => $id, 'class' => $class));
			}

			return $image == TRUE ? img($folder_img, $data['image'], array('class' => $class,
			                                                               'id'    => $id)) : '<div class="text-center dash-photo">' . i('fa fa-camera-retro') . '</div>';

			break;

		case 'Products::update':

			$img = TPL_DEFAULT_PRODUCT_PHOTO;

			if (!empty($data['photo_file_name']) && file_exists(PUBPATH . '/images/uploads/products/' . $data['photo_file_name']))
			{
				$img = 'images/uploads/products/' . $data['photo_file_name'];
				$image = TRUE;
			}

			return $image == TRUE ? img($img, $data['product_name'], array('class' => $class)) : '<div class="text-center preview-img">' . i('fa fa-shopping-' . config_item('layout_design_shopping_cart_or_bag')) . '</div>';

			break;

		case 'Affiliate_payment_options::view':

			$img = 'images/modules/tools.png';

			if (file_exists(PUBPATH . '/images/modules/module_' . $data['module_type'] . '_' . $data['module_folder'] . '.' . config_option('member_marketing_tool_ext')))
			{

				$img = 'images/modules/module_' . $data['module_type'] . '_' . $data['module_folder'] . '.' . config_option('member_marketing_tool_ext');
			}

			return img($img, $data['module_folder'], array('id' => $id, 'class' => $class));

			break;

		default:

			return img($img, $img, array('id' => $id, 'class' => $class));

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @return array
 */
function get_flags()
{
	$a = file_get_contents(PUBPATH . '/themes/css/flags/flags.css');

	$var = explode("\n", $a);

	$flags = array();

	foreach ($var as $v)
	{
		preg_match("/^.flag-(\w+)/", $v, $results);

		if (!empty($results))
		{
			$flags[$results[1]] = $results[1];
		}
	}

	asort($flags);

	return $flags;
}

// ------------------------------------------------------------------------

/**
 * @param string $link
 * @param string $lang
 * @param string $alert
 * @param string $class
 * @param bool $go_back
 * @return string
 */
function tpl_no_values($link = '', $lang = '', $alert = 'no_records_found', $class = 'danger', $go_back = TRUE)
{
	$CI = &get_instance();

	$html = '<div class="no-record-found alert alert-' . $class . ' animated fadeIn"><div class="row">
        <div class="col-md-8 text-' . $class . '"><p class="lead">' . i('fa fa-info-circle') . ' ' . lang($alert) . '</p></div>
        <div class="col-md-4 text-right">';

	if ($go_back == TRUE)
	{
		$html .= '<a href="javascript:history.go(-1)" class="btn btn-' . $class . '">' . i('fa fa-undo') . ' ' . lang('go_back') . '</a>';
	}

	if ($CI->sec->check_admin_permissions(CONTROLLER_CLASS, 'create') == TRUE)
	{
		if (!empty($link) && !empty($lang))
		{
			$html .= ' <a href="' . admin_url($link) . '" class="btn btn-' . $class . '">' . i('fa fa-plus') . ' ' . lang($lang) . '</a>';
		}
	}

	$html .= '</div></div></div>';

	return $html;
}

// ------------------------------------------------------------------------

/**
 * @param string $class
 * @return bool|string
 */
function mobile_view($class = '')
{
	$CI = &get_instance();

	if ($CI->session->admin['mobile_view'] == 1)
	{
		return !empty($class) ? $class : TRUE;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $lang
 * @param string $icon
 * @param string $total
 * @param bool $translate
 * @param string $sub
 * @return string
 */
function generate_sub_headline($lang = '', $icon = 'fa-desktop', $total = '', $translate = TRUE, $sub = '')
{
	$CI = &get_instance();

	$icon = empty($icon) ? 'fa-desktop' : $icon;

	if ($translate == TRUE)
	{
		if ($total == 1)
		{
			$lang = $total == '0' ? plural($lang) : singular($lang);
			$sub = !empty($sub) ? $total == '0' ? plural($sub) : singular($sub) : '';
		}
		else
		{
			$lang = plural($lang);
			$sub = !empty($sub) ? plural($sub) : '';
		}
	}

	$lang = $translate == TRUE ? lang(strtolower(str_replace(' ', '_', $lang))) : $lang;

	$desc = '';

	if ($CI->input->get('search_term'))
	{
		$desc = ' - ' . lang('search_term') . ': <strong>' . $CI->input->get('search_term') . '</strong>';
	}


	$a = heading(i('fa ' . $icon) . ' ' . $total . ' ' . $lang . ' ' . $desc, 2, 'class="sub-header block-title"');

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return string
 */
function format_notes($str = '')
{
	return nl2br($str);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return string
 */
function format_response($str = '')
{
	$str = html_entity_decode($str);
	$str = html_escape($str);
	$str = parse_codes($str);

	return nl2br_except_pre($str);
}

// ------------------------------------------------------------------------

/**
 * @param $text
 * @return string|string[]|null
 */
function parse_codes($text)
{
	// BBcode array
	$find = array(
		'~\[code\](.*?)\[/code\]~s',
		'~\[b\](.*?)\[/b\]~s',
		'~\[i\](.*?)\[/i\]~s',
		'~\[u\](.*?)\[/u\]~s',
		'~\[quote\](.*?)\[/quote\]~s',
		'~\[quote=(.*?)\](.*?)\[/quote\]~s',
		'~\[size=(.*?)\](.*?)\[/size\]~s',
		'~\[color=(.*?)\](.*?)\[/color\]~s',
		'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
		'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
	);

	// HTML tags to replace BBcode
	$replace = array(
		'<div class="show-code"><h6>' . lang('code') . ': </h6><pre class="pre-scrollable">$1</' . 'pre></div>',
		'<strong>$1</strong>',
		'<i>$1</i>',
		'<span style="text-decoration:underline;">$1</span>',
		'<blockquote class="blockquote"><i class="fa fa-quote-left"></i> $1<br /><i class="fa right"></i></' . 'blockquote>',
		'<blockquote class="blockquote"><i class="fa fa-quote-left"></i> <small>' . lang('by') . ' $1</small><hr /> $2 <i class="fa fa-quote-right"></i></' . 'blockquote>',
		'<span style="font-size:$1px;">$2</span>',
		'<span style="color:$1;">$2</span>',
		'<a href="$1" target="_blank">$1</a>',
		'<img src="$1" alt="$1" class="img-fluid img-forum-topic" />',
	);

	// Replacing the BBcodes with corresponding HTML tags
	if (config_enabled('sts_forum_enable_bbcode'))
	{
		$text = preg_replace($find, $replace, $text);
	}

	$a = strip_bbcode($text); //remove the other unnecessary code

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param $text_to_search
 * @return string|string[]|null
 */
function strip_bbcode($text_to_search)
{
	$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
	$replace = '';

	return preg_replace($pattern, $replace, $text_to_search);
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return int
 */
function get_id($str = '')
{
	if (!empty($str))
	{
		$a = explode('-', $str);

		if (count($a) > 1)
		{
			return (int)$a[0];
		}
	}

	redirect('404');

}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $lang
 * @param bool $name
 * @return array
 */
function address_array($data = array(), $lang = 'none', $name = FALSE)
{
	$a = array('' => lang($lang));

	foreach ($data as $v)
	{
		$add = '';
		if ($name == TRUE)
		{
			$add = $v['fname'] . ' ' . $v['lname'] . ' - ';
		}
		$add .= $v['address_1'] . ' ' . $v['address_2'] . ' ' . $v['city'] . ' ' . $v['region_name'] . ', ' . $v['postal_code'] . ' ' . $v['country_iso_code_2'];
		$a[$v['id']] = $add;
	}

	return $a;
}

// ------------------------------------------------------------------------

/**
 * @param string $html
 * @return string
 */
function small($html = '')
{
	return '<small>' . $html . '</small>';
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @param string $msg
 * @param string $class
 * @param bool $headline
 * @return string
 */
function alert($type = '', $msg = '', $class = 'hover-msg', $headline = TRUE)
{
	if ($type == 'error')
	{
		$a = '<div class="alert alert-danger animated ' . ALERT_ANIMATION_ERROR . ' text-capitalize ' . $class . '"><button type="button" class="close" data-dismiss="alert">×</button>';

		if ($headline == TRUE)
		{
			$a .= '<div class="alert-box">' . i('fa fa-info-circle') . ' <strong>' . lang('please_check_errors') . '</strong></div>';
		}
	}
	else
	{
		$a = '<div class="alert alert-success animated ' . ALERT_ANIMATION_SUCCESS . ' text-capitalize alert-msg ' . $class . '"><button type="button" class="close" data-dismiss="alert">×</button>';
	}

	$a .= $msg . ' <div id="msg-details"></div></div>';

	return $a;
}

// ------------------------------------------------------------------------

function css_ver()
{
	$a = array('primary', 'secondary', 'success', 'info', 'danger', 'warning', 'light', 'dark');
	$b = array('background', 'text', 'link','footer','footertext', 'pageheader', 'pageheadertext', 'topnav', 'topnavtext');
	$c = array('yi_contrast', 'border_radius', 'enable_rounded', 'enable_gradients', 'header_font', 'base_font');
	$vars = '';
	foreach ($a as $e)
	{
		$vars .= config_item('layout_design_theme_' . $e . '_button');
	}

	foreach ($b as $e)
	{
		$vars .= config_item('layout_design_theme_' . $e . '_color');
	}

	foreach ($c as $e)
	{
		$vars .= config_item('layout_design_theme_' . $e);
	}

	return substr(md5($vars), '1', '10');
}


// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed|string
 */
function coupon_redemptions($data = array())
{
	if ($data['uses_per_coupon'] > 0)
	{
		if ($data['uses_per_coupon'] > $data['coupon_uses'])
		{
			return '<span class="label label-danger text-capitalize">' . lang('limit_reached') . '</span>';
		}
		else
		{
			return $data['uses_per_coupon'];
		}
	}

	return lang('unlimited');
}

// ------------------------------------------------------------------------

/**
 * @param string $status
 * @param bool $words
 * @param bool $html
 * @return mixed|string
 */
function set_status($status = '', $words = FALSE, $html = TRUE)
{
	$a = 'warning';
	$line = 'inactive';
	$icon = 'exclamation-circle';
	if ($status == 1)
	{
		$a = 'success';
		$line = 'active';
		$icon = 'check';
	}

	if ($words == TRUE)
	{
		return $html == TRUE ? '<span class="label label-' . $a . '">' . lang($line) . '</span>' : lang($line);
	}
	else
	{
		return '<span class="status-' . $line . '">' . i('fa fa-' . $icon) . '</span>';
	}
}

// ------------------------------------------------------------------------

/**
 * @return bool
 */
function show_cart()
{
	if (!empty($_SESSION['cart_details']['items']))
	{
		return TRUE;
	}

	return FALSE;
}


/* End of file template_helper.php */
/* Location: ./application/helpers/template_helper.php */