<?php

/**
 * Opportunity API - Application Layer / Use Case Layer
 * 
 * Purpose:
 * - Coordinate opportunity operations
 * - Call controller/service layer
 * - Return structured results
 */

// Load application dependencies
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/BaseApi.php';
require_once __DIR__ . '/../controller/OpportunityController.php';

class OpportunityApi extends BaseApi
{
   private $controller;

   public function __construct()
   {
      parent::__construct();
      $this->controller = new OpportunityController();
   }

   /**
    * Search opportunities with optional filters
    * 
    * @param array $filters Optional filters (q, pipeline_id, status, contact_id, etc.)
    * @return array Array of opportunity objects
    * @throws Exception If retrieval fails
    */
   public function searchOpportunities($filters = [])
   {
      try {
         return $this->controller->searchOpportunities($filters);
      } catch (Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   /**
    * Get single opportunity by ID
    * 
    * @param string $opportunityId The opportunity ID
    * @return array|null Opportunity data or null if not found
    */
   public function getOpportunity($opportunityId)
   {
      $response = $this->controller->get($opportunityId);
      return $response['opportunity'] ?? null;
   }

   /**
    * Handle opportunity creation
    * 
    * @param array $formData Form data from request
    * @return array Result with 'success' boolean and 'message' string
    */
   public function createOpportunity($formData)
   {
      try {
         $this->controller->create($formData);
         return [
            'success' => true,
            'message' => 'Opportunity created successfully!'
         ];
      } catch (Throwable $e) {
         return [
            'success' => false,
            'message' => $e->getMessage()
         ];
      }
   }

   /**
    * Handle opportunity update
    * 
    * @param string $opportunityId The opportunity ID to update
    * @param array $formData Form data from request
    * @return array Result with 'success' boolean and 'message' string
    */
   public function updateOpportunity($opportunityId, $formData)
   {
      try {
         $this->controller->update($opportunityId, $formData);
         return [
            'success' => true,
            'message' => 'Opportunity updated successfully!'
         ];
      } catch (Throwable $e) {
         return [
            'success' => false,
            'message' => $e->getMessage()
         ];
      }
   }

   /**
    * Handle opportunity deletion
    * 
    * @param string $opportunityId The opportunity ID to delete
    * @return array Result with 'success' boolean and 'message' string
    */
   public function deleteOpportunity($opportunityId)
   {
      try {
         $this->controller->delete($opportunityId);
         return [
            'success' => true,
            'message' => 'Opportunity deleted successfully!'
         ];
      } catch (Throwable $e) {
         return [
            'success' => false,
            'message' => $e->getMessage()
         ];
      }
   }

   /**
    * Get all pipelines with stages
    * 
    * @return array Array of pipeline objects
    * @throws Exception If retrieval fails
    */
   public function getPipelines()
   {
      try {
         return $this->controller->getPipelines();
      } catch (Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   /**
    * Get all users (owners) for the location
    * 
    * @return array Array of user objects
    * @throws Exception If retrieval fails
    */
   public function getUsers()
   {
      try {
         return $this->controller->getUsers();
      } catch (Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }
}
