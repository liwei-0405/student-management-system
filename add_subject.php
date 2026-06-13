<?php
include 'db.php';
include 'includes/auth.php';
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
        <form action="add_subject.php" method="POST">
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Subject</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject_name = $_POST['subject_name'];

            $sql = "INSERT INTO subjects (subject_name) VALUES ('$subject_name')";
            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success'>Subject added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
        ?>
    </div>
</body>
</html>
