<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: forum.php");
    exit();
}
$post_id = intval($_GET['id']);
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_role = $_SESSION['role'] ?? 'user';

if (!$current_user_id) {
    header("Location: login_register.php");
    exit();
}

$stmt = $conn->prepare("SELECT user_id, content, topic_id FROM forum_posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    die("A hozzászólás nem található!");
}

if ($post['user_id'] != $current_user_id && $current_user_role != 'admin') {
    die("Nincs jogosultságod a hozzászólás szerkesztéséhez!");
}


$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_content = trim($_POST['content']);

    if (empty($new_content)) {
        $errors[] = "A hozzászólás nem lehet üres.";
    }

    if (empty($errors)) {
        $stmt_update = $conn->prepare("UPDATE forum_posts SET content = ?, edited_at = NOW() WHERE id = ?");
        $stmt_update->bind_param("si", $new_content, $post_id);
        
        if ($stmt_update->execute()) {
            header("Location: topic.php?id=" . $post['topic_id']);
            exit();
        } else {
            $errors[] = "Hiba történt a mentés során.";
        }
        $stmt_update->close();
    }
}

$page_title = "Hozzászólás szerkesztése";
include 'header.php';
?>
<div class="container mx-auto max-w-3xl py-8 px-4">
    <div class="mb-4">
        <a href="topic.php?id=<?php echo $post['topic_id']; ?>" class="text-sm text-primary hover:underline">&laquo; Vissza a témához</a>
        <h1 class="text-3xl font-bold mt-2">Hozzászólás szerkesztése</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_post.php?id=<?php echo $post_id; ?>" class="space-y-6 p-8 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark">
        <div>
            <label for="content" class="block text-sm font-medium mb-1">Hozzászólás új tartalma</label>
            <textarea name="content" id="content" rows="8" class="w-full form-textarea rounded-md bg-background-light dark:bg-background-dark border-border-light dark:border-border-dark" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <div class="flex justify-end gap-4">
             <a href="topic.php?id=<?php echo $post['topic_id']; ?>" class="px-6 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-black dark:text-white font-bold hover:bg-opacity-90">Mégse</a>
            <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">
                Mentés
            </button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>