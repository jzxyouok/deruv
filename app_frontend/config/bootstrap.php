<?php

define('STATIC_URL', './');

if (!is_array($setting) || !$setting['installed']) {
    if (!isset($_COOKIE['install'])) {
        setcookie('install', 1);
        header('Location: index.php?r=install');
        exit();
    }
}else{
    $app->language = \service\Setting::getSysConf('language');
    $app->timeZone = \service\Setting::getSysConf('timeZone');
    if (\service\Setting::getConf('sys', 'webClose')) {
        $app->catchAll = [
            'site/close',
        ];
    }
}
