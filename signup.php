<?php
// signup.php - User Signup
include 'db.php';

$meta_head = "EMIS Signup";

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = trim($_POST['uname']);
    $uemail = trim($_POST['uemail']);
    $upassword = $_POST['upassword'];
    $urole = $_POST['urole'];
    $CNIC = trim($_POST['CNIC']);

    // Validate Full Name (Only letters and spaces allowed)
    if (!preg_match("/^[a-zA-Z ]{3,50}$/", $uname)) {
        $message .= "Invalid Full Name. Use only letters and spaces (3-50 characters).<br>";
    }

    // Validate Email format
    if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
        $message .= "Invalid email format.<br>";
    } else {
        // Check for unique email
        $email_check_sql = "SELECT uemail FROM users WHERE uemail = ?";
        $stmt = $conn->prepare($email_check_sql);
        $stmt->bind_param("s", $uemail);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message .= "Email already exists.<br>";
        }
        $stmt->close();
    }

    // Validate CNIC (13 digits, not all same digits)
    if (!preg_match('/^\d{13}$/', $CNIC)) {
        $message .= "CNIC must be exactly 13 digits long.<br>";
    } elseif (preg_match('/^(\d)\1{12}$/', $CNIC)) {
        $message .= "CNIC cannot be all same digits.<br>";
    } else {
        // Check for unique CNIC
        $cnic_check_sql = "SELECT CNIC FROM users WHERE CNIC = ?";
        $stmt = $conn->prepare($cnic_check_sql);
        $stmt->bind_param("s", $CNIC);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message .= "This CNIC is already registered.<br>";
        }
        $stmt->close();
    }

    // Validate Password
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,32}$/", $upassword)) {
        $message .= "Password must be 8-32 characters long, with at least one uppercase letter, one lowercase letter, one number, and one special character.<br>";
    }

    // Insert data if validation passes
    if (empty($message)) {
        $hashed_password = password_hash($upassword, PASSWORD_DEFAULT);
		$token = md5(uniqid($uemail, true));
        $sql = "INSERT INTO users (uname, uemail, upassword, status, urole, CNIC,verification_token) VALUES (?, ?, ?, 'pending', ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $uname, $uemail, $hashed_password, $urole, $CNIC,$token);
		
        if ($stmt->execute()) {
            $message = "Signup successful!";
			$email = $uemail;
			
			////////////////////
			$signUP = true;
			include 'phpMailSettings.php';
			////////////////////
			
			
			
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
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
                <h2 class="text-center">Signup</h2>
                
                <?php if (!empty($message)) { ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php } ?>
                
                <form method="POST" action="signup.php">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="uname" class="form-control" required 
       pattern="^[A-Za-z ]{3,255}$" 
       title="Please enter only letters (3 to 255 characters)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="uemail" class="form-control" required 
       pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$"
       maxlength="255"
       title="Please enter a valid email address (max 255 characters)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNIC</label>
                        <input type="text" name="CNIC" class="form-control" required pattern="\d{13}" title="Enter exactly 13 digits">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="upassword" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="urole" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Parents">Parent</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Signup</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
