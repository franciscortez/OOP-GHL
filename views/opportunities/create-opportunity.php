<?php

/**
 * Create Opportunity Form
 * Allows users to create new opportunities in GoHighLevel
 */

// Load required classes
require_once __DIR__ . '/../../api/OpportunityApi.php';
require_once __DIR__ . '/../../api/ContactApi.php';
require_once __DIR__ . '/../../core/ViewHelper.php';
require_once __DIR__ . '/../../core/Redirect.php';

// Initialize APIs
$api = new OpportunityApi();
$contactApi = new ContactApi();

// Fetch contacts for dropdown
$contacts = [];
try {
   $contacts = $contactApi->getAllContacts();
} catch (Exception $e) {
   // Continue even if contacts fail to load
}

// Fetch pipelines for dropdown
$pipelines = [];
try {
   $pipelines = $api->getPipelines();
} catch (Exception $e) {
   // Continue even if pipelines fail to load
}

// Fetch users (owners) for dropdown
$users = [];
$usersError = null;
try {
   $users = $api->getUsers();
} catch (Exception $e) {
   // Store error message to display to user
   $usersError = $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Validate CSRF token
   if (!$api->validateCsrf($_POST['csrf_token'] ?? '')) {
      Redirect::withError($_SERVER['PHP_SELF'], 'Invalid CSRF token. Please try again.');
   }

   // Process the operation
   $result = $api->createOpportunity($_POST);

   // Handle the result
   if ($result['success']) {
      Redirect::withSuccess('/views/opportunities/', $result['message']);
   } else {
      Redirect::withError($_SERVER['PHP_SELF'], $result['message']);
   }
}

// Get session messages
$messages = ViewHelper::getSessionMessages();
$successMessage = $messages['success'];
$errorMessage = $messages['error'];

