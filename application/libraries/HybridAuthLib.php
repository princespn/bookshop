<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php';

class HybridAuthLib extends Hybrid_Auth
{
	function __construct($config = array())
	{
		$ci =& get_instance();

		$config['base_url'] = site_url((config_item('index_page') == '' ? SELF : '').$config['base_url']);

		parent::__construct($config);

		log_message('debug', 'HybridAuthLib Class Initalized');
	}

	public static function providerEnabled($provider)
	{
		return isset(parent::$config['providers'][$provider]) && parent::$config['providers'][$provider]['enabled'];
	}
}

/* End of file HybridAuthLib.php */
/* Location: ./application/libraries/HybridAuthLib.php */