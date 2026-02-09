<?php

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Csrf.php';
require_once __DIR__ . '/../services/TokenStorage.php';

use HighLevel\HighLevel;

/**
 * Authentication Service for GoHighLevel OAuth
 * Handles OAuth flow, token exchange, and authentication URL generation
 */
class AuthService
{
  private $ghl;
  private $storage;

  /**
   * Constructor - initializes the GoHighLevel SDK
   * @param TokenStorage $storage Token storage instance for saving OAuth tokens
   */
  public function __construct(TokenStorage $storage)
  {
    $this->storage = $storage;

    // Configure the GoHighLevel SDK with credentials from .env
    $config = [
      'clientId' => Config::get('GHL_CLIENT_ID'),
      'clientSecret' => Config::get('GHL_CLIENT_SECRET'),
    ];
    $this->ghl = new HighLevel($config);
  }

  /**
   * Generate the OAuth authorization URL
   * @return string The full OAuth URL to redirect users to
   */
  public function getAuthUrl()
  {
    // Generate CSRF token for security
    $state = Csrf::generate();
    $clientId = Config::get('GHL_CLIENT_ID');

    // Extract version ID from client ID (format: versionId-clientId)
    $versionId = explode('-', $clientId)[0];

    // Build OAuth authorization URL with all required parameters
    return "https://marketplace.leadconnectorhq.com/oauth/chooselocation?response_type=code&redirect_uri=" .
      urlencode(Config::get('GHL_REDIRECT_URI')) .
      "&client_id=" . $clientId .
      "&scope=contacts.write%20contacts.readonly%20locations.readonly" .
      "&version_id=" . $versionId .
      "&state=" . $state;
  }

  /**
   * Exchange authorization code for access token
   * @param string $code The authorization code from OAuth callback
   * @return object Token data including access token and refresh token
   */
  public function exchangeCodeForToken($code)
  {
    // Use GoHighLevel SDK to exchange code for tokens
    $tokenData = $this->ghl->oauth->getAccessToken([
      'code' => $code,
      'client_id' => Config::get('GHL_CLIENT_ID'),
      'client_secret' => Config::get('GHL_CLIENT_SECRET'),
      'grant_type' => 'authorization_code'
    ]);

    // Save tokens to storage for future use
    $this->storage->save($tokenData->toArray());
    return $tokenData;
  }
}
