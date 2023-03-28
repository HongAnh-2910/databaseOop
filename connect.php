<?php
    require_once 'Database.php';
    $config = require_once 'config.php';
    $db = Database::getInstance($config);
    return $db;

