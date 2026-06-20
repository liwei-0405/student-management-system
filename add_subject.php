<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$success = "";
$error = "";
$subject_name = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name'] ?? '');

    $check_stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ?");
    $check_stmt->bind_param("s", $subject_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Subject name already exists.";
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $insert_stmt->bind_param("s", $subject_name);

        if ($insert_stmt->execute()) {
            $success = "Subject added successfully.";
            $subject_name = "";
        } else {
            $error = "Failed to add subject. Please try again.";
        }

        $insert_stmt->close();
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
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Add Subject</h2>

        <?php if ($success !== '') { ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
        <?php } ?>

        <?php if ($error !== '') { ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php } ?>

        <form action="add_subject.php" method="POST">
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name"
                       value="<?php echo e($subject_name); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Subject</button>
        </form>
    </div>
</body>
</html>
