<?php
session_start();
include 'db.php';

$error = "";

if (!isset($_GET['id'])) {
    header("Location: view_subjects.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM subjects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Subject not found.</div>";
    exit();
}

$stmt->close();

if (isset($_POST['update'])) {
    $subject_name = trim($_POST['subject_name']);

    $check_sql = "SELECT id FROM subjects WHERE subject_name = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $subject_name, $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Subject name already exists.";
        $row['subject_name'] = $subject_name;
    } else {
        $update_sql = "UPDATE subjects SET subject_name = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $subject_name, $id);

        if ($update_stmt->execute()) {
            header("Location: view_subjects.php?message=Subject updated successfully");
            exit();
        } else {
            $error = "Failed to update subject. Please try again.";
        }

        $update_stmt->close();
    }

    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Subject</h2>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST" action="" class="card p-4">
        <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name</label>
            <input type="text" 
                   class="form-control" 
                   name="subject_name" 
                   id="subject_name" 
                   value="<?php echo htmlspecialchars($row['subject_name']); ?>" 
                   required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary" name="update">Update Subject</button>
        </div>
    </form>
</div>
</body>
</html>