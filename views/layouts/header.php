<!DOCTYPE html>
<html lang="en" class="dark">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= htmlspecialchars($title ?? 'GoHighLevel Contacts', ENT_QUOTES, 'UTF-8') ?></title>
   <!-- Tailwind CSS -->
   <script src="https://cdn.tailwindcss.com"></script>
   <!-- SweetAlert2 -->
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