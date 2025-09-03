<?php
include 'db.php';
 
if (!isset($_SESSION['userid']) || $_SESSION['urole'] != 'Parents') {
    header("Location: login.php");
    exit();
}
$meta_head = "My Sons / Daughters";
$parent_id = $_SESSION['userid'];

// get parent CNIC from users table
$sql = "SELECT CNIC FROM users WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc())
    $parent_cnic = $row['CNIC'];

    // fetch students whose father_cnic matches
    $sql2 = "SELECT s.student_id, c.CID, s.student_name, c.class_name, s.gender
         FROM students s
         JOIN classes c ON s.class = c.CID
         WHERE s.father_cnic = ?";
			// exit($sql2);
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $parent_cnic);
    $stmt2->execute();
    $students = $stmt2->get_result();

include 'header.php';
include 'navigation.php';
 ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3 bg-light p-3">
            <?php include 'leftbar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="card p-4 shadow-sm">
			
<?php			
    echo "<h2>Your Children</h2>";

    if ($students->num_rows > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead class='table-dark'>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Gender</th>
                    <th>Attendance</th>
                    <th>Study Material</th>
                </tr>
              </thead>";
        echo "<tbody>";

        $count = 1;
        while ($s = $students->fetch_assoc()) {
		$CID	=	htmlspecialchars($s['CID']);
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . htmlspecialchars($s['student_name']) . "</td>";
            echo "<td>" . htmlspecialchars($s['class_name']) . "</td>";
            echo "<td>" . htmlspecialchars($s['gender']) . "</td>";

            // Links
            echo "<td><a href='student_attendance.php?sid=" . $s['student_id'] . "' class='btn btn-sm btn-primary'>View</a></td>";
            echo "<td><a href='pstduymaterials.php?cid=" . $CID . "' class='btn btn-sm btn-success'>View</a></td>";

            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>No children found.</p>";
    }
?>             </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
