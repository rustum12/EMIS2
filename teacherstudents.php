<?php
// Include database connection
include 'db.php';
$meta_head = "List of Students";
// Replace with logged-in teacher ID dynamically
if ($_SESSION['urole'] != 'Teacher') {
    header("Location: index.php?action=logout");
    exit();
}
else
{	$CNIC	=	$_SESSION['CNIC'];
    // Check for duplicate CNIC
    $getTID = mysqli_query($conn, "SELECT * FROM teachers WHERE cnic = '$CNIC'");
    if (mysqli_num_rows($getTID) > 0) {
		
		 // Fetch the teacher_id
        $row = mysqli_fetch_assoc($getTID);
        $teacher_id = $row['teacher_id'];
        
    }
	else
		exit("<h1>Dear User Your Role is Teacher but Admin has not created your Teacher Profile</h1>");

}

// Fetch students of teacher across all active classes and sessions
$sql = "SELECT 
            s.student_id, 
            s.student_name, 
            s.father_name, 
            s.email, 
            s.phone, 
            c.class_name, 
            se.session_name
        FROM students s
        JOIN teacher_classes tc 
            ON tc.class_id = s.class 
           AND tc.session_id = s.session_id
        JOIN classes c 
            ON c.CID = s.class
        JOIN sessions se 
            ON se.session_id = s.session_id
        WHERE tc.teacher_id = ? 
          AND tc.status = 'Active' 
          AND c.class_status = 'active' 
          AND se.status = 'active'
        ORDER BY c.class_name, s.student_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
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

    <h2 class="mb-3">Students List</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Father Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Class</th>
                <th>Session</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$count++."</td>
                        <td>".$row['student_name']."</td>
                        <td>".$row['father_name']."</td>
                        <td>".$row['email']."</td>
                        <td>".$row['phone']."</td>
                        <td>".$row['class_name']."</td>
                        <td>".$row['session_name']."</td>
                        <td>
                            <a href='teacherstudent.php?student_id=".$row['student_id']."' class='btn btn-sm btn-primary'>Edit</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='8' class='text-center text-danger'>No students found</td></tr>";
        }
        ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
