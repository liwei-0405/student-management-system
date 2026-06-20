<?php 
include 'db.php'; 
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";
$enrollment_no = "";
$student_name = "";
$department = "";
$phone = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_no = trim($_POST['enrollment_no']);
    $student_name = trim($_POST['student_name']);
    $department = trim($_POST['department']);
    $phone = trim($_POST['phone']);

    if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $error = "Invalid phone number. Phone number must contain 10 to 11 digits only.";
    } else {
        $check_sql = "SELECT id FROM students WHERE enrollment_no = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $enrollment_no);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Enrollment number already exists.";
        } else {
            $sql = "INSERT INTO students (enrollment_no, student_name, department, phone) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $enrollment_no, $student_name, $department, $phone);

            if ($stmt->execute()) {
                $success = "Student added successfully.";

                $enrollment_no = "";
                $student_name = "";
                $department = "";
                $phone = "";
            } else {
                $error = "Failed to add student. Please try again.";
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}
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
    <div class="sidebar">
        <h4><b>Navigation</b></h4>
        <a href="index.php">Home</a>
        <a href="add_attendance.php">Add Attendance</a>
        <a href="view_attendance.php">View Attendance</a>
        <a href="add_marks.php">Add Marks</a>
        <a href="view_marks.php">View Marks</a>
        <a href="add_student.php">Add Student</a>
        <a href="view_students.php">View Students</a>
        <a href="add_subject.php">Add Subject</a>
        <a href="view_subjects.php">View Subjects</a>
        <a href="logout.php" class="nav-link" style="color: black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <h2>Add Student</h2>

        <?php if (!empty($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form action="add_student.php" method="POST">
            <div class="mb-3">
                <label for="enrollment_no" class="form-label">Enrollment No</label>
                <input type="text" 
                       class="form-control" 
                       id="enrollment_no" 
                       name="enrollment_no" 
                       value="<?php echo htmlspecialchars($enrollment_no); ?>" 
                       required>
            </div>

            <div class="mb-3">
                <label for="student_name" class="form-label">Student Name</label>
                <input type="text" 
                       class="form-control" 
                       id="student_name" 
                       name="student_name" 
                       value="<?php echo htmlspecialchars($student_name); ?>" 
                       required>
            </div>

            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select class="form-control" id="department" name="department" required>
                    <option value="">Select Department</option>
                    <option value="Computer Science" <?php if ($department == "Computer Science") echo "selected"; ?>>Computer Science</option>
                    <option value="Information Technology" <?php if ($department == "Information Technology") echo "selected"; ?>>Information Technology</option>
                    <option value="Software Engineering" <?php if ($department == "Software Engineering") echo "selected"; ?>>Software Engineering</option>
                    <option value="Business Management" <?php if ($department == "Business Management") echo "selected"; ?>>Business Management</option>
                    <option value="Accounting" <?php if ($department == "Accounting") echo "selected"; ?>>Accounting</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" 
                       class="form-control" 
                       id="phone" 
                       name="phone" 
                       value="<?php echo htmlspecialchars($phone); ?>" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
</body>
</html>