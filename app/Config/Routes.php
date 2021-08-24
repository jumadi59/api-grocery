<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Pages::index');

$routes->group('address', ['filter' => 'customer'], function ($routes) {
	$routes->post('create',					'Address::create');
	$routes->post('/',						'Address::create');   // alias
	$routes->get('/',						'Address::index'); //ok
	$routes->get('primary',					'Address::primary'); //ok
	$routes->get('show/(:num)',				'Address::show/$1'); //ok
	$routes->get('(:num)',					'Address::show/$1');   // alias
	$routes->put('update/(:num)',			'Address::update/$1');
	$routes->put('(:num)',					'Address::update/$1');   // alias
	$routes->delete('delete/(:num)',		'Address::delete/$1');
	$routes->delete('(:num)',				'Address::delete/$1');   // alias
});

$routes->group('ads', function ($routes) {
	$routes->post('create',					'Ads::create', ['filter' => 'customer']);
	$routes->post('/',						'Ads::create', ['filter' => 'customer']);   // alias
	$routes->get('/',						'Ads::index', ['filter' => 'admin']);
	$routes->get('slide',					'Ads::slide'); //ok
	$routes->get('banner',					'Ads::banner'); //ok
	$routes->get('dialog',					'Ads::dialog'); //ok
	$routes->get('click/(:num)',			'Ads::click/$1');
	$routes->get('show/(:num)',				'Ads::show/$1');
	$routes->get('(:num)',					'Ads::show/$1');
	$routes->put('update/(:num)',			'Ads::update/$1', ['filter' => 'admin']);
	$routes->put('(:num)',					'Ads::update/$1', ['filter' => 'admin']);   // alias
	$routes->delete('delete/(:num)',		'Ads::delete/$1', ['filter' => 'admin']);
	$routes->delete('(:num)',				'Ads::delete/$1', ['filter' => 'admin']);   // alias
});

$routes->group('auth', function ($routes) {
	$routes->post('login',					'Auth::login');
	$routes->post('login_with',				'Auth::loginWith');
	$routes->post('register',				'Auth::register');
	$routes->post('forgot',					'Auth::forgot');
	$routes->post('verify',					'Auth::verify');
	$routes->get('change/password',			'Auth::changePass');
	$routes->get('update_token',			'Auth::updateToken', ['filter' => 'customer']);
	$routes->get('logout',					'Auth::logout', ['filter' => 'customer']);
});

$routes->group('carts', ['filter' => 'customer'], function ($routes) {
	$routes->post('create',            		'Carts::create');
	$routes->post('/',                  	'Carts::create');   // alias
	$routes->get('/',                   	'Carts::index'); //ok
	$routes->get('show/(:num)',    			'Carts::show/$1'); //ok
	$routes->get('(:num)',         			'Carts::show/$1');  // alias
	$routes->put('update/(:num)', 			'Carts::update/$1');
	$routes->put('(:num)', 					'Carts::update/$1');  // alias
	$routes->delete('delete/(:num)', 		'Carts::delete/$1');
	$routes->delete('(:num)', 				'Carts::delete/$1');  // alias
});

$routes->group('categories', function ($routes) {
	$routes->post('create',					'Categories::create', ['filter' => 'admin']);
	$routes->post('/',						'Categories::create', ['filter' => 'admin']);   // alias
	$routes->get('/',               		'Categories::index');
	$routes->get('all',              		'Categories::allSub');
	$routes->get('sub/(:num)',              'Categories::sub/$1');
	$routes->get('child/(:num)',            'Categories::child/$1');
	$routes->get('show/(:num)',    			'Categories::show/$1');
	$routes->get('(:num)',    				'Categories::show/$1');   // alias
	$routes->put('update/(:num)',			'Categories::update/$1', ['filter' => 'admin']);
	$routes->put('(:num)',					'Categories::update/$1', ['filter' => 'admin']);   // alias
	$routes->delete('delete/(:num)',		'Categories::delete/$1', ['filter' => 'admin']);
	$routes->delete('(:num)',				'Categories::delete/$1', ['filter' => 'admin']);   // alias
});

$routes->group('chats', ['filter' => 'customer'], function ($routes) {
	$routes->post('/',						'Chats::create');
	$routes->post('create',					'Chats::create');
	$routes->get('/',               		'Chats::index');
	$routes->get('chat/(:num)',    			'Chats::chat/$1');
	$routes->put('chat/(:num)',				'Chats::update/$1');
	$routes->delete('(:num)',				'Chats::delete/$1');
});

