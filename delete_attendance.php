<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

if (!isset($_GET['id'])) {
    redirectWithStatus('view_attendance.php', 'warning', 'No attendance record selected for deletion.');
}

$id = (int) $_GET['id'];
$delete_stmt = $conn->prepare("DELETE FROM attendance WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    redirectWithStatus('view_attendance.php', 'success', 'Attendance record deleted successfully.');
}

redirectWithStatus('view_attendance.php', 'danger', 'Unable to delete attendance record. Please try again.');
?>
