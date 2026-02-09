<?php

/**
 * Main Application Entry Point
 * Handles authentication flow and displays the main dashboard
 */

// Load application dependencies and configuration
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/TokenStorage.php';

// Initialize token storage and authentication service
$storage = new TokenStorage();
$auth = new AuthService($storage);

// Handle logout request
if (isset($_GET['logout'])) {
   // Delete the stored tokens
   if (file_exists(__DIR__ . '/storage/tokens.json')) {
      unlink(__DIR__ . '/storage/tokens.json');
   }
   // Redirect to home page
   header("Location: /");
   exit;
}

// Check if user is already authenticated
if ($storage->hasToken()) {
   // Get stored token data

   $token = $storage->get();
   ?>
   <!DOCTYPE html>
   <html lang="en" class="dark">

   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>GoHighLevel - Authenticated</title>
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
      <div class="max-w-2xl w-full">
         <!-- Main Card -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-zinc-800 to-zinc-900 px-8 py-6 border-b border-zinc-800">
               <div class="flex items-center space-x-3">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                  <h1 class="text-2xl font-bold text-white">Authentication Complete</h1>
               </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-6 space-y-6">
               <!-- Success Message -->
               <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-4">
                  <div class="flex items-start space-x-3">
                     <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                     </svg>
                     <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Successfully Connected</h3>
                        <p class="text-zinc-400 text-sm">Your GoHighLevel account is now authenticated and ready to use.
                        </p>
                     </div>
                  </div>
               </div>

               <!-- Navigation -->
               <div class="space-y-3">
                  <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wide">Navigation</h2>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                     <!-- Contacts -->
                     <a href="views/contacts/"
                        class="bg-zinc-800 text-white px-5 py-4 rounded-lg font-semibold hover:bg-zinc-700 border border-zinc-700 transition-colors duration-200 flex items-center space-x-3 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                           viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Contacts</span>
                     </a>

                     <!-- Opportunities -->
                     <a href="views/opportunities/"
                        class="bg-zinc-800 text-white px-5 py-4 rounded-lg font-semibold hover:bg-zinc-700 border border-zinc-700 transition-colors duration-200 flex items-center space-x-3 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                           viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Opportunities</span>
                     </a>

                     <!-- Calendars -->
                     <a href="views/calendars/"
                        class="bg-zinc-800 text-white px-5 py-4 rounded-lg font-semibold hover:bg-zinc-700 border border-zinc-700 transition-colors duration-200 flex items-center space-x-3 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                           viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Calendars</span>
                     </a>

                     <!-- Conversations -->
                     <a href="views/conversations/"
                        class="bg-zinc-800 text-white px-5 py-4 rounded-lg font-semibold hover:bg-zinc-700 border border-zinc-700 transition-colors duration-200 flex items-center space-x-3 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                           viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Conversations</span>
                     </a>
                  </div>
               </div>

               <!-- Logout -->
               <div class="pt-2">
                  <a href="?logout=1"
                     class="w-full bg-zinc-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-zinc-700 border border-zinc-700 transition-colors duration-200 flex items-center justify-center space-x-2 group">
                     <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                     </svg>
                     <span>Logout</span>
                  </a>
               </div>
            </div>
         </div>

         <!-- Footer -->
         <div class="text-center mt-6 text-zinc-500 text-sm">
            <p>GoHighLevel API Integration</p>
         </div>
      </div>
   </body>

   </html>
   <?php
   // End authenticated user display
   exit;
}

// User is not authenticated - redirect to OAuth authorization
$authUrl = $auth->getAuthUrl();
header("Location: $authUrl");
exit;
