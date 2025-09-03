<?php
// download.php
include 'db.php';
 
// Ensure login
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Get file by material ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request");
}

$material_id = intval($_GET['id']);

// Fetch file details
$stmt = $conn->prepare("SELECT file_path, title FROM study_materials WHERE material_id = ?");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("File not found");
}

$row = $result->fetch_assoc();
$file_path = $row['file_path'];
$file_name = basename($file_path);

// Full server path (assuming uploads outside web root for security)
$full_path = __DIR__ . "/" . $file_path;

if (!file_exists($full_path)) {
    die("File does not exist on server");
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($full_path));
readfile($full_path);
exit();
?>
