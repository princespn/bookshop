<?php
add_action('get_slideshows', 'get_slideshows', 10);

function get_slideshows($data = array())
{
	$CI = &get_instance();

	$lang_id = !$CI->session->default_lang_id ? '1' : $CI->session->default_lang_id;

	$CI->load->model('slide_shows_model', 'slide');

	$rows = $CI->slide->get_slideshows($lang_id, $data);

	if (!empty($rows['slide_shows']))
	{
		$rows['values'] = $rows['slide_shows'];
	}

	return !empty($rows) ? $rows : FALSE;
}