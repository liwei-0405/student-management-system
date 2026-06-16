<?php
include 'db.php';
include 'includes/auth.php';
include 'includes/helpers.php';

$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM subjects";
$types = '';
$params = [];

if ($search !== '') {
    $sql .= " WHERE subject_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types = 's';
}

$sql .= " ORDER BY subject_name ASC";

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
    <title>View Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content">
        <h2>Subject Records</h2>
        <?php displayStatusMessage(); ?>

        <form method="GET" action="view_subjects.php" class="filter-form row g-3 mb-4">
            <div class="col-md-9">
                <label for="search" class="form-label">Search Subject</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Subject name">
            </div>
            <div class="col-md-3 filter-actions">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="view_subjects.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <!-- <th>Subject Code</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo e($row['id']); ?></td>
                    <td><?php echo e($row['subject_name']); ?></td>
                    <!-- <td><?php echo $row['subject_code']; ?></td>  -->
                    <td>
                        <a href="edit_subject.php?id=<?php echo e($row['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_subject.php?id=<?php echo e($row['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subject?');">Delete</a>
                    </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="3">No subjects found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