$routes->group('couriers', function ($routes) {
	$routes->post('create',					'Couriers::create', ['filter' => 'admin']);
	$routes->post('/',						'Couriers::create', ['filter' => 'admin']);   // alias
	$routes->get('/',               		'Couriers::index');
	$routes->get('all',               		'Couriers::all', ['filter' => 'admin']);
	$routes->get('cost',    				'Couriers::cost');
	$routes->get('show/(:num)',    			'Couriers::show/$1');
	$routes->get('store/(:num)',    		'Couriers::courier_store/$1');
	$routes->get('(:num)',         			'Couriers::show/$1');   // alias
	$routes->put('update/(:num)',			'Couriers::update/$1', ['filter' => 'admin']);
	$routes->put('(:num)',					'Couriers::update/$1', ['filter' => 'admin']);   // alias
	$routes->delete('delete/(:num)',		'Couriers::delete/$1', ['filter' => 'admin']);
	$routes->delete('(:num)',				'Couriers::delete/$1', ['filter' => 'admin']);   // alias
});

$routes->group('coupons', function ($routes) {
	$routes->get('/',        				'Coupons::index', ['filter' => 'customer']);
	$routes->get('claim',             		'Coupons::claim', ['filter' => 'customer']);
	$routes->get('store/(:segment)',        'Coupons::couponStore/$1');
	$routes->get('show/(:segment)',    		'Coupons::show/$1');
	$routes->get('(:segment)',         		'Coupons::show/$1');  // alias
});

$routes->group('discover', function ($routes) {
	$routes->get('flash_sale',             	'Discover::flashSale');
	$routes->get('new',               		'Discover::new');
	$routes->get('sold',               		'Discover::sold');
	$routes->get('officel_store',           'Discover::officel_store');
	$routes->get('recommend',               'Discover::recommend');
	$routes->get('history',               	'Discover::history');
});

//ok
$routes->group('favorites', ['filter' => 'customer'], function ($routes) {
	$routes->post('create',            		'Favorites::create'); //ok
	$routes->post('/',                  	'Favorites::create');   // alias
	$routes->get('/',                   	'Favorites::index'); //ok
	$routes->delete('delete/(:num)', 		'Favorites::delete/$1'); // ok
	$routes->delete('(:num)', 				'Favorites::delete/$1');   // alias ok
});

$routes->group('notifications', function ($routes) {
	$routes->post('handler', 				'Notifications::handler');
	$routes->get('badges', 					'Notifications::badges', ['filter' => 'customer']);
	$routes->get('/',                   	'Notifications::index', ['filter' => 'customer']);
	$routes->get('show/(:num)',    			'Notifications::show/$1', ['filter' => 'customer']);
	$routes->get('(:num)',         			'Notifications::show/$1', ['filter' => 'customer']);  // alias
	$routes->delete('delete/(:num)', 		'Notifications::delete/$1', ['filter' => 'customer']);
	$routes->delete('(:num)', 				'Notifications::delete/$1', ['filter' => 'customer']);   // alias
});

$routes->group('transactions', ['filter' => 'customer'], function ($routes) {
	$routes->get('cancel/(:num)', 			'Transactions::cancel/$1');
	$routes->get('show/(:num)', 			'Transactions::show/$1');
	$routes->get('(:num)', 					'Transactions::show/$1');   // alias
});

$routes->group('orders', ['filter' => 'customer'], function ($routes) {
	$routes->post('create',       			'Orders::create');
	$routes->post('/',              		'Orders::create');   // alias
	$routes->get('/',                   	'Orders::index');
	$routes->get('show/(:num)',         	'Orders::show/$1');
	$routes->put('([a-z])/(:num)', 			'Orders::status/$1/$2');
	$routes->get('(:num)',              	'Orders::show/$1');   // alias
});

$routes->group('payments', function ($routes) {
	$routes->post('create',					'Payments::create', ['filter' => 'admin']);
	$routes->post('/',						'Payments::create', ['filter' => 'admin']);   // alias
	$routes->get('/',               		'Payments::index');
	$routes->get('show/(:num)',    			'Payments::show/$1');
	$routes->get('(:num)',         			'Payments::show/$1');  // alias
	$routes->put('update/(:num)',			'Payments::update/$1', ['filter' => 'admin']);
	$routes->put('(:num)',					'Payments::update/$1', ['filter' => 'admin']);   // alias
	$routes->delete('delete/(:num)',		'Payments::delete/$1', ['filter' => 'admin']);
	$routes->delete('(:num)',				'Payments::delete/$1', ['filter' => 'admin']);   // alias
});

