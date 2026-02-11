<?php

/**
 * View Opportunity Details
 * Displays complete information for a single opportunity
 */

// Load required classes
require_once __DIR__ . '/../../api/OpportunityApi.php';
require_once __DIR__ . '/../../api/ContactApi.php';
require_once __DIR__ . '/../../core/Redirect.php';

// Initialize APIs
$api = new OpportunityApi();
$contactApi = new ContactApi();

// Get opportunity ID from query parameter
$opportunityId = $_GET['id'] ?? null;

if (!$opportunityId) {
   Redirect::to('/views/opportunities/');
}

// Fetch opportunity data
try {
   $opportunity = $api->getOpportunity($opportunityId);
   if (!$opportunity) {
      Redirect::withError('/views/opportunities/', 'Opportunity not found.');
   }
} catch (Exception $e) {
   Redirect::withError('/views/opportunities/', 'Failed to load opportunity: ' . $e->getMessage());
}

// Prepare opportunity data for display
$opportunityData = $opportunity ?? [];

// Fetch pipelines to get pipeline and stage names
$pipelineName = null;
$stageName = null;
try {
   $pipelines = $api->getPipelines();
   foreach ($pipelines as $pipeline) {
      if (($pipeline->id ?? '') === ($opportunity['pipelineId'] ?? '')) {
         $pipelineName = $pipeline->name ?? null;
         if (!empty($opportunity['pipelineStageId'])) {
            foreach (($pipeline->stages ?? []) as $stage) {
               if (($stage['id'] ?? '') === $opportunity['pipelineStageId']) {
                  $stageName = $stage['name'] ?? null;
                  break;
               }
            }
         }
         break;
      }
   }
} catch (Exception $e) {
   // Continue without pipeline data
}

// Fetch user name for owner
$ownerName = null;
if (!empty($opportunity['assignedTo'])) {
   try {
      $users = $api->getUsers();
      foreach ($users as $user) {
         if (($user->id ?? '') === $opportunity['assignedTo']) {
            $ownerName = trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? ''));
            $ownerName = $ownerName ?: ($user->name ?? null);
            break;
         }
      }
   } catch (Exception $e) {
      // Continue without user data
   }
}

// Fetch contact name
$contactName = null;
if (!empty($opportunity['contactId'])) {
   try {
      $contact = $contactApi->getContact($opportunity['contactId']);
      if ($contact) {
         $contactName = trim(($contact['firstName'] ?? '') . ' ' . ($contact['lastName'] ?? ''));
         $contactName = $contactName ?: ($contact['email'] ?? 'Unknown');
      }
   } catch (Exception $e) {
      // Continue without contact data
   }
}