// Set page variables
$title = 'Create Opportunity - GoHighLevel';
$backUrl = '/views/opportunities/';
$backText = 'Back to Opportunities';
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
                     d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
               </svg>
               <h1 class="text-3xl font-bold text-white">Create New Opportunity</h1>
            </div>
         </div>
      </div>

      <?php include __DIR__ . '/../layouts/alerts.php'; ?>

      <!-- Form -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
         <form method="POST" action="" class="p-6 space-y-8">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $api->getCsrfToken() ?>">

            <!-- Contact Details Section -->
            <div>
               <h2 class="text-lg font-semibold text-white mb-4 pb-2 border-b border-zinc-700 flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Contact Details
               </h2>
               <div class="space-y-4">
                  <!-- Primary Contact Name -->
                  <div>
                     <label for="contactId" class="block text-sm font-medium text-zinc-300 mb-2">
                        Primary Contact Name <span class="text-red-400">*</span>
                     </label>
                     <select id="contactId" name="contactId" required
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        onchange="updateContactInfo()">
                        <option value="">Select Contact</option>
                        <?php foreach ($contacts as $contact): ?>
                           <?php
                           $contactName = trim(($contact->firstName ?? '') . ' ' . ($contact->lastName ?? ''));
                           $contactEmail = $contact->email ?? '';
                           $contactPhone = $contact->phone ?? '';
                           $displayName = $contactName ?: $contactEmail ?: 'Unknown';
                           ?>
                           <option value="<?= htmlspecialchars($contact->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                              data-email="<?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8') ?>"
                              data-phone="<?= htmlspecialchars($contactPhone, ENT_QUOTES, 'UTF-8') ?>"
                              <?= ViewHelper::getSelected('contactId', $contact->id ?? '') ?>>
                              <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                     <?php if (empty($contacts)): ?>
                        <p class="mt-2 text-sm text-yellow-400">
                           ⚠ No contacts found. <a href="/views/contacts/create-contact.php"
                              class="underline hover:text-yellow-300">Create a contact first</a>
                        </p>
                     <?php endif; ?>
                  </div>

                  <!-- Primary Email & Phone (Read-only, auto-populated) -->
                  <div class="flex flex-col sm:flex-row gap-4">
                     <div class="flex-1">
                        <label for="primaryEmail" class="block text-sm font-medium text-zinc-300 mb-2">
                           Primary Email
                        </label>
                        <input type="email" id="primaryEmail" readonly
                           class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-zinc-400 cursor-not-allowed"
                           placeholder="Select contact to see email">
                     </div>
                     <div class="flex-1">
                        <label for="primaryPhone" class="block text-sm font-medium text-zinc-300 mb-2">
                           Primary Phone
                        </label>
                        <input type="tel" id="primaryPhone" readonly
                           class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-zinc-400 cursor-not-allowed"
                           placeholder="Select contact to see phone">
                     </div>
                  </div>
               </div>
            </div>

            <!-- Opportunity Details Section -->
            <div>
               <h2 class="text-lg font-semibold text-white mb-4 pb-2 border-b border-zinc-700 flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  Opportunity Details
               </h2>
               <div class="space-y-4">
                  <!-- Opportunity Name -->
                  <div>
                     <label for="name" class="block text-sm font-medium text-zinc-300 mb-2">
                        Opportunity Name <span class="text-red-400">*</span>
                     </label>
                     <input type="text" id="name" name="name" required value="<?= ViewHelper::getFieldValue('name') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="Enter opportunity name">
                  </div>

                  <!-- Pipeline & Stage -->
                  <div class="flex flex-col sm:flex-row gap-4">
                     <div class="flex-1">
                        <label for="pipelineId" class="block text-sm font-medium text-zinc-300 mb-2">
                           Pipeline <span class="text-red-400">*</span>
                        </label>
                        <select id="pipelineId" name="pipelineId" required
                           class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                           onchange="updatePipelineStages()">
                           <option value="">Select Pipeline</option>
                           <?php foreach ($pipelines as $pipeline): ?>
                              <option value="<?= htmlspecialchars($pipeline->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                 data-stages='<?= htmlspecialchars(json_encode($pipeline->stages ?? []), ENT_QUOTES, 'UTF-8') ?>'
                                 <?= ViewHelper::getSelected('pipelineId', $pipeline->id ?? '') ?>>
                                 <?= htmlspecialchars($pipeline->name ?? 'Unnamed Pipeline', ENT_QUOTES, 'UTF-8') ?>
                              </option>
                           <?php endforeach; ?>
                        </select>
                        <?php if (empty($pipelines)): ?>
                           <p class="mt-2 text-sm text-yellow-400">
                              ⚠ No pipelines found. Create a pipeline in GoHighLevel first.
                           </p>
                        <?php endif; ?>
                     </div>
                     <div class="flex-1">
                        <label for="pipelineStageId" class="block text-sm font-medium text-zinc-300 mb-2">
                           Stage
                        </label>
                        <select id="pipelineStageId" name="pipelineStageId"
                           class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
                           <option value="">Select pipeline first</option>
                        </select>
                     </div>
                  </div>

                  <!-- Status & Opportunity Value -->
                  <div class="flex flex-col sm:flex-row gap-4">
                     <div class="flex-1">
                        <label for="status" class="block text-sm font-medium text-zinc-300 mb-2">
                           Status <span class="text-red-400">*</span>
                        </label>
                        <select id="status" name="status" required
                           class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
                           <option value="open" <?= ViewHelper::getSelected('status', 'open') ?>>Open</option>
                           <option value="won" <?= ViewHelper::getSelected('status', 'won') ?>>Won</option>
                           <option value="lost" <?= ViewHelper::getSelected('status', 'lost') ?>>Lost</option>
                           <option value="abandoned" <?= ViewHelper::getSelected('status', 'abandoned') ?>>Abandoned
                           </option>
                        </select>
                     </div>
                     <div class="flex-1">
                        <label for="monetaryValue" class="block text-sm font-medium text-zinc-300 mb-2">
                           Opportunity Value (₱)
                        </label>
                        <input type="number" id="monetaryValue" name="monetaryValue" step="0.01" min="0"
                           value="<?= ViewHelper::getFieldValue('monetaryValue') ?>"
                           class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                           placeholder="0">
                     </div>
                  </div>

                  <!-- Owner (Assigned To) -->
                  <div>
                     <label for="assignedTo" class="block text-sm font-medium text-zinc-300 mb-2">
                        Owner
                     </label>
                     <select id="assignedTo" name="assignedTo"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
                        <option value="">Unassigned</option>
                        <?php foreach ($users as $user): ?>
                           <?php
                           $userName = trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? ''));
                           $userName = $userName ?: ($user->name ?? 'Unknown');
                           ?>
                           <option value="<?= htmlspecialchars($user->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                              <?= ViewHelper::getSelected('assignedTo', $user->id ?? '') ?>>
                              <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                     <?php if (empty($users)): ?>
                        <p class="mt-2 text-sm text-yellow-400">
                           ⚠ No users found.
                           <?php if ($usersError): ?>
                              Error:
                              <?= htmlspecialchars($usersError, ENT_QUOTES, 'UTF-8') ?>
                           <?php else: ?>
                              Check your permissions.
                           <?php endif; ?>
                        </p>
                     <?php endif; ?>
                  </div>

                  <!-- Business Name -->
                  <div>
                     <label for="businessName" class="block text-sm font-medium text-zinc-300 mb-2">
                        Business Name
                     </label>
                     <input type="text" id="businessName" name="businessName"
                        value="<?= ViewHelper::getFieldValue('businessName') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="Enter Business Name">
                  </div>

                  <!-- Opportunity Source -->
                  <div>
                     <label for="source" class="block text-sm font-medium text-zinc-300 mb-2">
                        Opportunity Source
                     </label>
                     <input type="text" id="source" name="source" value="<?= ViewHelper::getFieldValue('source') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="Enter Source">
                  </div>

                  <!-- Tags -->
                  <div>
                     <label for="tags" class="block text-sm font-medium text-zinc-300 mb-2">
                        Tags
                     </label>
                     <input type="text" id="tags" name="tags" value="<?= ViewHelper::getFieldValue('tags') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="Add tags (comma-separated)">
                     <p class="mt-1 text-xs text-zinc-500">Separate multiple tags with commas</p>
                  </div>
               </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-zinc-800">
               <a href="/views/opportunities/"
                  class="px-6 py-3 bg-zinc-800 text-white rounded-lg font-semibold hover:bg-zinc-700 transition-colors duration-200">
                  Cancel
               </a>
               <button type="submit"
                  class="px-6 py-3 bg-white text-black rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200 flex items-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Create Opportunity</span>
               </button>
            </div>
         </form>
      </div>

      <?php include __DIR__ . '/../layouts/footer.php'; ?>
   </div>
