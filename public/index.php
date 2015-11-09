<?php
define ( 'APP_PATH', dirname ( __DIR__ ) . '/app', true );
$app = new Yaf_Application ( APP_PATH . '/conf/app.ini' );
$app->bootstrap ()->run ();