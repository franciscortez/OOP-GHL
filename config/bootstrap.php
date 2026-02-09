<?php

/**
 * Bootstrap file - initializes the application
 * This file should be included at the start of every page
 */

// Start PHP session if not already started
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// Load Composer's autoloader for third-party dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Load the Config class
require_once __DIR__ . '/Config.php';

// Load environment variables from .env file
Config::load();
