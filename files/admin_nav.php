<aside class="flex w-64 flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex-shrink-0 h-screen sticky top-0">
    <div class="flex h-16 shrink-0 items-center px-6">
        <h1 class="text-xl font-bold">Admin Panel</h1>
    </div>
    <nav class="flex-1 space-y-2 p-4">
        <?php $current_page = basename($_SERVER['PHP_SELF']);?>

        <a class="flex items-center gap-3 rounded px-3 py-2 
            <?php echo ($current_page == 'admin.php') ? 'bg-primary/10 dark:bg-primary/20 text-primary' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'; ?>" 
            href="admin.php">
            <span class="material-symbols-outlined">group</span>
            <span class="text-sm font-medium">Felhasználók</span>
        </a>

        <a class="flex items-center gap-3 rounded px-3 py-2
            <?php echo ($current_page == 'admin_games.php') ? 'bg-primary/10 dark:bg-primary/20 text-primary' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'; ?>" 
            href="admin_games.php">
            <span class="material-symbols-outlined">sports_esports</span>
            <span class="text-sm font-medium">Játékok</span>
        </a>

        <a class="flex items-center gap-3 rounded px-3 py-2
            <?php echo ($current_page == 'admin_translations.php') ? 'bg-primary/10 dark:bg-primary/20 text-primary' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'; ?>" 
            href="admin_translations.php">
            <span class="material-symbols-outlined">translate</span>
            <span class="text-sm font-medium">Fordítások</span>
        </a>
        
        <div class="pt-8">
             <a class="flex items-center gap-3 rounded px-3 py-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800" href="index.php">
                <span class="material-symbols-outlined">home</span>
                <span class="text-sm font-medium">Vissza a főoldalra</span>
            </a>
        </div>
    </nav>
</aside>