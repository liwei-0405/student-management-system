<?php
session_start();
include 'db.php';
include 'includes/auth.php';

$error_message = '';
$success_message = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $marks = $_POST['marks'];

    if (!is_numeric($marks) || $marks < 0 || $marks > 100) {
        $error_message = "Invalid marks. Please enter a value between 0 and 100.";
    } else {
        $marks = floatval($marks);
        
        // Member 3: Prevent Duplicates (Ignoring current ID)
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
                header('Location: view_marks.php?msg=updated');
                exit();
            } else {
                $error_message = "Error updating record: " . $conn->error;
            }
        }
    }
}
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

        <?php 
        if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>";

        if ($id > 0) {
            $fetch_stmt = $conn->prepare("SELECT * FROM marks WHERE id = ?");
            $fetch_stmt->bind_param("i", $id);
            $fetch_stmt->execute();
            $result = $fetch_stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
        ?>

        <form action="edit_marks.php?id=<?php echo $id; ?>" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select class="form-control" id="student_id" name="student_id" required>
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
                <select class="form-control" id="subject_id" name="subject_id" required>
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
                <label for="marks" class="form-label">Marks (0-100)</label>
                <input type="number" step="0.1" class="form-control" id="marks" name="marks" min="0" max="100" value="<?php echo htmlspecialchars($row['marks']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Marks</button>
        </form>

        <?php
            } else {
                echo "<div class='alert alert-danger'>Marks record not found.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No marks ID specified.</div>";
        }
        ?>

    </div>
</body>
</html>