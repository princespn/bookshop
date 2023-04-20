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
 * @param string $id
 * @return bool
 */
function check_product_wish_list($id = '')
{
	$CI =& get_instance();

	if (config_enabled('sts_site_enable_wish_lists') && sess('user_logged_in'))
	{
		$CI->db->where('product_id', (int)$id);
		$CI->db->where('member_id', (int)sess('member_id'));

		$CI->db->join(TBL_WISH_LISTS,
			TBL_WISH_LISTS . '.wish_list_id =' . TBL_PRODUCTS_TO_WISH_LISTS . '.wish_list_id',
			'left');

		if (!$q = $CI->db->get(TBL_PRODUCTS_TO_WISH_LISTS))
		{
			get_error(__FILE__, __METHOD__, __LINE__);
		}

		return $q->num_rows() > 0 ? TRUE : FALSE;
	}


	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return bool
 */
function check_product_id($str = '')
{
	$CI =& get_instance();

	$row = $CI->dbv->get_record(TBL_PRODUCTS, 'product_id', (int)$str, TRUE);

	return !empty($row) ? FALSE : $row['product_id'];
}

// ------------------------------------------------------------------------

/**
 * @param string $qty
 * @param array $data
 * @param array $attr
 * @return array
 */
function format_product_inventory_alert_email($qty = '', $data = array(), $attr = array())
{
	$vars = $data;

	$vars['current_inventory'] = $qty;
	$vars['inventory_alert_level'] = config_item('sts_products_alert_inventory_level');

	if (!empty($attr))
	{
		$vars['attribute_name'] = $attr['attribute_name'];
		$vars['current_inventory'] = is_var($attr, 'inventory', FALSE, $qty);
		$vars['option_name'] = is_var($attr, 'option_name', FALSE, $attr['value']);
	}

	return $vars;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param int $lang_id
 * @return array
 */
function format_products($data = array(), $lang_id = 1)
{
	//for formatting the product's details and attributes
	if (!empty($data['attributes']))
	{
		foreach ($data['attributes'] as $k => $v)
		{
			$class = 'id="attribute-' . $v['prod_att_id'] . '"';
			$class .= $v['required'] == 1 ? ' class="form-control required"' : ' class="form-control"';
			$data['attributes'][$k]['form_html'] = format_attribute($data['attributes'][$k], $v['value'], $class);
		}
	}

	//set photos
	if (!empty($data['photos']))
	{
		foreach ($data['photos'] as $k => $v)
		{
			$data['photos'][$k]['thumb'] = $v['photo_file_name'];
			if (file_exists(PUBPATH . '/images/products/thumbs/' . $v['photo_file_name']))
			{
				$data['photos'][$k]['thumb'] = 'thumbs/' . $v['photo_file_name'];
			}
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed|string
 */
function set_template($data = array())
{
	switch ($data['product_type'])
	{
		case 'subscription':

			$tpl = 'product_subscription_details_default';

			break;

		case 'third_party':

			$tpl = 'product_affiliate_details_default';

			break;

		default:

			$tpl = 'product_general_details_default';

			break;
	}

	return empty($data['product_page_template']) ? $tpl : $data['product_page_template'];
}

// ------------------------------------------------------------------------

/**
 * @param array $tree
 * @param string $root
 * @param string $url
 * @param string $multiple
 * @param string $class
 * @return string
 */
function category_dropdown($tree = array(), $root = 'select_category', $url = '', $multiple = '', $class = '')
{
	$cat = '';

	if (!empty($url))
	{
		$cat .= '<select class="form-control ' . $class . '" ' . $multiple . ' name="parent_id" onchange="document.location=\'' . $url . '\'+this.value">';
	}
	else
	{
		$cat .= '<select name="category_id"  ' . $multiple . ' class="form-control">';
	}

	$cat .= "<option value=''>" . lang($root) . "</option>\n";
	$cat .= $tree;
	$cat .= '</select>';

	return $cat;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool|string
 */
function format_cat_path($data = array())
{
	if (!empty($data['path']))
	{
		$a = explode('/', $data['path']);
		$b = $a;
		$c = array_pop($b);

		$d = implode(' > ', $a);
	}

	return empty($d) ? FALSE : $d;
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @return bool
 */
function check_attribute_type($type = '')
{
	switch ($type)
	{
		case 'select':
		case 'checkbox':
		case 'image':
		case 'radio':
			return TRUE;
			break;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function get_product_types()
{
	$vars = array();
	foreach (config_item('product_types') as $v)
	{
		$vars[$v] = lang($v . '_product_type');
	}

	return form_dropdown('product_type', $vars, '', 'class="form-control"');
}

// ------------------------------------------------------------------------

/**
 * @return string
 */
function get_attribute_types()
{
	$CI =& get_instance();

	$vars = array();
	foreach ($CI->config->item('attribute_types') as $v)
	{
		$vars[$v] = lang($v);
	}

	return form_dropdown('attribute_type', $vars, '', 'class="form-control"');
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return mixed
 */
function format_product_specs($data = array())
{
	$lang = array();
	foreach ($data as $v)
	{
		if (!in_array($v['language_id'], $lang))
		{
			array_push($lang, $v['language_id']);
		}
	}

	foreach ($lang as $m)
	{
		$g[$m] = array();
		foreach ($data as $k => $v)
		{
			if ($v['language_id'] == $m)
			{
				if (!in_array($v, $g[$m]))
				{
					array_push($g[$m], $v);
				}
			}
		}
	}

	return $g;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param int $qty
 * @return array
 */
function check_attribute_inventory($data = array(), $qty = 0)
{

	if (!empty($data['enable_inventory']))
	{
		if ($data['inventory'] < $qty)
		{
			$data['msg'] = $data['option_name'] . ' - ' . lang('inventory_for_option_not_enough');
		}
	}

	return $data;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $qty
 * @return string
 */
function check_min_quantity($data = array(), $qty = '1')
{
	if ($data['product_type'] == 'general')
	{
		if ($data['min_quantity_required'] > $qty)
		{
			return lang('minimum_quantity_required_for_product') . ' - ' . $data['min_quantity_required'];
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $qty
 * @return string
 */
function check_max_quantity($data = array(), $qty = '1')
{
	if ($data['max_quantity_allowed'] > 0 && $data['max_quantity_allowed'] < $qty)
	{
		return lang('maximum_quantity_allowed_for_product') . ' - ' . $data['max_quantity_allowed'];
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $qty
 * @return mixed|string
 */
function check_inventory($data = array(), $qty = '1')
{
	if ($data['product_type'] == 'general')
	{
		if ($data['enable_inventory'] != '0')
		{
			if (config_enabled('sts_products_enable_inventory'))
			{
				if ($data['inventory_amount'] < $qty)
				{
					return lang('inventory_for_product_not_enough');
				}
			}
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param bool $show_price
 * @param bool $show_inventory
 * @return mixed|string
 */
function attribute_label($data = array(), $show_price = FALSE, $show_inventory = FALSE)
{
	//check if the attribute increases / decreases price
	$name = lang($data['option_description']);

	if ($show_price == TRUE)
	{
		if ($data['price'] > '0.00')
		{
			$name .= ' ' . $data['price_add'] . ' ' . format_amount(format_price($data['price'])) . '';
		}

	}

	if ($show_inventory == TRUE)
	{
		if ($data['enable_inventory'])
		{
			$name .= ' <small class="text-muted attribute-inventory">(' . $data['inventory'] . ' ' . lang('available') . ')</small>';
		}
	}

	return $name;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $value
 * @param string $attributes
 * @return array|string
 */
function format_attribute($data = array(), $value = '', $attributes = 'class="form-control"')
{
	$CI =& get_instance();

	switch ($data['attribute_type'])
	{
		case 'textarea':

			$attributes.= ' maxlength="' . DEFAULT_ATTRIBUTE_TEXTAREA_LENGTH. '"';
			return form_textarea('attribute_id[' . $data['prod_att_id'] . ']', $value, $attributes);

			break;

		case 'select':

			$options = array('' => lang('select_an_option'));

			if (!empty($data['option_values']))
			{
				foreach ($data['option_values'] as $v)
				{
					$options[$v['prod_att_value_id']] = attribute_label($v, TRUE);
				}
			}

			if (!empty($data['auto_update_image']))
			{
				$attributes .= ' onChange="updateProductAttribute(\'' . $data['prod_att_id'] . '\')"';
			}

			return form_dropdown('attribute_id[' . $data['prod_att_id'] . ']', $options, $value, $attributes);

			break;

		case 'radio':

			$options = '<div class="radio" id="radio-' . $data['prod_att_id'] . '">';
			if (!empty($data['option_values']))
			{
				foreach ($data['option_values'] as $v)
				{
					$checked = FALSE;
					if (!empty($value))
					{
						if ($v['prod_att_value_id'] == $value)
						{
							$checked = TRUE;
						}
					}

					$attributes = '';
					$options .= form_radio('attribute_id[' . $data['prod_att_id'] . ']', $v['prod_att_value_id'], $checked, $attributes) . ' ' . form_label(attribute_label($v, TRUE, TRUE), 'attribute_id[' . $data['prod_att_id'] . ']') . '<br />';
				}
			}

			$options .= '</div>';

			return $options;

			break;

		case 'checkbox':

			$attributes = $data['required'] == 1 ? 'class="required"' : '';
			$options = '<div class="form-check">';

			$options .= form_checkbox('attribute_id[' . $data['prod_att_id'] . ']', $data['description'], $value, $attributes);
			$options .= '<label class="form-check-label">';
			$options .= $data['attribute_name'];
			$options .= '</label></div>';

			return $options;

			break;

		case 'file':

			$a = '<button type="button" id="button-upload-' . $data['prod_att_id'] . '" class="' . DEFAULT_FILE_UPLOAD_BUTTON_CSS . '">' . i('fa fa-upload') . ' ' . lang('click_to_upload') . '</button>
			<input type="hidden" name="attribute_id[' . $data['prod_att_id'] . ']" value="" ' . $attributes . ' />';

			$a .= '<small class="text-muted float-left"><span></span></small><small class="text-muted pull-right hidden-md-down text-lowercase">';

			if ($data['required'] == 1)
			{
				$a .= '<strong class="text-danger">' . lang('required') . '</strong>';
			}

			$a .= ' ' . lang('allowed_file_types') . ' :' . str_replace('|', ',', $CI->config->item('sts_products_upload_types')) . '</small>';


			return $a;

			break;

		case 'image':

			$options = '<div class="images" id="radio-' . $data['prod_att_id'] . '">';
			if (!empty($data['option_values']))
			{
				foreach ($data['option_values'] as $v)
				{
					$checked = FALSE;
					if (!empty($value))
					{
						if ($v['prod_att_value_id'] == $value)
						{
							$checked = TRUE;
						}
					}

					$attributes = 'id="' . $v['prod_att_value_id'] . '"';
					if (!empty($data['auto_update_image']))
					{
						$attributes .= ' onClick="updateProductAttribute(\'' . $data['prod_att_id'] . '-' . $v['prod_att_value_id'] . '\', \'radio\')"';
					}

					$options .= form_radio('attribute_id[' . $data['prod_att_id'] . ']', $v['prod_att_value_id'], $checked, $attributes) . ' ' . '';
					$options .= '<label class="image-cc"  for="' . $v['prod_att_value_id'] . '" style="background-image:url(\'' . $v['path'] . '\')"></label>';
				}
			}

			$options .= '</div>';

			return $options;

			break;

		default: //text

			return form_input('attribute_id[' . $data['prod_att_id'] . ']', $value, $attributes);

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @return bool
 */
function login_for_price($data = array())
{
	if ($data['login_for_price'] == 1 && !sess('user_logged_in'))
	{
		return TRUE;
	}

	return FALSE;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return mixed|string
 */
function pricing_options($data = array(), $type = 'select')
{
	//first if the user must be logged in to view the price
	if ($data['login_for_price'] == 1 && !sess('user_logged_in'))
	{
		return '<h2 class="price">' . lang('please_login_to_view_price') . '</h2>';
	}

	if (!empty($data['pricing_options']))
	{
		$vars = array();
		foreach ($data['pricing_options'] as $v)
		{
			if (!empty($v['enable']))
			{
				$amount = !empty($v['enable_initial_amount']) ? $v['initial_amount'] : $v['amount'];

				$format_amount = format_amount($amount);

				if (!empty($v['enable_initial_amount']))
				{
					$interval_type = $v['initial_interval'] > 1 ? plural($v['initial_interval_type']) : $v['initial_interval_type'];

					$vars[$v['prod_price_id']] = $format_amount . ' ' . lang('for') . ' ' . $v['initial_interval'] . ' ' . $interval_type . ' ' . lang('then') . ' ' . format_amount($v['amount']) . ' ' . lang($v['name']);
				}
				else
				{
					$vars[$v['prod_price_id']] = $format_amount . ' - ' . lang($v['name']);
				}
			}
		}

		switch ($type)
		{
			case 'select':

				return form_dropdown('amount', $vars, '', 'class="form-control" id="amount"');

				break;

			case 'radio':

				$html = '';

				foreach ($data['pricing_options'] as $v)
				{
					$checked = !empty($v['default_price']) ? 'checked="checked"' : '';

					$html .= '<div class="radio">
							      <label>
							      <input type="radio" name="amount" id="price-' . $v['prod_price_id'] . '" value="' . $v['prod_price_id'] . '" ' . $checked . ' class="required">
							      ' . lang($v['name']) . '
							      </label>
							    </div>';

				}

				return $html;

				break;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $type
 * @return string
 */
function init_product_template($type = '')
{
	switch ($type)
	{
		case 'third_party':

			return TPL_ADMIN_PRODUCTS_AFFILIATE_UPDATE;

			break;

		case 'certificate':

			return TPL_ADMIN_PRODUCTS_CERTIFICATE_UPDATE;

			break;

		default: //general, subscription

			return TPL_ADMIN_PRODUCTS_UPDATE;

			break;
	}
}

// ------------------------------------------------------------------------

/**
 * @param string $str
 * @return mixed
 */
function format_photo_file_name($str = '')
{
	$a = explode('/', $str);

	return end($a);
}

/**
 *  Validate attribute
 *
 * Validate a product's attribute / options for inventory amounts
 *
 * @param array $data
 * @param string $attr
 * @param int $qty
 *
 * @return array
 */

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $attr
 * @param int $qty
 * @return array
 */
function validate_attribute($data = array(), $attr = '', $qty = 0)
{
	$CI = &get_instance();

	//only for select type options
	$select = array('select', 'radio', 'image');

	//check for select options only
	if (in_array($data['attribute_type'], $select))
	{
		//check if there are any options for this attribute
		if (!empty($data['option_values']))
		{
			foreach ($data['option_values'] as $v)
			{
				if ($v['prod_att_value_id'] == $attr)
				{
					$row = check_attribute_inventory($v, $qty);
				}
			}
		}
	}
	else
	{
		if ($data['attribute_type'] == 'file') //for file uploads only
		{
			$a = $CI->cart->get_cart_upload($attr);
			$row['file_name'] = $a['file_name'];
		}
	}

	$row['attribute_name'] = $data['attribute_name'];

	return $row;
}

// ------------------------------------------------------------------------

/**
 * @param array $data
 * @param string $type
 * @return string
 */
function format_subscription_interval($data = array(), $type = '')
{
	$html = '';

	if ($type == 'subscription')
	{
		if (!empty($data['enable_initial_amount']))
		{
			$html .= format_amount($data['initial_amount']);
			$html .= ' ' . lang('for') . ' ' . $data['initial_interval'] . ' ' . $data['initial_interval_type'];
			$html .= ' ' . lang('then') . ' ';
		}

		$html .= format_amount($data['amount']);
		$html .= ' ' . lang('every') . ' ' . $data['interval_amount'] . ' ';

		$html .= $data['interval_amount'] > 1 ? plural($data['interval_type']) : singular($data['interval_type']);

		if (!empty($data['recurrence']))
		{
			$html .= ' ' . lang('up_to') . ' ' . $data['recurrence'] . ' ' . lang('times');
		}
	}
	else
	{
		$html .= format_amount($data['amount']);
		$html .= ' ' . $data['name'];
	}

	return $html;
}

// ------------------------------------------------------------------------

/**
 * @param string $id
 * @return array|false
 */
function get_product_names($id = '')
{
	$CI = &get_instance();

	$a = $CI->prod->get_product_names($id);

	return format_array($a, 'language_id', 'product_name');
}

/* End of file products_helper.php */
/* Location: ./application/helpers/products_helper.php */