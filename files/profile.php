<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, avatar_url, bio, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) { 
    header("Location: logout.php"); 
    exit(); 
}

$page_title = htmlspecialchars($user['username']);
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
        
        <a href="edit_profile.php" class="flex min-w-[120px] cursor-pointer items-center justify-center rounded-full bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-lg transition-transform hover:scale-105">
            <span>Profil szerkesztése</span>
        </a>

    </div>
    <div class="border-b border-border-light dark:border-border-dark mt-8">
        <nav class="-mb-px flex space-x-8 px-4">
            <a class="whitespace-nowrap border-b-2 border-primary px-1 py-4 text-sm font-medium text-primary" href="#">Fordításaim</a>
            <a class="whitespace-nowrap border-b-2 border-transparent px-1 py-4 text-sm font-medium text-muted-light dark:text-muted-dark hover:border-gray-300" href="#">Kedvencek</a>
        </nav>
    </div>
    <div class="py-8">
        <p class="text-center text-muted-light dark:text-muted-dark">A felhasználó által feltöltött fordítások itt fognak megjelenni.</p>
    </div>
</div>
<?php include 'footer.php'; ?>