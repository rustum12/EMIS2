<?php
include 'db.php';
$meta_head = "Add Update Group";

if (isset($_SESSION['userid']))
	isLoggedIn ($_SESSION['uemail'], $_SESSION['upassword'],$conn);


if ($_SESSION['urole'] != 'Admin') {
    header("Location: index.php?action=logout");
    exit();
}

// Initialize variables
$gname = "";
$status = "active";
$id = 0;
$error = "";

// If form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gname = trim($_POST['gname']);
    $status = $_POST['status'];
    $id = intval($_POST['id']);

    // Check for duplicate group name (excluding current record if updating)
    $stmt = $conn->prepare("SELECT gid FROM groups WHERE gname = ? AND gid != ?");
    $stmt->bind_param("si", $gname, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Error: A group with this name already exists.";
    } else {
        if ($id > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE groups SET gname=?, status=? WHERE gid=?");
            $stmt->bind_param("ssi", $gname, $status, $id);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO groups (gname, status) VALUES (?, ?)");
            $stmt->bind_param("ss", $gname, $status);
        }
        $stmt->execute();
        header("Location: groups.php");
        exit();
    }
}

// Fetch data for update
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM groups WHERE gid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $gname = $data['gname'];
    $status = $data['status'];
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
                <h2><?= $id ? 'Edit' : 'Add' ?> Group</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <table class="table">
                        <tr>
                            <td><label>Group Name:</label></td>
                            <td><input type="text" name="gname" value="<?= htmlspecialchars($gname) ?>" class="form-control" required></td>
                        </tr>
                        <tr>
                            <td><label>Status</label></td>
                            <td>
                                <select name="status" class="form-control">
                                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">
                                <button type="submit" class="btn btn-success">Save</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>