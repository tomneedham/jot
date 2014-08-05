<?php
namespace OCA\Jot\AppInfo;

$application = new Application();

$application->registerRoutes($this, array(
    'routes' => array(
        array('name' => 'page#index', 'url' => '/', 'verb' => 'GET'),
        array('name' => 'item_api#getItems', 'url' => '/api/1.0/items/', 'verb' => 'GET'),
        array('name' => 'item_api#newItem', 'url' => '/api/1.0/items/', 'verb' => 'POST'),
        array('name' => 'item_api#updateItem', 'url' => '/api/1.0/items/{id}', 'verb' => 'PUT'),
        array('name' => 'item_api#deleteItem', 'url' => '/api/1.0/items/{id}', 'verb' => 'DELETE')
    )
));