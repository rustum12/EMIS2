<?php
include 'db.php';
$meta_head = "ForGot Password";

// Check if user is logged in
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT uid FROM users WHERE uemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Save token in database
        $conn->query("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(64) NOT NULL,
            expiry DATETIME NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(uid) ON DELETE CASCADE
        )");

        // Delete old reset requests
        $conn->query("DELETE FROM password_resets WHERE user_id=(SELECT uid FROM users WHERE uemail='$email')");

        $stmt2 = $conn->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES ((SELECT uid FROM users WHERE uemail=?), ?, ?)");
        $stmt2->bind_param("sss", $email, $token, $expiry);
        $stmt2->execute();

        // Send reset link (simplified - in real app use PHPMailer)
       // $reset_link = "http://yourdomain.com/reset_password.php?token=" . $token;
		
		/////////////////////////
		$forgotPassword = true;
		include 'phpMailSettings.php';
		////////////////////
    }   
	else{
	$message = "Email could not be found";
	}
       
     
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
    <h2>Forgot Password?</h2>
 <form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required class="form-control mb-3">
    <input type="submit" value="Send Reset Link" class="btn btn-danger mb-3">
</form>
   <div class='container mt-5'>
            <div class='alert alert-danger text-center' role='alert'><?php echo $message; ?> </div>
        </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
