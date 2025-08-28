<?php
include 'db.php';
$meta_head = "Verify Email";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT uid FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Mark email as verified
        $stmt = $conn->prepare("UPDATE users SET status = 'active', verification_token = NULL WHERE verification_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo "<h3>Email verified successfully!</h3>";
        echo "<a href='login.php'>Click here to login</a>";
    } else {
        echo "<h3>Invalid or expired token!</h3>";
    }
} else {
    echo "<h3>Invalid request!</h3>";
}
?>
