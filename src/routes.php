<?php
// INDEX
get('/', '../views/login.php');
post('/', '../views/login.php');

// FORGOT PASSWORD
get('/forgot-password', '../views/forgot-password.php');
post('/forgot-password', '../views/forgot-password.php');

// RESET PASSWORD
get('/reset-password', '../views/reset-password.php');
post('/reset-password', '../views/reset-password.php');

// REGISTER
get('/register', '../views/register.php');
post('/register', '../views/register.php');

// START
get('/start', '../views/start.php');
post('/start', '../views/start.php');

// ABOUT
get('/about', '../views/about.php');

// INSTRUCTIONS
get('/instructions', '../views/instructions.php');

// PROFILE
get('/profile', '../views/profile.php');
post('/profile', '../views/profile.php');

// LOG OUT
get('/log-out', function() {

    // Start a session
    session_start();

    logout($_SESSION['id']);

    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: ' . CONFIG['siteURL'] . '/');
    exit();

});

// ADMINISTRATION
get('/administration', '../views/administration.php');
post('/administration', '../views/administration.php');

// COURSE MANAGEMENT
get('/course-management', '../views/course-management.php');
post('/course-management', '../views/course-management.php');
get('/course-management/create', '../views/course-management-edit.php');
post('/course-management/create', '../views/course-management-edit.php');
get('/course-management/edit/$_id', '../views/course-management-edit.php');
post('/course-management/edit/$_id', '../views/course-management-edit.php');

// PRESENTATION
get('/course', '../views/course.php');
post('/course', '../views/course.php');

// EDIT COURSE
get('/edit-course/$_id', '../views/edit-course.php');
post('/edit-course/$_id', '../views/edit-course.php');

// 404 - NOT FOUND
any('/404', '../views/404.php');