$routes->group('pages', function ($routes) {
	$routes->post('create',					'Pages::create', ['filter' => 'admin']);
	$routes->post('/',						'Pages::create', ['filter' => 'admin']);   // alias
	$routes->get('/',						'Pages::index');
	$routes->get('show/(:segment)',			'Pages::show/$1');
	$routes->get('(:segment)',				'Pages::show/$1');   // alias
	$routes->put('update/(:num)',			'Pages::update/$1', ['filter' => 'admin']);
	$routes->put('(:num)',					'Pages::update/$1', ['filter' => 'admin']);   // alias
	$routes->delete('delete/(:num)',		'Pages::delete/$1', ['filter' => 'admin']);
	$routes->delete('(:num)',				'Pages::delete/$1', ['filter' => 'admin']);   // alias
});

$routes->group('products', function ($routes) {
	$routes->get('/',               		'Products::index'); //ok
	$routes->get('count',           		'Products::count');
	$routes->get('sugestion',           	'Products::sugestion');
	$routes->get('show/(:num)',    			'Products::show/$1');
	$routes->get('(:num)',         			'Products::show/$1');  // alias
});

$routes->group('reviews', function ($routes) {
	$routes->post('create',            		'Reviews::create', ['filter' => 'customer']);
	$routes->post('/',                  	'Reviews::create', ['filter' => 'customer']);   // alias
	$routes->get('store/(:num)',    		'Reviews::storeProductReviews/$1');
	$routes->get('user/(:num)',    			'Reviews::userReviews/$1', ['filter' => 'customer']);
	$routes->get('show/(:segment)',    		'Reviews::show/$1');
	$routes->get('product/(:num)',      	'Reviews::index/$1');
	$routes->get('(:num)',          		'Reviews::index/$1');    // alias
	$routes->put('update/(:num)',  			'Reviews::update/$1', ['filter' => 'customer']);
	$routes->put('(:num)',  				'Reviews::update/$1', ['filter' => 'customer']);
	$routes->delete('delete/(:num)',		'Reviews::delete/$1', ['filter' => 'customer']);
	$routes->delete('(:num)',				'Reviews::delete/$1', ['filter' => 'customer']);
});

$routes->group('regions', function ($routes) {
	$routes->get('/',      					'Regions::index');
	$routes->get('provinces',      			'Regions::provinces');
	$routes->get('province/(:segment)',     'Regions::citys/$1'); //alias
	$routes->get('city/(:segment)',  		'Regions::subdistricts/$1'); //alias
	$routes->get('address/(:num)',      	'Regions::address/$1');
});

$routes->group('transactions', ['filter' => 'admin'], function ($routes) {
	$routes->get('/',      					'Transactions::index');
	$routes->get('(:num)',      			'Transactions::show');
});

$routes->group('seller', ['filter' => 'seller'], function($routes)
{
	$routes->post('coupon/create',      			'Seller\Coupons::create');
	$routes->post('coupon',           				'Seller\Coupons::create');  // alias
	$routes->get('coupon',             				'Seller\Coupons::index');
	$routes->get('coupon/(:num)',             		'Seller\Coupons::show/$1');
	$routes->put('coupon/update/(:num)',    		'Seller\Coupons::update/$1');
	$routes->put('coupon/(:num)',         			'Seller\Coupons::update/$1');  // alias
	$routes->delete('coupon/delete/(:num)',    		'Seller\Coupons::delete/$1');
	$routes->delete('coupon/(:num)',         		'Seller\Coupons::delete/$1');  // alias

	$routes->post('product',         				'Seller\Products::create');  // alias
	$routes->post('product/create',    				'Seller\Products::create');
	$routes->post('product/update/(:segment)',    	'Seller\Products::updatePost/$1');
	$routes->get('product',           				'Seller\Products::index');
	$routes->get('product/cart',             		'Seller\Products::cart');
	$routes->get('product/(:segment)',             	'Seller\Products::show/$1');
	$routes->put('product/(:segment)',         		'Seller\Products::update/$1');  // alias
	$routes->delete('product/delete/(:segment)',    'Seller\Products::delete/$1');
	$routes->delete('product/(:segment)',         	'Seller\Products::delete/$1');  // alias

	$routes->post('variant/create',    				'Seller\Variants::create');
	$routes->post('variant',         				'Seller\Variants::create');  // alias
	$routes->get('variant',    						'Seller\Variants::index');
	$routes->get('variant/product/(:num)',    		'Seller\Variants::fromProduct/$1');
	$routes->get('variant/(:num)',             		'Seller\Variants::show/$1');
	$routes->put('variant/update/(:num)',    		'Seller\Variants::update/$1');
	$routes->put('variant/(:num)',         			'Seller\Variants::update/$1');  // alias
	$routes->delete('variant/delete/(:num)',   	 	'Seller\Variants::delete/$1');
	$routes->delete('variant/(:num)',         		'Seller\Variants::delete/$1');  // alias

	$routes->post('chat',							'Seller\Chats::create');  // alias
	$routes->post('chat/create',					'Seller\Chats::create');
	$routes->put('chat/(:num)',						'Seller\Chats::update/$1');
	$routes->get('chat',               				'Seller\Chats::index');
	$routes->get('chat/(:num)',    					'Seller\Chats::chat/$1');
	$routes->delete('chat/(:num)',					'Seller\Chats::delete/$1');

	$routes->get('order', 							'Seller\Orders::index');
	$routes->get('order/chart', 					'Seller\Orders::chart');
	$routes->get('order/(:num)', 					'Seller\Orders::show/$1');
	$routes->put('order/([a-z])/(:num)', 			'Seller\Orders::status/$1/$2');

	$routes->get('notifications/badges', 			'Seller\Notifications::badges');
	$routes->get('notifications/',                  'Seller\Notifications::index');
	$routes->get('notifications/show/(:num)',    	'Seller\Notifications::show/$1');
	$routes->get('notifications/(:num)',         	'Seller\Notifications::show/$1');  // alias
	$routes->delete('notifications/delete/(:num)', 	'Seller\Notifications::delete/$1');
	$routes->delete('notifications/(:num)', 		'Seller\Notifications::delete/$1');   // alias
});

