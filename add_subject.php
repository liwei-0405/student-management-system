<?php 
include 'db.php';

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";
$subject_name = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name']);

    $check_sql = "SELECT id FROM subjects WHERE subject_name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $subject_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Subject name already exists.";
    } else {
        $sql = "INSERT INTO subjects (subject_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $subject_name);

        if ($stmt->execute()) {
            $success = "Subject added successfully.";
            $subject_name = "";
        } else {
            $error = "Failed to add subject. Please try again.";
        }

        $stmt->close();
    }

    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>

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
        <h2>Add Subject</h2>

        <?php if (!empty($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form action="add_subject.php" method="POST">
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" 
                       class="form-control" 
                       id="subject_name" 
                       name="subject_name" 
                       value="<?php echo htmlspecialchars($subject_name); ?>" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary">Add Subject</button>
        </form>
    </div>
</body>
</html>