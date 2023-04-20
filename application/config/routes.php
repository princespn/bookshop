<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright    Copyright (c) 2014, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    http://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'home';
$route['404'] = 'error_pages/index';
$route['404_override'] = 'error_pages/index';
$route['translate_uri_dashes'] = FALSE;

$route[ ADMIN_ROUTE ] = ADMIN_ROUTE . '/dashboard';
$route[ MEMBERS_ROUTE ] = 'members/dashboard';

$route['admin_login'] = '';
$route[ ADMIN_LOGIN ] = "admin_login";
$route[ ADMIN_LOGIN . '/(:any)' ] = "admin_login/$1";
$route[ ADMIN_LOGIN . '/reset_password/(:any)' ] = "admin_login/reset_password/$1";
$route[ ADMIN_LOGIN . '/confirm/(:any)' ] = "admin_login/confirm/$1";
$route[ ADMIN_ROUTE . '/clients/view' ] = ADMIN_ROUTE . "/members/view/$1";
$route[ ADMIN_ROUTE . '/clients/view/:any' ] = ADMIN_ROUTE . "/members/view/$1/$2";
$route[ ADMIN_ROUTE . '/affiliates/view' ] = ADMIN_ROUTE . "/members/view/$1";
$route[ ADMIN_ROUTE . '/affiliates/view/:any' ] = ADMIN_ROUTE . "/members/view/$1/$2";

$route['affiliate_program_tos'] = 'page/system';
$route['age_verification'] = 'home/age_verification';
$route['blog'] = "blog/view";
$route['blog/(:num)'] = "blog/view/$1";
$route['blog/' . BLOG_PREPEND_LINK. '-(:any)'] = "blog/post/$1";
$route['brands'] = 'brands/view';
$route['cart'] = 'cart/view';
$route['cart/(:num)'] = "cart/view/$1";
$route['checkout'] = 'checkout/cart';
$route['contact'] = 'form/contact';
$route['javascript_required'] = 'home/javascript_required';
$route['faq'] = 'faq/view';
$route['become_affiliate'] = 'become_affiliate/view';
$route['usincome_calculator'] = 'income_calculator/viewus';
$route['ganaincome_calculator'] = 'income_calculator/view';
$route['form'] = "form/contact";
$route['forum'] = 'forum/view';
$route['gallery'] = 'gallery/view';
$route['kb'] = 'kb/view';
$route['locations'] = 'form/addresses';
$route['login'] = 'login/page';
$route['logout'] = "logout/now";
$route[MEMBERS_ROUTE . '/mass_email'] = MEMBERS_ROUTE . '/network_marketing/email';
$route['offline'] = 'home/offline';
$route['page/(:any)'] = "page/view/$1";
$route['privacy_policy'] = 'page/system';
$route['profile/(:any)'] = "profile/id/$1";
$route['products'] = 'product/shop';
$route['product_reviews'] = 'product_reviews/view';
$route['product_categories'] = 'product/categories';
$route['register'] = "register/view";
$route['register/affiliate'] = "register/view";
$route['rss/(:any)'] = "rss/feed/$1";
$route['shop'] = 'product/store';
$route['shop/(:any)'] = "shop/id/$1";
$route['store'] = 'product/store';
$route['store/(:num)'] = "product/store/$1";
$route['switch_currency/(:any)'] = "home/switch_currency/$1";
$route['tos'] = 'page/system';
$route['site_map'] = 'site_map/index';
$route['site_map.xml'] = 'site_map/site_map_index/site_map.xml';
$route['wish_list/(:any)'] = "wish_list/view/$1";
$route['search'] = 'search/site';
$route['search/(:num)'] = "search/site/$1";
$route['t/(:num)'] = "tracking/id/$1";
$route['thank_you'] = "thank_you/page";

$route['parties/(:num)/(:any)'] = "modules/id/$1/$2";

$route['install'] = 'install/view';
$route['install_hosted'] = 'install/hosted';

//sitebuilder
$route[ SITE_BUILDER . '/assets/designs/(:any)' ] = ADMIN_ROUTE . "/site_builder/assets/designs/$1";
$route[ SITE_BUILDER . '/assets/minimalist-blocks/(:any)' ] = ADMIN_ROUTE . "/site_builder/assets/minimalist-blocks/$1";
$route[ SITE_BUILDER . '/(:num)' ] = ADMIN_ROUTE . "/site_builder/layout/$1";

$route['(:any)/tools/(:any)/(:num)'] = "affiliate/id/$1/tools/$2/$3";
$route['(:any)'] = "affiliate/id/$1";

//$route['admin_modules/view'] = ADMIN_ROUTE . "/admin_modules/view";
//$route['admin_modules/view/(:any)/(:any)'] = ADMIN_ROUTE . "/admin_modules/view/$1/$2";

if (file_exists(APPPATH . 'config/custom_routes.php'))
{
	require_once(APPPATH . 'config/custom_routes.php');
}

/* End of file routes.php */
/* Location: ./application/config/routes.php */