$routes->group('stores', function ($routes) {
	$routes->post('create',  				'Stores::create', ['filter' => 'seller']);
	$routes->post('/',         				'Stores::create', ['filter' => 'seller']);  // alias

	$routes->get('/',                   	'Stores::index');
	$routes->get('user',    				'Stores::userStore', ['filter' => 'seller']);
	$routes->get('show/(:segment)',    		'Stores::show/$1');
	$routes->get('(:segment)',         		'Stores::show/$1');  // alias

	$routes->put('update/(:segment)',   	'Stores::update/$1', ['filter' => 'seller']);
	$routes->put('(:segment)',         		'Stores::update/$1', ['filter' => 'seller']);  // alias
});

$routes->group('trackings', function ($routes) {
	$routes->post('create',  				'Trackings::create', ['filter' => 'seller']);
	$routes->post('/',         				'Trackings::create', ['filter' => 'seller']);  // alias
	$routes->get('(:segment)',             	'Trackings::index/$1', ['filter' => 'customer']);
});

$routes->group('users', ['filter' => 'customer'], function ($routes) {
	$routes->post('change/email',      		'Users::changeEmail');
	$routes->post('change/phone',      		'Users::changePhone');
	$routes->post('change/password',    	'Users::changePassword');
	$routes->post('change/avatar',     		'Users::avatar');
	$routes->post('create',					'Users::create', ['filter' => 'admin']);
	$routes->post('/',						'Users::create', ['filter' => 'admin']);   // alias
	$routes->get('/',           			'Users::index');
	$routes->get('count',      				'Users::count');
	$routes->get('find',      				'Users::sugestion');
	$routes->get('show/(:segment)',      	'Users::show/$1');
	$routes->get('(:segment)',           	'Users::show/$1');  // alias
	$routes->put('update/(:segment)',    	'Users::update/$1');
	$routes->put('(:segment)',    			'Users::update/$1');
	$routes->delete('delete/(:num)',		'Users::delete/$1', ['filter' => 'admin']);
	$routes->delete('(:num)',				'Users::delete/$1', ['filter' => 'admin']);   // alias
});

$routes->group('variants', function ($routes) {
	$routes->get('(:segment)',             	'Variants::index/$1');
	$routes->get('product/(:segment)',    	'Variants::index/$1');   // alias
});

$routes->group('verify', function ($routes) {
	$routes->post('send_code',             	'Verify::send_code');
	$routes->get('/',             			'Verify::index');
	$routes->get('reset_code',             	'Verify::reset_code');
});


$routes->group('sanbox', function ($routes) {
	$routes->get('/',             			'Sanbox::index');
	$routes->get('generate/flash_sale',     'Sanbox::updateFlashSale');
});

$routes->group('jobs', function ($routes) {
	$routes->get('transactions', 			'cronJob::transactions');
	$routes->get('orders', 					'cronJob::orders');
	$routes->get('verify', 					'cronJob::verify');
});

$routes->get('migrate', 'Migrate::index');
$routes->get('migrate/test', 'Migrate::test');
$routes->get('(:segment)',				'Pages::show/$1');   // alias

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
