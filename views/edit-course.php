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

// Get course
$course =  get_course($_id, $_SESSION['id']);

// Wrong parameters and no course found
if (!isset($_id) || empty($_id) || !is_numeric($_id) || !$course) {
    
    http_response_code(404);
    exit();

}

// Page info
$page_title = 'Redigera ' . $course['title'];

// Update course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && is_csrf_valid()) {

    $update_course = update_course($_POST, $_SESSION['id']);

    if ($update_course) {

        // Display an status message
        $_SESSION['status'] = "<div class=\"alert alert-success rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Utbildningen uppdaterades utan problem!</h5></div>";

    } else {

        // Display an status message
        $_SESSION['status'] = "<div class=\"alert alert-danger rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen av utbildningen misslyckades!</h5></div>";

    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;

}

include 'templates/header.php';
?>
        <main class="container my-5">
            <h1 class="fs-1 mb-4">Redigera <em>"<?= $course['title']; ?>"</em></h1>
            <?php if (isset($_SESSION['status']) && !empty($_SESSION['status'])) { echo $_SESSION['status']; } $_SESSION['status'] = null; ?>
            <p>
                <em class="fw-light">Det går att ändra ordningen i utbildningen genom att dra och släppa en "slide". Glöm inte att spara!</em>
            </p>
          
            <form id="slidesForm" method="POST" action="<?= CONFIG['siteURL']; ?>/edit-course/<?= $course['id']; ?>">
                <?php set_csrf(); ?>
                <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                <div id="slidesContainer" class="row">
                    <?php foreach ($course['content'] as $key => $slide) { ?>
                    <div class="col-3" data-slide-id="<?= $slide['id'] ?>" data-slide-order="<?= $slide['order'] ?>">
                        <div class="card mb-4">
                            <img src="<?= CONFIG['siteURL']; ?>/courses/<?= $course['hash']; ?>/<?= $slide['thumb']; ?>" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?= $slide['title'] ?></h5>                            
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="include[]" value="<?= $slide['id'] ?>" role="switch" id="slideIsActive"<?= ($slide['is_active']) ? ' checked' : ''; ?><?= (!$slide['excludable']) ? ' disabled' : ''; ?>>
                                    <label class="form-check-label my-1" for="slideIsActive">Exkludera/inkludera</label>
                                </div>
                            </div>
                        </div>                    
                    </div>
                    <?php } ?>
                </div>
                <button type="submit" name="submit" class="btn btn-custom-secondary"><i class="ti ti-device-floppy"></i> Spara</button>
            </form>
        </main>
<?php include 'templates/footer.php'; ?>