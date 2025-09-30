<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}

$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $user_id_to_edit = intval($_POST['user_id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (empty($username) || empty($email)) {
        $errors[] = "A felhasználónév és az email megadása kötelező.";
    }
    
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt_check->bind_param("ssi", $username, $email, $user_id_to_edit);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $errors[] = "Ez a felhasználónév vagy email cím már egy másik felhasználóhoz tartozik.";
    }
    $stmt_check->close();

    if (empty($errors)) {
        $stmt_update = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt_update->bind_param("sssi", $username, $email, $role, $user_id_to_edit);
        if ($stmt_update->execute()) {
            $success_message = "A felhasználó adatai sikeresen frissítve!";
        } else {
            $errors[] = "Hiba a mentés során: " . $stmt_update->error;
        }
        $stmt_update->close();
    }
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
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script> tailwind.config = { darkMode: "class", theme: { extend: { colors: { primary: "#19a1e6", "background-light": "#f6f7f8", "background-dark": "#111c21" } } } } </script>
    <style>.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 }</style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-black dark:text-white">

<div class="flex" x-data="{ isModalOpen: false, editingUser: {} }">
    
    <?php include 'admin_nav.php';?>

    <main class="flex-1 p-8">
        <h2 class="text-3xl font-bold mb-6">Felhasználók kezelése</h2>
        
        <?php if (!empty($errors)): ?><div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?></div><?php endif; ?>
        <?php if ($success_message): ?><div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><p><?php echo $success_message; ?></p></div><?php endif; ?>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/50">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Felhasználó</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Szerepkör</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Műveletek</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium"><?php echo htmlspecialchars($user['username']); ?></div>
                            <div class="text-xs text-gray-500">ID: <?php echo $user['id']; ?></div>
                        </td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button @click="isModalOpen = true; editingUser = <?php echo htmlspecialchars(json_encode($user)); ?>" class="text-primary hover:underline">Szerkesztés</button>
                            <?php if ($user['id'] != 1): ?>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="text-red-500 hover:underline ml-4" onclick="return confirm('Biztosan törölni szeretnéd ezt a felhasználót?')">Törlés</a>
                            <?php endif; ?>
                        </td>
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
            <h3 class="text-2xl font-bold mb-6 text-black dark:text-white">Felhasználó szerkesztése</h3>
            <form method="POST" action="admin.php" class="space-y-4">
                <input type="hidden" name="edit_user" value="1">
                <input type="hidden" name="user_id" :value="editingUser.id">
                
                <div>
                    <label for="username" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Felhasználónév</label>
                    <input type="text" name="username" id="username" class="w-full form-input rounded-md" :value="editingUser.username" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Email cím</label>
                    <input type="email" name="email" id="email" class="w-full form-input rounded-md" :value="editingUser.email" required>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Szerepkör</label>
                    <select name="role" id="role" class="w-full form-select rounded-md">
                        <option value="user" :selected="editingUser.role === 'user'">Felhasználó</option>
                        <option value="admin" :selected="editingUser.role === 'admin'">Adminisztrátor</option>
                    </select>
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-black dark:text-white font-bold rounded-lg hover:bg-gray-300">Mégse</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white font-bold rounded-lg hover:bg-primary/90">Mentés</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>