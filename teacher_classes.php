<?php
include 'db.php';
$meta_head = "View Teacher Classes";

if (isset($_SESSION['userid']))
	isLoggedIn ($_SESSION['uemail'], $_SESSION['upassword'],$conn);


if ($_SESSION['urole'] != 'Admin') {
    header("Location: index.php?action=logout");
    exit();
}
 // Initialize variables
$query = "
    SELECT 
        t.teacher_name, 
        c.class_name, 
        s.session_name 
    FROM 
        teacher_classes tc 
    JOIN 
        teachers t ON tc.teacher_id = t.teacher_id 
    JOIN 
        classes c ON tc.class_id = c.CID 
    JOIN 
        sessions s ON c.session_id = s.session_id 
    WHERE 
        s.status = 'active' 
        AND c.class_status = 'active' 
    LIMIT 0, 25
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
    <table class="table">
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Class Name</th>
                <th>Batch Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['teacher_name']) ?></td>
                    <td><?= htmlspecialchars($row['class_name']) ?></td>
                    <td><?= htmlspecialchars($row['session_name']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
