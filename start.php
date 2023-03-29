<?php


    require_once 'Connect.php';
    $config = require_once 'config.php';
    $db = Connect::getInstance($config);
    return $db;
