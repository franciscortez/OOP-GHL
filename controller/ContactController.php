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

   /**
    * Get a single contact by ID
    * @param string $contactId The contact ID
    * @return array Contact data
    */
   public function get($contactId)
   {
      return $this->service->get($contactId);
   }

   /**
    * Create a new contact from form data
    * @param array $formData Raw form data from $_POST
    * @return array Created contact data
    */
   public function create($formData)
   {
      // Prepare contact data
      $contactData = [
         'firstName' => trim($formData['firstName'] ?? ''),
         'lastName' => trim($formData['lastName'] ?? ''),
         'email' => trim($formData['email'] ?? ''),
         'phone' => trim($formData['phone'] ?? ''),
      ];

      // Create the contact
      try {

         return $this->service->create($contactData);

      } catch (Exception $e) {
         // Extract simple error message from API response
         $errorMessage = $e->getMessage();

         // Check if it's a duplicate contact error
         if (strpos($errorMessage, 'duplicated contacts') !== false) {
            throw new Exception('Contact already exists with this email or phone number.');
         }

         // Check for other common errors
         if (strpos($errorMessage, '400') !== false) {
            throw new Exception('Invalid contact information. Please check your input.');
         }

         if (strpos($errorMessage, '401') !== false || strpos($errorMessage, '403') !== false) {
            throw new Exception('Authentication failed. Please login again.');
         }

         // Generic error message
         throw new Exception('Failed to create contact. Please try again.');
      }
   }

   /**
    * Update an existing contact from form data
    * @param string $contactId The contact ID to update
    * @param array $formData Raw form data from $_POST
    * @return array Updated contact data
    */
   public function update($contactId, $formData)
   {
      // Prepare contact data with all possible fields
      $contactData = [];

      // Add fields only if they exist in form data
      $fields = [
         'firstName',
         'lastName',
         'email',
         'phone',
         'gender',
         'dateOfBirth',
         'companyName',
         'address1',
         'city',
         'state',
         'postalCode',
         'country',
         'website',
         'timezone',
         'source'
      ];

      foreach ($fields as $field) {
         if (isset($formData[$field]) && $formData[$field] !== '') {
            $contactData[$field] = trim($formData[$field]);
         }
      }

      // Update the contact
      try {

         return $this->service->update($contactId, $contactData);

      } catch (Exception $e) {
         // Extract simple error message from API response
         $errorMessage = $e->getMessage();

         // Check for common errors
         if (strpos($errorMessage, '400') !== false) {
            throw new Exception('Invalid contact information. Please check your input.');
         }

         if (strpos($errorMessage, '401') !== false || strpos($errorMessage, '403') !== false) {
            throw new Exception('Authentication failed. Please login again.');
         }

         if (strpos($errorMessage, '404') !== false) {
            throw new Exception('Contact not found.');
         }

         // Generic error message
         throw new Exception('Failed to update contact. Please try again.');
      }
   }

   /**
    * Delete a contact
    * @param string $contactId The contact ID to delete
    * @return array Deletion response
    */
   public function delete($contactId)
   {
      try {
         return $this->service->delete($contactId);
      } catch (Exception $e) {
         // Extract simple error message from API response
         $errorMessage = $e->getMessage();

         // Check for common errors
         if (strpos($errorMessage, '401') !== false || strpos($errorMessage, '403') !== false) {
            throw new Exception('Authentication failed. Please login again.');
         }

         if (strpos($errorMessage, '404') !== false) {
            throw new Exception('Contact not found.');
         }

         // Generic error message
         throw new Exception('Failed to delete contact. Please try again.');
      }
   }

}
