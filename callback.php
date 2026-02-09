<?php

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/TokenStorage.php';

$storage = new TokenStorage();
$auth = new AuthService($storage);

// Handle OAuth callback
if (isset($_GET['code'])) {

   // Validate CSRF token
   if (!isset($_GET['state']) || !Csrf::validate($_GET['state'])) {
      http_response_code(403);
      exit('❌ Invalid CSRF token');
   }

   // Exchange code for token
   try {
      $tokenData = $auth->exchangeCodeForToken($_GET['code']);

      echo "✅ Connected to GoHighLevel successfully!<br>";
      echo "<a href='/'>Go to Home</a>";
   } catch (Exception $e) {
      http_response_code(500);
      echo "❌ Error: " . $e->getMessage();
   }
   exit;
}

// No code provided
http_response_code(400);
echo "❌ No authorization code provided";
