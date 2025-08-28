<?php
include 'db.php';
$meta_head = "Assigned Classes to Teacher";

if (isset($_SESSION['userid']))
	isLoggedIn ($_SESSION['uemail'], $_SESSION['upassword'],$conn);


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
 
 $query = "SELECT DISTINCT
    t.teacher_name,
    t.teacher_id,
    c.class_name,
    c.CID,
    s.session_name
FROM teacher_classes tc
JOIN teachers t ON tc.teacher_id = t.teacher_id
JOIN classes c ON tc.class_id = c.CID
JOIN sessions s ON c.session_id = s.session_id
WHERE s.status = 'active' and tc.session_id = c.session_id
  AND c.class_status = 'active'
  AND tc.teacher_id = '$teacher_id'
   
     
";

// Execute the query
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
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

    <h2>Teacher-Class-Batch (Active Only)</h2>
   <table class="table table-bordered table-striped table-hover text-center align-middle">
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th colspan="3">Class Name</th>
                <th>Batch Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="table-light">
    <td class="table-primary"><?= htmlspecialchars($row['teacher_name']) ?></td>

    <td class="table-success">
        <a href='attendance.php?class_id=<?= $row['CID'] ?>&teacher_id=<?= $row['teacher_id'] ?>' 
           class="link-primary text-decoration-none">
            <?= htmlspecialchars($row['class_name']) ?>
        </a>
    </td>

    <td class="table-warning">
        <a href='attendance.php?class_id=<?= $row['CID'] ?>&teacher_id=<?= $row['teacher_id'] ?>' 
           class="link-success text-decoration-none">
            Attendance
        </a>
    </td>

    <td class="table-danger">
        <a href='attendancesummary.php?class_id=<?= $row['CID'] ?>&teacher_id=<?= $row['teacher_id'] ?>' 
           class="link-danger text-decoration-none">
            Summary
        </a>
    </td>

    <td class="table-info"><?= htmlspecialchars($row['session_name']) ?></td>
</tr>
            <?php endwhile; ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
