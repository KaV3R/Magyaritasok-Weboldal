<?php
header('Content-Type: text/html; charset=utf-8');

require_once 'db.php';

$user_id_to_view = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id_to_view = intval($_GET['id']);
} elseif (isset($_SESSION['user_id'])) {
    $user_id_to_view = $_SESSION['user_id'];
} else {
    header("Location: login_register.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, username, avatar_url, bio, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id_to_view);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("A felhaszn�l� nem tal�lhat�!");
}

$stmt_trans = $conn->prepare(
    "SELECT t.version, t.status, g.id as game_id, g.title as game_title, g.cover_image
     FROM translations t
     JOIN games g ON t.game_id = g.id
     WHERE t.user_id = ? ORDER BY t.upload_date DESC"
);
$stmt_trans->bind_param("i", $user_id_to_view);
$stmt_trans->execute();
$translations = $stmt_trans->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_trans->close();


$page_title = htmlspecialchars($user['username']) . " profilja";
include 'header.php';
?>
<div class="container mx-auto max-w-5xl px-10 py-8">
    <div class="flex flex-col items-center gap-6 p-6">
        <div class="h-32 w-32 rounded-full bg-cover bg-center ring-4 ring-primary/20" style='background-image: url("<?php echo htmlspecialchars($user['avatar_url']); ?>");'></div>
        <div class="text-center">
            <p class="text-2xl font-bold"><?php echo htmlspecialchars($user['username']); ?></p>
            <p class="text-base text-muted-light dark:text-muted-dark">@<?php echo htmlspecialchars($user['username']); ?></p>
            <?php if (!empty($user['bio'])): ?>
                <p class="mt-4 max-w-xl text-center text-sm text-text-light dark:text-text-dark"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id_to_view): ?>
        <a href="edit_profile.php" class="flex min-w-[120px] cursor-pointer items-center justify-center rounded-full bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-lg transition-transform hover:scale-105">
            <span>Profil szerkeszt�se</span>
        </a>
        <?php endif; ?>

    </div>
    <div class="border-b border-border-light dark:border-border-dark mt-8">
        <nav class="-mb-px flex space-x-8 px-4">
            <a class="whitespace-nowrap border-b-2 border-primary px-1 py-4 text-sm font-medium text-primary" href="#">Felt�lt�tt ford�t�sok (<?php echo count($translations); ?>)</a>
        </nav>
    </div>
    <div class="py-8 space-y-6">
        <?php if (empty($translations)): ?>
            <p class="text-center text-muted-light dark:text-muted-dark"><?php echo htmlspecialchars($user['username']); ?> m�g nem t�lt�tt fel ford�t�st.</p>
        <?php else: ?>
            <?php foreach ($translations as $trans): ?>
            <div class="flex items-start gap-6 rounded-lg bg-surface-light dark:bg-surface-dark p-4 shadow-sm transition-shadow hover:shadow-lg border border-border-light dark:border-border-dark">
                <div class="w-1/4 sm:w-1/6 flex-shrink-0">
                    <a href="game.php?id=<?php echo $trans['game_id']; ?>">
                        <img src="<?php echo htmlspecialchars($trans['cover_image']); ?>" alt="<?php echo htmlspecialchars($trans['game_title']); ?>" class="aspect-[3/4] w-full object-cover rounded-lg">
                    </a>
                </div>
                <div class="flex flex-col gap-1">
                    <p class="text-sm text-primary"><?php echo htmlspecialchars($trans['status']); ?></p>
                    <a href="game.php?id=<?php echo $trans['game_id']; ?>" class="text-lg font-bold hover:text-primary transition-colors"><?php echo htmlspecialchars($trans['game_title']); ?></a>
                    <p class="text-sm text-muted-light dark:text-muted-dark">Verzi�: <?php echo htmlspecialchars($trans['version']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php'; ?>