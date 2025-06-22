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

if (!isset($_SESSION['admin']) || empty($_SESSION['admin']) || $_SESSION['admin'] !== 1) {
    
    // Redirect to the start page
    header('Location: ' . CONFIG['siteURL']. '/start');
    exit();

}

// Page info
$page_title = 'Administration';

// Check if invite form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_csrf_valid()) {

    $create_invite = create_invite($_POST);

    if ($create_invite) {

        // Display an status message
        $status = "<div class=\"alert alert-success rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Inbjudan lyckades!</h5>";
        $status .= "</div>";

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-danger rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Inbjudan misslyckades!</h5>";
        $status .= "</div>";

    }

}

$invites = get_invites();

include 'templates/header.php';
?>
        <main class="container my-5">

            <h1 class="fs-1 mb-4">Administration</h1>

            <?php if (isset($status) && !empty($status)) { echo $status; } ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="ti ti-book"></i> Kurshantering</h5>
                            <p class="card-text">Skapa, redigera och hantera utbildningskurser.</p>
                            <a href="<?= CONFIG['siteURL']; ?>/course-management" class="btn btn-custom-primary">
                                <i class="ti ti-arrow-right"></i> Hantera kurser
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="ti ti-mail"></i> Inbjudningar</h5>
                            <p class="card-text">Skapa och hantera inbjudningskoder för nya användare.</p>
                            <a href="#invitations" class="btn btn-custom-secondary">
                                <i class="ti ti-arrow-down"></i> Hantera inbjudningar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="invitations" class="bg-light border rounded-3 p-4 mb-5">
                <form method="POST" action="<?= CONFIG['siteURL']; ?>/administration">
                    <?php set_csrf(); ?>

                    <h4>Skapa inbjudningskod</h4>

                    <div class="row">
                        <div class="col-8">
                            <label for="email" class="form-label visually-hidden">E-postadress <i class="ti ti-asterisk small text-danger"></i></label>
                            <input type="email" name="email" minlength="3" maxlength="255" class="form-control" id="email" placeholder="E-postadress" required>
                        </div>
                        <div class="col-2 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" name="send_mail" value="1" class="form-check-input py-0 my-0" id="send_mail">
                                <label class="form-check-label" for="send_mail">Skicka som e-post</label>
                            </div>                            
                        </div>
                        <div class="col-2">
                            <button type="submit" name="submit" class="btn btn-custom-secondary w-100"><i class="ti ti-circle-plus align-middle"></i> Skapa</button>
                        </div>
                    </div>                       
                </form>
            </div>            

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col"><small>#</small></th>
                        <th scope="col"><small>E-POSTADRESS</small></th>
                        <th scope="col"><small>KOD</small></th>
                        <th scope="col"><small>SKAPAD</small></th>
                        <th scope="col" class="text-center"><small>ANVÄND</small></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($invites) && !empty($invites) && count($invites) > 0) {
                        foreach ($invites as $invite) {
                    ?>
                    <tr>
                        <th scope="row"><?= $invite['id']; ?></th>
                        <td><?= $invite['email']; ?></td>
                        <td><?= $invite['code']; ?></td>
                        <td><?= $invite['created_at']; ?></td>
                        <td class="text-center">
                            <?= ($invite['is_active'] == 1) ? '<i class="ti ti-circle-x-filled text-danger align-middle"></i>' : '<i class="ti ti-circle-check-filled text-success align-middle"></i>'; ?>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="5" class="text-center"><em>Tomt på inbjudningskoder...</em></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>            

        </main>
<?php include 'templates/footer.php'; ?>