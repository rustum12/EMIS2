<?php
include 'db.php';
$meta_head = "Manage Books";

if (isset($_SESSION['userid']))
	isLoggedIn ($_SESSION['uemail'], $_SESSION['upassword'],$conn);


if ($_SESSION['urole'] != 'Admin') {
    header("Location: index.php?action=logout");
    exit();
}


$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'add' && isset($_POST['save'])) {
    $stmt = $conn->prepare("INSERT INTO books (btitle, bcode, bauthor, bpublisher, blevel, gid, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $_POST['btitle'], $_POST['bcode'], $_POST['bauthor'], $_POST['bpublisher'], $_POST['blevel'], $_POST['gid'], $_POST['status']);
    $stmt->execute();
    header("Location: books.php");
    exit;
}

if ($action == 'edit' && isset($_POST['save']) && isset($_GET['bid'])) {
    $stmt = $conn->prepare("UPDATE books SET btitle=?, bcode=?, bauthor=?, bpublisher=?, blevel=?, gid=?, status=? WHERE bid=?");
    $stmt->bind_param("ssssssis", $_POST['btitle'], $_POST['bcode'], $_POST['bauthor'], $_POST['bpublisher'], $_POST['blevel'], $_POST['gid'], $_POST['status'], $_GET['bid']);
    $stmt->execute();
    header("Location: books.php");
    exit;
}

if ($action == 'delete' && isset($_GET['bid'])) {
    $stmt = $conn->prepare("DELETE FROM books WHERE bid=?");
    $stmt->bind_param("i", $_GET['bid']);
    $stmt->execute();
    header("Location: books.php");
    exit;
}

$result = $conn->query("SELECT b.*, g.gname FROM books b LEFT JOIN groups g ON b.gid = g.gid");
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
    <h2>Books</h2>
  
<table class="table" >
        <thead>
            <tr>
                <th colspan="8">&nbsp;</th>
                <th><a href="book.php?action=add" class="btn btn-success">Add Book</a>  </th>
            </tr>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Code</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Level</th>
                <th>Group</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['bid']; ?></td>
                <td><?php echo $row['btitle']; ?></td>
                <td><?php echo $row['bcode']; ?></td>
                <td><?php echo $row['bauthor']; ?></td>
                <td><?php echo $row['bpublisher']; ?></td>
                <td><?php echo ucfirst($row['blevel']); ?></td>
                <td><?php echo $row['gname']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td>
                    <a href="book.php?action=edit&bid=<?php echo $row['bid']; ?>">Edit</a>
                    <a href="books.php?action=delete&bid=<?php echo $row['bid']; ?>" onclick="return confirm('Delete this book?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
