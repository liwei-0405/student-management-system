<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$error_message = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirectWithStatus('view_attendance.php', 'warning', 'No valid attendance record selected for editing.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
    $attendance_date = $_POST['attendance_date'] ?? '';
    $status = $_POST['status'] ?? '';

    $current_date = date('Y-m-d');
    $min_date = date('Y-m-d', strtotime('-30 days'));

    if (!$student_id || !$subject_id) {
        $error_message = "Please select a valid student and subject.";
    } elseif ($status !== 'Present' && $status !== 'Absent') {
        $error_message = "Invalid attendance status selected.";
    } elseif ($attendance_date > $current_date) {
        $error_message = "Attendance date cannot be in the future.";
    } elseif ($attendance_date < $min_date) {
        $error_message = "Attendance date cannot be more than 30 days in the past.";
    } else {
        $db_status = ($status === 'Present') ? 1 : 0;

        $stmt = $conn->prepare("UPDATE attendance SET student_id=?, subject_id=?, attendance_date=?, attendance=? WHERE id=?");
        $stmt->bind_param("iisii", $student_id, $subject_id, $attendance_date, $db_status, $id);

        if ($stmt->execute()) {
            redirectWithStatus('view_attendance.php', 'success', 'Attendance record updated successfully.');
        }

        $error_message = "Failed to update attendance record. Please try again.";
        $stmt->close();
    }
}

$fetch_stmt = $conn->prepare("SELECT * FROM attendance WHERE id = ?");
$fetch_stmt->bind_param("i", $id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();

if ($result->num_rows === 0) {
    redirectWithStatus('view_attendance.php', 'warning', 'Attendance record was not found.');
}

$row = $result->fetch_assoc();
$fetch_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Edit Attendance</h2>

        <?php if ($error_message !== '') { ?>
            <div class="alert alert-danger"><?php echo e($error_message); ?></div>
        <?php } ?>

        <form action="edit_attendance.php?id=<?php echo e($id); ?>" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select class="form-control" id="student_id" name="student_id" required>
                    <option value="">Select Student</option>
                    <?php
                    $student_query = "SELECT id, student_name, enrollment_no FROM students";
                    $student_result = $conn->query($student_query);
                    if ($student_result->num_rows > 0) {
                        while($s_row = $student_result->fetch_assoc()) {
                            $selected = ($s_row['id'] == $row['student_id']) ? "selected" : "";
                            echo '<option value="' . e($s_row['id']) . '" ' . $selected . '>' . e($s_row['student_name'] . ' (' . $s_row['enrollment_no'] . ')') . '</option>';
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
                        while($sub_row = $subject_result->fetch_assoc()) {
                            $selected = (isset($row['subject_id']) && $sub_row['id'] == $row['subject_id']) ? "selected" : "";
                            echo '<option value="' . e($sub_row['id']) . '" ' . $selected . '>' . e($sub_row['subject_name']) . '</option>';
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
                       value="<?php echo e($row['attendance_date']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Attendance Status</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusPresent" value="Present" <?php echo ($row['attendance'] == 1) ? 'checked' : ''; ?> required>
                        <label class="form-check-label" for="statusPresent">Present</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusAbsent" value="Absent" <?php echo ($row['attendance'] == 0) ? 'checked' : ''; ?> required>
                        <label class="form-check-label" for="statusAbsent">Absent</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Attendance</button>
        </form>

    </div>
</body>
</html>
