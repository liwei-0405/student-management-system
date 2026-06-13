<?php
include 'db.php';
include 'includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <header>
            <h1><b>Welcome to the Student Management System</b></h1>
        </header>
        <hr>

        <div class="dashboard-container">
            <h2>Manage Students Easily</h2>
            <p>Track, update, and manage all student data in one place.</p>
            
            <a href="view_students.php" class="btn-custom">View Students</a>
            <a href="add_student.php" class="btn-custom">Add New Student</a>
        </div>
    </div>
</body>
</html>
