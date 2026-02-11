<?php
/**
 * Alert Messages Layout
 * Renders success and error messages from session
 */

// Success Message
if (isset($successMessage) && $successMessage): ?>
   <div id="success-message" class="bg-zinc-900 border border-green-900 rounded-lg shadow-2xl overflow-hidden mb-6">
      <div class="bg-gradient-to-r from-green-900 to-zinc-900 px-6 py-4 border-b border-zinc-800">
         <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
               <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
               </svg>
               <h2 class="text-xl font-semibold text-white">Success!</h2>
            </div>
            <button onclick="document.getElementById('success-message').remove()"
               class="text-green-400 hover:text-green-300 transition-colors">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
               </svg>
            </button>
         </div>
      </div>
      <div class="px-6 py-4">
         <p class="text-white text-sm"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
   </div>
<?php endif; ?>

<?php
// Error Message
if (isset($errorMessage) && $errorMessage): ?>
   <div id="error-message" class="bg-zinc-900 border border-red-900 rounded-lg shadow-2xl overflow-hidden mb-6">
      <div class="bg-gradient-to-r from-red-900 to-zinc-900 px-6 py-4 border-b border-zinc-800">
         <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
               <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
               </svg>
               <h2 class="text-xl font-semibold text-white">Error</h2>
            </div>
            <button onclick="document.getElementById('error-message').remove()"
               class="text-red-400 hover:text-red-300 transition-colors">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
               </svg>
            </button>
         </div>
      </div>
      <div class="px-6 py-4">
         <p class="text-white text-sm"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
   </div>
<?php endif; ?>