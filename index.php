<?php
include 'db.php';
// Include necessary files if required (e.g., database connection, session handling)
$meta_head = "Welcome to EMIS";


/////////// Logging Out Starts
$loggedoutMessage = '';
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy session and clear session variables
 

    // Finally, destroy the session
    session_destroy();
	
		$loggedoutMessage	=	"<div class='container mt-5'>
            <div class='alert alert-danger text-center' role='alert'>
                <strong>You have been logged out successfully.</strong><br>
                You will be redirected to the main page in 5 seconds.
            </div>
        </div>";
		   header("Refresh:5; url=index.php");

}



//////////////// Logging Out ends







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
       <?php
	   		if($loggedoutMessage){
				echo $loggedoutMessage;
			}
	   ?>
        <h1>Welcome to the Education Management Information System (EMIS)</h1>
        <p>The Education Management Information System (EMIS) is made to make college management easier. It gives one place where  teachers, students, and parents can manage attendance, check student progress, and see class schedules.</p>
        <p>Our EMIS System is made to make learning better. It gives quick updates, helps colleges use resources properly, and makes communication easy between teachers, students, parents, and admins. With simple dashboards and automatic work, schools can save time and focus more on teaching.</p>
        <p>Key Features of the EMIS include:</p>
        <ul>
            <li>Automated attendance tracking</li>
            <li>Manage teachers, students.</li>
            <li>Class scheduling and  management</li>
            <li>Manage student attendance (present, absent, on leave).</li>
            <li>Secure and scalable database for storing academic records</li>
        </ul>
        <p>Explore the system and discover how it can revolutionize college management and enhance the overall learning experience!</p>
        </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>