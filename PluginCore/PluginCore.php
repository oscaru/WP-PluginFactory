<?php

namespace PluginCore;

if(!defined('PluginCoreLoaded')){
    define('PluginCoreLoaded', __DIR__);
    
    require PluginCoreLoaded.'/ActionHandler.php';
    require PluginCoreLoaded.'/Container.php';
    require PluginCoreLoaded.'/UserData.php';
    require PluginCoreLoaded.'/BasePlugin.php';
    require PluginCoreLoaded.'/functions.php';
    
    $userData  = \PluginCore\UserData::getInstance();
    
    $container = \PluginCore\Container::getInstance();
    $container->register('wpdb',$wpdb);
    \PluginCore\Messages::getInstance(); //añadimos notificaciones de errores.
}

