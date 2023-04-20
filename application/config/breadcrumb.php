<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['multilang'] = FALSE;
$config['set_home'] = '<i class="fa fa-home"></i>';
$config['attr_home'] = array();
$config['unlink_home'] = FALSE;
$config['delimiter'] = "";
$config['replacer'] = array(ADMIN_ROUTE => 'dashboard',
                        'kb' => 'knowledgebase',
                        'cart' => 'shopping_' . config_item('layout_design_shopping_cart_or_bag'),
);
$config['replacer_embed'] = array(); // Don't change this line !!!
$config['partial_replace'] = array();
$config['exclude'] = array('');
$config['exclude_segment'] = array(5,6,7,8);
$config['use_wrapper'] = TRUE;
$config['wrapper'] = '<div class="breadcrumb-box"><ol class="breadcrumb">|</ol></div>';
$config['wrapper_inline'] = '<li class="breadcrumb-item">|</li>';
$config['unlink_last_segment'] = TRUE;
$config['hide_number'] = TRUE;
$config['hide_number_on_last_segment'] = TRUE;
$config['strip_characters'] = array('_', '.html', '.php', '.htm');
$config['strip_regexp'] = array();

/* End of file breadcrumb.php */
/* Location: ./system/application/config/breadcrumb.php */