<?php 
$page_title = "Főoldal";
include 'header.php'; 

$result = $conn->query("SELECT id, title, cover_image FROM games ORDER BY title ASC");
$games = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="text-center max-w-2xl mx-auto">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight">
            Találd meg a következő <span class="text-primary">játékfordításodat</span>
        </h1>
        <p class="mt-4 text-lg text-muted-light dark:text-muted-dark">
            Böngéssz a közösség által készített játékfordítások hatalmas gyűjteményében.
        </p>
    </div>
    <div class="mt-16">
        <h2 class="text-2xl font-bold tracking-tight">Játékok</h2>
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
            <?php foreach ($games as $game): ?>
            <a href="game.php?id=<?php echo $game['id']; ?>" class="group flex flex-col gap-3">
                <div class="relative overflow-hidden rounded-lg">
                    <img alt="<?php echo htmlspecialchars($game['title']); ?> borító" class="aspect-[3/4] w-full object-cover group-hover:scale-105 transition-transform duration-300" src="<?php echo htmlspecialchars($game['cover_image']); ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-3">
                        <h3 class="font-bold text-white leading-tight"><?php echo htmlspecialchars($game['title']); ?></h3>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>