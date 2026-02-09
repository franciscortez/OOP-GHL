<?php

/**
 * OAuth Callback Handler
 * Receives the authorization code from GoHighLevel and exchanges it for tokens
 */

// Load application dependencies
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/TokenStorage.php';

// Initialize token storage and authentication service
$storage = new TokenStorage();
$auth = new AuthService($storage);

/**
 * Render a response page with success or error message
 * @param bool $success Whether the operation was successful
 * @param string $message Message to display to the user
 * @param bool $showHomeLink Whether to show the "Go to Home" button
 */
function renderResponse($success, $message, $showHomeLink = true)
{ ?>
   <!DOCTYPE html>
   <html lang="en" class="dark">

   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>OAuth Callback</title>
      <script src="https://cdn.tailwindcss.com"></script>
      <script>
         tailwind.config = {
            darkMode: 'class',
            theme: {
               extend: {
                  colors: {
                     dark: {
                        bg: '#000000',
                        surface: '#111111',
                        border: '#222222',
                        text: '#ffffff',
                        muted: '#888888'
                     }
                  }
               }
            }
         }
      </script>
   </head>

   <body class="bg-black text-white min-h-screen flex items-center justify-center p-4">
      <div class="max-w-md w-full">
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <!-- Header -->
            <div
               class="<?= $success ? 'bg-gradient-to-r from-green-900 to-zinc-900' : 'bg-gradient-to-r from-red-900 to-zinc-900' ?> px-6 py-4 border-b border-zinc-800">
               <div class="flex items-center space-x-3">
                  <?php if ($success): ?>
                     <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                     </svg>
                     <h1 class="text-xl font-bold text-white">Success!</h1>
                  <?php else: ?>
                     <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                     </svg>
                     <h1 class="text-xl font-bold text-white">Error</h1>
                  <?php endif; ?>
               </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6 space-y-6">
               <div
                  class="<?= $success ? 'bg-green-950 border-green-800' : 'bg-red-950 border-red-800' ?> border rounded-lg p-4">
                  <p class="text-white text-sm leading-relaxed"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
               </div>

               <?php if ($showHomeLink): ?>
                  <a href="/"
                     class="block w-full bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200 text-center">
                     Go to Home
                  </a>
               <?php endif; ?>
            </div>
         </div>

         <!-- Footer -->
         <div class="text-center mt-6 text-zinc-500 text-sm">
            <p>OAuth Authentication</p>
         </div>
      </div>
   </body>

   </html>
   <?php
   // End response rendering and exit
   exit;
}

// Handle OAuth callback - check if authorization code is present
if (isset($_GET['code'])) {

   // Validate CSRF token for security
   if (!isset($_GET['state']) || !Csrf::validate($_GET['state'])) {
      http_response_code(403);
      renderResponse(false, 'Invalid CSRF token. Authentication failed for security reasons.');
   }

   // Exchange authorization code for access token
   try {
      $tokenData = $auth->exchangeCodeForToken($_GET['code']);
      renderResponse(true, 'Connected to GoHighLevel successfully! You can now access your contacts and other resources.');
   } catch (Exception $e) {
      http_response_code(500);
      renderResponse(false, 'Error: ' . $e->getMessage());
   }
}

// No authorization code provided - display error
http_response_code(400);
renderResponse(false, 'No authorization code provided. Please try authenticating again.');