// Set page variables
$title = 'View Opportunity - ' . htmlspecialchars($opportunityData['name'] ?? 'Opportunity', ENT_QUOTES, 'UTF-8');
$backUrl = '/views/opportunities/';
$backText = 'Back to Opportunities';
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
   <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
         <?php include __DIR__ . '/../layouts/navigation.php'; ?>

         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl p-6">
            <div class="flex items-center justify-between">
               <div class="flex items-center space-x-3">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <h1 class="text-3xl font-bold text-white">
                     <?= htmlspecialchars($opportunity['name'] ?? 'Opportunity', ENT_QUOTES, 'UTF-8') ?>
                  </h1>
               </div>
               <a href="edit-opportunity.php?id=<?= urlencode($opportunityId) ?>"
                  class="inline-flex items-center px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors duration-200">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Edit Opportunity
               </a>
            </div>
         </div>
      </div>

      <!-- Opportunity Information -->
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
               <label class="block text-sm font-medium text-zinc-400 mb-1">Opportunity Name</label>
               <p class="text-white text-lg">
                  <?= htmlspecialchars($opportunity['name'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
               </p>
            </div>
            <div>
               <label class="block text-sm font-medium text-zinc-400 mb-1">Status</label>
               <?php
               $status = strtolower($opportunity['status'] ?? 'open');
               $statusColors = [
                  'open' => 'bg-blue-900 text-blue-200 border-blue-700',
                  'won' => 'bg-green-900 text-green-200 border-green-700',
                  'lost' => 'bg-red-900 text-red-200 border-red-700',
                  'abandoned' => 'bg-gray-900 text-gray-200 border-gray-700'
               ];
               $statusClass = $statusColors[$status] ?? $statusColors['open'];
               ?>
               <span
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border <?= $statusClass ?>">
                  <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
               </span>
            </div>
            <div>
               <label class="block text-sm font-medium text-zinc-400 mb-1">Monetary Value</label>
               <p class="text-white text-lg">
                  <?php if (isset($opportunity['monetaryValue']) && $opportunity['monetaryValue'] > 0): ?>
                     <span class="font-semibold text-green-400">
                        $<?= number_format($opportunity['monetaryValue'], 2) ?>
                     </span>
                  <?php else: ?>
                     <span class="text-zinc-600">$0.00</span>
                  <?php endif; ?>
               </p>
            </div>
         </div>

         <!-- Contact Information -->
         <?php if (!empty($opportunity['contactId']) || !empty($opportunity['contact'])): ?>
            <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/50">
               <h2 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Contact Information
               </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Name</label>
                  <p class="text-white text-lg">
                     <?php
                     if ($contactName) {
                        echo htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8');
                     } elseif (!empty($opportunity['contact'])) {
                        $displayName = trim(($opportunity['contact']['firstName'] ?? '') . ' ' . ($opportunity['contact']['lastName'] ?? ''));
                        echo htmlspecialchars($displayName ?: 'N/A', ENT_QUOTES, 'UTF-8');
                     } else {
                        echo 'N/A';
                     }
                     ?>
                  </p>
               </div>
               <?php if (!empty($opportunity['contact']['email'])): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Email</label>
                     <p class="text-white text-lg">
                        <a href="mailto:<?= htmlspecialchars($opportunity['contact']['email'], ENT_QUOTES, 'UTF-8') ?>"
                           class="text-blue-400 hover:text-blue-300">
                           <?= htmlspecialchars($opportunity['contact']['email'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                     </p>
                  </div>
               <?php endif; ?>
               <?php if (!empty($opportunity['contact']['phone'])): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Phone</label>
                     <p class="text-white text-lg">
                        <a href="tel:<?= htmlspecialchars($opportunity['contact']['phone'], ENT_QUOTES, 'UTF-8') ?>"
                           class="text-blue-400 hover:text-blue-300">
                           <?= htmlspecialchars($opportunity['contact']['phone'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                     </p>
                  </div>
               <?php endif; ?>
            </div>
         <?php endif; ?>

         <!-- Pipeline Information -->
         <?php if ($pipelineName || $stageName): ?>
            <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/50">
               <h2 class="text-lg font-semibold text-white flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  Pipeline Information
               </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
               <?php if ($pipelineName): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Pipeline</label>
                     <p class="text-white text-lg">
                        <?= htmlspecialchars($pipelineName, ENT_QUOTES, 'UTF-8') ?>
                     </p>
                  </div>
               <?php endif; ?>
               <?php if ($stageName): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Stage</label>
                     <p class="text-white text-lg">
                        <?= htmlspecialchars($stageName, ENT_QUOTES, 'UTF-8') ?>
                     </p>
                  </div>
               <?php endif; ?>
            </div>
         <?php endif; ?>

         <!-- Additional Information -->
         <div class="border-t border-zinc-800 px-6 py-4 bg-zinc-800/50">
            <h2 class="text-lg font-semibold text-white flex items-center">
               <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
               </svg>
               Timeline
            </h2>
         </div>
         <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if (!empty($opportunity['createdAt'])): ?>
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Created At</label>
                  <p class="text-white text-lg">
                     <?= htmlspecialchars(date('F j, Y g:i A', strtotime($opportunity['createdAt'])), ENT_QUOTES, 'UTF-8') ?>
                  </p>
               </div>
            <?php endif; ?>
            <?php if (!empty($opportunity['updatedAt'])): ?>
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Last Updated</label>
                  <p class="text-white text-lg">
                     <?= htmlspecialchars(date('F j, Y g:i A', strtotime($opportunity['updatedAt'])), ENT_QUOTES, 'UTF-8') ?>
                  </p>
               </div>
            <?php endif; ?>
            <?php if ($ownerName): ?>
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Owner</label>
                  <p class="text-white text-lg">
                     <?= htmlspecialchars($ownerName, ENT_QUOTES, 'UTF-8') ?>
                  </p>
               </div>
            <?php endif; ?>
         </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-6 flex items-center justify-end space-x-4">
         <a href="/views/opportunities/"
            class="px-6 py-3 bg-zinc-800 text-white rounded-lg font-semibold hover:bg-zinc-700 transition-colors duration-200">
            Back to List
         </a>
         <a href="delete-opportunity.php?id=<?= urlencode($opportunityId) ?>"
            class="px-6 py-3 bg-red-900 text-white rounded-lg font-semibold hover:bg-red-800 transition-colors duration-200 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>Delete Opportunity</span>
         </a>
      </div>

      <?php include __DIR__ . '/../layouts/footer.php'; ?>
   </div>
</div>