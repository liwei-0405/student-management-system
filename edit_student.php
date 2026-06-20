<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$error = "";
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirectWithStatus('view_students.php', 'warning', 'No valid student selected for editing.');
}

$student_stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$student_stmt->bind_param("i", $id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();

if ($student_result->num_rows === 0) {
    redirectWithStatus('view_students.php', 'warning', 'Student record was not found.');
}

$student = $student_result->fetch_assoc();
$student_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_no = trim($_POST['enrollment_no'] ?? '');
    $student_name = trim($_POST['student_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $error = "Invalid phone number. Phone number must contain 10 to 11 digits only.";
    } else {
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE enrollment_no = ? AND id != ?");
        $check_stmt->bind_param("si", $enrollment_no, $id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Enrollment number already exists.";
        } else {
            $update_stmt = $conn->prepare(
                "UPDATE students SET enrollment_no = ?, student_name = ?, department = ?, phone = ? WHERE id = ?"
            );
            $update_stmt->bind_param("ssssi", $enrollment_no, $student_name, $department, $phone, $id);

            if ($update_stmt->execute()) {
                redirectWithStatus('view_students.php', 'success', 'Student updated successfully.');
            }

            $error = "Failed to update student. Please try again.";
            $update_stmt->close();
        }

        $check_stmt->close();
    }

    $student = [
        'enrollment_no' => $enrollment_no,
        'student_name' => $student_name,
        'department' => $department,
        'phone' => $phone,
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Edit Student</h2>

        <?php if ($error !== '') { ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php } ?>

        <form action="edit_student.php?id=<?php echo e($id); ?>" method="POST">
            <div class="mb-3">
                <label for="enrollment_no" class="form-label">Enrollment No</label>
                <input type="text" class="form-control" id="enrollment_no" name="enrollment_no"
                       value="<?php echo e($student['enrollment_no']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="student_name" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="student_name" name="student_name"
                       value="<?php echo e($student['student_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-control" id="department" name="department" required>
                    <option value="">Select Department</option>
                    <?php
                    $departments = [
                        'Computer Science',
                        'Information Technology',
                        'Software Engineering',
                        'Business Management',
                        'Accounting',
                    ];
                    foreach ($departments as $option) {
                        $selected = $student['department'] === $option ? 'selected' : '';
                        echo '<option value="' . e($option) . '" ' . $selected . '>' . e($option) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone"
                       value="<?php echo e($student['phone']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Student</button>
        </form>
    </div>
</body>
</html>
