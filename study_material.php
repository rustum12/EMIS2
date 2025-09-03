<?php
// study_material.php
include 'db.php';
$meta_head = "Study Material";
if (!isset($_SESSION['userid'])) { header("Location: login.php"); exit(); }
if (($_SESSION['urole'] ?? '') !== 'Teacher') { header("Location: index.php?action=logout"); exit(); }

$CNIC = $_SESSION['CNIC'] ?? null;
if (!$CNIC) { exit("<h3 class='text-danger'>CNIC not found in session.</h3>"); }

// Resolve teacher_id via CNIC
$q = $conn->prepare("SELECT teacher_id, teacher_name FROM teachers WHERE cnic = ?");
$q->bind_param("s", $CNIC);
$q->execute();
$tr = $q->get_result();
if (!$tr->num_rows) { exit("<h3 class='text-danger'>Your Teacher profile is missing. Contact Admin.</h3>"); }
$trow = $tr->fetch_assoc();
$teacher_id = (int)$trow['teacher_id'];

// Edit mode?
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load existing material if edit
$material = null;
if ($edit_id) {
    $st = $conn->prepare("SELECT * FROM study_materials WHERE material_id=? AND teacher_id=?");
    $st->bind_param("ii", $edit_id, $teacher_id);
    $st->execute();
    $res = $st->get_result();
    if (!$res->num_rows) {
        exit("<div class='container mt-4'><div class='alert alert-danger'>Material not found or not yours.</div></div>");
    }
    $material = $res->fetch_assoc();
}

