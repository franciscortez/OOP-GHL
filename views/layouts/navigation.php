<?php
/**
 * Navigation Layout
 * Renders back navigation link
 */
?>
<a href="<?= htmlspecialchars($backUrl ?? '/views/contacts/', ENT_QUOTES, 'UTF-8') ?>"
   class="inline-flex items-center text-zinc-400 hover:text-white transition-colors duration-200 mb-4">
   <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
   </svg>
   <?= htmlspecialchars($backText ?? 'Back to Contacts', ENT_QUOTES, 'UTF-8') ?>
</a>