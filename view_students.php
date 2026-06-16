<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$search = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? '');
$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(student_name LIKE ? OR enrollment_no LIKE ?)";
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

if ($department !== '') {
    $where[] = "department = ?";
    $params[] = $department;
    $types .= 's';
}

$sql = "SELECT * FROM students";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
bindParams($stmt, $types, $params);
$stmt->execute();
$result = $stmt->get_result();

$department_result = $conn->query("SELECT DISTINCT department FROM students WHERE department <> '' ORDER BY department");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Students List</h2>
        <?php displayStatusMessage(); ?>

        <form method="GET" action="view_students.php" class="filter-form row g-3 mb-4">
            <div class="col-md-5">
                <label for="search" class="form-label">Search Student</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Name or enrollment no">
            </div>
            <div class="col-md-4">
                <label for="department" class="form-label">Department</label>
                <select class="form-control" id="department" name="department">
                    <option value="">All Departments</option>
                    <?php while ($dept = $department_result->fetch_assoc()) { ?>
                        <option value="<?php echo e($dept['department']); ?>" <?php echo $department === $dept['department'] ? 'selected' : ''; ?>>
                            <?php echo e($dept['department']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3 filter-actions">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view_students.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Enrollment No</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . e($row['id']) . "</td>
                                <td>" . e($row['enrollment_no']) . "</td>
                                <td>" . e($row['student_name']) . "</td>
                                <td>" . e($row['department']) . "</td>
                                <td>" . e($row['phone']) . "</td>
                                <td>
                                    <a href='edit_student.php?id=" . e($row['id']) . "' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_student.php?id=" . e($row['id']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
