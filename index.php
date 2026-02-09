<?php

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/TokenStorage.php';

$storage = new TokenStorage();
$auth = new AuthService($storage);

// Check if already authenticated
if ($storage->hasToken()) {
   $token = $storage->get();
   echo "<h1>🔐 Already Authenticated</h1>";
   echo "<p>Location ID: " . ($token['locationId'] ?? 'N/A') . "</p>";
   echo "<p>User ID: " . ($token['userId'] ?? 'N/A') . "</p>";
   echo "<p><a href='?logout=1'>Logout</a></p>";
   exit;
}

// Handle logout
if (isset($_GET['logout'])) {
   if (file_exists(__DIR__ . '/storage/tokens.json')) {
      unlink(__DIR__ . '/storage/tokens.json');
   }
   header("Location: /");
   exit;
}

// Start OAuth
$authUrl = $auth->getAuthUrl();
header("Location: $authUrl");
exit;
