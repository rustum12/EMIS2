<?php
include 'db.php'; // Database connection

// Get user_id from URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die("User ID is required!");
}
$user_id = intval($_GET['user_id']);

// Fetch teacher_id using CNIC
$sql = "SELECT t.teacher_id FROM teachers t 
        INNER JOIN users u ON u.cnic = t.cnic 
        WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Teacher not found for this user!");
}
$row = $result->fetch_assoc();
$teacher_id = $row['teacher_id'];

// Handle file upload
if (isset($_POST['add_material'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // File upload handling
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['pdf', 'docx', 'pptx', 'jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_ext)) {
        echo "<div class='alert alert-danger'>Invalid file format!</div>";
    } else {
        $new_file_name = time() . "_" . basename($file_name);
        $upload_path = "uploads/study_material/" . $new_file_name;
        move_uploaded_file($file_tmp, $upload_path);

        // Insert record
        $sql = "INSERT INTO study_material (teacher_id, title, description, file_path, uploaded_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $teacher_id, $title, $description, $upload_path);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Study material added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding material!</div>";
        }
    }
}

// Update material
if (isset($_POST['update_material'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Check if file updated
    if (!empty($_FILES['file']['name'])) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['pdf', 'docx', 'pptx', 'jpg', 'jpeg', 'png'];

        if (!in_array($file_ext, $allowed_ext)) {
            echo "<div class='alert alert-danger'>Invalid file format!</div>";
        } else {
            $new_file_name = time() . "_" . basename($file_name);
            $upload_path = "uploads/study_material/" . $new_file_name;
            move_uploaded_file($file_tmp, $upload_path);

            $sql = "UPDATE study_material SET title=?, description=?, file_path=? WHERE id=? AND teacher_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $title, $description, $upload_path, $id, $teacher_id);
        }
    } else {
        $sql = "UPDATE study_material SET title=?, description=? WHERE id=? AND teacher_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $title, $description, $id, $teacher_id);
    }

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Study material updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating material!</div>";
    }
}

// Fetch all study materials
$sql = "SELECT * FROM study_material WHERE teacher_id=? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$materials = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Study Material</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="mb-4">Manage Study Material</h2>

    <!-- Add Material Form -->
    <div class="card mb-4">
        <div class="card-header">Add Study Material</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Upload File</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" name="add_material" class="btn btn-primary">Add Material</button>
            </form>
        </div>
    </div>

    <!-- Uploaded Materials Table -->
    <div class="card">
        <div class="card-header">Uploaded Study Materials</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>File</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $materials->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><a href="<?= $row['file_path'] ?>" target="_blank">Download</a></td>
                            <td><?= $row['uploaded_at'] ?></td>
                            <td>
                                <a href="edit_study_material.php?id=<?= $row['id'] ?>&user_id=<?= $user_id ?>" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
