	<?php
	// study_materials.php
	include 'db.php';
	 
	// Ensure login
	if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
		header("Location: login.php");
		exit();
	}
	
	$meta_head = "List of Study Material";
	
	// Only students can access this page
	 $student_class_id = (isset($_GET['cid']) && is_numeric($_GET['cid'])) ? intval($_GET['cid']) : 0; 
	if(!$student_class_id)
		exit("Class does not exist");
	// Fetch study materials only for the student's class
	$query = $conn->prepare("SELECT * FROM study_materials WHERE class_id = ? ORDER BY uploaded_at DESC");
	
	if (!$query) {
		die("Query failed: " . $conn->error);
	}
	
	$query->bind_param("i", $student_class_id);
	$query->execute();
	$result = $query->get_result();
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
	
		<h2>Study Materials for Your Class</h2>
		<table class="table table-striped table-bordered table-hover shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>File</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $counter = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$counter++."</td>";
                    echo "<td>".htmlspecialchars($row['title'])."</td>";
                    echo "<td>".htmlspecialchars($row['description'])."</td>";
                    echo "<td>
                            <a href='download.php?id=".$row['material_id']."' 
                              class='btn btn-sm btn-success'>
                             Download
                            </a>
                          </td>";
                    echo "<td>".date("M d, Y", strtotime($row['uploaded_at']))."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center text-muted'>
                          No study materials found for your class.
                      </td></tr>";
            }
            ?>
        </tbody>
    </table>
	 
				</div>
			</div>
		</div>
	</div>
	 
	<?php include 'footer.php'; ?>
