<?php

// Autoload classes
spl_autoload_register(function($class) {
    require 'class/' . $class . '.php';
});


// Database connection constants
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = '';
const DB_NAME = 'ideo_rekrutacja';


// Database connection
$db = new DB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);