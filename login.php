<?php
include 'db.php';
$meta_head = "Login to EMIS";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uemail = trim($_POST['uemail']);
    $upassword = $_POST['upassword'];
    
    // Validate input
    if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Check user in DB
        $sql = "SELECT * FROM users WHERE uemail = ? AND status = 'active'";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $uemail);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                if (password_verify($upassword, $row['upassword'])) {
                    $_SESSION['userid'] = $row['uid'];
                    $_SESSION['uname'] = $row['uname'];
                    $_SESSION['uemail'] = $row['uemail'];
                    $_SESSION['upassword'] = $row['upassword'];
                    $_SESSION['urole'] = $row['urole'];
                    $_SESSION['CNIC'] = $row['CNIC'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Incorrect password.";
                }
            } else {
                $message = "User not found or inactive.";
            }
        } else {
            $message = "Database error: " . $conn->error;
        }
    }
}
include 'header.php';
include 'navigation.php';
?>

  <style>
        
        .login-box {
            width: 100%;
            margin: 10px auto;
            padding: 10px;
            background: #fff;
            border-radius: 8px;
            /*box-shadow: 0 0 8px rgba(0,0,0,0.2);*/
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-box input[type="submit"] {
            width: 100%;
            padding: 5px;
            background: #28a745;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .login-box input[type="submit"]:hover {
            background: #218838;
        }
        .links {
            text-align: center;
            margin-top: 10px;
			width:50%;
			float:left;
        }
        .links a {
            text-decoration: none;
            color: #FFF;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .new-user-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            text-align: center;
            background: #007bff;
            color: white;
            border-radius: 12px;
            text-decoration: none;
        }
        .new-user-btn:hover {
            background: #0056b3;
        }
    </style>
<div class="container mt-4">
        <div class="row">
            <div class="col-md-3 bg-light p-3">
                <?php include 'leftbar.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card p-4">

<div class="login-box">
     
	<?php if (!empty($message)) echo '<p class="text-danger">' . $message . '</p>'; ?>
   <form id="loginForm" method="post" action="login.php">
    <h2 style="text-align:center;">Login</h2>
    <input type="email" id="uemail" name="uemail" placeholder="Enter Your Email" required class="form-control">
    <span id="emailError" class="error"></span>

    <input type="password" id="upassword" name="upassword" placeholder="Your Password Here" required class="form-control">
    <span id="passwordError" class="error"></span>

    <input type="submit" value="Login">
</form>

    <div class="links">
        <a href="forgot_password.php" class="new-user-btn" >Forgot Password?</a>
    </div>
 	<div class="links">
    <a href="signup.php" class="new-user-btn">Are you a new user? Register</a>
	</div>
</div>

     <!--
                       <h2 class="text-center">Login</h2>
                    <?php if (!empty($message)) echo '<p class="text-danger">' . $message . '</p>'; ?>
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="uemail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="upassword" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>-->
                    </div>
            </div>
        </div>
    </div>
    <script>
    document.getElementById("loginForm").addEventListener("submit", function(event) {
        // Prevent form submission initially
        event.preventDefault();

        // Get input values
        const email = document.getElementById("uemail").value.trim();
        const password = document.getElementById("upassword").value.trim();
        let isValid = true;

        // Clear old error messages
        document.getElementById("emailError").textContent = "";
        document.getElementById("passwordError").textContent = "";

        // Email validation using regex
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            document.getElementById("emailError").textContent = "Invalid email format";
            isValid = false;
        }

        // Password validation: 8 to 32 characters, at least 1 uppercase, 1 lowercase, 1 number, 1 special char
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,32}$/;
        if (!passwordPattern.test(password)) {
            document.getElementById("passwordError").textContent = 
                "Password must be 8-32 chars, incl. uppercase, lowercase, number & special char";
            isValid = false;
        }

        // Submit form if valid
        if (isValid) {
            document.getElementById("loginForm").submit();
        }
    });
</script>
    <?php include 'footer.php'; ?>