</div>

<script>
   // Update contact email and phone when contact is selected
   function updateContactInfo() {
      const contactSelect = document.getElementById('contactId');
      const selectedOption = contactSelect.options[contactSelect.selectedIndex];
      const email = selectedOption.getAttribute('data-email') || '';
      const phone = selectedOption.getAttribute('data-phone') || '';

      document.getElementById('primaryEmail').value = email || 'No email provided';
      document.getElementById('primaryPhone').value = phone || 'No phone provided';
   }

   // Update pipeline stages when pipeline is selected
   function updatePipelineStages() {
      const pipelineSelect = document.getElementById('pipelineId');
      const stageSelect = document.getElementById('pipelineStageId');
      const selectedOption = pipelineSelect.options[pipelineSelect.selectedIndex];
      const stagesData = selectedOption.getAttribute('data-stages');

      // Clear existing options
      stageSelect.innerHTML = '<option value="">Select Stage</option>';

      if (stagesData) {
         try {
            const stages = JSON.parse(stagesData);
            stages.forEach(stage => {
               const option = document.createElement('option');
               option.value = stage.id;
               option.textContent = stage.name || 'Unnamed Stage';
               stageSelect.appendChild(option);
            });
         } catch (e) {
            console.error('Error parsing stages:', e);
         }
      }
   }

   // Initialize on page load if values are pre-filled
   document.addEventListener('DOMContentLoaded', function () {
      updateContactInfo();
      updatePipelineStages();
   });
</script>