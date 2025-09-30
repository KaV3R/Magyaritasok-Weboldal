<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin.php"); exit();
}
$user_id_to_delete = intval($_GET['id']);

if ($user_id_to_delete == 1) {
    die("A fő adminisztrátor nem törölhető!");
}

$conn->begin_transaction();
try {
    $stmt_files = $conn->prepare("SELECT file_path FROM translations WHERE user_id = ?");
    $stmt_files->bind_param("i", $user_id_to_delete);
    $stmt_files->execute();
    $files_to_delete = $stmt_files->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_files->close();
    foreach ($files_to_delete as $file) {
        if (!empty($file['file_path']) && file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }
    }

    $stmt_trans = $conn->prepare("DELETE FROM translations WHERE user_id = ?");
    $stmt_trans->bind_param("i", $user_id_to_delete);
    $stmt_trans->execute();
    $stmt_trans->close();

    $stmt_posts = $conn->prepare("UPDATE forum_posts SET user_id = NULL WHERE user_id = ?");
    $stmt_posts->bind_param("i", $user_id_to_delete);
    $stmt_posts->execute();
    $stmt_posts->close();

    $stmt_topics = $conn->prepare("UPDATE forum_topics SET user_id = NULL WHERE user_id = ?");
    $stmt_topics->bind_param("i", $user_id_to_delete);
    $stmt_topics->execute();
    $stmt_topics->close();

    $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_id_to_delete);
    $stmt_user->execute();
    $stmt_user->close();

    $conn->commit();

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    die("Hiba történt a törlés során: " . $exception->getMessage());
}

header("Location: admin.php");
exit();
?>