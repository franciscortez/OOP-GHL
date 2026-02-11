<?php

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Csrf.php';
require_once __DIR__ . '/../services/TokenStorage.php';
require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

/**
 * Authentication Service for GoHighLevel OAuth
 * Handles OAuth flow, token exchange, and authentication URL generation
 */
class AuthService
{
  private $http;
  private $storage;

  public function __construct(TokenStorage $storage)
  {
    $this->storage = $storage;

    // Initialize Guzzle HTTP client
    $this->http = new Client([
      'base_uri' => 'https://services.leadconnectorhq.com/',
      'timeout' => 10
    ]);
  }

  /**
   * Generate OAuth authorization URL
   */
  public function getAuthUrl()
  {
    $state = Csrf::generate();
    $clientId = Config::get('GHL_CLIENT_ID');

    // Extract version ID from client ID
    $versionId = explode('-', $clientId)[0];

    return "https://marketplace.leadconnectorhq.com/oauth/chooselocation?response_type=code&redirect_uri=" .
      urlencode(Config::get('GHL_REDIRECT_URI')) .
      "&client_id=" . $clientId .
      "&scope=contacts.write%20contacts.readonly%20locations.readonly%20opportunities.readonly%20opportunities.write%20users.readonly" .
      "&version_id=" . $versionId .
      "&state=" . $state;
  }

  /**
   * Exchange authorization code for access token
   */
  public function exchangeCodeForToken($code)
  {
    try {
      $response = $this->http->post('oauth/token', [
        'form_params' => [
          'client_id' => Config::get('GHL_CLIENT_ID'),
          'client_secret' => Config::get('GHL_CLIENT_SECRET'),
          'grant_type' => 'authorization_code',
          'code' => $code,
          'redirect_uri' => Config::get('GHL_REDIRECT_URI')
        ]
      ]);

      $tokenData = json_decode($response->getBody(), true);

      // Save tokens
      $this->storage->save($tokenData);

      return $tokenData;

    } catch (\Exception $e) {
      throw new Exception("Token exchange failed: " . $e->getMessage());
    }
  }
}
