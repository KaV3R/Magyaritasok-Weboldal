<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit("Hozzáférés megtagadva!");
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_translations.php"); exit();
}
$translation_id = intval($_GET['id']);

$stmt_path = $conn->prepare("SELECT file_path FROM translations WHERE id = ?");
$stmt_path->bind_param("i", $translation_id);
$stmt_path->execute();
$result = $stmt_path->get_result()->fetch_assoc();
$stmt_path->close();

if ($result && file_exists($result['file_path'])) {
    unlink($result['file_path']);
}

$stmt_delete = $conn->prepare("DELETE FROM translations WHERE id = ?");
$stmt_delete->bind_param("i", $translation_id);
$stmt_delete->execute();
$stmt_delete->close();

header("Location: admin_translations.php");
exit();
?>