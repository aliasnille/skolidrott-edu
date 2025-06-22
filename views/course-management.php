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

// Check if user is admin
if (!isset($_SESSION['admin']) || empty($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    
    // Redirect to the start page
    header('Location: ' . CONFIG['siteURL']. '/start');
    exit();

}

// Page info
$page_title = 'Kurshantering';

// Handle course deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_delete']) && is_csrf_valid()) {

    $delete_course = delete_course_admin($_POST['course_id']);

    if ($delete_course) {

        // Display success message
        $_SESSION['status'] = "<div class=\"alert alert-success rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Kursen togs bort utan problem!</h5></div>";

    } else {

        // Display error message
        $_SESSION['status'] = "<div class=\"alert alert-danger rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Borttagning av kursen misslyckades!</h5></div>";

    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;    

}

// Get all courses
$courses = get_all_courses();

include 'templates/header.php';
?>
        <main class="container my-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-1 mb-0">Kurshantering</h1>
                <a href="<?= CONFIG['siteURL']; ?>/course-management/create" class="btn btn-custom-primary">
                    <i class="ti ti-plus"></i> Skapa ny kurs
                </a>
            </div>

            <?php if (isset($_SESSION['status']) && !empty($_SESSION['status'])) { echo $_SESSION['status']; } $_SESSION['status'] = null; ?>

            <?php if (isset($courses) && !empty($courses) && count($courses) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col"><small>ID</small></th>
                            <th scope="col"><small>TITEL</small></th>
                            <th scope="col"><small>BESKRIVNING</small></th>
                            <th scope="col"><small>SKAPAD</small></th>
                            <th scope="col" class="text-center"><small>ÅTGÄRDER</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course) { ?>
                        <tr>
                            <th scope="row"><?= $course['id']; ?></th>
                            <td>
                                <strong><?= $course['title']; ?></strong>
                                <?= (!empty($course['byline'])) ? '<br><small class="text-muted">' . $course['byline'] . '</small>' : ''; ?>
                            </td>
                            <td><?= $course['description']; ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($course['created_at'])); ?></td>
                            <td class="text-center">
                                <div class="d-flex gap-2">
                                    <a href="<?= CONFIG['siteURL']; ?>/course?id=<?= $course['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Förhandsgranska">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="<?= CONFIG['siteURL']; ?>/course-management/edit/<?= $course['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Redigera">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form action="<?= CONFIG['siteURL']; ?>/course-management" method="POST" onsubmit="return confirm('Är du säker på att du vill radera denna kurs? Detta kan inte ångras.');" style="display:inline-block;">
                                        <?php set_csrf(); ?>
                                        <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
                                        <button type="submit" name="submit_delete" class="btn btn-sm btn-outline-danger" title="Ta bort">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
            <div class="text-center py-5">
                <i class="ti ti-book-off display-1 text-muted"></i>
                <h3 class="mt-3">Inga kurser hittades</h3>
                <p class="text-muted">Börja med att skapa din första kurs.</p>
                <a href="<?= CONFIG['siteURL']; ?>/course-management/create" class="btn btn-custom-primary">
                    <i class="ti ti-plus"></i> Skapa ny kurs
                </a>
            </div>
            <?php } ?>

        </main>
<?php include 'templates/footer.php'; ?>
