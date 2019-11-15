<?php
define("VIEWS", dirname(__DIR__) . '../App/Views');

require dirname(__DIR__) . '/vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
session_start();

$length = 32;
$_SESSION['csrf_token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $length);
$_SESSION['csrf_token_expire'] = 0;

error_reporting(E_ALL);

/**
 * Routing
 */
$router = \Core\Router::getInstance();

$router->add('', ['controller' => 'Home', 'action' => 'index', 'methods' => 'get']);
$router->add('api/login', ['controller' => 'Admin', 'action' => 'login', 'methods' => ['post']]);
$router->add('api/logout', ['controller' => 'Admin', 'action' => 'logout', 'methods' => ['post']]);
$router->add('api/dashboard', ['controller' => 'Admin', 'action' => 'index', 'methods' => 'get']);

$router->add('api/list-pages', ['controller' => 'Admin', 'action' => 'list_pages', 'methods' => 'get']);
$router->add('api/read-page', ['controller' => 'Admin', 'action' => 'read_page', 'methods' => 'get']);
$router->add('api/create-page', ['controller' => 'Admin', 'action' => 'create_page', 'methods' => 'post']);
$router->add('api/update-page', ['controller' => 'Admin', 'action' => 'update_page', 'methods' => 'post']);
$router->add('api/delete-page', ['controller' => 'Admin', 'action' => 'delete_page', 'methods' => 'post']);

$router->add('api/upload-image', ['controller' => 'Admin', 'action' => 'upload_image', 'methods' => 'post']);
$router->add('api/delete-image', ['controller' => 'Admin', 'action' => 'delete_image', 'methods' => 'post']);

$router->add('api/list-posts', ['controller' => 'Admin', 'action' => 'list_posts', 'methods' => 'get']);
$router->add('api/list-featured-posts', ['controller' => 'Admin', 'action' => 'list_featured_posts', 'methods' => 'get']);
$router->add('api/read-post', ['controller' => 'Admin', 'action' => 'read_post', 'methods' => 'get']);
$router->add('api/create-post', ['controller' => 'Admin', 'action' => 'create_post', 'methods' => 'post']);
$router->add('api/update-post', ['controller' => 'Admin', 'action' => 'update_post', 'methods' => 'post']);
$router->add('api/delete-post', ['controller' => 'Admin', 'action' => 'delete_post', 'methods' => 'post']);

$router->add('api/read-distinct-tags', ['controller' => 'Admin', 'action' => 'read_distinct_tags', 'methods' => 'get']);

$router->add('api/list-comments', ['controller' => 'Admin', 'action' => 'list_comments', 'methods' => 'get']);
$router->add('api/read-comment', ['controller' => 'Admin', 'action' => 'read_comment', 'methods' => 'get']);
$router->add('api/create-comment', ['controller' => 'Admin', 'action' => 'create_comment', 'methods' => 'post']);
$router->add('api/update-comment', ['controller' => 'Admin', 'action' => 'update_comment', 'methods' => 'post']);
$router->add('api/delete-comment', ['controller' => 'Admin', 'action' => 'delete_comment', 'methods' => 'post']);

$router->add('api/update-about', ['controller' => 'Admin', 'action' => 'update_about', 'methods' => ['get','post']]);
$router->add('api/update-contacts', ['controller' => 'Admin', 'action' => 'update_contacts', 'methods' => ['get','post']]);
$router->add('api/update-social-links', ['controller' => 'Admin', 'action' => 'update_social_links', 'methods' => ['get','post']]);

$router->add('api/list-user-visits', ['controller' => 'Admin', 'action' => 'list_analytics_user_visit', 'methods' => 'get']);
// $router->add('api/read-user-visit', ['controller' => 'Admin', 'action' => 'read_analytics_user_visit', 'methods' => 'get']);
$router->add('api/create-user-visit', ['controller' => 'Admin', 'action' => 'create_analytics_user_visit', 'methods' => 'post']);
// $router->add('api/update-user-visit', ['controller' => 'Admin', 'action' => 'update_analytics_user_visit', 'methods' => 'post']);
// $router->add('api/delete-user-visit', ['controller' => 'Admin', 'action' => 'delete_analytics_user_visit', 'methods' => 'post']);

$router->add('api/update-advertisements', ['controller' => 'Admin', 'action' => 'update_advertisements', 'methods' => ['get','post']]);

$router->add('api/create-defaults',['controller' => 'Admin', 'action' => 'create_defaults', 'methods' => 'get' ]);
// $router->add('api/drop',['controller' => 'Admin', 'action' => 'drop_all_tables', 'methods' => 'get' ]);
$router->add('.*',['controller' => 'Home', 'action' => 'unknownPage', 'methods' => 'any' ]);
    
$router->dispatch($_SERVER['QUERY_STRING']);
