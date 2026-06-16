<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

if (!isset($_GET['id'])) {
    redirectWithStatus('view_marks.php', 'warning', 'No marks record selected for deletion.');
}

$id = (int) $_GET['id'];
$delete_stmt = $conn->prepare("DELETE FROM marks WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    redirectWithStatus('view_marks.php', 'success', 'Marks record deleted successfully.');
}

redirectWithStatus('view_marks.php', 'danger', 'Unable to delete marks record. Please try again.');
?>
