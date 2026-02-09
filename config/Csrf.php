<?php

/**
 * CSRF (Cross-Site Request Forgery) protection class
 * Provides token generation and validation for secure form submissions
 */
class Csrf
{
   /**
    * Generate or retrieve the current CSRF token
    * @return string The CSRF token
    */
   public static function generate()
   {
      // Create a new token if one doesn't exist in the session
      if (empty($_SESSION['csrf_token'])) {
         // Generate a cryptographically secure random 32-byte token
         $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      }

      return $_SESSION['csrf_token'];
   }

   /**
    * Validate a CSRF token against the session token
    * @param string $token The token to validate
    * @return bool True if token is valid, false otherwise
    */
   public static function validate($token)
   {
      // Use hash_equals to prevent timing attacks
      $validated = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
      return $validated;
   }
}
