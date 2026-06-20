<?php
session_start();
include 'db.php';
include 'includes/auth.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $marks = $_POST['marks'];

    // Member 3: Range Validation
    if (!is_numeric($marks) || $marks < 0 || $marks > 100) {
        $error_message = "Invalid marks. Please enter a value between 0 and 100.";
    } else {
        $marks = floatval($marks);

        // Member 3: Prevent Duplicate Records
        $check_stmt = $conn->prepare("SELECT id FROM marks WHERE student_id = ? AND subject_id = ?");
        $check_stmt->bind_param("ii", $student_id, $subject_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error_message = "Marks already exist for this student in this subject. Please edit the existing record.";
        } else {
            // Prepared Statement for Security
            $insert_stmt = $conn->prepare("INSERT INTO marks (student_id, subject_id, marks) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iid", $student_id, $subject_id, $marks);
            
            if ($insert_stmt->execute()) {
                $success_message = "Marks added successfully.";
            } else {
                $error_message = "Error: Failed to save marks record.";
            }
        }
    }
}
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
        <h2>Add Student Marks</h2>
        
        <?php 
        if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>";
        if ($success_message) echo "<div class='alert alert-success'>$success_message</div>";
        ?>

        <form action="add_marks.php" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select class="form-control" id="student_id" name="student_id" required>
                    <option value="">Select Student</option>
                    <?php
                    $student_query = "SELECT id, student_name, enrollment_no FROM students ORDER BY student_name ASC";
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
                    $subject_query = "SELECT id, subject_name FROM subjects ORDER BY subject_name ASC";
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
                <label for="marks" class="form-label">Marks (0-100)</label>
                <input type="number" step="0.1" class="form-control" id="marks" name="marks" min="0" max="100" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Marks</button>
        </form>
    </div>
</body>
</html>