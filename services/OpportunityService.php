<?php

require_once __DIR__ . '/../core/HttpClient.php';
require_once __DIR__ . '/../services/TokenStorage.php';

/**
 * Opportunity Service - Manages opportunity operations with GoHighLevel API
 * Provides methods for CRUD operations on opportunities
 */
class OpportunityService
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
    * Search opportunities with filters
    * @param array $filters Optional filters (q, pipeline_id, status, contact_id, etc.)
    * @return array Array of opportunity objects
    * @throws RuntimeException if location ID is missing
    */
   public function searchOpportunities($filters = [])
   {
      $locationId = $this->getLocationId();

      // Location ID is required for fetching opportunities
      if (!$locationId) {
         throw new RuntimeException('Missing locationId.');
      }

      // Build query parameters
      $queryParams = [
         'location_id' => $locationId
      ];

      // Add optional filters
      $allowedFilters = [
         'q',
         'pipeline_id',
         'pipeline_stage_id',
         'contact_id',
         'status',
         'assigned_to',
         'campaignId',
         'id',
         'order',
         'endDate',
         'startAfter',
         'startAfterId',
         'date',
         'country',
         'page',
         'limit'
      ];

      foreach ($allowedFilters as $filter) {
         if (isset($filters[$filter]) && $filters[$filter] !== '') {
            $queryParams[$filter] = $filters[$filter];
         }
      }

      // Make API request
      $response = $this->http->get('opportunities/search', $queryParams);

      $opportunities = $response['opportunities'] ?? [];

      // Convert arrays to objects for easier access in views
      return array_map(function ($opportunity) {
         return (object) $opportunity;
      }, $opportunities);
   }

   /**
    * Get a single opportunity by ID
    * @param string $opportunityId The opportunity ID
    * @return array Opportunity data
    */
   public function get($opportunityId)
   {
      return $this->http->get("opportunities/{$opportunityId}");
   }

   /**
    * Create a new opportunity
    * @param array $data Opportunity data (name, pipelineId, contactId, status, etc.)
    * @return array Created opportunity data
    * @throws RuntimeException if location ID is missing
    */
   public function create($data)
   {
      $locationId = $this->getLocationId();

      // Location ID is required for creating opportunities
      if (!$locationId) {
         throw new RuntimeException('Missing locationId.');
      }

      // Add location ID to opportunity data
      $data['locationId'] = $locationId;

      return $this->http->post('opportunities/', $data);
   }

   /**
    * Update an existing opportunity
    * @param string $opportunityId The opportunity ID to update
    * @param array $data Updated opportunity data
    * @return array Updated opportunity data
    */
   public function update($opportunityId, $data)
   {
      return $this->http->put("opportunities/{$opportunityId}", $data);
   }

   /**
    * Delete an opportunity
    * @param string $opportunityId The opportunity ID to delete
    * @return array Deletion response
    */
   public function delete($opportunityId)
   {
      return $this->http->delete("opportunities/{$opportunityId}");
   }

   /**
    * Get all pipelines for the current location
    * @return array Array of pipeline objects with stages
    * @throws RuntimeException if location ID is missing
    */
   public function getPipelines()
   {
      $locationId = $this->getLocationId();

      // Location ID is required for fetching pipelines
      if (!$locationId) {
         throw new RuntimeException('Missing locationId.');
      }

      // Build query parameters
      $queryParams = [
         'locationId' => $locationId
      ];

      // Make API request
      $response = $this->http->get('opportunities/pipelines', $queryParams);

      $pipelines = $response['pipelines'] ?? [];

      // Convert arrays to objects for easier access in views
      return array_map(function ($pipeline) {
         return (object) $pipeline;
      }, $pipelines);
   }

   /**
    * Get all users (owners) for the current location
    * @return array Array of user objects
    * @throws RuntimeException if company ID is missing
    */
   public function getUsers()
   {
      $companyId = $this->token['companyId'] ?? null;
      $locationId = $this->getLocationId();

      // Company ID is required for fetching users
      if (!$companyId) {
         throw new RuntimeException('Missing companyId.');
      }

      // Build query parameters
      $queryParams = [
         'companyId' => $companyId,
         'limit' => 100 // Get up to 100 users
      ];

      // Add location filter if available
      if ($locationId) {
         $queryParams['locationId'] = $locationId;
      }

      // Make API request
      $response = $this->http->get('users/search', $queryParams);

      $users = $response['users'] ?? [];

      // Convert arrays to objects for easier access in views
      return array_map(function ($user) {
         return (object) $user;
      }, $users);
   }
}
