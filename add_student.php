<?php
include 'db.php';
include 'includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Add Student</h2>
        <form action="add_student.php" method="POST">
            <div class="mb-3">
                <label for="enrollment_no" class="form-label">Enrollment No</label>
                <input type="text" class="form-control" id="enrollment_no" name="enrollment_no" required>
            </div>
            <div class="mb-3">
                <label for="student_name" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="student_name" name="student_name" required>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enrollment_no = $_POST['enrollment_no'];
            $student_name = $_POST['student_name'];
            $department = $_POST['department'];
            $phone = $_POST['phone'];

            // Insert the data into the students table
            $sql = "INSERT INTO students (enrollment_no, student_name, department, phone) VALUES ('$enrollment_no', '$student_name', '$department', '$phone')";
            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success'>Student added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
        ?>
    </div>
</body>
</html>
