<?php
use Yaf\Application;
$appPath = dirname(__DIR__) . '/app';
$app = new Application($appPath . '/../conf/app.ini');
$app->setAppDirectory($appPath)
    ->bootstrap()
    ->run();