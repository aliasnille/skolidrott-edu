<?php
// Start a session
session_start();

// Unset course
unset($_SESSION['course']);

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    
    // Redirect to the login page
    header('Location: ' . CONFIG['siteURL']. '/');
    exit();

}

// Page info
$page_title = 'Om';

include 'templates/header.php';
?>
        <main class="container my-5">

            <h1 class="fs-1">Om</h1>

            <hr class="mb-5">

            <h4 class="fst-italic my-5">Under utveckling...</h4>   

        </main>
<?php include 'templates/footer.php'; ?>