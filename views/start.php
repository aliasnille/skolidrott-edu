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
$page_title = 'Start';

// Duplicate course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_duplicate']) && is_csrf_valid()) {

    $duplicate_course = duplicate_course($_POST);

    if ($duplicate_course) {

        // Display an status message
        $_SESSION['status'] = "<div class=\"alert alert-success rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Utbildningen duplicerades utan problem!</h5></div>";

    } else {

        // Display an status message
        $_SESSION['status'] = "<div class=\"alert alert-danger rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Dupliceringen av utbildningen misslyckades!</h5></div>";

    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;    

}

// Delete course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_delete']) && is_csrf_valid()) {

    $delete_course = delete_course($_POST['course_id'], $_SESSION['id']);

    if ($delete_course) {

        // Display an status message
        $_SESSION['status'] = "<div class=\"alert alert-success rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Utbildningen togs bort utan problem!</h5></div>";

    } else {

        // Display an status message
        $_SESSION['status'] = "<div class=\"alert alert-danger rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Borttagning av utbildningen misslyckades!</h5></div>";

    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;    

}


include 'templates/header.php';
?>
        <header class="start py-5 mb-5 d-flex align-items-center">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Välkommen</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Digitalt utbildningsmaterial från Skolidrottsförbundet i Skåne</p>
                </div>
            </div>
        </header>
        
        <main class="container mb-5">
            <?php if (isset($_SESSION['status']) && !empty($_SESSION['status'])) { echo $_SESSION['status']; } $_SESSION['status'] = null; ?>
            <h1 class="fs-1">Start</h1>

            <h2 class="mt-5 mb-4">Tillgängliga utbildningar</h2>

            <?php
            $original_courses = get_courses();

            if (isset($original_courses) && !empty($original_courses) && count($original_courses) > 0) { ?>
            <div class="row">
                <?php foreach ($original_courses as $course) { ?>
                <div class="col-3 mb-5">
                    <div class="card">
                        <img src="<?= CONFIG['siteURL']; ?>/courses/<?= $course['hash']; ?>/<?= $course['image']; ?>" class="card-img-top" alt="<?= $course['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= $course['title']; ?>
                                <?= (!empty($course['byline'])) ? '<br><small class="text-black-50">' . $course['byline'] . '</small>' : ''; ?>
                            </h5>
                            <p class="card-text"><?= $course['description']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-custom-secondary" data-bs-toggle="modal" data-bs-target="#duplicateCourseModal" data-bs-course-id="<?= $course['id']; ?>" data-bs-course-title="<?= $course['title']; ?> [kopia]"><i class="ti ti-copy"></i> Duplicera</button>
                                <a href="<?= CONFIG['siteURL']; ?>/course?id=<?= $course['id']; ?>" target="_blank" class="btn btn-sm btn-custom-primary"><i class="ti ti-player-play"></i> Starta</a>
                            </div>
                        </div>
                    </div>                    
                </div>
                <?php } ?>
            </div>            
            <?php } else { ?>
            <p>
                <em>Det finns inga utbildningar...</em>
            </p>
            <?php } ?>

            <h2 class="mt-4 mb-4">Dina utbildningar</h2>

            <?php
            $duplicated_courses = get_courses('duplicates');

            if (isset($duplicated_courses) && !empty($duplicated_courses) && count($duplicated_courses) > 0) { ?>
            <div class="row">
                <?php foreach ($duplicated_courses as $course) { ?>
                <div class="col-3 mb-5">
                    <div class="card">
                        <img src="<?= CONFIG['siteURL']; ?>/courses/<?= $course['hash']; ?>/<?= $course['image']; ?>" class="card-img-top" alt="<?= $course['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= $course['title']; ?>
                                <?= (!empty($course['byline'])) ? '<br><small class="text-black-50">' . $course['byline'] . '</small>' : ''; ?>
                            </h5>
                            <p class="card-text"><?= $course['description']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= CONFIG['siteURL']; ?>/edit-course/<?= $course['id']; ?>" class="btn btn-sm btn-custom-secondary">
                                    <i class="ti ti-edit"></i> Redigera
                                </a>
                                <a href="<?= CONFIG['siteURL']; ?>/course?id=<?= $course['id']; ?>" target="_blank" class="btn btn-sm btn-custom-primary">
                                    <i class="ti ti-player-play"></i> Starta
                                </a>
                                <form action="<?= CONFIG['siteURL']; ?>/start" method="POST" onsubmit="return confirm('Är du säker på att du vill radera denna kurs?');" style="display:inline-block;">
                                    <?php set_csrf(); ?>
                                    <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                                    <button type="submit" name="submit_delete" class="btn btn-sm btn-danger">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>                            
                        </div>
                    </div>                    
                </div>
                <?php } ?>
            </div>            
            <?php } else { ?>
            <p>
                <em>Det finns inga utbildningar...</em>
            </p>
            <?php } ?>            

            <!-- Duplicate course -->
            <div class="modal fade" id="duplicateCourseModal" tabindex="-1" aria-labelledby="duplicateCourseModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <form action="<?= CONFIG['siteURL']; ?>/start" method="POST">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="duplicateCourseModal">Duplicera utbildning</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Stäng"></button>
                        </div>
                        <div class="modal-body">
                            <?php set_csrf(); ?>
                            <input type="hidden" name="uid" value="<?= $_SESSION['id']; ?>" required>
                            <input type="hidden" name="course_id" value="" required>
                            <div class="mb-3">
                                <label for="duplicateCourseTitle" class="form-label">Utbildningenstitel</label>
                                <input type="text" name="course_title" minlength="5" class="form-control form-control-lg" id="duplicateCourseTitle" placeholder="Namnge utbildningen" required>
                            </div>                                
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="ti ti-square-x"></i> Stäng</button>
                            <button  type="submit" name="submit_duplicate" class="btn btn-custom-secondary"><i class="ti ti-copy"></i> Duplicera</button>
                        </div>
                        </form>  
                    </div>
                </div>
            </div>            
        </main>
<?php include 'templates/footer.php'; ?>