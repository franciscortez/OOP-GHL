<?php

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Csrf.php';
require_once __DIR__ . '/../services/TokenStorage.php';

use HighLevel\HighLevel;

class AuthService
{
  private $ghl;
  private $storage;

  public function __construct(TokenStorage $storage)
  {
    $this->storage = $storage;
    $config = [
      'clientId' => Config::get('GHL_CLIENT_ID'),
      'clientSecret' => Config::get('GHL_CLIENT_SECRET'),
    ];
    $this->ghl = new HighLevel($config);
  }

  public function getAuthUrl()
  {
    $state = Csrf::generate();
    $clientId = Config::get('GHL_CLIENT_ID');
    $versionId = explode('-', $clientId)[0];

    return "https://marketplace.leadconnectorhq.com/oauth/chooselocation?response_type=code&redirect_uri=" .
      urlencode(Config::get('GHL_REDIRECT_URI')) .
      "&client_id=" . $clientId .
      "&scope=contacts.write%20contacts.readonly%20locations.readonly" .
      "&version_id=" . $versionId .
      "&state=" . $state;
  }

  public function exchangeCodeForToken($code)
  {
    $tokenData = $this->ghl->oauth->getAccessToken([
      'code' => $code,
      'client_id' => Config::get('GHL_CLIENT_ID'),
      'client_secret' => Config::get('GHL_CLIENT_SECRET'),
      'grant_type' => 'authorization_code'
    ]);

    $this->storage->save($tokenData->toArray());
    return $tokenData;
  }
}
