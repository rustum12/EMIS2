<?php 
include 'db.php';
header('Content-Type: text/html; charset=utf-8');
if (function_exists('mysqli_set_charset')) {
    mysqli_set_charset($conn, 'utf8mb4');
}
$meta_head = "Monthly Attendance Report";

if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Teacher') {
    header("Location: index.php?action=logout");
    exit();
}
if (!isset($_GET['class_id']) || !isset($_GET['teacher_id'])) {
    die("Class ID and Teacher ID are required!");
}

$class_id   = (int) $_GET['class_id'];
$teacher_id = (int) $_GET['teacher_id'];
$month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
$year  = isset($_GET['year'])  ? (int) $_GET['year']  : (int) date('Y');

if ($month < 1 || $month > 12) $month = (int) date('m');
if ($year < 2000 || $year > 2100) $year = (int) date('Y');

$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$students_sql = "
    SELECT student_id, student_name
    FROM students
    WHERE class = $class_id AND status = 'admitted'
    ORDER BY student_name ASC
";
$students_result = mysqli_query($conn, $students_sql);

$attendance_sql = "
    SELECT student_id, attendance_date, status
    FROM attendance
    WHERE class_id = $class_id
      AND teacher_id = $teacher_id
      AND MONTH(attendance_date) = $month
      AND YEAR(attendance_date) = $year
";
$attendance_result = mysqli_query($conn, $attendance_sql);

$attendance_data = [];
if ($attendance_result) {
    while ($row = mysqli_fetch_assoc($attendance_result)) {
        $attendance_data[(int)$row['student_id']][$row['attendance_date']] = $row['status'];
    }
}

include 'header.php';
include 'navigation.php';

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>

<!-- Custom CSS -->
<style>
    body { background:#f5f7fb; font-family: 'Inter', Arial, sans-serif; }
    h2 { color:#0d6efd; font-weight:600; margin-bottom:1rem; }
    .card { border-radius:15px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
    .form-label { font-weight:600; color:#333; }
    .btn-primary { background:linear-gradient(90deg,#0b5ed7,#0d6efd); border:none; border-radius:8px; padding:6px 16px; }
    .btn-primary:hover { opacity:0.9; }
    .table { border-radius:10px; overflow:hidden; }
    .table thead { background:#0d6efd; color:#fff; }
    .table td, .table th { text-align:center; vertical-align:middle; }
    .table td strong { font-weight:600; }
    .badge-present { background:#198754; color:#fff; padding:3px 6px; border-radius:6px; font-size:0.8rem; }
    .badge-absent { background:#dc3545; color:#fff; padding:3px 6px; border-radius:6px; font-size:0.8rem; }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3 bg-light p-3 rounded shadow-sm">
            <?php include 'leftbar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="card p-4">
                <h2>📊 Monthly Attendance Report</h2>

                <form method="GET" class="mb-4 row g-3">
                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">

                    <div class="col-md-3">
                        <label for="month" class="form-label">Month</label>
                        <input type="number" id="month" name="month" min="1" max="12" 
                            value="<?php echo $month; ?>" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="number" id="year" name="year" min="2000" max="2100" 
                            value="<?php echo $year; ?>" class="form-control" required>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">View Report</button>
                    </div>
                </form>

                <div style="overflow-x:auto;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <?php for ($d = 1; $d <= $total_days; $d++): ?>
                                    <th><?php echo $d; ?></th>
                                <?php endfor; ?>
                                <th>Total Days</th>
                                <th>Presents</th>
                                <th>%age</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            if ($students_result) {
                                while ($row = mysqli_fetch_assoc($students_result)) {
                                    $student_id = (int)$row['student_id'];
                                    $student_name = $row['student_name'];
                                    $total_present = 0;
                                    echo "<tr>";
                                    echo "<td>" . $sno++ . "</td>";
                                    echo "<td>" . $student_id . "</td>";
                                    echo "<td style='font-weight:600'>" . e($student_name) . "</td>";

                                    for ($d = 1; $d <= $total_days; $d++) {
                                        $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
                                        $status = $attendance_data[$student_id][$date] ?? '-';

                                        if ($status === 'Present') {
                                            echo "<td><span class='badge-present'>P</span></td>";
                                            $total_present++;
                                        } elseif ($status === 'Absent') {
                                            echo "<td><span class='badge-absent'>A</span></td>";
                                        } else {
                                            echo "<td>-</td>";
                                        }
                                    }

                                    $total_working_days = $total_days;
                                    $percentage = $total_working_days > 0 ? round(($total_present / $total_working_days) * 100, 2) : 0;

                                    echo "<td>" . $total_working_days . "</td>";
                                    echo "<td>" . $total_present . "</td>";
                                    echo "<td><strong>" . $percentage . "%</strong></td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
