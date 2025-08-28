<?php
include 'db.php';
$meta_head = "Attendance";

if (!isset($_SESSION['userid']) || $_SESSION['urole'] != 'Teacher') {
    header("Location: index.php?action=logout");
    exit();
}

if (!isset($_GET['class_id']) || !isset($_GET['teacher_id'])) {
    die("Class ID and Teacher ID are required!");
}

$class_id   = intval($_GET['class_id']);
$teacher_id = intval($_GET['teacher_id']);

//  Get attendance date from user OR default to today
$attendance_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

//  Fetch students of this class
$query  = "SELECT student_id, student_name FROM students WHERE class = $class_id AND status = 'admitted'";
$result = mysqli_query($conn, $query);

//  Fetch existing attendance for the selected date
$attendance_data = [];
$attendance_query = "SELECT student_id, status FROM attendance 
                     WHERE class_id = $class_id 
                     AND teacher_id = $teacher_id 
                     AND attendance_date = '$attendance_date'";
$attendance_result = mysqli_query($conn, $attendance_query);

if ($attendance_result && mysqli_num_rows($attendance_result) > 0) {
    while ($row = mysqli_fetch_assoc($attendance_result)) {
        $attendance_data[$row['student_id']] = $row['status'];
    }
    $is_update = true;  // Attendance exists ? Update mode
} else {
    $is_update = false; // No attendance ? Mark mode
}

//  Save or Update Attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['attendance'] as $student_id => $status) {
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, class_id, teacher_id, attendance_date, status)
                                VALUES (?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE status=?");
        $stmt->bind_param("iiisss", $student_id, $class_id, $teacher_id, $attendance_date, $status, $status);
        $stmt->execute();
    }

    //  Reload the same page with selected date
    echo "<script>window.location.href='attendance.php?class_id=$class_id&teacher_id=$teacher_id&date=$attendance_date';</script>";
    exit();
}

include 'header.php';
include 'navigation.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3 bg-light p-3">
            <?php include 'leftbar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="card p-4">

                <h2><?php echo $is_update ? "Update Attendance" : "Mark Attendance"; ?></h2>

                <!--  Date Picker -->
                <form method="GET" class="mb-3">
                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">

                    <label for="date"><b>Select Date:</b></label>
                    <input type="date" id="date" name="date" 
                           value="<?php echo $attendance_date; ?>" 
                           onchange="this.form.submit()"
                           class="form-control"
                           style="max-width:250px;">
                </form>

                <!--  Attendance Form -->
                <form method="POST">
                    <table border="1" cellpadding="8" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Present</th>
                                <th>Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { 
                                $student_id = $row['student_id'];
                                $status = isset($attendance_data[$student_id]) ? $attendance_data[$student_id] : "Present";
                            ?>
                            <tr>
                                <td><?php echo $student_id; ?></td>
                                <td><?php echo $row['student_name']; ?></td>
                                <td>
                                    <input type="radio" 
                                           name="attendance[<?php echo $student_id; ?>]" 
                                           value="Present" 
                                           <?php echo ($status === "Present") ? "checked" : ""; ?>>
                                </td>
                                <td>
                                    <input type="radio" 
                                           name="attendance[<?php echo $student_id; ?>]" 
                                           value="Absent" 
                                           <?php echo ($status === "Absent") ? "checked" : ""; ?>>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <br>

                    <!--  Dynamic Button Color -->
                    <button type="submit" 
                            class="btn <?php echo $is_update ? 'btn-primary' : 'btn-success'; ?>" 
                            style="font-weight:bold; padding:10px 20px;">
                        <?php echo $is_update ? "Update Attendance" : "Mark Attendance"; ?>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
