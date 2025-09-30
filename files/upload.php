<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); exit();
}

$errors = []; $success_message = '';
$games = $conn->query("SELECT id, title FROM games ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $game_id = $_POST['game_id'];
    $new_game_title = trim($_POST['new_game_title']);
    $user_id = $_SESSION['user_id'];
    
    $conn->begin_transaction();

    try {
        if (!empty($new_game_title) && $game_id === 'new') {
            $stmt_game = $conn->prepare("INSERT INTO games (title, cover_image) VALUES (?, ?)");
            $placeholder_cover = 'https://via.placeholder.com/278x370.png?text=' . urlencode($new_game_title);
            $stmt_game->bind_param("ss", $new_game_title, $placeholder_cover);
            $stmt_game->execute();
            $game_id = $conn->insert_id;
            $stmt_game->close();
        } elseif (empty($game_id) || $game_id === 'new') {
            throw new Exception("Kérlek, válassz egy játékot, vagy adj meg egy újat!");
        }

        if (isset($_FILES['translation-file']) && $_FILES['translation-file']['error'] == 0) {
            $target_dir = "uploads/";
            $original_filename = basename($_FILES["translation-file"]["name"]);
            $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
            if (!in_array($file_extension, ['zip', 'rar', '7z'])) { throw new Exception("Csak ZIP, RAR, 7Z fájlok tölthetők fel."); }
            $safe_filename = uniqid('translation_', true) . '.' . $file_extension;
            $target_file = $target_dir . $safe_filename;
            
            if (!move_uploaded_file($_FILES["translation-file"]["tmp_name"], $target_file)) {
                 throw new Exception("Hiba történt a fájl feltöltése során.");
            }
        } else {
             throw new Exception("Kérlek, válassz egy fájlt a feltöltéshez.");
        }

        $stmt_trans = $conn->prepare("INSERT INTO translations (user_id, game_id, version, status, description, translators, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_trans->bind_param("iisssss", $user_id, $game_id, $_POST['version'], $_POST['status'], $_POST['description'], $_POST['translators'], $target_file);
        $stmt_trans->execute();
        $stmt_trans->close();

        $conn->commit();
        $success_message = "A fordítás sikeresen feltöltve!";

    } catch (Exception $e) {
        $conn->rollback();
        $errors[] = $e->getMessage();
    }
}

$page_title = "Fordítás Feltöltése";
include 'header.php';
?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8"><h2 class="text-3xl font-bold">Fordítás beküldése</h2></div>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <?php foreach ($errors as $error): ?><p><?php echo $error; ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>
    
    <form class="space-y-6 bg-surface-light dark:bg-surface-dark p-8 rounded-lg shadow-md" method="POST" enctype="multipart/form-data">
        <div>
            <label class="block text-sm font-medium mb-1" for="game_id">Játék neve</label>
            <select class="w-full form-select rounded-md" id="game_id" name="game_id">
                <option value="">Válassz egy meglévő játékot...</option>
                <?php foreach($games as $game): ?><option value="<?php echo $game['id']; ?>"><?php echo htmlspecialchars($game['title']); ?></option><?php endforeach; ?>
                <option value="new">-- Új játék hozzáadása --</option>
            </select>
        </div>
        <div id="new-game-container" class="hidden">
             <label class="block text-sm font-medium mb-1" for="new_game_title">Új játék neve (ha nem szerepel a listán)</label>
             <input type="text" name="new_game_title" id="new_game_title" class="w-full form-input rounded-md">
        </div>
        <div><label class="block text-sm font-medium mb-1" for="version">Verzió</label><input class="w-full form-input rounded-md" name="version" required></div>
        <div><label class="block text-sm font-medium mb-1" for="status">Állapot</label><select class="w-full form-select rounded-md" name="status" required><option>Folyamatban</option><option>Béta</option><option>Kész</option></select></div>
        <div><label class="block text-sm font-medium mb-1" for="description">Leírás</label><textarea class="w-full form-textarea rounded-md" name="description" rows="4"></textarea></div>
        <div><label class="block text-sm font-medium mb-1" for="translators">Fordító(k)</label><input class="w-full form-input rounded-md" name="translators"></div>
        <div><label class="block text-sm font-medium mb-1">Fordítási fájl</label><input name="translation-file" type="file" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20"/></div>
        <div class="flex justify-end pt-4"><button class="px-6 py-2 rounded-md bg-primary text-white hover:bg-primary/90" type="submit">Feltöltés</button></div>
    </form>
</div>
<script>
    document.getElementById('game_id').addEventListener('change', function() {
        var container = document.getElementById('new-game-container');
        if (this.value === 'new') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    });
</script>
<?php include 'footer.php'; ?>