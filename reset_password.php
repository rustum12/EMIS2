<?php
// reset_password.php
include("db.php");
$meta_head = "RESET Password";

$message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT user_id, expiry FROM password_resets WHERE token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $expiry);
    $stmt->fetch();

    if ($stmt->num_rows == 0 || strtotime($expiry) < time()) {
        die("Invalid or expired token!");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Update user password
        $stmt2 = $conn->prepare("UPDATE users SET upassword=? WHERE uid=?");
        $stmt2->bind_param("si", $new_password, $user_id);
        $stmt2->execute();

        // Delete token
        $stmt3 = $conn->prepare("DELETE FROM password_resets WHERE user_id=?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();

        $message = "Password reset successful. <a href='login.php'>Login</a>";
    }
} else {
    die("Invalid request!");
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
    <h2>Reset Password</h2>
    <form method="POST">
        <input type="password" name="password" placeholder="Enter new password" required>
        <input type="submit" value="Reset Password">
    </form>
     <div class='container mt-5'>
            <div class='alert alert-danger text-center' role='alert'><?php echo $message; ?></div>
        </div>
</div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
