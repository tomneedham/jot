<?php
namespace OCA\Jot\AppInfo;

$application = new Application();

$application->registerRoutes($this, array(
    'routes' => array(
        array('name' => 'page#index', 'url' => '/', 'verb' => 'GET'),
        array('name' => 'item_api#getItems', 'url' => '/api/1.0/items/{space}?limit={limit}&offset={offset}', 'verb' => 'GET')
    )
));