<?php

/**
 * Opportunities List View
 * Displays all opportunities from GoHighLevel in a table format
 */

// Load required classes
require_once __DIR__ . '/../../api/OpportunityApi.php';
require_once __DIR__ . '/../../core/ViewHelper.php';

// Initialize OpportunityApi
$api = new OpportunityApi();

// Get session messages
$messages = ViewHelper::getSessionMessages();
$successMessage = $messages['success'];
$errorMessage = $messages['error'];

// Fetch opportunities from the API
$opportunities = [];
$error = null;
try {
   $opportunities = $api->searchOpportunities();
} catch (Throwable $e) {
   // Capture any errors during opportunity retrieval
   $error = $e->getMessage();
}

// Fetch pipelines to resolve pipeline and stage names
$pipelines = [];
try {
   $pipelines = $api->getPipelines();
} catch (Throwable $e) {
   // Continue without pipeline data
}

// Create a lookup map for pipelines and stages
$pipelineMap = [];
foreach ($pipelines as $pipeline) {
   $pipelineMap[$pipeline->id ?? ''] = [
      'name' => $pipeline->name ?? 'Unknown Pipeline',
      'stages' => []
   ];
   foreach (($pipeline->stages ?? []) as $stage) {
      $pipelineMap[$pipeline->id ?? '']['stages'][$stage['id'] ?? ''] = $stage['name'] ?? 'Unknown Stage';
   }
}

// Set page variables
$title = 'Opportunities List - GoHighLevel';
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
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                  </svg>
                  <h1 class="text-3xl font-bold text-white">Opportunities</h1>
               </div>
               <div class="flex items-center space-x-4">
                  <?php if (!$error && !empty($opportunities)): ?>
                     <!-- Display total opportunity count if successfully loaded -->
                     <div class="bg-zinc-800 border border-zinc-700 px-4 py-2 rounded-lg">
                        <span class="text-zinc-400 text-sm">Total:</span>
                        <span class="text-white font-semibold text-lg ml-2"><?= count($opportunities) ?></span>
                     </div>
                  <?php endif; ?>
                  <!-- Create Opportunity Button -->
                  <a href="create-opportunity.php"
                     class="bg-white text-black px-5 py-2.5 rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200 flex items-center space-x-2">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                     </svg>
                     <span>Create Opportunity</span>
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
                  <h2 class="text-xl font-semibold text-white">Error Loading Opportunities</h2>
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

      <?php elseif (empty($opportunities)): ?>
         <!-- Empty State -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <div class="px-6 py-12 text-center">
               <svg class="w-20 h-20 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
               </svg>
               <h3 class="text-xl font-semibold text-white mb-2">No Opportunities Found</h3>
               <p class="text-zinc-400 mb-6">There are no opportunities in this location yet.</p>
               <a href="/"
                  class="inline-block bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-zinc-200 transition-colors duration-200">
                  Back to Home
               </a>
            </div>
         </div>

      <?php else: ?>
         <!-- Opportunities Table -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
               <table class="w-full">
                  <thead>
                     <tr class="bg-zinc-800 border-b border-zinc-700">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Contact
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Value
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Pipeline / Stage
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Date Created
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                           Actions
                        </th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-zinc-800">
                     <?php foreach ($opportunities as $opportunity): ?>
                        <tr class="hover:bg-zinc-800 transition-colors duration-150">
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                 <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                 </div>
                                 <div class="ml-4">
                                    <div class="text-sm font-medium text-white">
                                       <?= htmlspecialchars($opportunity->name ?? '(No name)', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-zinc-300">
                                 <?php if (!empty($opportunity->contact)): ?>
                                    <?php
                                    $contactName = trim(($opportunity->contact->firstName ?? '') . ' ' . ($opportunity->contact->lastName ?? ''));
                                    ?>
                                    <?= htmlspecialchars($contactName ?: 'Unknown Contact', ENT_QUOTES, 'UTF-8') ?>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic">No contact</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap">
                              <?php
                              $status = strtolower($opportunity->status ?? 'open');
                              $statusColors = [
                                 'open' => 'bg-blue-900 text-blue-200 border-blue-700',
                                 'won' => 'bg-green-900 text-green-200 border-green-700',
                                 'lost' => 'bg-red-900 text-red-200 border-red-700',
                                 'abandoned' => 'bg-gray-900 text-gray-200 border-gray-700'
                              ];
                              $statusClass = $statusColors[$status] ?? $statusColors['open'];
                              ?>
                              <span
                                 class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border <?= $statusClass ?>">
                                 <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                              </span>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-zinc-300">
                                 <?php if (isset($opportunity->monetaryValue) && $opportunity->monetaryValue > 0): ?>
                                    <span class="font-semibold text-green-400">
                                       $<?= number_format($opportunity->monetaryValue, 2) ?>
                                    </span>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic">$0.00</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4">
                              <div class="text-sm text-zinc-300">
                                 <?php
                                 $pipelineId = $opportunity->pipelineId ?? '';
                                 $stageId = $opportunity->pipelineStageId ?? '';
                                 $pipelineName = $pipelineMap[$pipelineId]['name'] ?? null;
                                 $stageName = $pipelineMap[$pipelineId]['stages'][$stageId] ?? null;

                                 if ($pipelineName && $stageName):
                                    ?>
                                    <div class="space-y-1">
                                       <div class="font-medium text-white">
                                          <?= htmlspecialchars($pipelineName, ENT_QUOTES, 'UTF-8') ?>
                                       </div>
                                       <div class="text-xs text-zinc-400">
                                          <?= htmlspecialchars($stageName, ENT_QUOTES, 'UTF-8') ?>
                                       </div>
                                    </div>
                                 <?php elseif ($pipelineName): ?>
                                    <div class="font-medium text-white">
                                       <?= htmlspecialchars($pipelineName, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="text-xs text-zinc-500 italic">No stage</div>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic">No pipeline</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center text-sm">
                                 <?php if (isset($opportunity->createdAt)): ?>
                                    <svg class="w-4 h-4 text-zinc-500 mr-2" fill="none" stroke="currentColor"
                                       viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-zinc-300">
                                       <?= htmlspecialchars(date('Y-m-d H:i', strtotime($opportunity->createdAt)), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                 <?php else: ?>
                                    <span class="text-zinc-600 italic">N/A</span>
                                 <?php endif; ?>
                              </div>
                           </td>
                           <td class="px-6 py-4 whitespace-nowrap text-right">
                              <div class="flex items-center justify-end space-x-2">
                                 <!-- View Button -->
                                 <a href="view-opportunity.php?id=<?= htmlspecialchars($opportunity->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    class="inline-flex items-center p-2 bg-blue-900 text-white rounded-md hover:bg-blue-800 transition-colors duration-200"
                                    title="View Opportunity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                 </a>
                                 <!-- Edit Button -->
                                 <a href="edit-opportunity.php?id=<?= htmlspecialchars($opportunity->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    class="inline-flex items-center p-2 bg-zinc-700 text-white rounded-md hover:bg-zinc-600 transition-colors duration-200"
                                    title="Edit Opportunity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                 </a>
                                 <!-- Delete Button -->
                                 <a href="delete-opportunity.php?id=<?= htmlspecialchars($opportunity->id ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    class="inline-flex items-center p-2 bg-red-900 text-white rounded-md hover:bg-red-800 transition-colors duration-200"
                                    title="Delete Opportunity">
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