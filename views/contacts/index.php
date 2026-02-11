<?php

/**
 * Contacts List View
 * Displays all contacts from GoHighLevel in a table format
 */

// Load required classes
require_once __DIR__ . '/../../api/ContactApi.php';
require_once __DIR__ . '/../../core/ViewHelper.php';

// Initialize ContactApi
$api = new ContactApi();

// Get session messages
$messages = ViewHelper::getSessionMessages();
$successMessage = $messages['success'];
$errorMessage = $messages['error'];

// Fetch contacts from the API
$contacts = [];
$error = null;
try {
   $contacts = $api->getAllContacts();
} catch (Throwable $e) {
   // Capture any errors during contact retrieval
   $error = $e->getMessage();
}

// Set page variables
$title = 'Contacts List - GoHighLevel';
$backUrl = '/';
$backText = 'Back to Home';
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
   <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
         <div class="flex items-center justify-between mb-4">
            <div>
               <?php include __DIR__ . '/../layouts/navigation.php'; ?>
            </div>
         </div>

         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl p-6">
            <div class="flex items-center justify-between">
               <div class="flex items-center space-x-3">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <h1 class="text-3xl font-bold text-white">Contacts</h1>
               </div>
               <div class="flex items-center space-x-4">
                  <?php if (!$error && !empty($contacts)): ?>
                     <!-- Display total contact count if successfully loaded -->
                     <div class="bg-zinc-800 border border-zinc-700 px-4 py-2 rounded-lg">
                        <span class="text-zinc-400 text-sm">Total:</span>
                        <span class="text-white font-semibold text-lg ml-2"><?= count($contacts) ?></span>
                     </div>
                  <?php endif; ?>
                  <!-- Create Contact Button -->
                  <a href="create-contact.php"
                     class="bg-white text-black px-5 py-2.5 rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200 flex items-center space-x-2">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                     </svg>
                     <span>Create Contact</span>
                  </a>
               </div>
            </div>
         </div>
      </div>

      <?php include __DIR__ . '/../layouts/alerts.php'; ?>

      <?php if ($error): ?>
         <!-- Error State -->
         <div class="bg-zinc-900 border border-red-900 rounded-lg shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-900 to-zinc-900 px-6 py-4 border-b border-zinc-800">
               <div class="flex items-center space-x-3">
                  <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <h2 class="text-xl font-semibold text-white">Error Loading Contacts</h2>
               </div>
            </div>
            <div class="px-6 py-6 space-y-4">
               <div class="bg-red-950 border border-red-800 rounded-lg p-4">
                  <p class="text-white text-sm"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
               </div>
               <a href="/"
                  class="inline-block bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200">
                  Go to Home (Authenticate)
               </a>
            </div>
         </div>

      <?php elseif (empty($contacts)): ?>
         <!-- Empty State -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <div class="px-6 py-12 text-center">
               <svg class="w-20 h-20 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
               </svg>
               <h3 class="text-xl font-semibold text-white mb-2">No Contacts Found</h3>
               <p class="text-zinc-400 mb-6">There are no contacts in this location yet.</p>
               <a href="/"
                  class="inline-block bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200">
                  Back to Home
               </a>
            </div>
         </div>

      <?php else: ?>
         <!-- Contacts Table -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
               <table class="w-full">
                  <thead>
                     <tr class="bg-zinc-800 border-b border-zinc-700">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Email
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Phone
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Tags
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Date Added
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Actions
                        </th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-zinc-800">
                     <?php foreach ($contacts as $contact): ?>
                        <tr class="hover:bg-zinc-800 transition-colors duration-150">
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                 <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                 </div>
                                 <div class="ml-4">
                                    <?php
                                    // Build full name from first and last name
                                    $firstName = $contact->firstName ?? '';
                                    $lastName = $contact->lastName ?? '';
                                    $fullName = trim($firstName . ' ' . $lastName);
                                    ?>
                                    <div class="text-sm font-medium text-white">
                                       <?= htmlspecialchars($fullName ?: '(No name)', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center text-sm">
                                 <?php if (!empty($contact->email)): ?>
                                    <svg class="w-4 h-4 text-zinc-500 mr-2" fill="none" stroke="currentColor"
                                       viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span
                                       class="text-zinc-300"><?= htmlspecialchars($contact->email, ENT_QUOTES, 'UTF-8') ?></span>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic">No email</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center text-sm">
                                 <?php if (!empty($contact->phone)): ?>
                                    <svg class="w-4 h-4 text-zinc-500 mr-2" fill="none" stroke="currentColor"
                                       viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span
                                       class="text-zinc-300"><?= htmlspecialchars($contact->phone, ENT_QUOTES, 'UTF-8') ?></span>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic">No phone</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4">
                              <div class="flex flex-wrap gap-1">
                                 <?php if (!empty($contact->tags)): ?>
                                    <?php foreach ($contact->tags as $tag): ?>
                                       <span
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-700 text-zinc-200 border border-zinc-600">
                                          <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
                                       </span>
                                    <?php endforeach; ?>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic text-sm">No tags</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center text-sm">
                                 <?php if (isset($contact->dateAdded)): ?>
                                    <svg class="w-4 h-4 text-zinc-500 mr-2" fill="none" stroke="currentColor"
                                       viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-zinc-300">
                                       <?= htmlspecialchars(date('Y-m-d H:i', strtotime($contact->dateAdded)), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic">N/A</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap text-right">
                              <div class="flex items-center justify-end space-x-2">
                                 <!-- View Button -->
                                 <a href="view-contact.php?id=<?= htmlspecialchars($contact->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    class="inline-flex items-center p-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors duration-200"
                                    title="View Contact">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                 </a>
                                 <!-- Edit Button -->
                                 <a href="edit-contact.php?id=<?= htmlspecialchars($contact->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    class="inline-flex items-center p-2 bg-zinc-700 text-white rounded-md hover:bg-zinc-600 transition-colors duration-200"
                                    title="Edit Contact">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                 </a>
                                 <!-- Delete Button -->
                                 <a href="delete-contact.php?id=<?= htmlspecialchars($contact->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    class="inline-flex items-center p-2 bg-red-900 text-white rounded-md hover:bg-red-800 transition-colors duration-200"
                                    title="Delete Contact">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                 </a>
                              </div>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>
      <?php endif; ?>

      <?php include __DIR__ . '/../layouts/footer.php'; ?>
   </div>
</div>