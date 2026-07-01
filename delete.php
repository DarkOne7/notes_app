<?php
session_start();
include './db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=' . urlencode('Invalid note id'));
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("DELETE FROM `notes` WHERE `id` = ? AND `user_id` = ?");
if ($stmt) {
    $stmt->bind_param("ii", $id, $user_id);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        header('Location: index.php?deleted=1');
        exit;
    } else {
        header('Location: index.php?error=' . urlencode('Failed to delete note'));
        exit;
    }
} else {
    header('Location: index.php?error=' . urlencode('Database error: ' . $conn->error));
    exit;
}
?>
