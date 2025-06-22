<!doctype html>
<html lang="<?= CONFIG['language']; ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>404 - sidan hittades ej | <?= CONFIG['siteSlogan']; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.12.0/dist/tabler-icons.min.css" integrity="sha256-xF1OEYamw3B/y+2wYAM7qAzSq55TLQ5RpQYkwzApR1s=" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= CONFIG['siteURL'] . CONFIG['paths']['css']; ?>/style.css?v=<?php echo time(); ?>">
        <meta name="robots" content="noindex">
    </head>
    <body class="not-found bg-overlay">

        <div class="container text-center">
            <a href="<?= CONFIG['siteURL']; ?>/"><img src="<?= CONFIG['siteURL'] . CONFIG['paths']['images']; ?>/skolidrottsforbundet-i-skane-white.svg" class="d-block mx-auto my-5" width="95" alt="<?= CONFIG['companyName']; ?>"></a>
            
            <h1 class="text-white mb-5">Oops! Sidan hittades inte.</h1>

            <a href="<?= CONFIG['siteURL']; ?>/" class="btn btn-lg btn-custom-secondary px-4" style="margin-bottom: 150px;"><i class="ti ti-arrow-left align-middle"></i> Ta mig tillbaka</a>

            <p class="small text-white-50 text-center mb-5">
                &copy; <?= date('Y'); ?> <?= CONFIG['companyName']; ?>.<br>
                Alla rättigheter förbehållna.
            </p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>