<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}

$games = $conn->query("SELECT id, title, cover_image FROM games ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);
$page_title = "Admin Panel - Játékok";
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
        <h2 class="text-3xl font-bold mb-6">Játékok kezelése</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/50">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Borítókép</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Játék címe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Műveletek</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <?php foreach ($games as $game): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <img src="<?php echo htmlspecialchars($game['cover_image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" class="h-16 w-12 object-cover rounded">
                        </td>
                        <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($game['title']); ?></td>
                        <td class="px-6 py-4">
                            <a href="edit_game.php?id=<?php echo $game['id']; ?>" class="text-primary hover:underline">Szerkesztés</a>
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