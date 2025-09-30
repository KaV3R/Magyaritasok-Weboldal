<?php
require_once 'db.php';
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); exit();
}

function send_to_discord($payload) {
    if (!defined('DISCORD_WEBHOOK_URL') || DISCORD_WEBHOOK_URL === 'IDE_MASOLD_A_DISCORD_WEBHOOK_URL-EDET') {
        return;
    }
    $json_payload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $ch = curl_init(DISCORD_WEBHOOK_URL);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => ['Content-type: application/json'],
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $json_payload,
        CURLOPT_RETURNTRANSFER => 1
    ]);
    curl_exec($ch);
    curl_close($ch);
}

$errors = []; $success_message = '';
$games = $conn->query("SELECT id, title FROM games ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $game_id = $_POST['game_id'];
    $new_game_title = trim($_POST['new_game_title']);
    $user_id = $_SESSION['user_id'];
    $version = trim($_POST['version']);
    $status = $_POST['status'];
    $translators = trim($_POST['translators']);
    $description = trim($_POST['description']);
    $download_url = trim($_POST['download_url']);
    $file_path = null;

    if (!preg_match('/^[0-9.]+$/', $version)) {
        $errors[] = "A verziószám csak számokat és pontot tartalmazhat! (pl. 1.0 vagy 2.5.1)";
    }
    
    if (empty($download_url) && (!isset($_FILES['translation-file']) || $_FILES['translation-file']['error'] != 0)) {
        $errors[] = "Vagy egy letöltési link megadása, vagy egy fájl feltöltése kötelező!";
    }
    if (!empty($download_url) && !filter_var($download_url, FILTER_VALIDATE_URL)) {
        $errors[] = "A megadott letöltési link érvénytelen formátumú.";
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            $game_info_for_discord = [];

            if (!empty($new_game_title) && $game_id === 'new') {
                $placeholder_cover = 'https://via.placeholder.com/278x370.png?text=' . urlencode($new_game_title);
                $stmt_game = $conn->prepare("INSERT INTO games (title, cover_image, description) VALUES (?, ?, 'Nincs még leírás.')");
                $stmt_game->bind_param("ss", $new_game_title, $placeholder_cover);
                $stmt_game->execute();
                $game_id = $conn->insert_id;
                $stmt_game->close();
                $game_info_for_discord = ['title' => $new_game_title, 'cover_image' => $placeholder_cover];
            } elseif (empty($game_id) || $game_id === 'new') {
                throw new Exception("Kérlek, válassz egy játékot, vagy adj meg egy újat!");
            } else {
                 $stmt_game_info = $conn->prepare("SELECT title, cover_image FROM games WHERE id = ?");
                 $stmt_game_info->bind_param("i", $game_id);
                 $stmt_game_info->execute();
                 $game_info_for_discord = $stmt_game_info->get_result()->fetch_assoc();
                 $stmt_game_info->close();
            }

            if (empty($download_url) && isset($_FILES['translation-file']) && $_FILES['translation-file']['error'] == 0) {
                $target_dir = "uploads/";
                $original_filename = basename($_FILES["translation-file"]["name"]);
                $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
                if (!in_array($file_extension, ['zip', 'rar', '7z'])) { throw new Exception("Csak ZIP, RAR, 7Z fájlok tölthetők fel."); }
                $safe_filename = uniqid('translation_', true) . '.' . $file_extension;
                $file_path = $target_dir . $safe_filename;
                if (!move_uploaded_file($_FILES["translation-file"]["tmp_name"], $file_path)) { throw new Exception("Hiba történt a fájl feltöltése során.");}
            }
            
            $stmt_trans = $conn->prepare("INSERT INTO translations (user_id, game_id, version, status, description, translators, file_path, download_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_trans->bind_param("iissssss", $user_id, $game_id, $version, $status, $description, $translators, $file_path, $download_url);
            $stmt_trans->execute();
            $stmt_trans->close();

            $conn->commit();
            $success_message = "A fordítás sikeresen feltöltve!";
            
            $status_color = 3447003;
            if ($status == 'Kész') $status_color = 3066993;
            if ($status == 'Béta') $status_color = 15105570;
            $blank_field = ['name' => "\u{200b}", 'value' => "\u{200b}", 'inline' => false];
            $fields = [['name' => 'Verzió', 'value' => htmlspecialchars($version), 'inline' => true], ['name' => 'Állapot', 'value' => htmlspecialchars($status), 'inline' => true]];
            if (!empty($description)) { $fields[] = $blank_field; $fields[] = ['name' => 'Fordítók megjegyzése', 'value' => htmlspecialchars($description), 'inline' => false]; }
            $fields[] = $blank_field; 
            $fields[] = ['name' => 'Fordítók', 'value' => !empty($translators) ? htmlspecialchars($translators) : 'Nincs megadva', 'inline' => false];
            $discord_payload = ['username'   => 'Magyarítás Figyelő', 'avatar_url' => DISCORD_AVATAR_URL, 'embeds' => [['title' => 'Új fordítás érkezett!', 'description' => "**" . htmlspecialchars($game_info_for_discord['title']) . "**", 'url' => BASE_URL . "/game.php?id=" . $game_id, 'color' => $status_color, 'fields' => $fields, 'thumbnail' => ['url' => htmlspecialchars($game_info_for_discord['cover_image'])], 'footer'      => ['text' => 'Magyarítások Portál'], 'timestamp'   => date('c')]], 'components' => [['type' => 1, 'components' => [['type' => 2, 'style' => 5, 'label' => 'Letöltés az oldalon', 'url' => BASE_URL . "/game.php?id=" . $game_id]]]]];
            send_to_discord($discord_payload);

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = $e->getMessage();
        }
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
             <label class="block text-sm font-medium mb-1" for="new_game_title">Új játék neve</label>
             <input type="text" name="new_game_title" id="new_game_title" class="w-full form-input rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="version">Verzió</label>
            <input type="text" name="version" id="version" class="w-full form-input rounded-md" pattern="[0-9.]+" title="Csak számokat és pontot tartalmazhat." placeholder="pl. 1.2.3" required>
        </div>
        <div><label class="block text-sm font-medium mb-1" for="status">Állapot</label><select class="w-full form-select rounded-md" name="status" required><option>Folyamatban</option><option>Béta</option><option>Kész</option></select></div>
        <div><label class="block text-sm font-medium mb-1" for="description">Leírás (Fordítók megjegyzése)</label><textarea class="w-full form-textarea rounded-md" name="description" rows="4"></textarea></div>
        <div><label class="block text-sm font-medium mb-1" for="translators">Fordító(k)</label><input class="w-full form-input rounded-md" name="translators" placeholder="pl. Minta János, Kovács Eszter"></div>
        
        <div>
            <label class="block text-sm font-medium mb-1" for="download_url">Letöltési link (opcionális)</label>
            <input type="url" name="download_url" id="download_url" class="w-full form-input rounded-md" placeholder="https://...">
            <p class="text-xs text-muted-light dark:text-muted-dark mt-1">Ha ezt kitöltöd, nem kell fájlt feltöltened.</p>
        </div>
        <div class="text-center text-sm font-bold text-muted-light dark:text-muted-dark">VAGY</div>
        <div>
            <label class="block text-sm font-medium mb-1">Fordítási fájl feltöltése</label>
            <input name="translation-file" type="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20"/>
            <p class="text-xs text-muted-light dark:text-muted-dark mt-1">Hagyd üresen, ha letöltési linket adtál meg.</p>
        </div>

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