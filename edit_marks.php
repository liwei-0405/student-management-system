<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$error_message = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirectWithStatus('view_marks.php', 'warning', 'No valid marks record selected for editing.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
    $marks = $_POST['marks'] ?? '';

    if (!$student_id || !$subject_id) {
        $error_message = "Please select a valid student and subject.";
    } elseif (!is_numeric($marks) || (float) $marks < 0 || (float) $marks > 100) {
        $error_message = "Invalid marks. Please enter a value between 0 and 100.";
    } else {
        $marks = round((float) $marks, 2);

        $check_stmt = $conn->prepare("SELECT id FROM marks WHERE student_id = ? AND subject_id = ? AND id != ?");
        $check_stmt->bind_param("iii", $student_id, $subject_id, $id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error_message = "Another record already has marks for this specific student and subject.";
        } else {
            $update_stmt = $conn->prepare("UPDATE marks SET student_id=?, subject_id=?, marks=? WHERE id=?");
            $update_stmt->bind_param("iidi", $student_id, $subject_id, $marks, $id);

            if ($update_stmt->execute()) {
                redirectWithStatus('view_marks.php', 'success', 'Marks record updated successfully.');
            }

            $error_message = "Failed to update marks record. Please try again.";
            $update_stmt->close();
        }

        $check_stmt->close();
    }
}

$fetch_stmt = $conn->prepare("SELECT * FROM marks WHERE id = ?");
$fetch_stmt->bind_param("i", $id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();

if ($result->num_rows === 0) {
    redirectWithStatus('view_marks.php', 'warning', 'Marks record was not found.');
}

$row = $result->fetch_assoc();
$fetch_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Edit Marks</h2>

        <?php if ($error_message !== '') { ?>
            <div class="alert alert-danger"><?php echo e($error_message); ?></div>
        <?php } ?>

        <form action="edit_marks.php?id=<?php echo e($id); ?>" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select class="form-control" id="student_id" name="student_id" required>
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
                    <?php
                    $subject_query = "SELECT id, subject_name FROM subjects";
                    $subject_result = $conn->query($subject_query);
                    if ($subject_result->num_rows > 0) {
                        while($sub_row = $subject_result->fetch_assoc()) {
                            $selected = ($sub_row['id'] == $row['subject_id']) ? "selected" : "";
                            echo '<option value="' . e($sub_row['id']) . '" ' . $selected . '>' . e($sub_row['subject_name']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="marks" class="form-label">Marks (0-100)</label>
                <input type="number" step="0.01" class="form-control" id="marks" name="marks" min="0" max="100" value="<?php echo e($row['marks']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Marks</button>
        </form>

    </div>
</body>
</html>
