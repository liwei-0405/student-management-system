<?php
include 'db.php';
include 'includes/auth.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM marks WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Marks deleted successfully";
        header('Location: view_marks.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
