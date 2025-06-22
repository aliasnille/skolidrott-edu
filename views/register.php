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

        // Get the values from the form
        $first_name             = $_POST['first_name'];
        $last_name              = $_POST['last_name'];
        $phone_number           = $_POST['phone_number'];
        $email                  = $_POST['email'];
        $password               = $_POST['password'];
        $password_confirmation  = $_POST['password_confirmation'];
        $invite_code            = $_POST['invite_code'];
        
        // Check if the username and password are correct
        if (
            isset($first_name) && !empty($first_name)
            && isset($last_name) && !empty($last_name)
            && isset($phone_number) && !empty($phone_number)
            && isset($email) && !empty($email)
            && isset($password) && !empty($password)
            && isset($password_confirmation) && !empty($password_confirmation)
            && $password == $password_confirmation
            && isset($invite_code) && !empty($invite_code)
            && $register = register($first_name, $last_name, $phone_number, $email, $password, $invite_code)
        ) {

            // Display an status message
            $status = "<div class=\"alert alert-success rounded-0 pb-2\" role=\"alert\">";
            $status .= "Registreringen lyckades! Logga in för att komma igång.";
            $status .= "</div>";

        } else {

            // Display an status message
            $status = "<div class=\"alert alert-danger rounded-0 pb-2\" role=\"alert\">";
            $status .= "Registreringen misslyckades! Försök igen eller kontakta oss.";
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
        <title>Registrering | <?= CONFIG['siteSlogan']; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.12.0/dist/tabler-icons.min.css" integrity="sha256-xF1OEYamw3B/y+2wYAM7qAzSq55TLQ5RpQYkwzApR1s=" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= CONFIG['siteURL'] . CONFIG['paths']['css']; ?>/style.css?v=<?php echo time(); ?>">
        <meta name="robots" content="noindex">
    </head>
    <body class="register bg-overlay">

        <div class="register-container">
            <a href="<?= CONFIG['siteURL']; ?>/"><img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/skolidrottsforbundet-i-skane-white.svg" class="d-block mx-auto mt-5 mb-4" width="95" alt="<?= CONFIG['companyName']; ?>"></a>
            
            <h1 class="h4 text-center mb-4 text-uppercase text-white"><?= CONFIG['siteName']; ?></h1>

            <form method="POST" action="<?= CONFIG['siteURL']; ?>/register" class="bg-white p-4 rounded shadow mb-4">
                <?php set_csrf(); ?>
                <div class="tw-bg-gray-100 px-3 pt-3 pb-2 mb-3 border-start border-info border-3">
                    <h2 class="h5 fw-bold">Registrering för utbildare</h2>
                    <p class="mb-0">Endast utbildare med giltig inbjudningskod kan registrera sig. Inbjudningskod får godkända utbildare av utbildningsansvarig hos Skolidrottsförbundet i Skåne.</p>
                </div>

                <?php if (isset($status) && !empty($status)) { echo $status; } ?>

                <div class="row mb-3">
                    <div class="col">
                        <label for="first_name" class="form-label">Förnamn <i class="ti ti-asterisk small text-danger"></i></label>
                        <input type="text" name="first_name" minlength="2" maxlength="255" class="form-control" id="first_name" placeholder="Förnamn" required>
                    </div>
                    <div class="col">
                        <label for="last_name" class="form-label">Efternamn <i class="ti ti-asterisk small text-danger"></i></label>
                        <input type="text" name="last_name" minlength="2" maxlength="255" class="form-control" id="last_name" placeholder="Efternamn" required>
                    </div>
                </div>       
                <div class="row mb-3">
                    <div class="col">
                        <label for="phone_number" class="form-label">Telefon/mobilnummer <i class="ti ti-asterisk small text-danger"></i></label>
                        <input type="tel" name="phone_number" minlength="7" maxlength="20" class="form-control" id="phone_number" placeholder="Telefon/mobilnummer" required>
                    </div>
                    <div class="col">
                        <label for="email" class="form-label">E-postadress <i class="ti ti-asterisk small text-danger"></i></label>
                        <input type="email" name="email" minlength="3" maxlength="255" class="form-control" id="email" placeholder="E-postadress" required>
                    </div>
                </div>                
                <div class="row mb-3">
                    <div class="col">
                        <label for="password" class="form-label">Lösenord <i class="ti ti-asterisk small text-danger"></i></label>
                        <input type="password" name="password" minlength="10" class="form-control" id="password" placeholder="Lösenord" required>
                    </div>
                    <div class="col">
                        <label for="password_confirmation" class="form-label">Lösenord igen <i class="ti ti-asterisk small text-danger"></i></label>
                        <input type="password" name="password_confirmation" minlength="10" class="form-control" id="password_confirmation" placeholder="Lösenord igen" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="invite_code" class="form-label">Inbjudningskod <i class="ti ti-asterisk small text-danger"></i></label>
                    <input type="text" name="invite_code" minlength="35" maxlength="45" class="form-control" id="invite_code" placeholder="Inbjudningskod" value="<?= (isset($_GET['code']) && !empty($_GET['code'])) ? $_GET['code'] : ''; ?>" required>
                </div>                             
                <div class="d-grid mb-4">
                    <button type="submit" name="register" class="btn btn-custom-secondary btn-block">Registrera <i class="ti ti-arrow-right align-middle"></i></button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= CONFIG['siteURL']; ?>/" class="small link-secondary link-underline-opacity-0"><i class="ti ti-arrow-left"></i> Tillbaka</a>
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