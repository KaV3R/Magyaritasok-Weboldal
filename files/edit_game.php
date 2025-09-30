<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_games.php"); exit();
}
$game_id = intval($_GET['id']);
$errors = []; $success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $cover_image_url = trim($_POST['cover_image']);

    if (empty($title)) {
        $errors[] = "A játék címe nem lehet üres.";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE games SET title = ?, description = ?, cover_image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $description, $cover_image_url, $game_id);
        if ($stmt->execute()) {
            $success_message = "A játék adatai sikeresen frissítve!";
        } else {
            $errors[] = "Hiba a mentés során.";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT title, description, cover_image FROM games WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$game) {
    die("A játék nem található!");
}

$page_title = "Játék szerkesztése";
?>
<!DOCTYPE html>
<html lang="hu" class="">
<head>
    <meta charset="utf-8"/>
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <script> tailwind.config = { darkMode: "class", theme: { extend: { colors: { primary: "#19a1e6", "background-light": "#f6f7f8", "background-dark": "#111c21" } } } } </script>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-black dark:text-white">
<div class="container mx-auto max-w-2xl py-8 px-4">
    <div class="mb-4">
        <a href="admin_games.php" class="text-sm text-primary hover:underline">&laquo; Vissza a játékok listájához</a>
        <h1 class="text-3xl font-bold mt-2">Játék szerkesztése: <?php echo htmlspecialchars($game['title']); ?></h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_game.php?id=<?php echo $game_id; ?>" class="space-y-6 p-8 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-800">
        <div>
            <label for="title" class="block text-sm font-medium mb-1">Játék címe</label>
            <input type="text" name="title" id="title" class="w-full form-input rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600" value="<?php echo htmlspecialchars($game['title']); ?>" required>
        </div>
        <div>
            <label for="cover_image" class="block text-sm font-medium mb-1">Borítókép URL</label>
            <input type="url" name="cover_image" id="cover_image" class="w-full form-input rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600" value="<?php echo htmlspecialchars($game['cover_image']); ?>" required>
            <img src="<?php echo htmlspecialchars($game['cover_image']); ?>" alt="Jelenlegi borító" class="mt-2 h-40 object-cover rounded">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium mb-1">Leírás</label>
            <textarea name="description" id="description" rows="6" class="w-full form-textarea rounded-md bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600"><?php echo htmlspecialchars($game['description']); ?></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">
                Mentés
            </button>
        </div>
    </form>
</div>
</body>
</html>