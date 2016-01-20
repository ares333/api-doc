<?php
use Yaf\Application;
define ( 'APP_PATH', dirname ( __DIR__ ) . '/app', true );
$app = new Application( APP_PATH . '/conf/app.ini' );
$app->bootstrap ()->run ();