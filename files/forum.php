<?php
$page_title = "Fórum";
include 'header.php';

$query = "SELECT 
            t.id, 
            t.title, 
            t.created_at, 
            u.id as author_id,
            u.username as author_name,
            (SELECT COUNT(*) FROM forum_posts p WHERE p.topic_id = t.id) as post_count,
            (SELECT MAX(p.created_at) FROM forum_posts p WHERE p.topic_id = t.id) as last_post_date,
            (SELECT lu.id FROM forum_posts lp LEFT JOIN users lu ON lp.user_id = lu.id WHERE lp.topic_id = t.id ORDER BY lp.created_at DESC LIMIT 1) as last_poster_id,
            (SELECT lu.username FROM forum_posts lp LEFT JOIN users lu ON lp.user_id = lu.id WHERE lp.topic_id = t.id ORDER BY lp.created_at DESC LIMIT 1) as last_poster_name
          FROM forum_topics t
          LEFT JOIN users u ON t.user_id = u.id
          ORDER BY last_post_date DESC";
$topics = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<div class="container mx-auto w-full max-w-5xl py-8 px-4">
    <header class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">Közösségi Fórum</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="create_topic.php" class="px-4 py-2 rounded-lg bg-primary text-white font-bold hover:bg-opacity-90 transition-colors">
                Új téma indítása
            </a>
        <?php else: ?>
            <a href="login_register.php" class="px-4 py-2 rounded-lg bg-primary/80 text-white font-bold" title="Új téma indításához be kell jelentkezned">
                Új téma indítása
            </a>
        <?php endif; ?>
    </header>
    <div class="overflow-hidden rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-background-light dark:bg-surface-dark/50">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-muted-light dark:text-muted-dark w-2/5">Téma</th>
                        <th class="px-6 py-4 text-sm font-semibold text-muted-light dark:text-muted-dark text-center">Válaszok</th>
                        <th class="px-6 py-4 text-sm font-semibold text-muted-light dark:text-muted-dark">Utolsó hozzászólás</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-light dark:divide-border-dark">
                    <?php if (empty($topics)): ?>
                        <tr><td colspan="3" class="px-6 py-4 text-center text-muted-light dark:text-muted-dark">Még nincsenek témák. Légy te az első!</td></tr>
                    <?php else: ?>
                        <?php foreach($topics as $topic): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <a class="font-semibold hover:text-primary transition-colors" href="topic.php?id=<?php echo $topic['id']; ?>">
                                    <?php echo htmlspecialchars($topic['title']); ?>
                                </a>
                                <p class="text-xs text-muted-light dark:text-muted-dark">
                                    Indította: 
                                    <?php if ($topic['author_name']): ?>
                                        <a href="profile.php?id=<?php echo $topic['author_id']; ?>" class="font-medium text-primary hover:underline">
                                            <?php echo htmlspecialchars($topic['author_name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="font-medium text-gray-500 italic">Törölt felhasználó</span>
                                    <?php endif; ?>
                                    , <?php echo date('Y.m.d', strtotime($topic['created_at'])); ?>
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center text-muted-light dark:text-muted-dark"><?php echo $topic['post_count'] > 0 ? $topic['post_count'] - 1 : 0; ?></td>
                            <td class="px-6 py-4 text-sm text-muted-light dark:text-muted-dark">
                                <?php if ($topic['last_post_date']): ?>
                                    <div><?php echo date('Y.m.d H:i', strtotime($topic['last_post_date'])); ?></div>
                                    <div class="text-xs">Írta: 
                                        <?php if ($topic['last_poster_name']): ?>
                                            <a href="profile.php?id=<?php echo $topic['last_poster_id']; ?>" class="font-medium text-primary hover:underline">
                                                <?php echo htmlspecialchars($topic['last_poster_name']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="font-medium text-gray-500 italic">Törölt felhasználó</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span>Nincs még hozzászólás</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>