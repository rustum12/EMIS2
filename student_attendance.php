<?php
include 'db.php';
 
// Ensure UTF-8
header('Content-Type: text/html; charset=utf-8');
if (function_exists('mysqli_set_charset')) {
    mysqli_set_charset($conn, 'utf8mb4');
}

$meta_head = "Attendance Report";

// ----------------- ROLE CHECK -----------------
if (!isset($_SESSION['userid']) || !in_array($_SESSION['urole'], ['Student', 'Parents'])) {
    header("Location: index.php?action=logout");
    exit();
}

// ----------------- DETERMINE STUDENT -----------------
$student_id = null;
$student_name = '';
$class_id = 0;

// Case 1: Student logged in
if ($_SESSION['urole'] === 'Student') {
    $CNIC = $_SESSION['CNIC'];
    $getStudent = $conn->prepare("SELECT * FROM students WHERE student_cnic = ?");
    $getStudent->bind_param("s", $CNIC);
    $getStudent->execute();
    $res = $getStudent->get_result();

    if ($res->num_rows === 0) {
        exit("<h3>No student profile found for your CNIC!</h3>");
    }
    $student = $res->fetch_assoc();
    $student_id   = (int)$student['student_id'];
    $student_name = $student['student_name'];
    $class_id     = (int)$student['class'];
}
// Case 2: Parents logged in
elseif ($_SESSION['urole'] === 'Parents') {
    if (!isset($_GET['sid']) || !is_numeric($_GET['sid'])) {
        exit("<h3>Invalid student selection.</h3>");
    }
    $sid = (int) $_GET['sid'];

    // Get Parents CNIC
    $stmt = $conn->prepare("SELECT CNIC FROM users WHERE uid = ?");
    $stmt->bind_param("i", $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$row = $result->fetch_assoc()) {
        exit("<h3>Parents record not found.</h3>");
    }
    $parent_cnic = $row['CNIC'];

    // Verify the student really belongs to this Parents
    $sql2 = "SELECT s.student_id, s.student_name, s.class, c.class_name
             FROM students s
             JOIN classes c ON s.class = c.CID
             WHERE s.student_id = ? AND s.father_cnic = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("is", $sid, $parent_cnic);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    if ($res2->num_rows === 0) {
        exit("<h3>You are not authorized to view this student's attendance.</h3>");
    }
    $student = $res2->fetch_assoc();
    $student_id   = (int)$student['student_id'];
    $student_name = $student['student_name'];
    $class_id     = (int)$student['class'];
}

// ----------------- MONTH & YEAR -----------------
$month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
$year  = isset($_GET['year'])  ? (int) $_GET['year']  : (int) date('Y');

if ($month < 1 || $month > 12) $month = (int) date('m');
if ($year < 2000 || $year > 2100) $year = (int) date('Y');

$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// ----------------- ATTENDANCE DATA -----------------
$attendance_sql = "
    SELECT attendance_date, status
    FROM attendance
    WHERE student_id = ?
      AND class_id = ?
      AND MONTH(attendance_date) = ?
      AND YEAR(attendance_date) = ?
";
$stmt3 = $conn->prepare($attendance_sql);
$stmt3->bind_param("iiii", $student_id, $class_id, $month, $year);
$stmt3->execute();
$attendance_result = $stmt3->get_result();

$attendance_data = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_data[$row['attendance_date']] = $row['status'];
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
            <div class="card p-4 shadow-sm">
                <h2 class="mb-3 text-primary">Attendance Report</h2>

                <form method="GET" class="mb-3 d-flex gap-2 align-items-end">
                    <?php if ($_SESSION['urole'] === 'Parents'): ?>
                        <input type="hidden" name="sid" value="<?php echo $student_id; ?>">
                    <?php endif; ?>

                    <div>
                        <label for="month" class="form-label">Month</label>
                        <input type="number" id="month" name="month" min="1" max="12"
                               value="<?php echo $month; ?>" class="form-control" required>
                    </div>

                    <div>
                        <label for="year" class="form-label">Year</label>
                        <input type="number" id="year" name="year" min="2000" max="2100"
                               value="<?php echo $year; ?>" class="form-control" required>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">View</button>
                    </div>
                </form>

                <h5><?php echo e($student_name); ?> (ID: <?php echo $student_id; ?>)</h5>

                <div style="overflow-x:auto;">
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <?php for ($d = 1; $d <= $total_days; $d++): ?>
                                    <th><?php echo $d; ?></th>
                                <?php endfor; ?>
                                <th>Total Days</th>
                                <th>Present</th>
                                <th>Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr> 
                                <?php
                                $total_present = 0;
                                for ($d = 1; $d <= $total_days; $d++) {
                                    $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
                                    $status = $attendance_data[$date] ?? '-';

                                    if ($status === 'Present') {
                                        echo "<td class='text-success fw-bold'>P</td>";
                                        $total_present++;
                                    } elseif ($status === 'Absent') {
                                        echo "<td class='text-danger fw-bold'>A</td>";
                                    } else {
                                        echo "<td>-</td>";
                                    }
                                }
                                $percentage = $total_days > 0 ? round(($total_present / $total_days) * 100, 2) : 0;
                                ?>
                                <td><?php echo $total_days; ?></td>
                                <td><?php echo $total_present; ?></td>
                                <td><?php echo $percentage; ?>%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
