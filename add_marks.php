<?php
include 'db.php';
include 'includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Add Marks</h2>
        <form action="add_marks.php" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select class="form-control" id="student_id" name="student_id" required>
                    <option value="">Select Student</option>
                    <?php
                    $student_query = "SELECT id, student_name, enrollment_no FROM students";
                    $student_result = $conn->query($student_query);
                    if ($student_result->num_rows > 0) {
                        while($row = $student_result->fetch_assoc()) {
                            echo "<option value='".$row['id']."'>".$row['student_name']." (".$row['enrollment_no'].")</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subject_id" class="form-label">Subject</label>
                <select class="form-control" id="subject_id" name="subject_id" required>
                    <option value="">Select Subject</option>
                    <?php
                    $subject_query = "SELECT id, subject_name FROM subjects";
                    $subject_result = $conn->query($subject_query);
                    if ($subject_result->num_rows > 0) {
                        while($row = $subject_result->fetch_assoc()) {
                            echo "<option value='".$row['id']."'>".$row['subject_name']."</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="marks" class="form-label">Marks</label>
                <input type="number" class="form-control" id="marks" name="marks" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Marks</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $student_id = $_POST['student_id'];
            $subject_id = $_POST['subject_id'];
            $marks = $_POST['marks'];

            $sql = "INSERT INTO marks (student_id, subject_id, marks) VALUES ('$student_id', '$subject_id', '$marks')";
            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success'>Marks added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
        ?>
    </div>
</body>
</html>
