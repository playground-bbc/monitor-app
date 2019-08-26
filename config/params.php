<?php

// path to folder flat archives
$s = DIRECTORY_SEPARATOR;

Yii::setAlias('@monitor', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-data{$s}");
Yii::setAlias('@resources', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-data{$s}resources");
Yii::setAlias('@backup', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-data{$s}backup");
Yii::setAlias('@drive_account', dirname(dirname(__DIR__)) . "{$s}monitor-app{$s}monitor-app-data{$s}service_account{$s}monitor-app-96f0293a0153.json");

return [
    'twitter' => [
    	'api_key' => 'oxmyn1WmBKihhfdcQGCTXlgQh',
    	'api_secret_key'=> 'msPakDIfXECOe6NrgGrAVwkHdCtbDHzeaHMgVqO4R0ioDyPWlh',
    	'access_token' => '305928001-TTdlPqtbByToHaReoou7LBSOAYPa4uS7WQKqn3xx',
    	'access_secret_token'=> 'IP9mAjRyK9u3xzysDAw43tKkoRy8mYVxxihhZvqXuuZYO',
        'bearer_token'=> 'AAAAAAAAAAAAAAAAAAAAAPfj%2BQAAAAAAnmUTOdGSql%2FZdpdThzlk77I0pkY%3DveTBOEAVxJRkNguDDXNJvtPt83fkl2EVYUncRGdTGtZ6UWLHyc',
    ],
    'liveChat' => [
    	'apiLogin' => 'eduardo@montana-studio.com',
        'apiKey' => 'b1f9cbda5e332b55d03fa2a5deb4c037'
    ],
    'drive' => [
        'Drive Diccionario Listening' => '1LBf9kTwPswIQuvNx0xH8RiMBZiXNZeBGi_QjTrHVwAc',
        'Drive Diccionario Listening test' => '1pK1-E2PZVEqKmH1vle2bU7kmj26MdWve6xukG4DgXPY',
        'spreadsheetId' => '14IGcLxkD4uqINWIebmD9_u_iIaPtLo_4moL2TdUyOdo'
    ]
];
