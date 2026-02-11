<?php

/**
 * Delete Opportunity
 * Confirms and deletes an opportunity from GoHighLevel
 */

// Load required classes
require_once __DIR__ . '/../../api/OpportunityApi.php';
require_once __DIR__ . '/../../core/Redirect.php';

// Initialize OpportunityApi
$api = new OpportunityApi();

// Get opportunity ID from query parameter
$opportunityId = $_GET['id'] ?? null;

if (!$opportunityId) {
   Redirect::to('/views/opportunities/');
}

// Handle POST request (actual deletion)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Validate CSRF token
   if (!$api->validateCsrf($_POST['csrf_token'] ?? '')) {
      Redirect::withError('/views/opportunities/', 'Invalid CSRF token. Please try again.');
   }

   // Confirm opportunity ID matches
   if (($_POST['opportunity_id'] ?? '') !== $opportunityId) {
      Redirect::withError('/views/opportunities/', 'Invalid opportunity ID.');
   }

   // Process the operation
   $result = $api->deleteOpportunity($opportunityId);

   // Handle the result
   if ($result['success']) {
      Redirect::withSuccess('/views/opportunities/', $result['message']);
   } else {
      Redirect::withError('/views/opportunities/', $result['message']);
   }
}

// Fetch opportunity data for display
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

// Fetch pipelines to get pipeline name
$pipelineName = null;
try {
   $pipelines = $api->getPipelines();
   foreach ($pipelines as $pipeline) {
      if (($pipeline->id ?? '') === ($opportunity['pipelineId'] ?? '')) {
         $pipelineName = $pipeline->name ?? null;
         break;
      }
   }
} catch (Exception $e) {
   // Continue without pipeline data
}

// Set page variables
$title = 'Delete Opportunity - ' . htmlspecialchars($opportunityData['name'] ?? 'Opportunity', ENT_QUOTES, 'UTF-8');
$backUrl = '/views/opportunities/';
$backText = 'Back to Opportunities';
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
   <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
         <?php include __DIR__ . '/../layouts/navigation.php'; ?>

         <div class="bg-zinc-900 border border-red-900/50 rounded-lg shadow-2xl p-6">
            <div class="flex items-center space-x-3">
               <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
               </svg>
               <h1 class="text-3xl font-bold text-white">Delete Opportunity</h1>
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
               You are about to permanently delete this opportunity. This action cannot be undone.
            </p>
         </div>
      </div>

      <!-- Opportunity Information -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden mb-6">
         <div class="border-b border-zinc-800 px-6 py-4 bg-zinc-800/50">
            <h2 class="text-lg font-semibold text-white">Opportunity Information</h2>
         </div>
         <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div>
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Name</label>
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
               <?php if (isset($opportunity['monetaryValue']) && $opportunity['monetaryValue'] > 0): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Monetary Value</label>
                     <p class="text-white text-lg font-semibold text-green-400">
                        $<?= number_format($opportunity['monetaryValue'], 2) ?>
                     </p>
                  </div>
               <?php endif; ?>
               <?php if (!empty($opportunity['contact'])): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Contact</label>
                     <p class="text-white text-lg">
                        <?php
                        $contactName = trim(($opportunity['contact']['firstName'] ?? '') . ' ' . ($opportunity['contact']['lastName'] ?? ''));
                        echo htmlspecialchars($contactName ?: 'Unknown', ENT_QUOTES, 'UTF-8');
                        ?>
                     </p>
                  </div>
               <?php endif; ?>
               <?php if ($pipelineName): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Pipeline</label>
                     <p class="text-white text-lg">
                        <?= htmlspecialchars($pipelineName, ENT_QUOTES, 'UTF-8') ?>
                     </p>
                  </div>
               <?php endif; ?>
               <?php if (!empty($opportunity['pipelineStage'])): ?>
                  <div>
                     <label class="block text-sm font-medium text-zinc-400 mb-1">Pipeline Stage</label>
                     <p class="text-white text-lg">
                        <?= htmlspecialchars($opportunity['pipelineStage'], ENT_QUOTES, 'UTF-8') ?>
                     </p>
                  </div>
               <?php endif; ?>
            </div>

            <?php if (!empty($opportunity['createdAt'])): ?>
               <div class="pt-4 border-t border-zinc-800">
                  <label class="block text-sm font-medium text-zinc-400 mb-1">Created Date</label>
                  <p class="text-zinc-300 text-sm">
                     <?= htmlspecialchars(date('F j, Y g:i A', strtotime($opportunity['createdAt'])), ENT_QUOTES, 'UTF-8') ?>
                  </p>
               </div>
            <?php endif; ?>
         </div>
      </div>

      <!-- Delete Form (Hidden, triggered by SweetAlert2) -->
      <form id="deleteForm" method="POST" action="" style="display: none;">
         <input type="hidden" name="csrf_token" value="<?= $api->getCsrfToken() ?>">
         <input type="hidden" name="opportunity_id"
            value="<?= htmlspecialchars($opportunityId, ENT_QUOTES, 'UTF-8') ?>">
      </form>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end space-x-4">
         <a href="/views/opportunities/"
            class="px-6 py-3 bg-zinc-800 text-white rounded-lg font-semibold hover:bg-zinc-700 transition-colors duration-200">
            Cancel
         </a>
         <button type="button" id="deleteButton"
            class="px-6 py-3 bg-red-900 text-white rounded-lg font-semibold hover:bg-red-800 transition-colors duration-200 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>Delete Opportunity</span>
         </button>
      </div>

      <!-- Footer -->
      <div class="mt-8 text-center text-zinc-500 text-sm">
         <p>GoHighLevel Opportunities Management</p>
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
            document.getElementById('deleteForm').submit();
         }
      });
   });
</script>