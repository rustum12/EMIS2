<?php
session_start();
require 'db.php';

// Secure session check for student
if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$logged_in_uid = $_SESSION['userid'];

// Step 1: Get CNIC of the logged-in student
$cnic = '';
$cnic_stmt = $conn->prepare("SELECT CNIC FROM users WHERE uid = ?");
$cnic_stmt->bind_param("i", $logged_in_uid);
$cnic_stmt->execute();
$cnic_result = $cnic_stmt->get_result();

if ($cnic_result->num_rows > 0) {
    $row = $cnic_result->fetch_assoc();
    $cnic = $row['CNIC'];
} else {
    die("Student CNIC not found.");
}
$cnic_stmt->close();

// Filters
$status_filter = $_GET['status'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Step 2: Main query using CNIC
$query = "
SELECT 
    a.date, 
    a.status, 
    c.class_name, 
    s.session_name, 
    t.uname AS marked_by,
    stu.CNIC AS student_cnic
FROM attendance a
JOIN classes c ON a.class_id = c.CID
JOIN sessions s ON a.session_id = s.session_id
JOIN users t ON a.teacher_id = t.uid -- teacher info
JOIN students st ON a.student_id = st.student_id
JOIN users stu ON st.student_cnic = stu.CNIC -- student info (you)
WHERE stu.CNIC = ?
";

$params = [$cnic];
$types = "s"; // CNIC is a string

// Append filters
if ($status_filter !== '') {
    $query .= " AND a.status = ?";
    $types .= "s";
    $params[] = $status_filter;
}

if ($from_date !== '') {
    $query .= " AND a.date >= ?";
    $types .= "s";
    $params[] = $from_date;
}

if ($to_date !== '') {
    $query .= " AND a.date <= ?";
    $types .= "s";
    $params[] = $to_date;
}

$query .= " ORDER BY a.date DESC";

// Execute query
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Stats for chart
$stats = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Excused' => 0];
$attendance_data = [];

while ($row = $result->fetch_assoc()) {
    $attendance_data[] = $row;
    $stats[$row['status']] = ($stats[$row['status']] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4 text-center">📅 My Attendance Record</h2>

    <!-- Filter Form -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" name="status" id="status">
                <option value="">All</option>
                <option value="Present" <?= $status_filter == 'Present' ? 'selected' : '' ?>>Present</option>
                <option value="Absent" <?= $status_filter == 'Absent' ? 'selected' : '' ?>>Absent</option>
                <option value="Late" <?= $status_filter == 'Late' ? 'selected' : '' ?>>Late</option>
                <option value="Excused" <?= $status_filter == 'Excused' ? 'selected' : '' ?>>Excused</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= htmlspecialchars($from_date) ?>">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label">To Date</label>
            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= htmlspecialchars($to_date) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="view_attendance.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Attendance Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Attendance Records</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Class</th>
                        <th>Session</th>
                        <th>Marked By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($attendance_data) > 0): ?>
                        <?php foreach ($attendance_data as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['date']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['class_name']) ?></td>
                                <td><?= htmlspecialchars($row['session_name']) ?></td>
                                <td><?= htmlspecialchars($row['marked_by']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No attendance records found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card shadow-sm mx-auto mb-4" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title text-center">Attendance Summary</h5>
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <div class="text-center">
        <a href="dashboard.php" class="btn btn-outline-secondary">⬅ Back to Dashboard</a>
    </div>
</div>

<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent', 'Late', 'Excused'],
            datasets: [{
                label: 'Attendance Summary',
                data: [
                    <?= $stats['Present'] ?>,
                    <?= $stats['Absent'] ?>,
                    <?= $stats['Late'] ?>,
                    <?= $stats['Excused'] ?>
                ],
                backgroundColor: ['#4caf50', '#f44336', '#ff9800', '#2196f3']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
