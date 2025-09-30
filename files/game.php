<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$game_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT title, description, cover_image FROM games WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$game) {
    die("A játék nem található!");
}

$stmt_trans = $conn->prepare(
    "SELECT t.version, t.status, t.translators, t.upload_date, t.description, t.file_path, t.download_url, u.id as uploader_id, u.username 
     FROM translations t
     JOIN users u ON t.user_id = u.id
     WHERE t.game_id = ? ORDER BY t.upload_date DESC"
);
$stmt_trans->bind_param("i", $game_id);
$stmt_trans->execute();
$translations = $stmt_trans->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_trans->close();

$page_title = htmlspecialchars($game['title']);
include 'header.php';
?>

<div class="container mx-auto max-w-5xl px-4 py-8">
    <div class="mb-6">
        <h1 class="text-4xl font-bold tracking-tight"><?php echo htmlspecialchars($game['title']); ?></h1>
    </div>
    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
        <div class="md:col-span-1">
            <div class="overflow-hidden rounded-xl shadow-lg">
                <img src="<?php echo htmlspecialchars($game['cover_image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?> borító" class="w-full aspect-[3/4] object-cover"/>
            </div>
             <div class="mt-6 space-y-4 rounded-lg bg-surface-light dark:bg-surface-dark p-4 border border-border-light dark:border-border-dark">
                <h3 class="text-lg font-bold border-b border-border-light dark:border-border-dark pb-2">A játékról</h3>
                <p class="text-sm leading-relaxed text-muted-light dark:text-muted-dark pt-2">
                    <?php echo nl2br(htmlspecialchars($game['description'])); ?>
                </p>
             </div>
        </div>
        <div class="md:col-span-2">
            <h2 class="text-2xl font-bold tracking-tight border-b border-border-light dark:border-border-dark pb-2">Elérhető Fordítások</h2>
            <div class="mt-6 space-y-6">
                <?php if (empty($translations)): ?>
                    <p class="text-muted-light dark:text-muted-dark">Ehhez a játékhoz még nem töltöttek fel fordítást.</p>
                <?php else: ?>
                    <?php foreach ($translations as $trans): ?>
                    <?php
                        $status_colors = '';
                        switch ($trans['status']) {
                            case 'Kész':
                                $status_colors = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400';
                                break;
                            case 'Béta':
                                $status_colors = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400';
                                break;
                            default:
                                $status_colors = 'bg-primary/10 dark:bg-primary/20 text-primary';
                                break;
                        }
                    ?>
                    <div class="p-4 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xl font-bold">Verzió: <?php echo htmlspecialchars($trans['version']); ?></p>
                                <p class="text-sm text-muted-light dark:text-muted-dark">Feltöltötte: 
                                    <a href="profile.php?id=<?php echo $trans['uploader_id']; ?>" class="text-primary hover:underline font-medium">
                                        <?php echo htmlspecialchars($trans['username']); ?>
                                    </a>
                                </p>
                                <p class="text-sm text-muted-light dark:text-muted-dark">Fordítók: <?php echo htmlspecialchars($trans['translators'] ?: 'Nincs megadva'); ?></p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php echo $status_colors; ?> flex-shrink-0">
                                <?php echo htmlspecialchars($trans['status']); ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($trans['description'])): ?>
                        <div class="pt-4 border-t border-border-light dark:border-border-dark">
                             <p class="text-sm text-muted-light dark:text-muted-dark leading-relaxed">
                                <span class="font-bold text-text-light dark:text-text-dark">Fordítók megjegyzése:</span>
                                <?php echo htmlspecialchars($trans['description']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="pt-4 border-t border-border-light dark:border-border-dark flex justify-between items-center">
                             <p class="text-xs text-muted-light dark:text-muted-dark">Dátum: <?php echo date('Y.m.d', strtotime($trans['upload_date'])); ?></p>
                             
                             <?php
                                $download_link = !empty($trans['download_url']) ? $trans['download_url'] : $trans['file_path'];
                             ?>
                             <a href="<?php echo htmlspecialchars($download_link); ?>" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="px-4 py-2 text-sm font-bold rounded-lg bg-primary text-white hover:bg-primary/90">
                                Letöltés
                             </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>