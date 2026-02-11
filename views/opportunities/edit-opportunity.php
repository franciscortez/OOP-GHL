<?php

/**
 * Edit Opportunity Form
 * Allows users to update existing opportunities in GoHighLevel
 */

// Load required classes
require_once __DIR__ . '/../../api/OpportunityApi.php';
require_once __DIR__ . '/../../core/ViewHelper.php';
require_once __DIR__ . '/../../core/Redirect.php';

// Initialize OpportunityApi
$api = new OpportunityApi();

// Fetch pipelines for dropdown
$pipelines = [];
try {
   $pipelines = $api->getPipelines();
} catch (Exception $e) {
   // Continue even if pipelines fail to load
}

// Fetch users (owners) for dropdown
$users = [];
try {
   $users = $api->getUsers();
} catch (Exception $e) {
   // Continue even if users fail to load
}

// Get opportunity ID from query parameter
$opportunityId = $_GET['id'] ?? null;

if (!$opportunityId) {
   Redirect::to('/views/opportunities/');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Validate CSRF token
   if (!$api->validateCsrf($_POST['csrf_token'] ?? '')) {
      Redirect::withError($_SERVER['PHP_SELF'] . '?id=' . urlencode($opportunityId), 'Invalid CSRF token. Please try again.');
   }

   // Process the operation
   $result = $api->updateOpportunity($opportunityId, $_POST);

   // Handle the result
   if ($result['success']) {
      Redirect::withSuccess($_SERVER['PHP_SELF'] . '?id=' . urlencode($opportunityId), $result['message']);
   } else {
      Redirect::withError($_SERVER['PHP_SELF'] . '?id=' . urlencode($opportunityId), $result['message']);
   }
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

// Get session messages
$messages = ViewHelper::getSessionMessages();
$successMessage = $messages['success'];
$errorMessage = $messages['error'];

// Prepare opportunity data for form fields
$opportunityData = $opportunity ?? [];

// Set page variables
$title = 'Edit Opportunity - ' . htmlspecialchars($opportunityData['name'] ?? 'Opportunity', ENT_QUOTES, 'UTF-8');
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
                     d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
               </svg>
               <h1 class="text-3xl font-bold text-white">Edit Opportunity</h1>
            </div>
         </div>
      </div>

      <?php include __DIR__ . '/../layouts/alerts.php'; ?>

      <!-- Form -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
         <form method="POST" action="" class="p-6 space-y-6">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $api->getCsrfToken() ?>">

            <!-- Opportunity Name -->
            <div>
               <label for="name" class="block text-sm font-medium text-zinc-300 mb-2">
                  Opportunity Name <span class="text-red-400">*</span>
               </label>
               <input type="text" id="name" name="name" required
                  value="<?= ViewHelper::getFieldValue('name', $opportunityData['name'] ?? '') ?>"
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                  placeholder="Enter opportunity name">
            </div>

            <!-- Status -->
            <div>
               <label for="status" class="block text-sm font-medium text-zinc-300 mb-2">
                  Status <span class="text-red-400">*</span>
               </label>
               <select id="status" name="status" required
                  class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
                  <?php $currentStatus = strtolower($opportunityData['status'] ?? 'open'); ?>
                  <option value="open" <?= $currentStatus === 'open' ? 'selected' : '' ?>>Open</option>
                  <option value="won" <?= $currentStatus === 'won' ? 'selected' : '' ?>>Won</option>
                  <option value="lost" <?= $currentStatus === 'lost' ? 'selected' : '' ?>>Lost</option>
                  <option value="abandoned" <?= $currentStatus === 'abandoned' ? 'selected' : '' ?>>Abandoned</option>
               </select>
            </div>

            <!-- Pipeline & Stage -->
            <div class="flex flex-col sm:flex-row gap-4">
               <div class="flex-1">
                  <label for="pipelineId" class="block text-sm font-medium text-zinc-300 mb-2">
                     Pipeline
                  </label>
                  <select id="pipelineId" name="pipelineId"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     onchange="updatePipelineStages()">
                     <option value="">Select Pipeline</option>
                     <?php
                     $currentPipelineId = $opportunityData['pipelineId'] ?? '';
                     foreach ($pipelines as $pipeline):
                        ?>
                        <option value="<?= htmlspecialchars($pipeline->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           data-stages='<?= htmlspecialchars(json_encode($pipeline->stages ?? []), ENT_QUOTES, 'UTF-8') ?>'
                           <?= $currentPipelineId === ($pipeline->id ?? '') ? 'selected' : '' ?>>
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

            <!-- Monetary Value & Assigned To -->
            <div class="flex flex-col sm:flex-row gap-4">
               <div class="flex-1">
                  <label for="monetaryValue" class="block text-sm font-medium text-zinc-300 mb-2">
                     Monetary Value ($)
                  </label>
                  <input type="number" id="monetaryValue" name="monetaryValue" step="0.01" min="0"
                     value="<?= ViewHelper::getFieldValue('monetaryValue', $opportunityData['monetaryValue'] ?? '') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="0.00">
               </div>
               <div class="flex-1">
                  <label for="assignedTo" class="block text-sm font-medium text-zinc-300 mb-2">
                     Owner
                  </label>
                  <select id="assignedTo" name="assignedTo"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
                     <option value="">Unassigned</option>
                     <?php
                     $currentAssignedTo = $opportunityData['assignedTo'] ?? '';
                     foreach ($users as $user):
                        $userName = trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? ''));
                        $userName = $userName ?: ($user->name ?? 'Unknown');
                        ?>
                        <option value="<?= htmlspecialchars($user->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           <?= $currentAssignedTo === ($user->id ?? '') ? 'selected' : '' ?>>
                           <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
                  <?php if (empty($users)): ?>
                     <p class="mt-2 text-sm text-yellow-400">
                        ⚠ No users found. Check your permissions.
                     </p>
                  <?php endif; ?>
               </div>
            </div>

            <!-- Info Display (Read-only fields) -->
            <div class="border-t border-zinc-800 pt-6">
               <h3 class="text-sm font-semibold text-zinc-400 mb-4">Additional Information (Read-only)</h3>
               <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <?php if (!empty($opportunityData['createdAt'])): ?>
                     <div>
                        <label class="block text-xs font-medium text-zinc-500 mb-1">Created At</label>
                        <p class="text-sm text-zinc-400">
                           <?= htmlspecialchars(date('M j, Y g:i A', strtotime($opportunityData['createdAt'])), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                     </div>
                  <?php endif; ?>
                  <?php if (!empty($opportunityData['updatedAt'])): ?>
                     <div>
                        <label class="block text-xs font-medium text-zinc-500 mb-1">Last Updated</label>
                        <p class="text-sm text-zinc-400">
                           <?= htmlspecialchars(date('M j, Y g:i A', strtotime($opportunityData['updatedAt'])), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                     </div>
                  <?php endif; ?>
               </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-zinc-800">
               <a href="view-opportunity.php?id=<?= urlencode($opportunityId) ?>"
                  class="px-6 py-3 bg-zinc-800 text-white rounded-lg font-semibold hover:bg-zinc-700 transition-colors duration-200">
                  Cancel
               </a>
               <button type="submit"
                  class="px-6 py-3 bg-white text-black rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200 flex items-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Update Opportunity</span>
               </button>
            </div>
         </form>
      </div>

      <?php include __DIR__ . '/../layouts/footer.php'; ?>
   </div>
</div>

<script>
   // Update pipeline stages when pipeline is selected
   function updatePipelineStages() {
      const pipelineSelect = document.getElementById('pipelineId');
      const stageSelect = document.getElementById('pipelineStageId');
      const selectedOption = pipelineSelect.options[pipelineSelect.selectedIndex];
      const stagesData = selectedOption.getAttribute('data-stages');
      const currentStageId = '<?= $opportunityData['pipelineStageId'] ?? '' ?>';

      // Clear existing options
      stageSelect.innerHTML = '<option value="">Select Stage</option>';

      if (stagesData) {
         try {
            const stages = JSON.parse(stagesData);
            stages.forEach(stage => {
               const option = document.createElement('option');
               option.value = stage.id;
               option.textContent = stage.name || 'Unnamed Stage';
               if (stage.id === currentStageId) {
                  option.selected = true;
               }
               stageSelect.appendChild(option);
            });
         } catch (e) {
            console.error('Error parsing stages:', e);
         }
      }
   }

   // Initialize on page load to populate stages for the current pipeline
   document.addEventListener('DOMContentLoaded', function () {
      updatePipelineStages();
   });
</script>