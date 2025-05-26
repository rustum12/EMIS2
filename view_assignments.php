<?php
session_start();
require 'db.php';

// Ensure the user is a teacher
if (!isset($_SESSION['userid']) || $_SESSION['urole'] !== 'Teacher') {
    header("Location: login.php");
    exit();
}

// Fetch all submitted assignments with JOINs to show related class/session/student info
$query = "
    SELECT s.id, st.student_name AS student_name, c.class_name, se.session_name, s.title, s.filename, s.submitted_at
    FROM submissions s
    JOIN students st ON s.student_id = st.student_id
    JOIN classes c ON s.class_id = c.CID
    JOIN sessions se ON s.session_id = se.session_id
    ORDER BY s.submitted_at DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher - View Assignments</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container">
    <h2>📚 Submitted Assignments</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Student</th>
                <th>Class</th>
                <th>Session</th>
                <th>Title</th>
                <th>File</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['class_name']) ?></td>
                        <td><?= htmlspecialchars($row['session_name']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><a href="uploads/assignments/<?= htmlspecialchars($row['filename']) ?>" target="_blank">📄 View</a></td>
                        <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No assignments submitted yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>
