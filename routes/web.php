<?php

return [
    ['path' => '/',              'method' => 'GET',  'action' => 'HomeController@index'],

    ['path' => '/users',         'method' => 'GET',  'action' => 'UserController@index'],
    ['path' => '/users/create',  'method' => 'GET',  'action' => 'UserController@create'],
    ['path' => '/users/store',   'method' => 'POST', 'action' => 'UserController@store'],
    ['path' => '/users/edit',    'method' => 'GET',  'action' => 'UserController@edit'],
    ['path' => '/users/update',  'method' => 'POST', 'action' => 'UserController@update'],
    ['path' => '/users/delete',  'method' => 'GET',  'action' => 'UserController@delete'],
];