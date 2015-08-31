<?php
namespace OCA\Jot\AppInfo;

$application = new Application();

$application->registerRoutes($this, array(
    'routes' => array(
        array('name' => 'page#index', 'url' => '/', 'verb' => 'GET'),
        array('name' => 'jot_api#getJots', 'url' => '/api/1.0/jots/', 'verb' => 'GET'),
        array('name' => 'jot_api#newItem', 'url' => '/api/1.0/jots/', 'verb' => 'POST'),
        array('name' => 'jot_api#updateItem', 'url' => '/api/1.0/jots/{id}', 'verb' => 'PUT'),
        array('name' => 'jot_api#deleteItem', 'url' => '/api/1.0/jots/{id}', 'verb' => 'DELETE')
    )
));
