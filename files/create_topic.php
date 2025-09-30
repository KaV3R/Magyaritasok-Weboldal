<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (empty($title)) {
        $errors[] = "A téma címe nem lehet üres.";
    }
    if (empty($content)) {
        $errors[] = "A hozzászólás nem lehet üres.";
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            $stmt_topic = $conn->prepare("INSERT INTO forum_topics (user_id, title) VALUES (?, ?)");
            $stmt_topic->bind_param("is", $user_id, $title);
            $stmt_topic->execute();
            
            $new_topic_id = $conn->insert_id;

            $stmt_post = $conn->prepare("INSERT INTO forum_posts (topic_id, user_id, content) VALUES (?, ?, ?)");
            $stmt_post->bind_param("iis", $new_topic_id, $user_id, $content);
            $stmt_post->execute();

            $conn->commit();

            header("Location: topic.php?id=" . $new_topic_id);
            exit();

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $errors[] = "Adatbázis hiba történt a téma létrehozása során.";
        }
    }
}


$page_title = "Új téma indítása";
include 'header.php';
?>
<div class="container mx-auto max-w-3xl py-8 px-4">
    <div class="mb-4">
        <a href="forum.php" class="text-sm text-primary hover:underline">&laquo; Vissza a fórumra</a>
        <h1 class="text-3xl font-bold mt-2">Új téma indítása</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="create_topic.php" class="space-y-6 p-8 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark">
        <div>
            <label for="title" class="block text-sm font-medium mb-1">Téma címe</label>
            <input type="text" name="title" id="title" class="w-full form-input rounded-md bg-background-light dark:bg-background-dark border-border-light dark:border-border-dark" required>
        </div>
        <div>
            <label for="content" class="block text-sm font-medium mb-1">Első hozzászólás</label>
            <textarea name="content" id="content" rows="8" class="w-full form-textarea rounded-md bg-background-light dark:bg-background-dark border-border-light dark:border-border-dark" placeholder="Írd ide a téma bevezetőjét..." required></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">
                Téma létrehozása
            </button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>