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
        <h2>Students List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Enrollment No</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM students";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['enrollment_no']}</td>
                                <td>{$row['student_name']}</td>
                                <td>{$row['department']}</td>
                                <td>{$row['phone']}</td>
                                <td>
                                    <a href='edit_student.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_student.php?id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
