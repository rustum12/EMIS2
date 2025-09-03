<div class="list-group">
    <?php if (isset($_SESSION['urole'])): ?>
        
        <?php if ($_SESSION['urole'] == 'Admin'): ?>
            <a href="users.php" class="list-group-item list-group-item-action">
                <i class="fas fa-users-cog me-2 text-danger"></i>Manage Users
            </a>
            <a href="batches.php" class="list-group-item list-group-item-action">
                <i class="fas fa-layer-group me-2 text-danger"></i>Manage Batches
            </a>
            <a href="classes.php" class="list-group-item list-group-item-action">
                <i class="fas fa-chalkboard me-2 text-danger"></i>Manage Classes
            </a>
            <a href="students.php" class="list-group-item list-group-item-action">
                <i class="fas fa-user-graduate me-2 text-danger"></i>Manage Students
            </a>
            <a href="teachers.php" class="list-group-item list-group-item-action">
                <i class="fas fa-chalkboard-teacher me-2 text-danger"></i>Manage Teachers
            </a>
            <a href="teacher_class.php" class="list-group-item list-group-item-action">
                <i class="fas fa-tasks me-2 text-danger"></i>Assign Classes to Teachers
            </a>
            <a href="teacher_classes.php" class="list-group-item list-group-item-action">
                <i class="fas fa-list me-2 text-danger"></i>Assigned Classes to Teachers
            </a>
            <a href="groups.php" class="list-group-item list-group-item-action">
                <i class="fas fa-object-group me-2 text-danger"></i>Manage Groups
            </a>
            <a href="books.php" class="list-group-item list-group-item-action">
                <i class="fas fa-book-reader me-2 text-danger"></i>Manage Books
            </a>

        <?php elseif ($_SESSION['urole'] == 'Teacher'): ?>
            <a href="teacher.php" class="list-group-item list-group-item-action">
                <i class="fas fa-list me-2 text-danger"></i>Teacher Prfoile
            </a>
            <a href="t_teacher_classes.php" class="list-group-item list-group-item-action">
                <i class="fas fa-list me-2 text-danger"></i>My Classes
            </a>
            <a href="teacherstudents.php" class="list-group-item list-group-item-action">
                <i class="fas fa-user-graduate me-2 text-danger"></i>My Students
            </a>
            <a href="study_materials.php" class="list-group-item list-group-item-action">
                <i class="fas fa-book-reader me-2 text-danger"></i>Study Material
            </a>

        <?php elseif ($_SESSION['urole'] == 'Student'): ?>
            <a href="student_study_materials.php" class="list-group-item list-group-item-action">
                <i class="fas fa-chalkboard me-2 text-danger"></i>My Study Materials
            </a>
            <a href="student_attendance.php" class="list-group-item list-group-item-action">
                <i class="fas fa-book-reader me-2 text-danger"></i>My Attendance
            </a>

        <?php elseif ($_SESSION['urole'] == 'Parents'): ?>
            <a href="parents-students.php" class="list-group-item list-group-item-action">
                <i class="fas fa-user-graduate me-2 text-danger"></i>My Children
            </a>
            

        <?php endif; ?>

    <?php else: ?>
        <p class="text-muted p-2">Please log in to see the menu.</p>
    <?php endif; ?>
</div>
