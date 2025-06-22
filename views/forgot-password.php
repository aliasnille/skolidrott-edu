<?php
// Start a session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is already authenticated
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {

    header('Location: ' . CONFIG['siteURL'] . '/start');
    exit();

}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (is_csrf_valid()) {

        // Get the email from the form
        $email = $_POST['email'];
        
        // Check if the email are correct
        if (isset($email) && !empty($email) && $id = email_lookup($email)) {

            if (forgot_password($id, $email)) {

                // Display an status message
                $status = "<div class=\"alert alert-success rounded-0 pb-2\" role=\"alert\">";
                $status .= "Ett mail har skickats till dig för återställning av lösenord.";
                $status .= "</div>";

            } else {

                // Display an status message
                $status = "<div class=\"alert alert-danger rounded-0 pb-2\" role=\"alert\">";
                $status .= "Ett fel uppstod, försök igen.";
                $status .= "</div>";

            }

        } else {

            // Display an status message
            $status = "<div class=\"alert alert-danger rounded-0 pb-2\" role=\"alert\">";
            $status .= "Ogiltig e-postadress eller användaren saknas.";
            $status .= "</div>";

        }

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-warning rounded-0 pb-2\" role=\"alert\">";
        $status .= "CSRF-token matchar inte, ladda om sidan och försök igen.";
        $status .= "</div>";

    }

}
?>
<!doctype html>
<html lang="<?= CONFIG['language']; ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Glömt lösenord | <?= CONFIG['siteSlogan']; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.12.0/dist/tabler-icons.min.css" integrity="sha256-xF1OEYamw3B/y+2wYAM7qAzSq55TLQ5RpQYkwzApR1s=" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= CONFIG['siteURL'] . CONFIG['paths']['css']; ?>/style.css?v=<?php echo time(); ?>">
        <meta name="robots" content="noindex">
    </head>
    <body class="forgot-password bg-overlay">

        <div class="forgot-password-container">
            <a href="<?= CONFIG['siteURL']; ?>/"><img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/skolidrottsforbundet-i-skane-white.svg" class="d-block mx-auto mt-5 mb-4" width="95" alt="<?= CONFIG['companyName']; ?>"></a>
            
            <h1 class="h4 text-center mb-4 text-uppercase text-white"><?= CONFIG['siteName']; ?></h1>

            <form method="POST" action="<?= CONFIG['siteURL']; ?>/forgot-password" class="bg-white p-4 rounded shadow mb-4">
                <?php set_csrf(); ?>
                <div class="tw-bg-gray-100 px-3 pt-3 pb-2 mb-3 border-start border-info border-3">
                    <h2 class="h5 fw-bold">Glömt lösenord</h2>
                    <p class="mb-0">Ange din e-postadress för att få en länk för återställning av lösenord.</p>
                </div>

                <?php if (isset($status) && !empty($status)) { echo $status; } ?>

                <div class="mb-3">
                    <label for="email" class="form-label">E-postadress <i class="ti ti-asterisk small text-danger"></i></label>
                    <input type="email" name="email" minlength="3" maxlength="255" class="form-control" id="email" placeholder="Ange din e-postadress" required>
                </div>
                <div class="d-grid mb-4">
                    <button type="submit" name="reset" class="btn btn-custom-secondary btn-block">Skicka <i class="ti ti-arrow-right align-middle"></i></button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= CONFIG['siteURL']; ?>/" class="small link-secondary link-underline-opacity-0"><i class="ti ti-login"></i> Logga in</a>
                    <a href="<?= CONFIG['siteURL']; ?>/register" class="small link-secondary link-underline-opacity-0"><i class="ti ti-pencil"></i> Registrera</a>
                </div>                
            </form>

            <p class="small text-white-50 text-center mb-5">
                &copy; <?= date('Y'); ?> <?= CONFIG['companyName']; ?>.<br>
                Alla rättigheter förbehållna.
            </p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>