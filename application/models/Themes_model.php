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



use ScssPhp\ScssPhp\Compiler;

class Themes_model extends CI_Model
{

	// ------------------------------------------------------------------------

	/**
	 * @param string $type
	 * @return array
	 */
	public function get_themes($type = 'site')
	{
		$map = directory_map('./themes/' . $type);

		$themes = array();

		foreach ($map as $k => $v)
		{
			if (file_exists(PUBPATH . '/themes/' . $type . '/' . $k . '/theme_info.php'))
			{
				$theme_data = file_get_contents(PUBPATH . '/themes/' . $type . '/' . $k . '/theme_info.php');

				preg_match('|Theme Name:(.*)$|mi', $theme_data, $name);
				preg_match('|Theme URI:(.*)$|mi', $theme_data, $uri);
				preg_match('|Theme Preview:(.*)$|mi', $theme_data, $preview);
				preg_match('|Version:(.*)|i', $theme_data, $version);
				preg_match('|Description:(.*)$|mi', $theme_data, $description);
				preg_match('|Author:(.*)$|mi', $theme_data, $author_name);
				preg_match('|Author URI:(.*)$|mi', $theme_data, $author_uri);

				$theme['theme_folder'] = str_replace('/', '', $k);
				$theme['theme_name'] = !empty($name[1]) ? trim($name[1]) : '';
				$theme['theme_uri'] = !empty($uri[1]) ? trim($uri[1]) : '';
				$theme['theme_image'] = !empty($preview[1]) ? trim($preview[1]) : '';
				$theme['theme_version'] = !empty($version[1]) ? trim($version[1]) : '';
				$theme['theme_description'] = !empty($description[1]) ? trim($description[1]) : '';
				$theme['theme_author'] = !empty($author_name[1]) ? trim($author_name[1]) : '';
				$theme['theme_author_uri'] = !empty($author_uri[1]) ? trim($author_uri[1]) : '';

				array_push($themes, $theme);
			}
		}

		return $themes;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $theme
	 * @return false|string
	 */
	public function get_css($theme = '')
	{
		$css = file_get_contents(PUBPATH . '/themes/site/' . $theme . '/css/style.css');

		return $css;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $theme
	 * @return array
	 */
	public function get_css_colors($theme = '')
	{
		$colors = array();

		$path = PUBPATH . '/themes/site/' . $theme . '/css/colors';

		if (file_exists($path))
		{
			$vars = directory_map($path, 1);

			foreach ($vars as $v)
			{
				$name = $this->read_theme_config(PUBPATH . '/themes/site/' . $theme . '/css/colors/' . $v);

				if (!empty($name['color']))
				{
					$v = rtrim($v, '.css');
					$colors[$v] = $name['color'];
				}
			}

			return $colors;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * @param string $theme
	 * @return bool
	 */
	public function load_palette($theme = '')
	{
		$a = array();

		if (file_exists(PUBPATH . '/themes/site/' . $theme . '/theme_info.php'))
		{
			$palette = file_get_contents(PUBPATH . '/themes/site/' . $theme . '/theme_info.php');

			preg_match('|primary:(.*)$|mi', $palette, $primary);
			preg_match('|secondary:(.*)$|mi', $palette, $secondary);
			preg_match('|success:(.*)$|mi', $palette, $success);
			preg_match('|info:(.*)$|mi', $palette, $info);
			preg_match('|warning:(.*)$|mi', $palette, $warning);
			preg_match('|danger:(.*)$|mi', $palette, $danger);
			preg_match('|light:(.*)$|mi', $palette, $light);
			preg_match('|dark:(.*)$|mi', $palette, $dark);

			preg_match('|body-bg:(.*)$|mi', $palette, $background);
			preg_match('|body-color:(.*)$|mi', $palette, $text);
			preg_match('|link-color:(.*)$|mi', $palette, $link);
			preg_match('|footer-color:(.*)$|mi', $palette, $footer);

			preg_match('|headings-font-family:(.*)$|mi', $palette, $header_font);
			preg_match('|font-family-base:(.*)$|mi', $palette, $base_font);

			preg_match('|border-radius:(.*)$|mi', $palette, $border_radius);
			preg_match('|yi-contrast:(.*)$|mi', $palette, $yi_contrast);

			$a['primary'] = empty($primary[1]) ? '#007bff' : trim(str_replace(';', '', $primary[1]));
			$a['secondary'] = empty($secondary[1]) ? '#868E96' : trim(str_replace(';', '', $secondary[1]));
			$a['success'] = empty($success[1]) ? '#28A745' : trim(str_replace(';', '', $success[1]));
			$a['info'] = empty($info[1]) ? '#17a2b8' : trim(str_replace(';', '', $info[1]));
			$a['warning'] = empty($warning[1]) ? '#ffc107' : trim(str_replace(';', '', $warning[1]));
			$a['danger'] = empty($danger[1]) ? '#dc3545' : trim(str_replace(';', '', $danger[1]));
			$a['light'] = empty($light[1]) ? '#f8f9fa' : trim(str_replace(';', '', $light[1]));
			$a['dark'] = empty($dark[1]) ? '#343a40' : trim(str_replace(';', '', $dark[1]));

			$a['background'] = empty($background[1]) ? '#ffffff' : trim(str_replace(';', '', $background[1]));
			$a['text'] = empty($text[1]) ? '#343a40' : trim(str_replace(';', '', $text[1]));
			$a['link'] = empty($link[1]) ? '#343a40' : trim(str_replace(';', '', $link[1]));
			$a['footer'] = empty($footer[1]) ? '#343a40' : trim(str_replace(';', '', $footer[1]));
			$a['footertext'] = empty($footertext[1]) ? '#ffffff' : trim(str_replace(';', '', $footertext[1]));

			$a['topnav'] = empty($topnav[1]) ? '#eeeeee' : trim(str_replace(';', '', $topnav[1]));
			$a['topnavtext'] = empty($topnavtext[1]) ? '#343a40' : trim(str_replace(';', '', $topnavtext[1]));

			$a['pageheader'] = empty($pageheader[1]) ? '#f4f4f4' : trim(str_replace(';', '', $pageheader[1]));
			$a['pageheadertext'] = empty($pageheadertext[1]) ? '#343a40' : trim(str_replace(';', '', $pageheadertext[1]));

			$a['header_font'] = empty($header_font[1]) ? 'Open Sans' : trim(str_replace(';', '', $header_font[1]));
			$a['base_font'] = empty($base_font[1]) ? 'Open Sans' : trim(str_replace(';', '', $base_font[1]));

			$a['border_radius'] = empty($border_radius) ? '10px' : trim(str_replace(';', '', $border_radius[1]));
			$a['yi_contrast'] = empty($yi_contrast) ? '150': trim(str_replace(';', '', $yi_contrast[1]));

			$row = $this->update_palette($a);
		}
		elseif (config_item('palettes'))
		{
			$palette = config_item('palettes');

			if (isset($palette[$theme]))
			{
				$row = $this->update_palette($palette[$theme]);
			}
		}

		return !empty($row) ? $row : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param $path
	 * @return mixed
	 */
	public function read_theme_config($path)
	{
		$file_data = file_get_contents($path);

		preg_match('|Theme Name:(.*)$|mi', $file_data, $name);
		preg_match('|Theme Color:(.*)$|mi', $file_data, $description);

		$data['theme_name'] = !empty($name[1]) ? trim($name[1]) : '';
		$data['color'] = !empty($description[1]) ? trim($description[1]) : '';

		return $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return array
	 */
	public function save_palette($data = array())
	{
		//update bootsrap palette file first
		$css = $this->show->display('js', 'bootstrap', $data, TRUE);
		//set the scss file for compiling...
		if (!write_file(PUBPATH . '/themes/css/bootstrap/scss/bootstrap-custom.scss', $css))
		{
			show_error(lang('could_not_write_to_file') . ' bootstrap-custom.scss');
		}

		//compile it...
		$scss = new Compiler();
		$scss->setImportPaths(PUBPATH . '/themes/css/bootstrap/scss');
		//now write the new bootstrap.css file...
		$css = '/*!
 * Bootstrap (https://getbootstrap.com/)
 * Copyright 2011-2019 The Bootstrap Authors
 * Copyright 2011-2019 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */
 ';
		if (in_array(config_item('layout_design_theme_header_font'), config_item('google_fonts')))
		{
			$css .= '@import "//fonts.googleapis.com/css?family=' . str_replace(' ', ' ', config_item('layout_design_theme_header_font')) . '";';
		}
		if (in_array(config_item('layout_design_theme_base_font'), config_item('google_fonts')))
		{
			$css .= "\n";
			$css .= '@import "//fonts.googleapis.com/css?family=' . str_replace(' ', ' ', config_item('layout_design_theme_base_font')) . '";';
		}

		$scss = $scss->compile('@import "bootstrap-custom.scss";');
		$css .= minify($scss);

		if (!write_file(PUBPATH . '/themes/css/bootstrap/bootstrap.css', $css))
		{
			show_error(lang('could_not_write_to_file') . ' bootstrap.css');
		}

		$row = array(
			'msg_text' => 'bootstrap.css ' . lang('updated_successfully'),
			'success'  => TRUE,
		);

		return $row;
	}

	// ------------------------------------------------------------------------

	/**
	 * @param array $data
	 * @return mixed
	 */
	private function update_palette($data = array())
	{
		$a = array('layout_design_theme_primary_button'   => $data['primary'],
		           'layout_design_theme_secondary_button' => $data['secondary'],
		           'layout_design_theme_success_button'   => $data['success'],
		           'layout_design_theme_info_button'      => $data['info'],
		           'layout_design_theme_warning_button'   => $data['warning'],
		           'layout_design_theme_danger_button'    => $data['danger'],
		           'layout_design_theme_light_button'     => $data['light'],
		           'layout_design_theme_dark_button'      => $data['dark'],

		           'layout_design_theme_background_color' => $data['background'],
		           'layout_design_theme_text_color'       => $data['text'],
		           'layout_design_theme_link_color'       => $data['link'],
		           'layout_design_theme_footer_color'     => $data['footer'],
		           'layout_design_theme_footertext_color' => $data['footertext'],

		           'layout_design_theme_topnav_color'     => $data['topnav'],
		           'layout_design_theme_topnavtext_color' => $data['topnavtext'],

		           'layout_design_theme_pageheader_color'     => $data['pageheader'],
		           'layout_design_theme_pageheadertext_color' => $data['pageheadertext'],

		           'layout_design_theme_header_font' => $data['header_font'],
		           'layout_design_theme_base_font'   => $data['base_font'],

		           'layout_design_theme_enable_rounded' => config_item('layout_design_theme_enable_rounded'),
		           'layout_design_theme_enable_gradients' => config_item('layout_design_theme_border_radius'),

		           'layout_design_theme_border_radius' => !empty($data['border_radius']) ? $data['border_radius'] : config_item('layout_design_theme_border_radius'),
		           'layout_design_theme_yi_contrast' => !empty($data['yi_contrast']) ? $data['yi_contrast'] : config_item('layout_design_theme_yi_contrast'),
		);

		$row = $this->set->update_db_settings($a);

		$row['data'] = $a;

		return $row;
	}
}

/* End of file Themes_model.php */
/* Location: ./application/models/Themes_model.php */