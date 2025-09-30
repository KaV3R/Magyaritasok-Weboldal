<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_translations.php"); exit();
}
$translation_id = intval($_GET['id']);
$errors = []; $success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $version = trim($_POST['version']);
    $status = $_POST['status'];
    $translators = trim($_POST['translators']);
    $description = trim($_POST['description']);

    if (empty($version)) $errors[] = "A verzió megadása kötelező.";
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE translations SET version = ?, status = ?, translators = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $version, $status, $translators, $description, $translation_id);
        if ($stmt->execute()) {
            $success_message = "A fordítás adatai sikeresen frissítve!";
        } else {
            $errors[] = "Hiba a mentés során.";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT t.*, g.title as game_title, u.username as uploader_name 
                      FROM translations t 
                      JOIN games g ON t.game_id = g.id
                      JOIN users u ON t.user_id = u.id
                      WHERE t.id = ?");
$stmt->bind_param("i", $translation_id);
$stmt->execute();
$translation = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$translation) die("A fordítás nem található!");

$page_title = "Fordítás szerkesztése";
?>
<!DOCTYPE html>
<html lang="hu" class="">
<head>
    <meta charset="utf-8"/>
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <script> 
        tailwind.config = { 
            darkMode: "class", 
            theme: { 
                extend: { 
                    colors: { primary: "#19a1e6", "background-light": "#f6f7f8", "background-dark": "#111c21" } 
                } 
            } 
        } 
    </script>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-black dark:text-white">
<div class="container mx-auto max-w-2xl py-8 px-4">
    <div class="mb-6">
        <a href="admin_translations.php" class="text-sm text-primary hover:underline">&laquo; Vissza a fordítások listájához</a>
        <h1 class="text-3xl font-bold mt-2">Fordítás szerkesztése</h1>
        <p class="text-lg text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($translation['game_title']); ?></p>
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

    <form method="POST" action="edit_translation.php?id=<?php echo $translation_id; ?>" class="space-y-6 p-8 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-800">
        <div>
            <label for="uploader" class="block text-sm font-medium mb-1">Feltöltő</label>
            <input type="text" id="uploader" class="w-full form-input rounded-md bg-gray-200 dark:bg-gray-700/50" value="<?php echo htmlspecialchars($translation['uploader_name']); ?>" disabled>
        </div>
        <div>
            <label for="version" class="block text-sm font-medium mb-1">Verzió</label>
            <input type="text" name="version" id="version" class="w-full form-input rounded-md bg-gray-100 dark:bg-gray-700" value="<?php echo htmlspecialchars($translation['version']); ?>" required>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium mb-1">Állapot</label>
            <select name="status" id="status" class="w-full form-select rounded-md bg-gray-100 dark:bg-gray-700">
                <option <?php if($translation['status'] == 'Folyamatban') echo 'selected'; ?>>Folyamatban</option>
                <option <?php if($translation['status'] == 'Béta') echo 'selected'; ?>>Béta</option>
                <option <?php if($translation['status'] == 'Kész') echo 'selected'; ?>>Kész</option>
            </select>
        </div>
        <div>
            <label for="translators" class="block text-sm font-medium mb-1">Fordító(k)</label>
            <input type="text" name="translators" id="translators" class="w-full form-input rounded-md bg-gray-100 dark:bg-gray-700" value="<?php echo htmlspecialchars($translation['translators']); ?>">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium mb-1">Leírás</label>
            <textarea name="description" id="description" rows="5" class="w-full form-textarea rounded-md bg-gray-100 dark:bg-gray-700"><?php echo htmlspecialchars($translation['description']); ?></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-8 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">
                Mentés
            </button>
        </div>
    </form>
</div>
</body>
</html>