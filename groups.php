<?php
include 'db.php';
$meta_head = "Manage Groups";

if (isset($_SESSION['userid']))
	isLoggedIn ($_SESSION['uemail'], $_SESSION['upassword'],$conn);


if ($_SESSION['urole'] != 'Admin') {
    header("Location: index.php?action=logout");
    exit();
}

// Toggle status
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $currentStatus = $_GET['status'] ?? '';
    $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

    $stmt = $conn->prepare("UPDATE groups SET status = ? WHERE gid = ?");
    $stmt->bind_param("si", $newStatus, $id);
    $stmt->execute();
    header("Location: groups.php");
    exit();
}

// Fetch groups
$result = $conn->query("SELECT * FROM groups");

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
                <h2>All Groups</h2>
                <table class="table table-bordered">
                    <tr>
                        <th colspan="4"></th>
                        <th><a href="group.php" class="btn btn-success">+ Add</a></th>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>Group Name</th>
                        <th>Status</th>
                        <th colspan="2">Actions</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['gid'] ?></td>
                        <td><?= htmlspecialchars($row['gname']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><a href="group.php?id=<?= $row['gid'] ?>">Edit</a></td>
                        <td>
                            <a href="groups.php?toggle=<?= $row['gid'] ?>&status=<?= $row['status'] ?>"
                               onclick="return confirm('Are you sure you want to <?= $row['status'] === 'active' ? 'inactivate' : 'activate' ?> this group?')">
                               <?= $row['status'] === 'active' ? 'Inactivate' : 'Activate' ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>