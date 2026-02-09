<?php

require_once __DIR__ . '/../services/ContactService.php';
require_once __DIR__ . '/../services/TokenStorage.php';

/**
 * Contact Controller - Handles contact-related requests
 * Acts as a bridge between views and the ContactService
 */
class ContactController
{
   private $service;

   /**
    * Constructor - initializes the contact service
    */
   public function __construct()
   {
      // Create token storage instance
      $storage = new TokenStorage();
      // Initialize contact service with authentication tokens
      $this->service = new ContactService($storage);
   }

   /**
    * Get all contacts from GoHighLevel
    * @param string|null $query Optional search query to filter contacts
    * @return array Array of contact objects
    */
   public function getAllContacts($query = null)
   {
      return $this->service->getAllContacts($query);
   }

}
