<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}

$users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$page_title = "Admin Panel - Felhasználók";
?>
<!DOCTYPE html>
<html lang="hu" class="">
<head>
    <meta charset="utf-8"/>
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script> tailwind.config = { darkMode: "class", theme: { extend: { colors: { primary: "#19a1e6", "background-light": "#f6f7f8", "background-dark": "#111c21" } } } } </script>
    <style>.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 }</style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-black dark:text-white">
<div class="flex">
    
    <?php include 'admin_nav.php';?>

    <main class="flex-1 p-8">
        <h2 class="text-3xl font-bold mb-6">Felhasználók kezelése</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/50">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Felhasználó</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Szerepkör</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Regisztrált</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="px-6 py-4"><?php echo date('Y.m.d', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>