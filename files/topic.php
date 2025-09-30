<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: forum.php");
    exit();
}
$topic_id = intval($_GET['id']);
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_role = $_SESSION['role'] ?? 'user';


$errors = [];

if (isset($_GET['delete_post']) && is_numeric($_GET['delete_post'])) {
    if (!$current_user_id) {
        die("Hozzáférés megtagadva!");
    }
    $post_to_delete_id = intval($_GET['delete_post']);

    $stmt = $conn->prepare("SELECT user_id FROM forum_posts WHERE id = ?");
    $stmt->bind_param("i", $post_to_delete_id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($post && ($post['user_id'] == $current_user_id || $current_user_role == 'admin')) {
        $stmt_delete = $conn->prepare("DELETE FROM forum_posts WHERE id = ?");
        $stmt_delete->bind_param("i", $post_to_delete_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        header("Location: topic.php?id=" . $topic_id);
        exit();
    } else {
        die("Nincs jogosultságod a törléshez!");
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_reply'])) {
    if (!$current_user_id) {
        $errors[] = "A hozzászóláshoz be kell jelentkezned!";
    } else {
        $content = trim($_POST['content']);
        if (empty($content)) {
            $errors[] = "A hozzászólás nem lehet üres!";
        } else {
            $stmt = $conn->prepare("INSERT INTO forum_posts (topic_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $topic_id, $current_user_id, $content);
            if ($stmt->execute()) {
                header("Location: topic.php?id=" . $topic_id);
                exit();
            } else {
                $errors[] = "Hiba történt a hozzászólás mentése során.";
            }
            $stmt->close();
        }
    }
}


$stmt = $conn->prepare("SELECT t.title, t.user_id as topic_author_id, t.edited_at as topic_edited_at FROM forum_topics t WHERE id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$topic = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$topic) {
    die("A téma nem található!");
}

$stmt = $conn->prepare(
    "SELECT p.id as post_id, p.content, p.created_at, p.edited_at as post_edited_at, p.user_id as post_author_id, u.username, u.avatar_url, u.role 
     FROM forum_posts p 
     JOIN users u ON p.user_id = u.id 
     WHERE p.topic_id = ? ORDER BY p.created_at ASC"
);
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$page_title = htmlspecialchars($topic['title']);
include 'header.php';
?>
<div class="container mx-auto max-w-4xl py-8 px-4">
    <div class="mb-4">
        <a href="forum.php" class="text-sm text-primary hover:underline">&laquo; Vissza a fórumra</a>
        <div class="flex justify-between items-start mt-2">
            <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($topic['title']); ?></h1>
            <?php if ($current_user_id && ($current_user_id == $topic['topic_author_id'] || $current_user_role == 'admin')): ?>
                <a href="edit_topic.php?id=<?php echo $topic_id; ?>" class="text-sm text-primary hover:underline flex-shrink-0 ml-4">Téma szerkesztése</a>
            <?php endif; ?>
        </div>
        <?php if($topic['topic_edited_at']): ?>
            <p class="text-xs italic text-muted-light dark:text-muted-dark">Szerkesztve: <?php echo date('Y.m.d H:i', strtotime($topic['topic_edited_at'])); ?></p>
        <?php endif; ?>
    </div>

    <div class="space-y-6">
        <?php foreach ($posts as $post): ?>
        <div class="flex flex-col sm:flex-row gap-4 p-4 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark">
            <div class="flex-shrink-0 text-center w-full sm:w-32">
                <img src="<?php echo htmlspecialchars($post['avatar_url']); ?>" alt="avatar" class="w-16 h-16 rounded-full mx-auto">
                <p class="font-bold mt-2 text-primary"><?php echo htmlspecialchars($post['username']); ?></p>
                <p class="text-xs text-muted-light dark:text-muted-dark"><?php echo ucfirst($post['role']); ?></p>
            </div>
            <div class="flex-1 sm:border-l sm:border-border-light sm:dark:border-border-dark sm:pl-4">
                <div class="flex justify-between items-center text-xs text-muted-light dark:text-muted-dark mb-2">
                    <span>Posztolva: <?php echo date('Y.m.d H:i', strtotime($post['created_at'])); ?></span>
                    <?php if ($current_user_id && ($current_user_id == $post['post_author_id'] || $current_user_role == 'admin')): ?>
                        <div class="flex gap-3">
                             <a href="edit_post.php?id=<?php echo $post['post_id']; ?>" class="hover:underline">Szerkesztés</a>
                             <a href="topic.php?id=<?php echo $topic_id; ?>&delete_post=<?php echo $post['post_id']; ?>" 
                                class="text-red-500 hover:underline" 
                                onclick="return confirm('Biztosan törölni szeretnéd ezt a hozzászólást? Ez a művelet nem vonható vissza.')">Törlés</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="prose dark:prose-invert max-w-none text-text-light dark:text-text-dark">
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
                 <?php if($post['post_edited_at']): ?>
                    <p class="text-xs italic text-muted-light dark:text-muted-dark mt-4">Szerkesztve: <?php echo date('Y.m.d H:i', strtotime($post['post_edited_at'])); ?></p>
                 <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="mt-8">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h3 class="text-2xl font-bold mb-4">Válasz írása</h3>
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="topic.php?id=<?php echo $topic_id; ?>">
                <textarea name="content" class="w-full form-textarea rounded-md bg-surface-light dark:bg-surface-dark border-border-light dark:border-border-dark" rows="5" placeholder="Írd ide a hozzászólásodat..." required></textarea>
                <button type="submit" name="submit_reply" class="mt-4 px-6 py-2 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">
                    Hozzászólás elküldése
                </button>
            </form>
        <?php else: ?>
            <div class="text-center p-6 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark">
                <p class="text-muted-light dark:text-muted-dark">A hozzászóláshoz <a href="login_register.php" class="text-primary font-bold hover:underline">be kell jelentkezned</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php'; ?>