<?php

/**
 * Redirect - Redirect Helper
 * Provides clean redirect functionality
 */
class Redirect
{
   /**
    * Redirect to a specific URL
    * 
    * @param string $url URL to redirect to
    * @return void
    */
   public static function to($url)
   {
      header('Location: ' . $url);
      exit;
   }

   /**
    * Redirect back to the current page
    * 
    * @param string $queryParams Optional query string parameters
    * @return void
    */
   public static function back($queryParams = '')
   {
      $url = $_SERVER['PHP_SELF'];
      if ($queryParams) {
         $url .= '?' . $queryParams;
      }
      header('Location: ' . $url);
      exit;
   }

   /**
    * Redirect to the contacts list page
    * 
    * @return void
    */
   public static function toContacts()
   {
      self::to('/views/contacts/');
   }

   /**
    * Redirect with a success message
    * 
    * @param string $url URL to redirect to
    * @param string $message Success message to set in session
    * @return void
    */
   public static function withSuccess($url, $message)
   {
      $_SESSION['success'] = $message;
      self::to($url);
   }

   /**
    * Redirect with an error message
    * 
    * @param string $url URL to redirect to
    * @param string $message Error message to set in session
    * @return void
    */
   public static function withError($url, $message)
   {
      $_SESSION['error'] = $message;
      self::to($url);
   }
}
