<?php
// study_materials.php
include 'db.php';
 
// Ensure login
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$meta_head = "List of Study Material";
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
$urole = $_SESSION['urole'] ;
$CNIC  = $_SESSION['CNIC']; // we still use session only for CNIC (not teacher_id)

// Resolve teacher_id from CNIC (no teacher_id in session)
$teacher_id = null;
if ($CNIC) {
    $q = $conn->prepare("SELECT teacher_id, teacher_name FROM teachers WHERE cnic = ?");
    $q->bind_param("s", $CNIC);
    $q->execute();
    $tres = $q->get_result();
    if ($tres->num_rows) {
        $trow = $tres->fetch_assoc();
        $teacher_id = (int)$trow['teacher_id'];
    }
}

// AJAX delete handler
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = (int)$_GET['id'];

    // Only allow delete of own material (unless Admin)
    if ($urole === 'Teacher') {
        $stmt = $conn->prepare("SELECT file_path FROM study_materials WHERE material_id=? AND teacher_id=?");
        $stmt->bind_param("ii", $id, $teacher_id);
    } else {
        $stmt = $conn->prepare("SELECT file_path FROM study_materials WHERE material_id=?");
        $stmt->bind_param("i", $id);
    }
    $stmt->execute();
    $r = $stmt->get_result();
    if (!$r->num_rows) {
        echo json_encode(['ok'=>false,'msg'=>'Not allowed or not found.']);
        exit;
    }
    $row = $r->fetch_assoc();
    $filepath = $row['file_path'];

    if ($urole === 'Teacher') {
        $del = $conn->prepare("DELETE FROM study_materials WHERE material_id=? AND teacher_id=?");
        $del->bind_param("ii", $id, $teacher_id);
    } else {
        $del = $conn->prepare("DELETE FROM study_materials WHERE material_id=?");
        $del->bind_param("i", $id);
    }
    if ($del->execute()) {
        if ($filepath && file_exists($filepath)) { @unlink($filepath); }
        echo json_encode(['ok'=>true]);
    } else {
        echo json_encode(['ok'=>false,'msg'=>'DB delete failed']);
    }
    exit;
}

// Fetch materials
if ($urole === 'Teacher') {
    // Only own materials
    $sql = "SELECT sm.*, c.class_name
            FROM study_materials sm
            JOIN classes c ON c.CID = sm.class_id
            WHERE sm.teacher_id = ?
            ORDER BY sm.uploaded_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
} else {
    // Admin sees all
    $sql = "SELECT sm.*, c.class_name
            FROM study_materials sm
            JOIN classes c ON c.CID = sm.class_id
            ORDER BY sm.uploaded_at DESC";
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$materials = $stmt->get_result();
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

          <h4 class="m-0">Study Materials</h4>
          <?php if ($urole === 'Teacher') { ?>
            <a class="btn btn-primary btn-sm" href="study_material.php">+ Add New</a>
          <?php } ?>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Class</th>
                <th>Title</th>
                <th>Description</th>
                <th>Type</th>
                <th>File</th>
                <th>Uploaded</th>
                <?php if ($urole === 'Teacher') echo "<th>Actions</th>"; ?>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($materials->num_rows) {
                  $i=1;
                  while ($m = $materials->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>".$i++."</td>";
                      echo "<td>".htmlspecialchars($m['class_name'])."</td>";
                      echo "<td>".htmlspecialchars($m['title'])."</td>";
                      echo "<td>".nl2br(htmlspecialchars($m['description'] ?? ''))."</td>";
                      echo "<td>".htmlspecialchars($m['file_type'])."</td>";
                      echo "<td>";
                      if (!empty($m['file_path'])) {
                          echo "<a class='btn btn-sm btn-success' target='_blank' href='".htmlspecialchars($m['file_path'])."'>View</a>";
                      } else {
                          echo "<span class='text-danger'>No File</span>";
                      }
                      echo "</td>";
                      echo "<td>".date('d M Y, h:i A', strtotime($m['uploaded_at']))."</td>";
                      if ($urole === 'Teacher') {
                          // Extra safety: only show actions if it's theirs
                          if ((int)$m['teacher_id'] === (int)$teacher_id) {
                              echo "<td>
                                <a class='btn btn-sm btn-warning' href='study_material.php?id=".$m['material_id']."'>Edit</a>
                                <button data-id='".$m['material_id']."' class='btn btn-sm btn-danger btn-del'>Delete</button>
                              </td>";
                          } else {
                              echo "<td><em class='text-muted'>N/A</em></td>";
                          }
                      }
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='".($urole==='Teacher'?8:7)."' class='text-center text-danger'>No materials found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
 
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('click', function(e){
  if (e.target.classList.contains('btn-del')) {
    const id = e.target.getAttribute('data-id');
    if (!confirm('Delete this material?')) return;
    fetch('study_materials.php?action=delete&id=' + encodeURIComponent(id), {
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(j => {
      if (j.ok) location.reload();
      else alert(j.msg || 'Delete failed');
    })
    .catch(() => alert('Network error'));
  }
});
</script>

<?php include 'footer.php'; ?>
