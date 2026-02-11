<?php

/**
 * ViewHelper - Form and View Helper Utilities
 * Provides helper methods for views including:
 * - Form field value escaping
 * - Select field option handling
 * - Input pre-population
 */
class ViewHelper
{
   /**
    * Get form field value with proper escaping
    * 
    * @param string $fieldName The name of the form field
    * @param string $default Default value if field is not set
    * @param array $source Data source (defaults to $_POST)
    * @return string Escaped field value
    */
   public static function getFieldValue($fieldName, $default = '', $source = null)
   {
      $source = $source ?? $_POST;
      $value = $source[$fieldName] ?? $default;
      return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
   }

   /**
    * Get selected attribute for select fields
    * 
    * @param string $fieldName The name of the select field
    * @param string $value The value to check against
    * @param array $source Data source (defaults to $_POST)
    * @return string 'selected' if matches, empty string otherwise
    */
   public static function getSelected($fieldName, $value, $source = null)
   {
      $source = $source ?? $_POST;
      return (($source[$fieldName] ?? '') === $value) ? 'selected' : '';
   }

   /**
    * Get checked attribute for checkboxes and radio buttons
    * 
    * @param string $fieldName The name of the field
    * @param string $value The value to check against
    * @param array $source Data source (defaults to $_POST)
    * @return string 'checked' if matches, empty string otherwise
    */
   public static function getChecked($fieldName, $value, $source = null)
   {
      $source = $source ?? $_POST;
      return (($source[$fieldName] ?? '') === $value) ? 'checked' : '';
   }

   /**
    * Escape output for HTML display
    * 
    * @param string $value Value to escape
    * @return string Escaped value
    */
   public static function escape($value)
   {
      return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
   }

   /**
    * Get session messages and clear them
    * 
    * @return array Array with 'success' and 'error' keys
    */
   public static function getSessionMessages()
   {
      $messages = [
         'success' => null,
         'error' => null
      ];

      if (isset($_SESSION['success'])) {
         $messages['success'] = $_SESSION['success'];
         unset($_SESSION['success']);
      }
      if (isset($_SESSION['error'])) {
         $messages['error'] = $_SESSION['error'];
         unset($_SESSION['error']);
      }

      return $messages;
   }
}
