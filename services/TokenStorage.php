<?php

/**
 * Token Storage Service
 * Manages saving and retrieving OAuth tokens from a JSON file
 */
class TokenStorage
{
  private $filePath;

  /**
   * Constructor - initializes the token storage file path
   * @param string $filePath Path to the JSON file for storing tokens
   */
  public function __construct($filePath = __DIR__ . '/../storage/tokens.json')
  {
    $this->filePath = $filePath;

    // Create storage directory if it doesn't exist
    if (!file_exists(dirname($this->filePath))) {
      mkdir(dirname($this->filePath), 0777, true);
    }
  }

  /**
   * Save tokens to the storage file
   * @param array $tokens Token data to save (access_token, refresh_token, etc.)
   * @return int|false Number of bytes written, or false on failure
   */
  public function save(array $tokens)
  {
    // Write tokens as formatted JSON to file
    return file_put_contents($this->filePath, json_encode($tokens, JSON_PRETTY_PRINT));
  }

  /**
   * Retrieve tokens from storage
   * @return array|null Token data or null if file doesn't exist
   */
  public function get()
  {
    // Return null if token file doesn't exist
    if (!file_exists($this->filePath)) {
      return null;
    }

    // Read and decode JSON token data
    return json_decode(file_get_contents($this->filePath), true);
  }

  /**
   * Check if a token exists in storage
   * @return bool True if token file exists, false otherwise
   */
  public function hasToken()
  {
    return file_exists($this->filePath);
  }
}
