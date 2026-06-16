<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$search = trim($_GET['search'] ?? '');
$attendance_date = trim($_GET['attendance_date'] ?? '');
$attendance_status = trim($_GET['attendance_status'] ?? '');
if (!in_array($attendance_status, ['', '0', '1'], true)) {
    $attendance_status = '';
}
$where = [];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = "(students.student_name LIKE ? OR students.enrollment_no LIKE ?)";
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

if ($attendance_date !== '') {
    $where[] = "attendance.attendance_date = ?";
    $params[] = $attendance_date;
    $types .= 's';
}

if ($attendance_status !== '') {
    $where[] = "attendance.attendance = ?";
    $params[] = $attendance_status;
    $types .= 'i';
}

$sql = "SELECT attendance.id, attendance.attendance_date, attendance.attendance, students.student_name, students.enrollment_no
        FROM attendance
        LEFT JOIN students ON attendance.student_id = students.id";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY attendance.attendance_date DESC, attendance.id DESC";

$stmt = $conn->prepare($sql);
bindParams($stmt, $types, $params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Attendance Records</h2>
        <?php displayStatusMessage(); ?>

        <form method="GET" action="view_attendance.php" class="filter-form row g-3 mb-4">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Student</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Name or enrollment no">
            </div>
            <div class="col-md-3">
                <label for="attendance_date" class="form-label">Date</label>
                <input type="date" class="form-control" id="attendance_date" name="attendance_date" value="<?php echo e($attendance_date); ?>">
            </div>
            <div class="col-md-3">
                <label for="attendance_status" class="form-label">Status</label>
                <select class="form-control" id="attendance_status" name="attendance_status">
                    <option value="">All Status</option>
                    <option value="1" <?php echo $attendance_status === '1' ? 'selected' : ''; ?>>Present</option>
                    <option value="0" <?php echo $attendance_status === '0' ? 'selected' : ''; ?>>Absent</option>
                </select>
            </div>
            <div class="col-md-2 filter-actions">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view_attendance.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Date</th>
                    <th>Attendance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo e($row['id']); ?></td>
                    <td><?php echo e($row['student_name'] . ' (' . $row['enrollment_no'] . ')'); ?></td>
                    <td><?php echo e($row['attendance_date']); ?></td>
                    <td><?php echo $row['attendance'] == 1 ? 'Present' : 'Absent'; ?></td>
                    <td>
                        <a href="edit_attendance.php?id=<?php echo e($row['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_attendance.php?id=<?php echo e($row['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this attendance record?');">Delete</a>
                    </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5">No attendance records found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
