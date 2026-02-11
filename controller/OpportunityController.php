<?php

require_once __DIR__ . '/../services/OpportunityService.php';
require_once __DIR__ . '/../services/TokenStorage.php';

/**
 * Opportunity Controller - Handles opportunity-related requests
 * Acts as a bridge between views and the OpportunityService
 */
class OpportunityController
{
   private $service;

   /**
    * Constructor - initializes the opportunity service
    */
   public function __construct()
   {
      // Create token storage instance
      $storage = new TokenStorage();
      // Initialize opportunity service with authentication tokens
      $this->service = new OpportunityService($storage);
   }

   /**
    * Search opportunities with optional filters
    * @param array $filters Optional filters (q, pipeline_id, status, contact_id, etc.)
    * @return array Array of opportunity objects
    */
   public function searchOpportunities($filters = [])
   {
      return $this->service->searchOpportunities($filters);
   }

   /**
    * Get all pipelines with stages
    * @return array Array of pipeline objects
    */
   public function getPipelines()
   {
      return $this->service->getPipelines();
   }

   /**
    * Get all users (owners) for the current location
    * @return array Array of user objects
    */
   public function getUsers()
   {
      return $this->service->getUsers();
   }

   /**
    * Get a single opportunity by ID
    * @param string $opportunityId The opportunity ID
    * @return array Opportunity data
    */
   public function get($opportunityId)
   {
      return $this->service->get($opportunityId);
   }

   /**
    * Create a new opportunity from form data
    * @param array $formData Raw form data from $_POST
    * @return array Created opportunity data
    */
   public function create($formData)
   {
      // Prepare opportunity data - required fields
      $opportunityData = [
         'name' => trim($formData['name'] ?? ''),
         'pipelineId' => trim($formData['pipelineId'] ?? ''),
         'contactId' => trim($formData['contactId'] ?? ''),
         'status' => trim($formData['status'] ?? 'open'),
      ];

      // Add optional fields if provided
      $optionalFields = [
         'pipelineStageId',
         'monetaryValue',
         'assignedTo'
      ];

      foreach ($optionalFields as $field) {
         if (isset($formData[$field]) && $formData[$field] !== '') {
            $opportunityData[$field] = trim($formData[$field]);
         }
      }

      // Convert monetaryValue to number if provided
      if (isset($opportunityData['monetaryValue'])) {
         $opportunityData['monetaryValue'] = (float) $opportunityData['monetaryValue'];
      }

      // Create the opportunity
      try {
         return $this->service->create($opportunityData);
      } catch (Exception $e) {
         // Extract simple error message from API response
         $errorMessage = $e->getMessage();

         // Check for common errors
         if (strpos($errorMessage, '400') !== false) {
            throw new Exception('Invalid opportunity information. Please check your input.');
         }

         if (strpos($errorMessage, '401') !== false || strpos($errorMessage, '403') !== false) {
            throw new Exception('Authentication failed. Please login again.');
         }

         if (strpos($errorMessage, '404') !== false) {
            throw new Exception('Pipeline or contact not found.');
         }

         // Generic error message
         throw new Exception('Failed to create opportunity. Please try again.');
      }
   }

   /**
    * Update an existing opportunity from form data
    * @param string $opportunityId The opportunity ID to update
    * @param array $formData Raw form data from $_POST
    * @return array Updated opportunity data
    */
   public function update($opportunityId, $formData)
   {
      // Prepare opportunity data with all possible fields
      $opportunityData = [];

      // Add fields only if they exist in form data
      $fields = [
         'name',
         'pipelineId',
         'pipelineStageId',
         'status',
         'monetaryValue',
         'assignedTo'
      ];

      foreach ($fields as $field) {
         if (isset($formData[$field]) && $formData[$field] !== '') {
            $opportunityData[$field] = trim($formData[$field]);
         }
      }

      // Convert monetaryValue to number if provided
      if (isset($opportunityData['monetaryValue'])) {
         $opportunityData['monetaryValue'] = (float) $opportunityData['monetaryValue'];
      }

      // Update the opportunity
      try {
         return $this->service->update($opportunityId, $opportunityData);
      } catch (Exception $e) {
         // Extract simple error message from API response
         $errorMessage = $e->getMessage();

         // Check for common errors
         if (strpos($errorMessage, '400') !== false) {
            throw new Exception('Invalid opportunity information. Please check your input.');
         }

         if (strpos($errorMessage, '401') !== false || strpos($errorMessage, '403') !== false) {
            throw new Exception('Authentication failed. Please login again.');
         }

         if (strpos($errorMessage, '404') !== false) {
            throw new Exception('Opportunity not found.');
         }

         // Generic error message
         throw new Exception('Failed to update opportunity. Please try again.');
      }
   }

   /**
    * Delete an opportunity
    * @param string $opportunityId The opportunity ID to delete
    * @return array Deletion response
    */
   public function delete($opportunityId)
   {
      try {
         return $this->service->delete($opportunityId);
      } catch (Exception $e) {
         // Extract simple error message from API response
         $errorMessage = $e->getMessage();

         // Check for common errors
         if (strpos($errorMessage, '401') !== false || strpos($errorMessage, '403') !== false) {
            throw new Exception('Authentication failed. Please login again.');
         }

         if (strpos($errorMessage, '404') !== false) {
            throw new Exception('Opportunity not found.');
         }

         // Generic error message
         throw new Exception('Failed to delete opportunity. Please try again.');
      }
   }
}
