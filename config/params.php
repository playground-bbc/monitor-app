<?php

// path to folder flat archives
$s = DIRECTORY_SEPARATOR;

//Yii::setAlias('@live-chat', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-filebase{$s}live-chat");
Yii::setAlias('@resources', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-data{$s}resources");
Yii::setAlias('@backup', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-data{$s}backup");
Yii::setAlias('@service_account', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-data{$s}service_account");

return [
    'twitter' => [
    	'api_key' => 'o1Ah41T1AhjUs9e2xYJQMuCaE',
    	'api_secret_key'=> '5U7whZh5KEyOQ3qt6WlwtNsf8g4kriEU1lzUzef9HkT6hOkHoZ',
    	'access_token' => '305928001-Lr0BCqUYTFetCgejpC0G8U99BtCRi9zVHvz4hCGx',
    	'access_secret_token'=> 'LxoblFlA0a1AkopN92fVa7ejkQ6HjKwRyRJCpJdARxgIU',
    ],
    'liveChat' => [
    	'apiLogin' => 'eduardo@montana-studio.com',
        'apiKey' => 'ad24335143918c8470d4962a1c52866e'
    	/*'apiKey' => 'ad24335143918c8470d4962a1c52866e'*/
    ],
    'drive' => [
        'clientId' => '664489977267-15o8cja0nao16549i567mg6iuh74fuad.apps.googleusercontent.com',
        'clientSecret' => 'BkerwlfGvd0RdKJo2x_ie7kS',
        'spreadsheetId' => '14IGcLxkD4uqINWIebmD9_u_iIaPtLo_4moL2TdUyOdo'
    ]
];
