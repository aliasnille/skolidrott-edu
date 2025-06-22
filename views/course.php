<?php
// Start a session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    
    // Redirect to the login page
    header('Location: ' . CONFIG['siteURL']. '/');
    exit();

}

// Get course
$course =  get_course($_GET['id']);

// Wrong parameters and no course found
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id']) || !$course) {
    
    http_response_code(404);
    exit();

}

//
if (isset($_SESSION['course']['educator']) && !isset($_GET['presentation'])) {

    unset($_SESSION['course']['educator']);

    if (isset($_SESSION['course']['co_educator'])) {
        unset($_SESSION['course']['co_educator']);
    }

}

// Get users
$users = get_users($_SESSION['id']);

// Page info
$page_title = $course['title'];

// Check if basic form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_co_educator']) && is_csrf_valid()) {

    // Create course session
    $_SESSION['course']['educator'] = get_educator($_SESSION['id']);
    if ($_POST['co_educator'] >= 1) {
        $_SESSION['course']['co_educator'] = get_educator($_POST['co_educator']);
    }

    $_SESSION['course']['content'] = make_content($course['content'], $_SESSION['course']['educator'], $_SESSION['course']['co_educator']);

    header('Location: ' . CONFIG['siteURL']. '/course?id=' . $_POST['course_id'] . '&presentation=true');
    exit();

}
?>
<!doctype html>
<html lang="<?= CONFIG['language']; ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $page_title; ?> | <?= CONFIG['siteSlogan']; ?></title>
        <meta name="robots" content="noindex">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.12.0/dist/tabler-icons.min.css" integrity="sha256-xF1OEYamw3B/y+2wYAM7qAzSq55TLQ5RpQYkwzApR1s=" crossorigin="anonymous">
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
        <link rel="stylesheet" href="<?= CONFIG['siteURL'] . CONFIG['paths']['css']; ?>/style.css?v=<?php echo time(); ?>">
        <style>
            body, html {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            .wrapper {
                height: 100vh;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 50px;
            }

            .wrapper a.btn {
                color: rgba(255, 255, 255, 0.25);
                background-color: transparent;
                border: none;                
            }

            .wrapper a.btn:hover {
                color: rgba(255, 255, 255, 1);
                background-color: transparent;
                border: none;
            } 

            .content {
                text-align: center;
                flex: 1;
            }

            .content img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }         
        </style>
    </head>
    <body class="bg-custom-primary">
        <?php
        if (isset($_SESSION['course']['educator']) && !empty($_SESSION['course']['educator']) && isset($_GET['presentation']) && $_GET['presentation'] == 'true') {
            
            // Get course content
            $content        = $_SESSION['course']['content'];
            $last_slide     = count($content);
            $current_slide  = isset($_GET['slide']) && $_GET['slide'] > 0 ? $_GET['slide'] : 1;
            $slide_id       = $current_slide - 1;
        ?>
        <div class="wrapper">
            <!-- Previous Button -->
            <?php if (isset($_GET['slide']) && $_GET['slide'] > 1) { ?>
            <a class="btn btn-light" href="<?= CONFIG['siteURL'] . '/course?id=' . $course['id'] . '&presentation=true&slide=' . ($current_slide-1); ?>" role="button"><i class="ti ti-chevron-left display-1"></i></a>
            <?php } else { ?>
            <a class="btn btn-light disabled" href="<?= CONFIG['siteURL'] . '/course?id=' . $course['id'] . '&presentation=true'; ?>" role="button" aria-disabled="true"><i class="ti ti-chevron-left display-1"></i></a>                
            <?php } ?>

            <!-- Main Content -->
            <?php if ($content[$slide_id]['type'] == 'image') { ?>
            <div class="content">
                <img src="<?= CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/' . $content[$slide_id]['link']; ?>" alt="Page Content">
            </div>
            <?php } ?>

            <?php if ($content[$slide_id]['type'] == 'video') { ?>
            <div class="content p-5">
                <div class="ratio ratio-16x9">
                    <iframe class="w-100 h-100"
                            src="<?= $content[$slide_id]['link']; ?>"
                            allowfullscreen>
                    </iframe>
                </div>
            </div>
            <?php } ?>     

            <?php if ($content[$slide_id]['type'] == 'placeholder' && $content[$slide_id]['module'] == 'educator') { ?>            
            <div class="content">
                <div class="ratio ratio-16x9 position-relative">
                    <img src="<?= (!empty($content[$slide_id]['link'])) ? CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/' . $content[$slide_id]['link'] : CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/default.png'; ?>" class="position-absolute w-100 h-100" style="object-fit: cover; z-index: 1;">
                    <div class="position-absolute top-0 start-0 w-100 h-100 px-5" style="z-index: 2;">
                        <div class="row w-100 h-100">
                            <div class="col d-flex justify-content-center align-items-center">
                                <img src="<?= (isset($content[$slide_id]['content']['image']) && !empty($content[$slide_id]['content']['image'])) ? CONFIG['siteURL'] . '/' . CONFIG['paths']['uploads'] . '/' . $content[$slide_id]['content']['image'] : CONFIG['siteURL'] . '/' . CONFIG['paths']['images'] . '/' . 'dummy-avatar.jpg'; ?>" class="rounded-circle mb-4" width="350">                                
                            </div>
                            <div class="col text-start">
                                <div class="ps-5 pe-2 py-5">
                                    <h1 class="fw-bold text-custom-primary"><?= $content[$slide_id]['content']['heading']; ?></h1>

                                    <p class="fs-4">
                                        <?= $content[$slide_id]['content']['text']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <?php } ?>            

            <?php if ($content[$slide_id]['type'] == 'placeholder' && $content[$slide_id]['module'] == 'co_educator') { ?>            
            <div class="content">
                <div class="ratio ratio-16x9 position-relative">
                    <img src="<?= (!empty($content[$slide_id]['link'])) ? CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/' . $content[$slide_id]['link'] : CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/default.png'; ?>" class="position-absolute w-100 h-100" style="object-fit: cover; z-index: 1;">
                    <div class="position-absolute top-0 start-0 w-100 h-100 px-5" style="z-index: 2;">
                        <div class="row w-100 h-100">
                            <div class="col text-start">
                                <div class="ps-2 pe-3 py-5">
                                    <h1 class="fw-bold text-custom-primary"><?= $content[$slide_id]['content']['heading']; ?></h1>

                                    <p class="fs-4">
                                        <?= $content[$slide_id]['content']['text']; ?>
                                    </p>
                                </div>
                            </div>                            
                            <div class="col d-flex justify-content-center align-items-center">
                                <img src="<?= (isset($content[$slide_id]['content']['image']) && !empty($content[$slide_id]['content']['image'])) ? CONFIG['siteURL'] . '/' . CONFIG['paths']['uploads'] . '/' . $content[$slide_id]['content']['image'] : CONFIG['siteURL'] . '/' . CONFIG['paths']['images'] . '/' . 'dummy-avatar.jpg'; ?>" class="rounded-circle mb-4" width="350">                                
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <?php } ?>               

            <?php if ($content[$slide_id]['type'] == 'placeholder' && $content[$slide_id]['module'] == 'statement') { ?>            
            <div class="content">
                <div class="ratio ratio-16x9 position-relative">
                    <img src="<?= (!empty($content[$slide_id]['link'])) ? CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/' . $content[$slide_id]['link'] : CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/default.png'; ?>" class="position-absolute w-100 h-100" style="object-fit: cover; z-index: 1;">
                    <div class="text-over-content position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center px-5" style="z-index: 2; color: #fff; text-align: center;">
                        <h1 class="display-3 fw-bold">
                            <?= $content[$slide_id]['content']['heading']; ?>
                        </h1>
                    </div>
                </div>                
            </div>
            <?php } ?>  
            
            <?php if ($content[$slide_id]['type'] == 'placeholder' && $content[$slide_id]['module'] == 'text_block') { ?>            
            <div class="content">
                <div class="ratio ratio-16x9 position-relative">
                    <img src="<?= (!empty($content[$slide_id]['link'])) ? CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/' . $content[$slide_id]['link'] : CONFIG['siteURL'] . '/courses/' . $course['hash'] . '/default.png'; ?>" class="position-absolute w-100 h-100" style="object-fit: cover; z-index: 1;">
                    <div class="text-over-content position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center px-5" style="z-index: 2; color: #fff; text-align: center;">
                        <?php if (!empty($content[$slide_id]['content']['heading'])) { ?>
                        <h1 class="display-3 fw-bold">
                            <?= $content[$slide_id]['content']['heading']; ?>
                        </h1>
                        <?php } ?>
                        <?php if (!empty($content[$slide_id]['content']['text'])) { ?>
                        <p class="fs-3">
                            <?= $content[$slide_id]['content']['text']; ?>
                        </p>
                        <?php } ?>                        
                    </div>
                </div>                
            </div>
            <?php } ?>             
            <!-- Next Button -->
            <?php if ($_GET['slide'] < $last_slide) { ?>
            <a class="btn btn-light" href="<?= CONFIG['siteURL'] . '/course?id=' . $course['id'] . '&presentation=true&slide=' . ($current_slide+1); ?>" role="button"><i class="ti ti-chevron-right display-1"></i></a>
            <?php } else { ?>
            <a class="btn btn-light disabled" href="<?= CONFIG['siteURL'] . '/course?id=' . $course['id'] . '&presentation=true&slide=' . $current_slide; ?>" role="button" aria-disabled="true"><i class="ti ti-chevron-right display-1"></i></a>
            <?php } ?>               
        </div> 

        <?php } else { ?>

        <div id="loader">
            <div class="spinner"></div>
        </div>            
        <div class="container-fluid d-flex align-items-center vh-100">
            <div id="content" class="w-100" style="display: none;">

                <h1 class="display-5 text-center text-white"><?= $course['title']; ?></h1>

                <div class="mx-auto rounded shadow text-center bg-white p-4" style="min-width: 360px; max-width: 500px;">
                    <h2 class="mb-4">Vem utbildar du med?</h2>

                    <form action="<?= CONFIG['siteURL']; ?>/course?id=<?= $course['id']; ?>" method="POST">
                        <?php set_csrf(); ?>
                        <input name="course_id" type="hidden" value="<?= $course['id']; ?>">
                        <select name="co_educator" class="form-select form-select-lg mb-4 pt-3" required>
                            <option selected>&mdash; Välj utbildare &mdash;</option>
                            <option value="0">Ingen - utbildar själv</option>
                            <?php foreach ($users as $user) { ?>
                            <option value="<?= $user['id']; ?>"><?= $user['first_name']; ?> <?= $user['last_name']; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" name="submit_co_educator" class="btn btn-lg btn-custom-secondary w-100 lh-sm">Fortsätt <i class="ti ti-arrow-narrow-right"></i></button>
                    </form>                    
                </div>
            </div>
        </div>

        <?php } ?>        

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="<?= CONFIG['siteURL'] . CONFIG['paths']['js']; ?>/scripts.js"></script>
    </body>
</html>