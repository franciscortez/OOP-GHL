<?php

/**
 * Create Contact Form
 * Allows users to create new contacts in GoHighLevel
 */

// Load application dependencies
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/Csrf.php';
require_once __DIR__ . '/../../controller/ContactController.php';

// Initialize variables
$success = null;
$error = null;
$csrfToken = Csrf::generate();

// Check for session messages
if (isset($_SESSION['success'])) {
   $success = $_SESSION['success'];
   unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
   $error = $_SESSION['error'];
   unset($_SESSION['error']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Validate CSRF token
   if (!isset($_POST['csrf_token']) || !Csrf::validate($_POST['csrf_token'])) {
      $_SESSION['error'] = 'Invalid CSRF token. Please try again.';
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;
   } else {
      try {
         $controller = new ContactController();
         $result = $controller->create($_POST);
         $_SESSION['success'] = 'Contact created successfully!';

         // Redirect to prevent form resubmission
         header('Location: ' . $_SERVER['PHP_SELF']);
         exit;
      } catch (Throwable $e) {
         $_SESSION['error'] = $e->getMessage();
         header('Location: ' . $_SERVER['PHP_SELF']);
         exit;
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Create Contact - GoHighLevel</title>
   <script src="https://cdn.tailwindcss.com"></script>
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

         <?php if ($success): ?>
            <!-- Success Message -->
            <div id="success-message"
               class="bg-zinc-900 border border-green-900 rounded-lg shadow-2xl overflow-hidden mb-6">
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
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                        </svg>
                     </button>
                  </div>
               </div>
               <div class="px-6 py-4">
                  <p class="text-white text-sm">
                     <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                  </p>
               </div>
            </div>
         <?php endif; ?>

         <?php if ($error): ?>
            <!-- Error Message -->
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
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                        </svg>
                     </button>
                  </div>
               </div>
               <div class="px-6 py-4">
                  <p class="text-white text-sm">
                     <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                  </p>
               </div>
            </div>
         <?php endif; ?>

         <!-- Form -->
         <div class="bg-zinc-900 border border-zinc-800 rounded-lg shadow-2xl overflow-hidden">
            <form method="POST" action="" class="p-6 space-y-6">
               <!-- CSRF Token -->
               <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

               <!-- First Name & Last Name -->
               <div class="flex flex-col sm:flex-row gap-4">
                  <div class="flex-1">
                     <label for="firstName" class="block text-sm font-medium text-zinc-300 mb-2">
                        First Name <span class="text-red-400">*</span>
                     </label>
                     <input type="text" id="firstName" name="firstName" required
                        value="<?= htmlspecialchars($_POST['firstName'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="Juan">
                  </div>
                  <div class="flex-1">
                     <label for="lastName" class="block text-sm font-medium text-zinc-300 mb-2">
                        Last Name <span class="text-red-400">*</span>
                     </label>
                     <input type="text" id="lastName" name="lastName" required
                        value="<?= htmlspecialchars($_POST['lastName'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                     <input type="email" id="email" name="email" required
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="juan.delacruz@example.com">
                  </div>
                  <div class="flex-1">
                     <label for="phone" class="block text-sm font-medium text-zinc-300 mb-2">
                        Phone
                     </label>
                     <input type="tel" id="phone" name="phone"
                        value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                        <option value="male" <?= (($_POST['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= (($_POST['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Female
                        </option>
                        <option value="other" <?= (($_POST['gender'] ?? '') === 'other') ? 'selected' : '' ?>>Other
                        </option>
                     </select>
                  </div>
                  <div class="flex-1">
                     <label for="dateOfBirth" class="block text-sm font-medium text-zinc-300 mb-2">
                        Date of Birth
                     </label>
                     <input type="date" id="dateOfBirth" name="dateOfBirth"
                        value="<?= htmlspecialchars($_POST['dateOfBirth'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors">
                  </div>
               </div>

               <!-- Company Name -->
               <div>
                  <label for="companyName" class="block text-sm font-medium text-zinc-300 mb-2">
                     Company Name
                  </label>
                  <input type="text" id="companyName" name="companyName"
                     value="<?= htmlspecialchars($_POST['companyName'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="Company Name">
               </div>

               <!-- Address -->
               <div>
                  <label for="address1" class="block text-sm font-medium text-zinc-300 mb-2">
                     Address
                  </label>
                  <input type="text" id="address1" name="address1"
                     value="<?= htmlspecialchars($_POST['address1'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                     placeholder="123 Main St">
               </div>

               <!-- City, State, Postal Code Grid -->
               <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                     <label for="city" class="block text-sm font-medium text-zinc-300 mb-2">
                        City
                     </label>
                     <input type="text" id="city" name="city"
                        value="<?= htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="City">
                  </div>
                  <div>
                     <label for="state" class="block text-sm font-medium text-zinc-300 mb-2">
                        State
                     </label>
                     <input type="text" id="state" name="state"
                        value="<?= htmlspecialchars($_POST['state'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="State">
                  </div>
                  <div>
                     <label for="postalCode" class="block text-sm font-medium text-zinc-300 mb-2">
                        Postal Code
                     </label>
                     <input type="text" id="postalCode" name="postalCode"
                        value="<?= htmlspecialchars($_POST['postalCode'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                     <input type="text" id="country" name="country"
                        value="<?= htmlspecialchars($_POST['country'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="PH">
                  </div>
                  <div class="flex-1">
                     <label for="website" class="block text-sm font-medium text-zinc-300 mb-2">
                        Website
                     </label>
                     <input type="url" id="website" name="website"
                        value="<?= htmlspecialchars($_POST['website'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                     <input type="text" id="timezone" name="timezone"
                        value="<?= htmlspecialchars($_POST['timezone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-600 focus:border-transparent transition-colors"
                        placeholder="America/New_York">
                  </div>
                  <div class="flex-1">
                     <label for="source" class="block text-sm font-medium text-zinc-300 mb-2">
                        Source
                     </label>
                     <input type="text" id="source" name="source"
                        value="<?= htmlspecialchars($_POST['source'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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

         <!-- Footer -->
         <div class="mt-8 text-center text-zinc-500 text-sm">
            <p>GoHighLevel Contacts Management</p>
         </div>
      </div>
   </div>
</body>

</html>