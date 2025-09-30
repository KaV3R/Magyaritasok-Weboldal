<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}

$query = "SELECT t.id, t.version, t.status, t.upload_date, g.title as game_title, u.username as uploader_name
          FROM translations t
          JOIN games g ON t.game_id = g.id
          JOIN users u ON t.user_id = u.id
          ORDER BY t.upload_date DESC";
$translations = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
$page_title = "Admin Panel - Fordítások";
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
<div class="flex h-screen">
    
    <?php include 'admin_nav.php'; ?>

    <main class="flex-1 overflow-y-auto p-8">
        <h2 class="text-3xl font-bold mb-6">Feltöltött fordítások kezelése</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/50">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Játék</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Verzió</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Állapot</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Feltöltő</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Dátum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Műveletek</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <?php foreach ($translations as $trans): ?>
                    <tr>
                        <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($trans['game_title']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($trans['version']); ?></td>
                        <td class="px-6 py-4"><span class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary"><?php echo htmlspecialchars($trans['status']); ?></span></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($trans['uploader_name']); ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo date('Y.m.d', strtotime($trans['upload_date'])); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="edit_translation.php?id=<?php echo $trans['id']; ?>" class="text-primary hover:underline">Szerkesztés</a>
                            <a href="delete_translation.php?id=<?php echo $trans['id']; ?>" class="text-red-500 hover:underline ml-4" onclick="return confirm('Biztosan törölni szeretnéd ezt a fordítást? Ez a művelet nem vonható vissza, és a feltöltött fájl is törlődni fog!')">Törlés</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
