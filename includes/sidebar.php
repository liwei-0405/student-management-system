<?php
$current_page = basename($_SERVER['PHP_SELF']);

$nav_items = [
    'index.php' => 'Home',
    'add_attendance.php' => 'Add Attendance',
    'view_attendance.php' => 'View Attendance',
    'add_marks.php' => 'Add Marks',
    'view_marks.php' => 'View Marks',
    'add_student.php' => 'Add Student',
    'view_students.php' => 'View Students',
    'add_subject.php' => 'Add Subject',
    'view_subjects.php' => 'View Subjects',
];
?>

<div class="sidebar">
    <h4><b>Navigation</b></h4>
    <?php foreach ($nav_items as $url => $label): ?>
        <a href="<?php echo $url; ?>" class="<?php echo $current_page === $url ? 'active' : ''; ?>">
            <?php echo $label; ?>
        </a>
    <?php endforeach; ?>
    <a href="logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
