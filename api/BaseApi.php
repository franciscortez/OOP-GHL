<?php

/**
 * Base API Class
 * 
 * Purpose:
 * - Provide common functionality for all API classes
 * - Handle CSRF token generation and validation
 * - Reduce code duplication
 */

require_once __DIR__ . '/../config/Csrf.php';

abstract class BaseApi
{
   protected $csrfToken;

   public function __construct()
   {
      $this->csrfToken = Csrf::generate();
   }

   /**
    * Get CSRF token for forms
    * 
    * @return string The CSRF token
    */
   public function getCsrfToken()
   {
      return $this->csrfToken;
   }

   /**
    * Validate CSRF token
    * 
    * @param string $token The CSRF token to validate
    * @return bool True if valid, false otherwise
    */
   public function validateCsrf($token)
   {
      return Csrf::validate($token);
   }
}
