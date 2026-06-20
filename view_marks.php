<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$search = trim($_GET['search'] ?? '');
$subject_id = trim($_GET['subject_id'] ?? '');
if ($subject_id !== '' && !ctype_digit($subject_id)) {
    $subject_id = '';
}
$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(students.student_name LIKE ? OR students.enrollment_no LIKE ? OR subjects.subject_name LIKE ?)";
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sss';
}

if ($subject_id !== '') {
    $where[] = "marks.subject_id = ?";
    $params[] = $subject_id;
    $types .= 'i';
}

$sql = "SELECT marks.id, marks.marks, students.student_name, students.enrollment_no, subjects.subject_name
        FROM marks
        LEFT JOIN students ON marks.student_id = students.id
        LEFT JOIN subjects ON marks.subject_id = subjects.id";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY marks.id DESC";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    bindParams($stmt, $types, $params);
}
$stmt->execute();
$result = $stmt->get_result();

$subject_result = $conn->query("SELECT id, subject_name FROM subjects ORDER BY subject_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Marks Records</h2>
        <?php displayStatusMessage(); ?>

        <form method="GET" action="view_marks.php" class="filter-form row g-3 mb-4">
            <div class="col-md-5">
                <label for="search" class="form-label">Search Marks</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Student, enrollment, or subject">
            </div>
            <div class="col-md-4">
                <label for="subject_id" class="form-label">Subject</label>
                <select class="form-control" id="subject_id" name="subject_id">
                    <option value="">All Subjects</option>
                    <?php while ($subject = $subject_result->fetch_assoc()) { ?>
                        <option value="<?php echo e($subject['id']); ?>" <?php echo $subject_id === (string) $subject['id'] ? 'selected' : ''; ?>>
                            <?php echo e($subject['subject_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3 filter-actions">
                <button type="submit" class="btn btn-primary mt-4">Filter</button>
                <a href="view_marks.php" class="btn btn-secondary mt-4">Reset</a>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo e($row['id']); ?></td>
                    <td><?php echo e($row['student_name'] . ' (' . $row['enrollment_no'] . ')'); ?></td>
                    <td><?php echo e($row['subject_name']); ?></td>
                    
                    <td>
                        <?php 
                        $markClass = 'bg-success'; // Pass
                        if ($row['marks'] < 50) $markClass = 'bg-danger'; // Fail
                        elseif ($row['marks'] >= 80) $markClass = 'bg-primary'; // Distinction
                        ?>
                        <span class="badge <?php echo $markClass; ?> fs-6">
                            <?php echo e($row['marks']); ?>
                        </span>
                    </td>
                    
                    <td>
                        <a href="edit_marks.php?id=<?php echo e($row['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_marks.php?id=<?php echo e($row['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this mark record?');">Delete</a>
                    </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5" class="text-center">No marks found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>