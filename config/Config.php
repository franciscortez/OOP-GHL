<?php

use Dotenv\Dotenv;

/**
 * Configuration class for managing environment variables
 * Uses the Dotenv library to load and access .env file values
 */
class Config
{
   /**
    * Load environment variables from the .env file
    * This should be called once at application startup
    */
   public static function load()
   {
      // Create Dotenv instance pointing to the root directory
      $dotenv = Dotenv::createImmutable(__DIR__ . "/../");
      // Load all variables from .env into $_ENV
      $dotenv->load();
   }

   /**
    * Get an environment variable value
    * @param string $key The name of the environment variable
    * @param mixed $default Default value if the key doesn't exist
    * @return mixed The environment variable value or default
    */
   public static function get($key, $default = null)
   {
      return $_ENV[$key] ?? $default;
   }
}
