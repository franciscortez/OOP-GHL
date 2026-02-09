<?php

/**
 * View Contact Details
 * Displays complete information for a single contact
 */

// Load application dependencies
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controller/ContactController.php';

// Get contact ID from query parameter
$contactId = $_GET['id'] ?? null;

if (!$contactId) {
   header('Location: /views/contacts/');
   exit;
}

// Fetch contact data
try {
   $controller = new ContactController();
   $response = $controller->get($contactId);
   $contact = $response['contact'] ?? null;

   if (!$contact) {
      $_SESSION['error'] = 'Contact not found.';
      header('Location: /views/contacts/');
      exit;
   }
} catch (Exception $e) {
   $_SESSION['error'] = 'Failed to load contact: ' . $e->getMessage();
   header('Location: /views/contacts/');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Contact -
      <?= htmlspecialchars($contact['firstName'] ?? '' . ' ' . $contact['lastName'] ?? '', ENT_QUOTES, 'UTF-8') ?>
   </title>
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

<body class="bg-black text-white min-h-screen">
   <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
         <!-- Header -->
         <div class="mb-8">
            <a href="/views/contacts/"
               class="inline-flex items-center text-zinc-400 hover:text-white transition-colors duration-200 mb-4">
               <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M10 19l-7-7m0 0l7-7m-7 7h18" />
               </svg>
               Back to Contacts
            </a>

            <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl p-6">
               <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                     </svg>
                     <h1 class="text-3xl font-bold text-white">
                        <?= htmlspecialchars(($contact['firstName'] ?? '') . ' ' . ($contact['lastName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                     </h1>
                  </div>
                  <a href="edit-contact.php?id=<?= urlencode($contactId) ?>"
                     class="inline-flex items-center px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors duration-200">
                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                     </svg>
                     Edit Contact
                  </a>
               </div>
            </div>
         </div>

         <!-- Contact Information -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <!-- Basic Information -->
            <div class="border-b border-zinc-800 px-6 py-4 bg-zinc-800/50">
               <h2 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Basic Information
               </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">First Name</label>
                  <p class="text-white text-lg">
                     <?= htmlspecialchars($contact['firstName'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
               </div>
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Last Name</label>
                  <p class="text-white text-lg">
                     <?= htmlspecialchars($contact['lastName'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
               </div>
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
                  <p class="text-white text-lg">
                     <?php if (!empty($contact['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>"
                           class="text-blue-400 hover:text-blue-300">
                           <?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                     <?php else: ?>
                        N/A
                     <?php endif; ?>
                  </p>
               </div>
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Phone</label>
                  <p class="text-white text-lg">
                     <?php if (!empty($contact['phone'])): ?>
                        <a href="tel:<?= htmlspecialchars($contact['phone'], ENT_QUOTES, 'UTF-8') ?>"
                           class="text-blue-400 hover:text-blue-300">
                           <?= htmlspecialchars($contact['phone'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                     <?php else: ?>
                        N/A
                     <?php endif; ?>
                  </p>
               </div>
               <?php if (!empty($contact['gender'])): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Gender</label>
                     <p class="text-white text-lg"><?= htmlspecialchars($contact['gender'], ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
               <?php endif; ?>
               <?php if (!empty($contact['dateOfBirth'])): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Date of Birth</label>
                     <p class="text-white text-lg"><?= htmlspecialchars($contact['dateOfBirth'], ENT_QUOTES, 'UTF-8') ?>
                     </p>
                  </div>
               <?php endif; ?>
            </div>

            <!-- Company Information -->
            <?php if (!empty($contact['companyName']) || !empty($contact['website'])): ?>
               <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/50">
                  <h2 class="text-lg font-semibold text-white flex items-center">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                     </svg>
                     Company Information
                  </h2>
               </div>
               <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <?php if (!empty($contact['companyName'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Company Name</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['companyName'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['website'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Website</label>
                        <p class="text-white text-lg">
                           <a href="<?= htmlspecialchars($contact['website'], ENT_QUOTES, 'UTF-8') ?>" target="_blank"
                              rel="noopener noreferrer" class="text-blue-400 hover:text-blue-300">
                              <?= htmlspecialchars($contact['website'], ENT_QUOTES, 'UTF-8') ?>
                           </a>
                        </p>
                     </div>
                  <?php endif; ?>
               </div>
            <?php endif; ?>

            <!-- Address Information -->
            <?php if (!empty($contact['address1']) || !empty($contact['city']) || !empty($contact['state']) || !empty($contact['postalCode']) || !empty($contact['country'])): ?>
               <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/50">
                  <h2 class="text-lg font-semibold text-white flex items-center">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                     </svg>
                     Address Information
                  </h2>
               </div>
               <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <?php if (!empty($contact['address1'])): ?>
                     <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Address</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['address1'], ENT_QUOTES, 'UTF-8') ?></p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['city'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">City</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['city'], ENT_QUOTES, 'UTF-8') ?></p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['state'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">State</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['state'], ENT_QUOTES, 'UTF-8') ?></p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['postalCode'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Postal Code</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['postalCode'], ENT_QUOTES, 'UTF-8') ?></p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['country'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Country</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['country'], ENT_QUOTES, 'UTF-8') ?></p>
                     </div>
                  <?php endif; ?>
               </div>
            <?php endif; ?>

            <!-- Additional Information -->
            <?php if (!empty($contact['timezone']) || !empty($contact['source'])): ?>
               <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/50">
                  <h2 class="text-lg font-semibold text-white flex items-center">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                     </svg>
                     Additional Information
                  </h2>
               </div>
               <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <?php if (!empty($contact['timezone'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Timezone</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['timezone'], ENT_QUOTES, 'UTF-8') ?></p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['source'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Source</label>
                        <p class="text-white text-lg"><?= htmlspecialchars($contact['source'], ENT_QUOTES, 'UTF-8') ?></p>
                     </div>
                  <?php endif; ?>
               </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php if (!empty($contact['tags']) && is_array($contact['tags'])): ?>
               <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/50">
                  <h2 class="text-lg font-semibold text-white flex items-center">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                     </svg>
                     Tags
                  </h2>
               </div>
               <div class="p-6">
                  <div class="flex flex-wrap gap-2">
                     <?php foreach ($contact['tags'] as $tag): ?>
                        <span class="px-3 py-1 bg-zinc-800 text-zinc-300 text-sm rounded-full border border-zinc-700">
                           <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                     <?php endforeach; ?>
                  </div>
               </div>
            <?php endif; ?>

            <!-- Metadata -->
            <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/30">
               <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <?php if (!empty($contact['dateAdded'])): ?>
                     <div class="text-zinc-400">
                        <span class="font-medium">Created:</span>
                        <?= htmlspecialchars(date('F j, Y g:i A', strtotime($contact['dateAdded'])), ENT_QUOTES, 'UTF-8') ?>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['dateUpdated'])): ?>
                     <div class="text-zinc-400">
                        <span class="font-medium">Last Updated:</span>
                        <?= htmlspecialchars(date('F j, Y g:i A', strtotime($contact['dateUpdated'])), ENT_QUOTES, 'UTF-8') ?>
                     </div>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>

</html>