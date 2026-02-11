<?php

/**
 * Create Contact Form
 * Allows users to create new contacts in GoHighLevel
 */

// Load required classes
require_once __DIR__ . '/../../api/ContactApi.php';
require_once __DIR__ . '/../../core/ViewHelper.php';
require_once __DIR__ . '/../../core/Redirect.php';

// Initialize ContactApi
$api = new ContactApi();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Validate CSRF token
   if (!$api->validateCsrf($_POST['csrf_token'] ?? '')) {
      Redirect::withError($_SERVER['PHP_SELF'], 'Invalid CSRF token. Please try again.');
   }

   // Process the operation
   $result = $api->createContact($_POST);

   // Handle the result
   if ($result['success']) {
      Redirect::withSuccess('/views/contacts/', $result['message']);
   } else {
      Redirect::withError($_SERVER['PHP_SELF'], $result['message']);
   }
}

// Get session messages
$messages = ViewHelper::getSessionMessages();
$successMessage = $messages['success'];
$errorMessage = $messages['error'];

// Set page variables
$title = 'Create Contact - GoHighLevel';
$backUrl = '/views/contacts/';
$backText = 'Back to Contacts';
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
   <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
         <?php include __DIR__ . '/../layouts/navigation.php'; ?>

         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl p-6">
            <div class="flex items-center space-x-3">
               <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
               </svg>
               <h1 class="text-3xl font-bold text-white">Create New Contact</h1>
            </div>
         </div>
      </div>

      <?php include __DIR__ . '/../layouts/alerts.php'; ?>

      <!-- Form -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
         <form method="POST" action="" class="p-6 space-y-6">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $api->getCsrfToken() ?>">

            <!-- First Name & Last Name -->
            <div class="flex flex-col sm:flex-row gap-4">
               <div class="flex-1">
                  <label for="firstName" class="block text-sm font-medium text-zinc-300 mb-2">
                     First Name <span class="text-red-400">*</span>
                  </label>
                  <input type="text" id="firstName" name="firstName" required
                     value="<?= ViewHelper::getFieldValue('firstName') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="Juan">
               </div>
               <div class="flex-1">
                  <label for="lastName" class="block text-sm font-medium text-zinc-300 mb-2">
                     Last Name <span class="text-red-400">*</span>
                  </label>
                  <input type="text" id="lastName" name="lastName" required
                     value="<?= ViewHelper::getFieldValue('lastName') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="Dela Cruz">
               </div>
            </div>

            <!-- Email & Phone -->
            <div class="flex flex-col sm:flex-row gap-4">
               <div class="flex-1">
                  <label for="email" class="block text-sm font-medium text-zinc-300 mb-2">
                     Email <span class="text-red-400">*</span>
                  </label>
                  <input type="email" id="email" name="email" required value="<?= ViewHelper::getFieldValue('email') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="juan.delacruz@example.com">
               </div>
               <div class="flex-1">
                  <label for="phone" class="block text-sm font-medium text-zinc-300 mb-2">
                     Phone
                  </label>
                  <input type="tel" id="phone" name="phone" value="<?= ViewHelper::getFieldValue('phone') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="+63 XXX XXX XXXX">
               </div>
            </div>

            <!-- Gender & Date of Birth -->
            <div class="flex flex-col sm:flex-row gap-4">
               <div class="flex-1">
                  <label for="gender" class="block text-sm font-medium text-zinc-300 mb-2">
                     Gender
                  </label>
                  <select id="gender" name="gender"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
                     <option value="">Select Gender</option>
                     <option value="male" <?= ViewHelper::getSelected('gender', 'male') ?>>Male</option>
                     <option value="female" <?= ViewHelper::getSelected('gender', 'female') ?>>Female</option>
                     <option value="other" <?= ViewHelper::getSelected('gender', 'other') ?>>Other</option>
                  </select>
               </div>
               <div class="flex-1">
                  <label for="dateOfBirth" class="block text-sm font-medium text-zinc-300 mb-2">
                     Date of Birth
                  </label>
                  <input type="date" id="dateOfBirth" name="dateOfBirth"
                     value="<?= ViewHelper::getFieldValue('dateOfBirth') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
               </div>
            </div>

            <!-- Company Name -->
            <div>
               <label for="companyName" class="block text-sm font-medium text-zinc-300 mb-2">
                  Company Name
               </label>
               <input type="text" id="companyName" name="companyName"
                  value="<?= ViewHelper::getFieldValue('companyName') ?>"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                  placeholder="Company Name">
            </div>

            <!-- Address -->
            <div>
               <label for="address1" class="block text-sm font-medium text-zinc-300 mb-2">
                  Address
               </label>
               <input type="text" id="address1" name="address1" value="<?= ViewHelper::getFieldValue('address1') ?>"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                  placeholder="123 Main St">
            </div>

            <!-- City, State, Postal Code Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
               <div>
                  <label for="city" class="block text-sm font-medium text-zinc-300 mb-2">
                     City
                  </label>
                  <input type="text" id="city" name="city" value="<?= ViewHelper::getFieldValue('city') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="City">
               </div>
               <div>
                  <label for="state" class="block text-sm font-medium text-zinc-300 mb-2">
                     State
                  </label>
                  <input type="text" id="state" name="state" value="<?= ViewHelper::getFieldValue('state') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="State">
               </div>
               <div>
                  <label for="postalCode" class="block text-sm font-medium text-zinc-300 mb-2">
                     Postal Code
                  </label>
                  <input type="text" id="postalCode" name="postalCode"
                     value="<?= ViewHelper::getFieldValue('postalCode') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="12345">
               </div>
            </div>

            <!-- Country & Website -->
            <div class="flex flex-col sm:flex-row gap-4">
               <div class="flex-1">
                  <label for="country" class="block text-sm font-medium text-zinc-300 mb-2">
                     Country
                  </label>
                  <input type="text" id="country" name="country" value="<?= ViewHelper::getFieldValue('country') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="PH">
               </div>
               <div class="flex-1">
                  <label for="website" class="block text-sm font-medium text-zinc-300 mb-2">
                     Website
                  </label>
                  <input type="url" id="website" name="website" value="<?= ViewHelper::getFieldValue('website') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="https://example.com">
               </div>
            </div>

            <!-- Timezone & Source -->
            <div class="flex flex-col sm:flex-row gap-4">
               <div class="flex-1">
                  <label for="timezone" class="block text-sm font-medium text-zinc-300 mb-2">
                     Timezone
                  </label>
                  <input type="text" id="timezone" name="timezone" value="<?= ViewHelper::getFieldValue('timezone') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="America/New_York">
               </div>
               <div class="flex-1">
                  <label for="source" class="block text-sm font-medium text-zinc-300 mb-2">
                     Source
                  </label>
                  <input type="text" id="source" name="source" value="<?= ViewHelper::getFieldValue('source') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="public api">
               </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-zinc-800">
               <a href="/views/contacts/"
                  class="px-6 py-3 bg-zinc-800 text-white rounded-lg font-semibold hover:bg-zinc-700 transition-colors duration-200">
                  Cancel
               </a>
               <button type="submit"
                  class="px-6 py-3 bg-white text-black rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200 flex items-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  <span>Create Contact</span>
               </button>
            </div>
         </form>
      </div>

      <?php include __DIR__ . '/../layouts/footer.php'; ?>
   </div>
</div>