<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="hu" class="">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Magyarítások' : 'Magyarítások Portál'; ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#19a1e6",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111c21",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a282f",
                        "text-light": "#111518",
                        "text-dark": "#e3e8e9",
                        "muted-light": "#637c88",
                        "muted-dark": "#8a9a9f",
                        "border-light": "#e3e8e9",
                        "border-dark": "#2d3f47",
                    },
                    fontFamily: { "display": ["Space Grotesk", "sans-serif"] },
                }
            }
        }
    </script>
    <style>
         .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark">
<div class="flex flex-col min-h-screen">
    <header class="sticky top-0 z-20 bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-sm border-b border-border-light dark:border-border-dark">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-8">
                <a href="index.php" class="flex items-center gap-3 text-xl font-bold">
                    <span class="text-primary"><svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"></path></svg></span>
                    <span>Magyarítások</span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a class="text-sm font-medium text-muted-light dark:text-muted-dark hover:text-primary transition-colors" href="index.php">Főoldal</a>
                    <a class="text-sm font-medium text-muted-light dark:text-muted-dark hover:text-primary transition-colors" href="forum.php">Fórum</a>
                    <a class="text-sm font-medium text-muted-light dark:text-muted-dark hover:text-primary transition-colors" href="upload.php">Feltöltés</a>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-800"><span class="material-symbols-outlined">brightness_6</span></button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="px-4 py-2 rounded-lg bg-primary/20 dark:bg-primary/30 text-primary font-bold hover:bg-primary/30 dark:hover:bg-primary/40 transition-colors">Profil</a>
                    <a href="logout.php" class="px-4 py-2 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">Kijelentkezés</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php" class="px-4 py-2 rounded-lg bg-red-500 text-white font-bold hover:bg-red-600 transition-colors">Admin</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login_register.php" class="px-4 py-2 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">Bejelentkezés</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="flex-grow">