<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}

$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_game'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $cover_image = trim($_POST['cover_image']);

    if (empty($title) || empty($cover_image)) {
        $errors[] = "A játék címe és a borítókép URL megadása kötelező.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO games (title, description, cover_image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $cover_image);
        if ($stmt->execute()) {
            $success_message = "Az új játék sikeresen hozzáadva!";
        } else {
            $errors[] = "Hiba történt a játék hozzáadása során.";
        }
        $stmt->close();
    }
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
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script> tailwind.config = { darkMode: "class", theme: { extend: { colors: { primary: "#19a1e6", "background-light": "#f6f7f8", "background-dark": "#111c21" } } } } </script>
    <style>.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 }</style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-black dark:text-white">
<div class="flex" x-data="{ isModalOpen: false }">
    
    <?php include 'admin_nav.php';?>

    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Játékok kezelése</h2>
            <button @click="isModalOpen = true" class="px-4 py-2 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 transition-colors">
                Új játék hozzáadása
            </button>
        </div>
        
        <?php if (!empty($errors)): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?></div><?php endif; ?>
        <?php if ($success_message): ?><div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><p><?php echo $success_message; ?></p></div><?php endif; ?>

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
                        <td class="px-6 py-4"><img src="<?php echo htmlspecialchars($game['cover_image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" class="h-16 w-12 object-cover rounded"></td>
                        <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($game['title']); ?></td>
                        <td class="px-6 py-4"><a href="edit_game.php?id=<?php echo $game['id']; ?>" class="text-primary hover:underline">Szerkesztés</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div x-show="isModalOpen" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.away="isModalOpen = false"
         style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 w-full max-w-lg" @click.stop>
            <h3 class="text-2xl font-bold mb-6 text-black dark:text-white">Új játék hozzáadása</h3>
            <form method="POST" action="admin_games.php" class="space-y-4">
                <input type="hidden" name="add_game" value="1">
                <div>
                    <label for="title" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Játék címe</label>
                    <input type="text" name="title" id="title" class="w-full form-input rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600" required>
                </div>
                <div>
                    <label for="cover_image" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Borítókép URL</label>
                    <input type="url" name="cover_image" id="cover_image" class="w-full form-input rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600" placeholder="https://..." required>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Leírás</label>
                    <textarea name="description" id="description" rows="5" class="w-full form-textarea rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600"></textarea>
                </div>
                <div class="flex justify-end gap-4 pt-4">
                    <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-black dark:text-white font-bold rounded-lg hover:bg-gray-300">Mégse</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white font-bold rounded-lg hover:bg-primary/90">Hozzáadás</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>