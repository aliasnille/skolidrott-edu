<!doctype html>
<html lang="<?= CONFIG['language']; ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= (isset($page_title) && !empty($page_title)) ? $page_title : 'Untitled' ; ?> | <?= CONFIG['siteSlogan']; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.12.0/dist/tabler-icons.min.css" integrity="sha256-xF1OEYamw3B/y+2wYAM7qAzSq55TLQ5RpQYkwzApR1s=" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= CONFIG['siteURL'] . CONFIG['paths']['css']; ?>/style.css?v=20250313093413">
    </head>
    <body class="dashboard">

        <nav class="navbar navbar-expand-lg bg-custom-primary navbar-dark py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-uppercase fw-bold" href="<?= CONFIG['siteURL']; ?>/start" title="<?= CONFIG['companyName']; ?> - Utbildningsplattform">
                    <img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/skol-if-skane-logo-beige.svg" alt="<?= CONFIG['companyName']; ?> - Utbildningsplattform" width="53" height="60" class="d-inline-block align-text-middle me-2">
                    Utbildningsplattform
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link<?= current_page($_SERVER['REQUEST_URI'], 'start'); ?>" href="<?= CONFIG['siteURL']; ?>/start">Start</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= current_page($_SERVER['REQUEST_URI'], 'about'); ?>" href="<?= CONFIG['siteURL']; ?>/about">Om</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= current_page($_SERVER['REQUEST_URI'], 'instructions'); ?>" href="<?= CONFIG['siteURL']; ?>/instructions">Instruktioner</a>
                        </li>    
                        <li class="nav-item">
                            <a class="nav-link<?= current_page($_SERVER['REQUEST_URI'], 'profile'); ?>" href="<?= CONFIG['siteURL']; ?>/profile">Profil</a>
                        </li>    
                        <?php if (isset($_SESSION['admin']) && !empty($_SESSION['admin']) && $_SESSION['admin'] == 11) { ?>
                        <li class="nav-item">
                            <a class="nav-link<?= current_page($_SERVER['REQUEST_URI'], 'administration'); ?>" href="<?= CONFIG['siteURL']; ?>/administration">Administration</a>
                        </li>                                                                         
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= CONFIG['siteURL']; ?>/log-out">Logga ut</a>
                        </li>                         
                    </ul>
                </div>
            </div>
        </nav>    
