<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

if (!isset($_GET['id'])) {
    redirectWithStatus('view_students.php', 'warning', 'No student selected for deletion.');
}

$id = (int) $_GET['id'];

$student_stmt = $conn->prepare("SELECT student_name, enrollment_no FROM students WHERE id = ?");
$student_stmt->bind_param("i", $id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();

if ($student_result->num_rows === 0) {
    redirectWithStatus('view_students.php', 'warning', 'Student record was not found.');
}

$student = $student_result->fetch_assoc();

$dependency_stmt = $conn->prepare("
    SELECT
        (SELECT COUNT(*) FROM attendance WHERE student_id = ?) AS attendance_count,
        (SELECT COUNT(*) FROM marks WHERE student_id = ?) AS marks_count
");
$dependency_stmt->bind_param("ii", $id, $id);
$dependency_stmt->execute();
$dependencies = $dependency_stmt->get_result()->fetch_assoc();

if ((int) $dependencies['attendance_count'] > 0 || (int) $dependencies['marks_count'] > 0) {
    $message = "Cannot delete " . $student['student_name'] . " (" . $student['enrollment_no'] . ") because related attendance or marks records still exist.";
    redirectWithStatus('view_students.php', 'danger', $message);
}

$delete_stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    redirectWithStatus('view_students.php', 'success', 'Student deleted successfully.');
}

redirectWithStatus('view_students.php', 'danger', 'Unable to delete student. Please try again.');
?>
