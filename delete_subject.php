<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

if (!isset($_GET['id'])) {
    redirectWithStatus('view_subjects.php', 'warning', 'No subject selected for deletion.');
}

$id = (int) $_GET['id'];

$subject_stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE id = ?");
$subject_stmt->bind_param("i", $id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();

if ($subject_result->num_rows === 0) {
    redirectWithStatus('view_subjects.php', 'warning', 'Subject record was not found.');
}

$subject = $subject_result->fetch_assoc();

$dependency_stmt = $conn->prepare("SELECT COUNT(*) AS marks_count FROM marks WHERE subject_id = ?");
$dependency_stmt->bind_param("i", $id);
$dependency_stmt->execute();
$dependencies = $dependency_stmt->get_result()->fetch_assoc();

if ((int) $dependencies['marks_count'] > 0) {
    redirectWithStatus('view_subjects.php', 'danger', 'Cannot delete ' . $subject['subject_name'] . ' because related marks records still exist.');
}

$delete_stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    redirectWithStatus('view_subjects.php', 'success', 'Subject deleted successfully.');
}

redirectWithStatus('view_subjects.php', 'danger', 'Unable to delete subject. Please try again.');
?>
