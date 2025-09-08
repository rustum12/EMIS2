<?php
include 'db.php';


// Ensure UTF-8 for output and DB connection
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

// month-year input
if (isset($_GET['month_year'])) {
    [$year, $month] = explode('-', $_GET['month_year']);
    $year  = (int)$year;
    $month = (int)$month;
} else {
    $year  = (int)date('Y');
    $month = (int)date('m');
}

// Clamp month/year to reasonable ranges
if ($month < 1 || $month > 12) $month = (int) date('m');
if ($year < 2000 || $year > 2100) $year = (int) date('Y');

$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$today = date('Y-m-d');

// --- Build working days (exclude Sat & Sun, stop at today) ---
$working_days = [];
for ($d = 1; $d <= $total_days; $d++) {
    $date = sprintf('%04d-%02d-%02d', $year, $month, $d);

    if ($date > $today) break; // don’t count future dates

    $dayOfWeek = date('N', strtotime($date)); // 1=Mon ... 7=Sun
    if ($dayOfWeek == 6 || $dayOfWeek == 7) continue; // skip Sat & Sun

    $working_days[] = $date;
}

// --- Fetch students ---
$students_sql = "
    SELECT student_id, student_name
    FROM students
    WHERE class = $class_id AND status = 'admitted'
    ORDER BY student_name ASC
";
$students_result = mysqli_query($conn, $students_sql);

// --- Fetch attendance ---
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

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3 bg-light p-3">
            <?php include 'leftbar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="card p-4">
                <h2>Monthly Attendance Report</h2>

                <form method="GET" class="mb-3 d-flex gap-2 align-items-end">
                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">

                    <div>
                        <label for="month_year" class="form-label">Month & Year</label>
                        <input type="month" id="month_year" name="month_year"
                               max="<?php echo date('Y-m'); ?>"
                               value="<?php echo sprintf('%04d-%02d', $year, $month); ?>"
                               class="form-control" required>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">View Report</button>
                    </div>
                </form>

                <div style="overflow-x:auto;">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <?php foreach ($working_days as $date): ?>
                                    <th><?php echo date('j', strtotime($date)); ?></th>
                                <?php endforeach; ?>
                                <th>Total Working Days</th>
                                <th>Total Attendance</th>
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
                                    echo "<td>" . e($student_name) . "</td>";

                                    foreach ($working_days as $date) {
                                        $status = $attendance_data[$student_id][$date] ?? '-';

                                        if ($status === 'Present') {
                                            echo "<td><strong>P</strong></td>";
                                            $total_present++;
                                        } elseif ($status === 'Absent') {
                                            echo "<td><strong>A</strong></td>";
                                        } else {
                                            echo "<td>-</td>";
                                        }
                                    }

                                    $total_working_days = count($working_days);
                                    $percentage = $total_working_days > 0
                                        ? round(($total_present / $total_working_days) * 100, 2)
                                        : 0;

                                    echo "<td>" . $total_working_days . "</td>";
                                    echo "<td>" . $total_present . "</td>";
                                    echo "<td>" . $percentage . "%</td>";
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
