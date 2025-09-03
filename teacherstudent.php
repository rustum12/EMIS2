<?php
// Include database connection
include 'db.php';
$meta_head = "Modify Student";
// Check if logged-in user is a Teacher
if ($_SESSION['urole'] != 'Teacher') {
    header("Location: index.php?action=logout");
    exit();
}

// Get teacher ID using CNIC
$CNIC = $_SESSION['CNIC'];
$getTID = mysqli_query($conn, "SELECT * FROM teachers WHERE cnic = '$CNIC'");
if (mysqli_num_rows($getTID) > 0) {
    $row = mysqli_fetch_assoc($getTID);
    $teacher_id = $row['teacher_id'];
} else {
    exit("<h1>Dear User, your role is Teacher but Admin has not created your Teacher Profile</h1>");
}

// Check if student_id is provided
if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    header("Location: teacherstudent.php");
    exit();
}

$student_id = intval($_GET['student_id']);

// Verify that this student belongs to the teacher's allowed classes
$checkStudent = "
    SELECT s.*
    FROM students s
    JOIN teacher_classes tc 
        ON tc.class_id = s.class 
       AND tc.session_id = s.session_id
    WHERE s.student_id = ? AND tc.teacher_id = ? AND tc.status = 'Active'
";
$stmt = $conn->prepare($checkStudent);
$stmt->bind_param("ii", $student_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("<h3 style='color:red;'>Access Denied! You cannot modify this student.</h3>");
}

$student = $result->fetch_assoc();

// Update student details if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name']);
    $father_name  = trim($_POST['father_name']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);

    $updateSQL = "UPDATE students 
                  SET student_name = ?, father_name = ?, email = ?, phone = ?
                  WHERE student_id = ?";

    $stmt = $conn->prepare($updateSQL);
    $stmt->bind_param("ssssi", $student_name, $father_name, $email, $phone, $student_id);

    if ($stmt->execute()) {
        header("Location: teacherstudent.php?student_id=$student_id&updated=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating student details.</div>";
    }
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
                <h3>Edit Student Details</h3>
				<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
        <div class="alert alert-success">Student updated successfully!</div>
    <?php endif; ?>
<form action="teacherstudent.php?student_id=<?= $student_id ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" name="student_name" class="form-control" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Father Name</label>
                        <input type="text" name="father_name" class="form-control" value="<?php echo htmlspecialchars($student['father_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>">
                    </div>

                    <button type="submit" class="btn btn-success">Update Student</button>
                    <a href="teacherstudent.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
