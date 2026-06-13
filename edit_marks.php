<?php
include 'db.php';
include 'includes/auth.php';

// Check if a specific mark record is selected for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the marks details based on the selected record
    $sql = "SELECT * FROM marks WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Marks not found for this student.</div>";
        exit;
    }
}

// Update the marks record if form is submitted
if (isset($_POST['update'])) {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $marks = $_POST['marks'];

    // Update query to modify the marks table
    $update_sql = "UPDATE marks SET student_id = '$student_id', subject_id = '$subject_id', marks = '$marks' WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<div class='alert alert-success'>Marks updated successfully.</div>";
        header('Location: view_marks.php');
    } else {
        echo "<div class='alert alert-danger'>Error updating marks: " . $conn->error . "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Marks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

<div class="content">
    <h2>Edit Marks</h2>
    
    <form method="POST" action="" class="card p-4">
        <div class="mb-3">
            <label for="student_id" class="form-label">Student</label>
            <select class="form-control" name="student_id" id="student_id" required>
                <option value="">Select Student</option>
                <?php
                $student_query = "SELECT id, student_name, enrollment_no FROM students";
                $student_result = $conn->query($student_query);
                if ($student_result->num_rows > 0) {
                    while($s_row = $student_result->fetch_assoc()) {
                        $selected = ($s_row['id'] == $row['student_id']) ? "selected" : "";
                        echo "<option value='".$s_row['id']."' $selected>".$s_row['student_name']." (".$s_row['enrollment_no'].")</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject</label>
            <select class="form-control" name="subject_id" id="subject_id" required>
                <option value="">Select Subject</option>
                <?php
                $subject_query = "SELECT id, subject_name FROM subjects";
                $subject_result = $conn->query($subject_query);
                if ($subject_result->num_rows > 0) {
                    while($sub_row = $subject_result->fetch_assoc()) {
                        $selected = ($sub_row['id'] == $row['subject_id']) ? "selected" : "";
                        echo "<option value='".$sub_row['id']."' $selected>".$sub_row['subject_name']."</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="marks" class="form-label">Marks</label>
            <input type="number" class="form-control" name="marks" id="marks" value="<?php echo $row['marks']; ?>" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary" name="update">Update Marks</button>
        </div>
    </form>
</div>

</body>
</html>