// Classes assigned to this teacher (active only)
$classesStmt = $conn->prepare("
    SELECT DISTINCT c.CID, c.class_name
    FROM teacher_classes tc
    JOIN classes c ON c.CID = tc.class_id
    JOIN sessions se ON se.session_id = tc.session_id
    WHERE tc.teacher_id = ? 
      AND tc.status = 'Active'
      AND c.class_status = 'active'
      AND se.status = 'active'
    ORDER BY c.class_name ASC
");
$classesStmt->bind_param("i", $teacher_id);
$classesStmt->execute();
$classes = $classesStmt->get_result();

// AJAX save handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    header('Content-Type: application/json');

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $class_id    = (int)($_POST['class_id'] ?? 0);
    $edit_id_in  = (int)($_POST['id'] ?? 0);

    if ($title === '' || !$class_id) {
        echo json_encode(['ok'=>false, 'msg'=>'Title and Class are required.']);
        exit;
    }

    // Verify class belongs to this teacher
    $chk = $conn->prepare("
        SELECT 1
        FROM teacher_classes tc
        JOIN classes c ON c.CID = tc.class_id
        JOIN sessions se ON se.session_id = tc.session_id
        WHERE tc.teacher_id = ? 
          AND tc.class_id = ?
          AND tc.status = 'Active'
          AND c.class_status = 'active'
          AND se.status = 'active'
        LIMIT 1
    ");
    $chk->bind_param("ii", $teacher_id, $class_id);
    $chk->execute();
    $okClass = $chk->get_result()->num_rows > 0;
    if (!$okClass) {
        echo json_encode(['ok'=>false, 'msg'=>'Selected class is not assigned or not active.']);
        exit;
    }

    // File upload (optional on edit)
    $upload_dir = "uploads/study_materials/";
    if (!is_dir($upload_dir)) { @mkdir($upload_dir, 0777, true); }
    $new_file_path = $material['file_path'] ?? '';
    $new_file_type = $material['file_type'] ?? '';

    if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
        $orig = basename($_FILES['file']['name']);
        $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['pdf','doc','docx','ppt','pptx','xls','xlsx','txt'];
        if (!in_array($ext, $allowed)) {
            echo json_encode(['ok'=>false,'msg'=>'Invalid file type. Allowed: '.implode(', ',$allowed)]);
            exit;
        }
        $fname = time().'_'.preg_replace('/[^A-Za-z0-9_\.-]/','_',$orig);
        $target = $upload_dir.$fname;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            echo json_encode(['ok'=>false,'msg'=>'File upload failed.']);
            exit;
        }
        // delete old file if editing
        if ($edit_id && !empty($material['file_path']) && file_exists($material['file_path'])) {
            @unlink($material['file_path']);
        }
        $new_file_path = $target;
        $new_file_type = $ext;
    }

    if ($edit_id_in) {
        // Update
        $u = $conn->prepare("
            UPDATE study_materials
            SET class_id=?, title=?, description=?, file_path=?, file_type=?
            WHERE material_id=? AND teacher_id=?
        ");
        $u->bind_param("issssii", $class_id, $title, $description, $new_file_path, $new_file_type, $edit_id_in, $teacher_id);
        if ($u->execute()) {
            echo json_encode(['ok'=>true,'id'=>$edit_id_in,'redirect'=>'study_material.php?id='.$edit_id_in]);
        } else {
            echo json_encode(['ok'=>false,'msg'=>'DB update failed.']);
        }
    } else {
        // Insert
        $i = $conn->prepare("
            INSERT INTO study_materials (teacher_id, class_id, title, description, file_path, file_type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $i->bind_param("iissss", $teacher_id, $class_id, $title, $description, $new_file_path, $new_file_type);
        if ($i->execute()) {
            echo json_encode(['ok'=>true,'id'=>$i->insert_id,'redirect'=>'study_material.php?id='.$i->insert_id]);
        } else {
            echo json_encode(['ok'=>false,'msg'=>'DB insert failed.']);
        }
    }
    exit;
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

        <h4 class="mb-3"><?= $material ? 'Edit Study Material' : 'Add Study Material' ?></h4>

        <form id="matForm" enctype="multipart/form-data" action="study_material.php<?= $material ? ('?id='.$material['material_id']) : '' ?>" method="post">
          <input type="hidden" name="ajax" value="1">
          <input type="hidden" name="id" value="<?= $material['material_id'] ?? 0 ?>">

          <div class="mb-3">
            <label class="form-label">Class <span class="text-danger">*</span></label>
            <select name="class_id" class="form-control" required>
              <option value="">-- Select Class --</option>
              <?php while ($c = $classes->fetch_assoc()) { ?>
                <option value="<?= (int)$c['CID'] ?>" <?= ($material && (int)$material['class_id']===(int)$c['CID']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['class_name']) ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($material['title'] ?? '') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control"><?= htmlspecialchars($material['description'] ?? '') ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">File <?= $material ? '(leave blank to keep current)' : '' ?></label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt">
            <?php if ($material && !empty($material['file_path'])) { ?>
              <p class="mt-2">
                Current: <a target="_blank" href="<?= htmlspecialchars($material['file_path']) ?>">View</a>
                <small class="text-muted">(<?= htmlspecialchars($material['file_type']) ?>)</small>
              </p>
            <?php } ?>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success"><?= $material ? 'Update' : 'Save' ?></button>
            <a href="study_materials.php" class="btn btn-secondary">Back</a>
          </div>

          <div id="msg" class="mt-3"></div>
        </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
const form = document.getElementById('matForm');
const msg  = document.getElementById('msg');

form.addEventListener('submit', function(e){
  e.preventDefault();
  msg.innerHTML = 'Saving...';

  const fd = new FormData(form);
  fetch(form.getAttribute('action'), {
    method: 'POST',
    body: fd,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(j => {
    if (j.ok) {
      msg.innerHTML = '<div class="alert alert-success">Saved successfully.</div>';
      if (j.redirect) {
        // Keep URL persistent with ?id=...
        window.history.replaceState({}, '', j.redirect);
      }
	  setTimeout(function() {
                    window.location.href = 'study_materials.php';
                }, 3000);
    } else {
      msg.innerHTML = '<div class="alert alert-danger">'+(j.msg || 'Save failed')+'</div>';
    }
  })
  .catch(() => {
    msg.innerHTML = '<div class="alert alert-danger">Network error.</div>';
  });
});
</script>
