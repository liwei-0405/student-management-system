<?php
include 'db.php';
include 'includes/auth.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM attendance WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Attendance deleted successfully";
        header('Location: view_attendance.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
