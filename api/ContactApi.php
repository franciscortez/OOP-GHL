<?php

/**
 * Contact API - Application Layer / Use Case Layer
 * 
 * Purpose:
 * - Coordinate contact operations
 * - Call controller/service layer
 * - Return structured results
 */

// Load application dependencies
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/BaseApi.php';
require_once __DIR__ . '/../controller/ContactController.php';

class ContactApi extends BaseApi
{
   private $controller;

   public function __construct()
   {
      parent::__construct();
      $this->controller = new ContactController();
   }

   /**
    * Get all contacts for listing
    * 
    * @param string|null $query Optional search query to filter contacts
    * @return array Array of contact objects
    * @throws Exception If retrieval fails
    */
   public function getAllContacts($query = null)
   {
      try {
         return $this->controller->getAllContacts($query);
      } catch (Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   /**
    * Get single contact by ID
    * 
    * @param string $contactId The contact ID
    * @return array|null Contact data or null if not found
    */
   public function getContact($contactId)
   {
      $response = $this->controller->get($contactId);
      return $response['contact'] ?? null;
   }

   /**
    * Handle contact creation
    * 
    * @param array $formData Form data from request
    * @return array Result with 'success' boolean and 'message' string
    */
   public function createContact($formData)
   {
      try {
         $this->controller->create($formData);
         return [
            'success' => true,
            'message' => 'Contact created successfully!'
         ];
      } catch (Throwable $e) {
         return [
            'success' => false,
            'message' => $e->getMessage()
         ];
      }
   }

   /**
    * Handle contact update
    * 
    * @param string $contactId The contact ID to update
    * @param array $formData Form data from request
    * @return array Result with 'success' boolean and 'message' string
    */
   public function updateContact($contactId, $formData)
   {
      try {
         $this->controller->update($contactId, $formData);
         return [
            'success' => true,
            'message' => 'Contact updated successfully!'
         ];
      } catch (Throwable $e) {
         return [
            'success' => false,
            'message' => $e->getMessage()
         ];
      }
   }

   /**
    * Handle contact deletion
    * 
    * @param string $contactId The contact ID to delete
    * @return array Result with 'success' boolean and 'message' string
    */
   public function deleteContact($contactId)
   {
      try {
         $this->controller->delete($contactId);
         return [
            'success' => true,
            'message' => 'Contact deleted successfully!'
         ];
      } catch (Throwable $e) {
         return [
            'success' => false,
            'message' => $e->getMessage()
         ];
      }
   }
}
