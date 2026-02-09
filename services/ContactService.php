<?php

require_once __DIR__ . '/../core/HttpClient.php';
require_once __DIR__ . '/../services/TokenStorage.php';

/**
 * Contact Service - Manages contact operations with GoHighLevel API
 * Provides methods for CRUD operations on contacts
 */
class ContactService
{
   private $http;
   private $token;

   /**
    * Constructor - initializes HTTP client with authentication
    * @param TokenStorage $storage Token storage instance containing OAuth tokens
    * @throws RuntimeException if access token is missing
    */
   public function __construct(TokenStorage $storage)
   {
      $token = $storage->get();

      // Validate that access token exists
      if (!$token || empty($token['access_token'])) {
         throw new RuntimeException('Missing access token. Authenticate first.');
      }

      $this->token = $token;
      // Initialize HTTP client with access token
      $this->http = new HttpClient($token['access_token']);
   }

   /**
    * Get the location ID from stored token data
    * @return string|null The location ID or null if not found
    */
   private function getLocationId()
   {
      return $this->token['locationId'] ?? null;
   }

   /**
    * Get all contacts for the current location
    * @param string|null $query Optional search query to filter contacts
    * @return array Array of contact objects
    * @throws RuntimeException if location ID is missing
    */
   public function getAllContacts($query = null)
   {
      $locationId = $this->getLocationId();

      // Location ID is required for fetching contacts
      if (!$locationId) {
         throw new RuntimeException('Missing locationId.');
      }

      // Build query parameters
      $queryParams = [
         'locationId' => $locationId
      ];

      // Add search query if provided
      if ($query) {
         $queryParams['query'] = $query;
      }

      // Make API request
      $response = $this->http->get('contacts/', $queryParams);

      $contacts = $response['contacts'] ?? [];

      // Convert arrays to objects for easier access in views
      return array_map(function ($contact) {
         return (object) $contact;
      }, $contacts);
   }

   /**
    * Get a single contact by ID
    * @param string $contactId The contact ID
    * @return array Contact data
    */
   public function get($contactId)
   {
      return $this->http->get("contacts/{$contactId}");
   }

   /**
    * Create a new contact
    * @param array $data Contact data (firstName, lastName, email, phone, etc.)
    * @return array Created contact data
    * @throws RuntimeException if location ID is missing
    */
   public function create($data)
   {
      $locationId = $this->getLocationId();

      // Location ID is required for creating contacts
      if (!$locationId) {
         throw new RuntimeException('Missing locationId.');
      }

      // Add location ID to contact data
      $data['locationId'] = $locationId;

      return $this->http->post('contacts/', $data);
   }

   /**
    * Update an existing contact
    * @param string $contactId The contact ID to update
    * @param array $data Updated contact data
    * @return array Updated contact data
    */
   public function update($contactId, $data)
   {
      return $this->http->put("contacts/{$contactId}", $data);
   }

   /**
    * Delete a contact
    * @param string $contactId The contact ID to delete
    * @return array Deletion response
    */
   public function delete($contactId)
   {
      return $this->http->delete("contacts/{$contactId}");
   }
}
