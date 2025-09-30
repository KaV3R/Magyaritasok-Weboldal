<?php
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); exit();
}
$user_id = $_SESSION['user_id'];
$errors = []; $success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = trim($_POST['bio']);
    
    $avatar_sql_part = "";
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        if ($_FILES['avatar']['size'] > 3000000) { // 3MB
            $errors[] = "A kép mérete nem haladhatja meg a 3MB-t.";
        } else {
            $target_dir = "uploads/avatars/";
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $errors[] = "Csak JPG, PNG és GIF képek engedélyezettek.";
            } else {
                
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $avatar_path = $target_dir . uniqid('avatar_', true) . '.' . $ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
                    $avatar_sql_part = ", avatar_url = ?";
                } else {
                    $errors[] = "Hiba a kép feltöltése során. Ellenőrizd a mappa jogosultságait!";
                }
            }
        }
    }
    
    if (empty($errors)) {
        $sql = "UPDATE users SET bio = ? $avatar_sql_part WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($avatar_sql_part) {
            $stmt->bind_param("ssi", $bio, $avatar_path, $user_id);
        } else {
            $stmt->bind_param("si", $bio, $user_id);
        }
        
        if ($stmt->execute()) {
            $success_message = "Profil sikeresen frissítve!";
        } else {
            $errors[] = "Hiba a profil frissítése során.";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT username, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$page_title = "Profil szerkesztése";
include 'header.php';
?>
<div class="container mx-auto max-w-2xl py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Profil szerkesztése</h1>
    
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

    <form method="POST" enctype="multipart/form-data" class="space-y-6 p-8 rounded-lg bg-surface-light dark:bg-surface-dark">
        <div>
            <label class="block font-medium">Felhasználónév</label>
            <p class="text-muted-light dark:text-muted-dark"><?php echo htmlspecialchars($user['username']); ?> (nem módosítható)</p>
        </div>
        <div>
            <label for="bio" class="block font-medium mb-1">Bemutatkozás (Bio)</label>
            <textarea name="bio" id="bio" rows="4" class="w-full form-textarea rounded-md"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
        </div>
        <div>
            <label for="avatar" class="block font-medium mb-1">Profilkép cseréje (max 3MB)</label>
            <input type="file" name="avatar" id="avatar" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-bold">Mentés</button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>```