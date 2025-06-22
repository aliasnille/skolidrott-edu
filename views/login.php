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

// The reset password status message
if (isset($_SESSION['reset_password']) && !empty($_SESSION['reset_password']) && $_SESSION['reset_password'] == 'success') {

    unset($_SESSION['reset_password']);

    // Display an status message
    $status = "<div class=\"alert alert-success rounded-0 pb-2\" role=\"alert\">";
    $status .= "Lösenordet är uppdaterat. Logga in med ditt nya lösenord.";
    $status .= "</div>";

}

// The valid reset status message
if (isset($_SESSION['valid_reset']) && !empty($_SESSION['valid_reset']) && $_SESSION['valid_reset'] == 'fail') {

    unset($_SESSION['valid_reset']);
    
    // Display an status message
    $status = "<div class=\"alert alert-danger rounded-0 pb-2\" role=\"alert\">";
    $status .= "Ogiltig återställningslänk! Påbörjar ny återställning av lösenord.";
    $status .= "</div>";

}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (is_csrf_valid()) {

        // Get the username and password from the form
        $username   = $_POST['username'];
        $password   = $_POST['password'];
        
        // Check if the username and password are correct
        if (isset($username) && !empty($username) && isset($password) && !empty($password) && $session = login($username, $password)) {

            session_regenerate_id();
            $_SESSION['authenticated']  = true;
            $_SESSION['id']             = $session['id'];
            $_SESSION['first_name']     = $session['first_name'];
            $_SESSION['last_name']      = $session['last_name'];
            $_SESSION['email']          = $session['email'];
            $_SESSION['admin']          = $session['admin'];
            $_SESSION['signed_in']      = [
                'date_time'     => $session['date_time'],
                'ip_address'    => $session['ip_address']
            ];
            
            // Redirect to the start page
            header('Location: ' . CONFIG['siteURL'] . '/start');
            exit();

        } else {

            // Display an status message
            $status = "<div class=\"alert alert-danger rounded-0 pb-2\" role=\"alert\">";
            $status .= "Ogiltig e-postadress eller lösenord.";
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
        <title>Inloggning | <?= CONFIG['siteSlogan']; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.12.0/dist/tabler-icons.min.css" integrity="sha256-xF1OEYamw3B/y+2wYAM7qAzSq55TLQ5RpQYkwzApR1s=" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= CONFIG['siteURL'] . CONFIG['paths']['css']; ?>/style.css?v=<?php echo time(); ?>">
        <meta name="robots" content="noindex">
    </head>
    <body class="login bg-overlay">

        <div class="login-container">
            <a href="<?= CONFIG['siteURL']; ?>/" title="<?= CONFIG['companyName']; ?> - Utbildningsplattform"><img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/skol-if-skane-logo-beige.svg" class="d-block mx-auto mt-5 mb-4" width="120" alt="<?= CONFIG['companyName']; ?> - Utbildningsplattform"></a>
            
            <h1 class="h4 text-center mb-4 text-uppercase text-white"><?= CONFIG['siteName']; ?></h1>

            <form method="POST" action="<?= CONFIG['siteURL']; ?>/" class="bg-white p-4 rounded shadow mb-4">
                <?php set_csrf(); ?>

                <?php if (isset($status) && !empty($status)) { echo $status; } ?>                

                <div class="mb-3">
                    <label for="username" class="form-label">Användarnamn</label>
                    <input type="email" name="username" minlength="3" maxlength="255" class="form-control" id="username" placeholder="Ange ditt användarnamn" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Lösenord</label>
                    <input type="password" name="password" minlength="10" class="form-control" id="password" placeholder="Ange ditt lösenord" required>
                </div>
                <div class="d-grid mb-4">
                    <button type="submit" name="submit" class="btn btn-custom-secondary btn-block">Logga in <i class="ti ti-arrow-right align-middle"></i></button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= CONFIG['siteURL']; ?>/forgot-password" class="small link-secondary link-underline-opacity-0"><i class="ti ti-key"></i> Glömt lösenord</a>
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