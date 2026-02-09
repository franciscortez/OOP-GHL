<?php

use GuzzleHttp\Client;

/**
 * HTTP Client for GoHighLevel API
 * Handles all API requests with proper authentication and headers
 */
class HttpClient
{
   private $client;
   private $token;

   /**
    * Constructor - initializes the Guzzle HTTP client
    * @param string $token OAuth access token for API authentication
    */
   public function __construct($token)
   {
      $this->token = $token;

      // Initialize Guzzle client with GoHighLevel API base URL
      $this->client = new Client([
         'base_uri' => 'https://services.leadconnectorhq.com/'
      ]);
   }

   /**
    * Build common headers for all API requests
    * @return array Headers including authorization and content type
    */
   private function headers()
   {
      return [
         'Authorization' => 'Bearer ' . $this->token,
         'Accept' => 'application/json',
         'Content-Type' => 'application/json',
         'Version' => '2021-07-28'
      ];
   }

   /**
    * Make an HTTP request to the API
    * @param string $method HTTP method (GET, POST, PUT, DELETE)
    * @param string $uri API endpoint URI
    * @param array $data Request body data
    * @param array $query Query parameters
    * @return array Decoded JSON response
    */
   public function request($method, $uri, $data = [], $query = [])
   {
      // Start with headers
      $options = [
         'headers' => $this->headers()
      ];

      // Add JSON body if data provided
      if (!empty($data)) {
         $options['json'] = $data;
      }

      // Add query parameters if provided
      if (!empty($query)) {
         $options['query'] = $query;
      }

      // Make the request
      $response = $this->client->request($method, $uri, $options);

      // Return decoded JSON response
      return json_decode($response->getBody(), true);
   }

   /**
    * Make a GET request
    * @param string $uri API endpoint URI
    * @param array $query Query parameters
    * @return array Decoded JSON response
    */
   public function get($uri, $query = [])
   {
      return $this->request('GET', $uri, [], $query);
   }

   /**
    * Make a POST request
    * @param string $uri API endpoint URI
    * @param array $data Request body data
    * @return array Decoded JSON response
    */
   public function post($uri, $data)
   {
      return $this->request('POST', $uri, $data);
   }

   /**
    * Make a PUT request
    * @param string $uri API endpoint URI
    * @param array $data Request body data
    * @return array Decoded JSON response
    */
   public function put($uri, $data)
   {
      return $this->request('PUT', $uri, $data);
   }

   /**
    * Make a DELETE request
    * @param string $uri API endpoint URI
    * @return array Decoded JSON response
    */
   public function delete($uri)
   {
      return $this->request('DELETE', $uri);
   }
}
