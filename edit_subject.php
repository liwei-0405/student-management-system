<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$error = "";
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirectWithStatus('view_subjects.php', 'warning', 'No valid subject selected for editing.');
}

$subject_stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
$subject_stmt->bind_param("i", $id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();

if ($subject_result->num_rows === 0) {
    redirectWithStatus('view_subjects.php', 'warning', 'Subject record was not found.');
}

$subject = $subject_result->fetch_assoc();
$subject_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name'] ?? '');

    $check_stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ? AND id != ?");
    $check_stmt->bind_param("si", $subject_name, $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Subject name already exists.";
    } else {
        $update_stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE id = ?");
        $update_stmt->bind_param("si", $subject_name, $id);

        if ($update_stmt->execute()) {
            redirectWithStatus('view_subjects.php', 'success', 'Subject updated successfully.');
        }

        $error = "Failed to update subject. Please try again.";
        $update_stmt->close();
    }

    $check_stmt->close();
    $subject['subject_name'] = $subject_name;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Edit Subject</h2>

        <?php if ($error !== '') { ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php } ?>

        <form method="POST" action="edit_subject.php?id=<?php echo e($id); ?>" class="card p-4">
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" name="subject_name" id="subject_name"
                       value="<?php echo e($subject['subject_name']); ?>" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Subject</button>
            </div>
        </form>
    </div>
</body>
</html>
