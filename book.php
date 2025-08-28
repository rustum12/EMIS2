<?php
include 'db.php';
$meta_head = "Add Update Book";

if (isset($_SESSION['userid']))
	isLoggedIn ($_SESSION['uemail'], $_SESSION['upassword'],$conn);


if ($_SESSION['urole'] != 'Admin') {
    header("Location: index.php?action=logout");
    exit();
}

$btitle = $bcode = $bauthor = $bpublisher = $blevel = $status = $gid = "";
$update = false;

// Fetch record for update
if (isset($_GET['bid'])) {
    $bid = intval($_GET['bid']);
    $stmt = $conn->prepare("SELECT * FROM books WHERE bid = ?");
    $stmt->bind_param("i", $bid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $btitle = $row['btitle'];
    $bcode = $row['bcode'];
    $bauthor = $row['bauthor'];
    $bpublisher = $row['bpublisher'];
    $blevel = $row['blevel'];
    $status = $row['status'];
    $gid = $row['gid'];
    $update = true;
}

// Handle insert or update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bid = $_POST['bid'];
    $btitle = $_POST['btitle'];
    $bcode = $_POST['bcode'];
    $bauthor = $_POST['bauthor'];
    $bpublisher = $_POST['bpublisher'];
    $blevel = $_POST['blevel'];
   	$status = $_POST['status'];
    $gid = $_POST['gid'];

    if ($bid == "") {
        // Insert
        $stmt = $conn->prepare("INSERT INTO books (btitle, bcode, bauthor, bpublisher, blevel, gid, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssis", $btitle, $bcode, $bauthor, $bpublisher, $blevel, $gid, $status);
        $stmt->execute();
    } else {
        // Update
        $stmt = $conn->prepare("UPDATE books SET btitle=?, bcode=?, bauthor=?, bpublisher=?, blevel=?, gid=?, status=? WHERE bid=?");
        $stmt->bind_param("sssssssi", $btitle, $bcode, $bauthor, $bpublisher, $blevel, $gid, $status, $bid);
        $stmt->execute();
    }
    header("Location: books.php");
    exit();
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
                <h2 class="text-center">
    <h2><?php echo $update ? 'Edit' : 'Add'; ?> Book</h2>
    <form method="post" action="">
        <input type="hidden" name="bid" value="<?php echo $update ? $bid : ''; ?>">
        <table class="table">
            <tr>
                <td width="50%"><label>Book Title</label></td>
                <td><input type="text" name="btitle" value="<?php echo htmlspecialchars($btitle); ?>" required class="form-control"> </td>
            </tr>
            <tr>
                <td><label>Book Code</label></td>
                <td><input type="text" name="bcode" value="<?php echo htmlspecialchars($bcode); ?>" required class="form-control"></td>
            </tr>
            <tr>
                <td><label>Author</label></td>
                <td><input type="text" name="bauthor" value="<?php echo htmlspecialchars($bauthor); ?>" required class="form-control"></td>
            </tr>
            <tr>
                <td><label>Publisher</label></td>
                <td><input type="text" name="bpublisher" value="<?php echo htmlspecialchars($bpublisher); ?>" required class="form-control"></td>
            </tr>
            <tr>
                <td><label>Level</label></td>
                <td>
                    <select name="blevel" required class="form-control">
                        <option value="first year" <?php if($blevel=='first year') echo 'selected'; ?>>First Year</option>
                        <option value="second year" <?php if($blevel=='second year') echo 'selected'; ?>>Second Year</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Group</label></td>
                <td>
                    <select name="gid" required class="form-control">
                        <?php
                        $groups = $conn->query("SELECT gid, gname FROM groups WHERE status='active'");
                        while ($g = $groups->fetch_assoc()) {
                            $sel = ($gid == $g['gid']) ? 'selected' : '';
                            echo "<option value='{$g['gid']}' $sel>{$g['gname']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Status</label></td>
                <td>
                    <select name="status" required class="form-control">
                        <option value="active" <?php if($status=='active') echo 'selected'; ?>>Active</option>
                        <option value="inactive" <?php if($status=='inactive') echo 'selected'; ?>>Inactive</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;">
                    <input type="submit" value="Save" class="btn btn-primary">
                </td>
            </tr>
        </table>
    </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
