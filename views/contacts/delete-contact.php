<?php

/**
 * Delete Contact
 * Confirms and deletes a contact from GoHighLevel
 */

// Load application dependencies
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/Csrf.php';
require_once __DIR__ . '/../../controller/ContactController.php';

// Get contact ID from query parameter
$contactId = $_GET['id'] ?? null;

if (!$contactId) {
   header('Location: /views/contacts/');
   exit;
}

// Initialize variables
$csrfToken = Csrf::generate();
$contact = null;

// Handle POST request (actual deletion)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Validate CSRF token
   if (!isset($_POST['csrf_token']) || !Csrf::validate($_POST['csrf_token'])) {
      $_SESSION['error'] = 'Invalid CSRF token. Please try again.';
      header('Location: /views/contacts/');
      exit;
   }

   // Confirm contact ID matches
   if (($_POST['contact_id'] ?? '') !== $contactId) {
      $_SESSION['error'] = 'Invalid contact ID.';
      header('Location: /views/contacts/');
      exit;
   }

   // Delete the contact
   try {
      $controller = new ContactController();
      $controller->delete($contactId);
      $_SESSION['success'] = 'Contact deleted successfully!';
      header('Location: /views/contacts/');
      exit;
   } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      header('Location: /views/contacts/');
      exit;
   }
}

// Fetch contact data for display
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
   <title>Delete Contact -
      <?= htmlspecialchars(($contact['firstName'] ?? '') . ' ' . ($contact['lastName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
   </title>
   <script src="https://cdn.tailwindcss.com"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      <div class="max-w-3xl mx-auto">
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

            <div class="bg-zinc-900 border border-red-900/50 rounded-lg shadow-2xl p-6">
               <div class="flex items-center space-x-3">
                  <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <h1 class="text-3xl font-bold text-white">Delete Contact</h1>
               </div>
            </div>
         </div>

         <!-- Warning Message -->
         <div class="bg-zinc-900 border border-yellow-900 rounded-lg shadow-2xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-yellow-900 to-zinc-900 px-6 py-4 border-b border-zinc-800">
               <div class="flex items-center space-x-3">
                  <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <h2 class="text-xl font-semibold text-white">Warning</h2>
               </div>
            </div>
            <div class="px-6 py-4">
               <p class="text-white text-sm">
                  You are about to permanently delete this contact. This action cannot be undone.
               </p>
            </div>
         </div>

         <!-- Contact Information -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden mb-6">
            <div class="border-b border-zinc-800 px-6 py-4 bg-zinc-800/50">
               <h2 class="text-lg font-semibold text-white">Contact Information</h2>
            </div>
            <div class="p-6 space-y-4">
               <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Name</label>
                     <p class="text-white text-lg">
                        <?= htmlspecialchars(($contact['firstName'] ?? '') . ' ' . ($contact['lastName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                     </p>
                  </div>
                  <?php if (!empty($contact['email'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
                        <p class="text-white text-lg">
                           <?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['phone'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Phone</label>
                        <p class="text-white text-lg">
                           <?= htmlspecialchars($contact['phone'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($contact['companyName'])): ?>
                     <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Company</label>
                        <p class="text-white text-lg">
                           <?= htmlspecialchars($contact['companyName'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                     </div>
                  <?php endif; ?>
               </div>

               <?php if (!empty($contact['tags']) && is_array($contact['tags'])): ?>
                  <div class="pt-4 border-t border-zinc-800">
                     <label class="block text-sm font-medium text-zinc-400 mb-2">Tags</label>
                     <div class="flex flex-wrap gap-2">
                        <?php foreach ($contact['tags'] as $tag): ?>
                           <span class="px-3 py-1 bg-zinc-800 text-zinc-300 text-sm rounded-full border border-zinc-700">
                              <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
                           </span>
                        <?php endforeach; ?>
                     </div>
                  </div>
               <?php endif; ?>
            </div>
         </div>

         <!-- Delete Form (Hidden, triggered by SweetAlert2) -->
         <form id="deleteForm" method="POST" action="" style="display: none;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="contact_id" value="<?= htmlspecialchars($contactId, ENT_QUOTES, 'UTF-8') ?>">
         </form>

         <!-- Action Buttons -->
         <div class="flex items-center justify-end space-x-4">
            <a href="/views/contacts/"
               class="px-6 py-3 bg-zinc-800 text-white rounded-lg font-semibold hover:bg-zinc-700 transition-colors duration-200">
               Cancel
            </a>
            <button type="button" id="deleteButton"
               class="px-6 py-3 bg-red-900 text-white rounded-lg font-semibold hover:bg-red-800 transition-colors duration-200 flex items-center space-x-2">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
               </svg>
               <span>Delete Contact</span>
            </button>
         </div>

         <!-- Footer -->
         <div class="mt-8 text-center text-zinc-500 text-sm">
            <p>GoHighLevel Contacts Management</p>
         </div>
      </div>
   </div>

   <script>
      document.getElementById('deleteButton').addEventListener('click', function () {
         Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7f1d1d',
            cancelButtonColor: '#3f3f46',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: '#18181b',
            color: '#ffffff',
            customClass: {
               popup: 'border border-zinc-800',
               confirmButton: 'font-semibold',
               cancelButton: 'font-semibold'
            }
         }).then((result) => {
            if (result.isConfirmed) {
               // Show loading state
               Swal.fire({
                  title: 'Deleting...',
                  text: 'Please wait',
                  icon: 'info',
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  showConfirmButton: false,
                  background: '#18181b',
                  color: '#ffffff',
                  customClass: {
                     popup: 'border border-zinc-800'
                  },
                  didOpen: () => {
                     Swal.showLoading();
                  }
               });

               // Submit the form
               document.getElementById('deleteForm').submit();
            }
         });
      });
   </script>
</body>

</html>