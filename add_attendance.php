<?php
session_start();
include 'db.php';
include 'includes/auth.php';

$error_message = '';
$success_message = '';

// Backend Validation Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']); // Subject added
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    $current_date = date('Y-m-d');
    $min_date = date('Y-m-d', strtotime('-30 days'));

    if ($status !== 'Present' && $status !== 'Absent') {
        $error_message = "Invalid attendance status selected.";
    } elseif ($attendance_date > $current_date) {
        $error_message = "Attendance date cannot be in the future.";
    } elseif ($attendance_date < $min_date) {
        $error_message = "Attendance date cannot be more than 30 days in the past.";
    } else {
        // Convert string back to database integer (1 for Present, 0 for Absent)
        $db_status = ($status === 'Present') ? 1 : 0;

        // Prepared statement to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, subject_id, attendance_date, attendance) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $student_id, $subject_id, $attendance_date, $db_status);
        
        if ($stmt->execute()) {
            $success_message = "Attendance added successfully.";
        } else {
            $error_message = "Error: Failed to save attendance record.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Add Attendance</h2>
        
        <?php 
        if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>";
        if ($success_message) echo "<div class='alert alert-success'>$success_message</div>";
        ?>

        <form action="add_attendance.php" method="POST">
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
                <label for="attendance_date" class="form-label">Attendance Date</label>
                <input type="date" class="form-control" id="attendance_date" name="attendance_date" 
                       max="<?php echo date('Y-m-d'); ?>" 
                       min="<?php echo date('Y-m-d', strtotime('-30 days')); ?>" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Attendance Status</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusPresent" value="Present" checked required>
                        <label class="form-check-label" for="statusPresent">Present</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusAbsent" value="Absent" required>
                        <label class="form-check-label" for="statusAbsent">Absent</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Add Attendance</button>
        </form>
    </div>
</body>
</html>