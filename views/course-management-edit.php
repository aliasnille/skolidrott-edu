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

// Determine if this is create or edit mode
$is_edit_mode = isset($_id) && !empty($_id) && is_numeric($_id);
$course = null;

if ($is_edit_mode) {
    // Get course for editing (only original courses)
    $course = get_course($_id);

    // Check if course exists and is not a copy
    if (!$course || (isset($course['is_copy']) && $course['is_copy'] == 1)) {
        http_response_code(404);
        exit();
    }

    $page_title = 'Redigera kurs: ' . $course['title'];
} else {
    $page_title = 'Skapa ny kurs';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {

    if ($is_edit_mode && isset($_POST['submit_update'])) {
        // Handle file uploads
        $image_filename = null;
        $background_filename = null;

        $course = get_course($_id);
        if ($course) {
            // Handle course image upload
            if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
                $image_filename = upload_course_file($_FILES['course_image'], $course['hash']);
            }

            // Handle background image upload
            if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                $background_filename = upload_course_file($_FILES['background_image'], $course['hash']);
            }
        }

        // Update existing course info
        $update_result = update_course_info($_POST, $_id, $image_filename, $background_filename);

        // Also update slides if provided
        if (isset($_POST['slides']) && is_array($_POST['slides'])) {
            $target_course = get_course($_id);
            if ($target_course) {
                // Process slides data
                $slides = [];
                foreach ($_POST['slides'] as $index => $slide_data) {
                    if (!empty($slide_data['title'])) {
                        $slide = [
                            'id' => $index + 1,
                            'order' => $index + 1,
                            'title' => $slide_data['title'],
                            'excludable' => isset($slide_data['excludable']) ? true : false,
                            'is_active' => isset($slide_data['is_active']) ? true : false,
                            'type' => $slide_data['type'] ?? 'image',
                            'module' => $slide_data['module'] ?? '',
                            'link' => $slide_data['link'] ?? '',
                            'thumb' => $slide_data['thumb'] ?? 'default.png'
                        ];

                        // Add content for text_block module only
                        if ($slide['type'] === 'placeholder' && $slide['module'] === 'text_block') {
                            $content = [];

                            // Add heading if provided
                            if (!empty($slide_data['heading_content'])) {
                                $content['heading'] = $slide_data['heading_content'];
                            }

                            // Add text if provided
                            if (!empty($slide_data['text_content'])) {
                                $content['text'] = $slide_data['text_content'];
                            }

                            if (!empty($content)) {
                                $slide['content'] = $content;
                            }
                        }

                        // Handle file upload for image slides
                        if ($slide['type'] === 'image' && isset($_FILES['slide_files']) && isset($_FILES['slide_files']['tmp_name'][$index])) {
                            $file = [
                                'name' => $_FILES['slide_files']['name'][$index],
                                'type' => $_FILES['slide_files']['type'][$index],
                                'tmp_name' => $_FILES['slide_files']['tmp_name'][$index],
                                'error' => $_FILES['slide_files']['error'][$index],
                                'size' => $_FILES['slide_files']['size'][$index]
                            ];

                            $uploaded_filename = upload_course_file($file, $target_course['hash']);
                            if ($uploaded_filename) {
                                $slide['link'] = $uploaded_filename;
                                $slide['thumb'] = $uploaded_filename;
                            }
                        }

                        $slides[] = $slide;
                    }
                }

                // Update course content
                update_course_content($_id, $slides);
            }
        }

        if ($update_result) {
            $_SESSION['status'] = "<div class=\"alert alert-success rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Kursen uppdaterades utan problem!</h5></div>";
        } else {
            $_SESSION['status'] = "<div class=\"alert alert-danger rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen misslyckades!</h5></div>";
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;

    } elseif (!$is_edit_mode && isset($_POST['submit_create'])) {
        // Create new course first
        $course_id = create_course($_POST);

        if ($course_id) {
            // Get the created course to get its hash
            $new_course = get_course($course_id);

            if ($new_course) {
                $image_filename = null;
                $background_filename = null;

                // Handle course image upload
                if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
                    $image_filename = upload_course_file($_FILES['course_image'], $new_course['hash']);
                }

                // Handle background image upload
                if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                    $background_filename = upload_course_file($_FILES['background_image'], $new_course['hash']);
                }

                // Update course with uploaded filenames if any
                if ($image_filename || $background_filename) {
                    update_course_info($_POST, $course_id, $image_filename, $background_filename);
                }
            }
        }

        if ($course_id) {
            // If slides data is provided, also save slides
            if (isset($_POST['slides']) && is_array($_POST['slides'])) {
                $new_course = get_course($course_id);
                if ($new_course) {
                    // Process slides data (same logic as above)
                    $slides = [];
                    foreach ($_POST['slides'] as $index => $slide_data) {
                        if (!empty($slide_data['title'])) {
                            $slide = [
                                'id' => $index + 1,
                                'order' => $index + 1,
                                'title' => $slide_data['title'],
                                'excludable' => isset($slide_data['excludable']) ? true : false,
                                'is_active' => isset($slide_data['is_active']) ? true : false,
                                'type' => $slide_data['type'] ?? 'image',
                                'module' => $slide_data['module'] ?? '',
                                'link' => $slide_data['link'] ?? '',
                                'thumb' => $slide_data['thumb'] ?? 'default.png'
                            ];

                            // Add content for text_block module only
                            if ($slide['type'] === 'placeholder' && $slide['module'] === 'text_block') {
                                $content = [];

                                // Add heading if provided
                                if (!empty($slide_data['heading_content'])) {
                                    $content['heading'] = $slide_data['heading_content'];
                                }

                                // Add text if provided
                                if (!empty($slide_data['text_content'])) {
                                    $content['text'] = $slide_data['text_content'];
                                }

                                if (!empty($content)) {
                                    $slide['content'] = $content;
                                }
                            }

                            // Handle file upload for image slides
                            if ($slide['type'] === 'image' && isset($_FILES['slide_files']) && isset($_FILES['slide_files']['tmp_name'][$index])) {
                                $file = [
                                    'name' => $_FILES['slide_files']['name'][$index],
                                    'type' => $_FILES['slide_files']['type'][$index],
                                    'tmp_name' => $_FILES['slide_files']['tmp_name'][$index],
                                    'error' => $_FILES['slide_files']['error'][$index],
                                    'size' => $_FILES['slide_files']['size'][$index]
                                ];

                                $uploaded_filename = upload_course_file($file, $new_course['hash']);
                                if ($uploaded_filename) {
                                    $slide['link'] = $uploaded_filename;
                                    $slide['thumb'] = $uploaded_filename;
                                }
                            }

                            $slides[] = $slide;
                        }
                    }

                    // Update course content
                    update_course_content($course_id, $slides);
                }
            }

            $_SESSION['status'] = "<div class=\"alert alert-success rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Kursen skapades utan problem!</h5></div>";
            header('Location: ' . CONFIG['siteURL'] . '/course-management/edit/' . $course_id);
            exit;
        } else {
            $_SESSION['status'] = "<div class=\"alert alert-danger rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Skapandet av kursen misslyckades!</h5></div>";
        }
    } elseif (isset($_POST['submit_slides']) && ($is_edit_mode || isset($_POST['course_id']))) {
        // Handle slide updates
        $target_course_id = $is_edit_mode ? $_id : $_POST['course_id'];
        $target_course = get_course($target_course_id);

        if ($target_course) {
            // Process slides data
            $slides = [];
            if (isset($_POST['slides']) && is_array($_POST['slides'])) {
                foreach ($_POST['slides'] as $index => $slide_data) {
                    if (!empty($slide_data['title'])) {
                        $slide = [
                            'id' => $index + 1,
                            'order' => $index + 1,
                            'title' => $slide_data['title'],
                            'excludable' => isset($slide_data['excludable']) ? true : false,
                            'is_active' => isset($slide_data['is_active']) ? true : false,
                            'type' => $slide_data['type'] ?? 'image',
                            'module' => $slide_data['module'] ?? '',
                            'link' => $slide_data['link'] ?? '',
                            'thumb' => $slide_data['thumb'] ?? 'default.png'
                        ];

                        // Handle file upload for image slides
                        if ($slide['type'] === 'image' && isset($_FILES['slide_files']) && isset($_FILES['slide_files']['tmp_name'][$index])) {
                            $file = [
                                'name' => $_FILES['slide_files']['name'][$index],
                                'type' => $_FILES['slide_files']['type'][$index],
                                'tmp_name' => $_FILES['slide_files']['tmp_name'][$index],
                                'error' => $_FILES['slide_files']['error'][$index],
                                'size' => $_FILES['slide_files']['size'][$index]
                            ];

                            $uploaded_filename = upload_course_file($file, $target_course['hash']);
                            if ($uploaded_filename) {
                                $slide['link'] = $uploaded_filename;
                                $slide['thumb'] = $uploaded_filename;
                            }
                        }

                        $slides[] = $slide;
                    }
                }
            }

            // Update course content
            if (update_course_content($target_course_id, $slides)) {
                $_SESSION['status'] = "<div class=\"alert alert-success rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Slides uppdaterades utan problem!</h5></div>";
            } else {
                $_SESSION['status'] = "<div class=\"alert alert-danger rounded-0 pb-2 text-center mb-4\" role=\"alert\"><h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdatering av slides misslyckades!</h5></div>";
            }
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

include 'templates/header.php';
?>
        <main class="container my-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-1 mb-0"><?= $page_title; ?></h1>
                <a href="<?= CONFIG['siteURL']; ?>/course-management" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Tillbaka till kurshantering
                </a>
            </div>

            <?php if (isset($_SESSION['status']) && !empty($_SESSION['status'])) { echo $_SESSION['status']; } $_SESSION['status'] = null; ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Kombinerat formulär för både kursinfo och slides -->
                    <form method="POST" action="<?= $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="course-form">
                        <?php set_csrf(); ?>

                        <div class="bg-light border rounded-3 p-4 mb-5">
                            <h4><?= $is_edit_mode ? 'Redigera kursinformation' : 'Grundläggande kursinformation'; ?></h4>

                            <div class="mb-3">
                                <label for="title" class="form-label">Kurstitel <i class="ti ti-asterisk small text-danger"></i></label>
                                <input type="text" name="title" minlength="3" maxlength="255" class="form-control" id="title" placeholder="Ange kurstitel" value="<?= $is_edit_mode ? htmlspecialchars($course['title']) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="byline" class="form-label">Underrubrik</label>
                                <input type="text" name="byline" maxlength="255" class="form-control" id="byline" placeholder="Ange underrubrik (valfritt)" value="<?= $is_edit_mode ? htmlspecialchars($course['byline']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Beskrivning</label>
                                <textarea name="description" class="form-control" id="description" rows="3" maxlength="255" placeholder="Ange kursbeskrivning (valfritt, max 255 tecken)"><?= $is_edit_mode ? htmlspecialchars($course['description']) : ''; ?></textarea>
                                <small class="text-muted">Maximalt 255 tecken.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="course_image" class="form-label">Kursbild (thumbnail)</label>
                                        <input type="file" name="course_image" class="form-control" id="course_image" accept="image/*">
                                        <?php if ($is_edit_mode && !empty($course['image']) && $course['image'] !== 'thumb.png') { ?>
                                        <small class="text-muted">Nuvarande: <?= htmlspecialchars($course['image']); ?></small>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="background_image" class="form-label">Bakgrundsbild</label>
                                        <input type="file" name="background_image" class="form-control" id="background_image" accept="image/*">
                                        <?php if ($is_edit_mode && !empty($course['background']) && $course['background'] !== 'default.png') { ?>
                                        <small class="text-muted">Nuvarande: <?= htmlspecialchars($course['background']); ?></small>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide-redigeraren visas alltid, både för nya och befintliga kurser -->
                        <div class="bg-light border rounded-3 p-4 mb-5">
                            <h4>Kursinnehåll (Slides)</h4>

                            <div id="slides-container">
                                <?php
                                $slides = $is_edit_mode && !empty($course['content']) ? $course['content'] : [];
                                if (empty($slides)) {
                                    $slides = [['title' => '', 'type' => 'image', 'link' => '', 'module' => '', 'excludable' => true, 'is_active' => true]];
                                }

                                foreach ($slides as $index => $slide) { ?>
                                <div class="slide-item border rounded p-3 mb-3" data-index="<?= $index; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Slide <?= $index + 1; ?></h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary move-up" <?= $index === 0 ? 'disabled' : ''; ?>>↑</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary move-down" <?= $index === count($slides) - 1 ? 'disabled' : ''; ?>>↓</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-slide">×</button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Titel</label>
                                                <input type="text" name="slides[<?= $index; ?>][title]" class="form-control" value="<?= htmlspecialchars($slide['title'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Typ</label>
                                                <select name="slides[<?= $index; ?>][type]" class="form-control slide-type-select">
                                                    <option value="image" <?= ($slide['type'] ?? '') === 'image' ? 'selected' : ''; ?>>Bild</option>
                                                    <option value="video" <?= ($slide['type'] ?? '') === 'video' ? 'selected' : ''; ?>>Video</option>
                                                    <option value="placeholder" <?= ($slide['type'] ?? '') === 'placeholder' ? 'selected' : ''; ?>>Platshållare</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="slide-type-content">
                                        <!-- Image upload -->
                                        <div class="image-content" style="display: <?= ($slide['type'] ?? 'image') === 'image' ? 'block' : 'none'; ?>;">
                                            <div class="mb-3">
                                                <label class="form-label">Ladda upp bild</label>
                                                <input type="file" name="slide_files[<?= $index; ?>]" class="form-control" accept="image/*">
                                                <?php if (!empty($slide['link']) && $slide['type'] === 'image') { ?>
                                                <small class="text-muted">Nuvarande: <?= htmlspecialchars($slide['link']); ?></small>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <!-- Video URL -->
                                        <div class="video-content" style="display: <?= ($slide['type'] ?? '') === 'video' ? 'block' : 'none'; ?>;">
                                            <div class="mb-3">
                                                <label class="form-label">Video URL (YouTube embed)</label>
                                                <input type="url" name="slides[<?= $index; ?>][link]" class="form-control" value="<?= htmlspecialchars($slide['link'] ?? ''); ?>" placeholder="https://www.youtube.com/embed/...">
                                            </div>
                                        </div>

                                        <!-- Placeholder module -->
                                        <div class="placeholder-content" style="display: <?= ($slide['type'] ?? '') === 'placeholder' ? 'block' : 'none'; ?>;">
                                            <div class="mb-3">
                                                <label class="form-label">Modul</label>
                                                <select name="slides[<?= $index; ?>][module]" class="form-control slide-module-select">
                                                    <option value="">Välj modul</option>
                                                    <option value="educator" <?= ($slide['module'] ?? '') === 'educator' ? 'selected' : ''; ?>>Utbildare</option>
                                                    <option value="co_educator" <?= ($slide['module'] ?? '') === 'co_educator' ? 'selected' : ''; ?>>Medutbildare</option>
                                                    <option value="statement" <?= ($slide['module'] ?? '') === 'statement' ? 'selected' : ''; ?>>Påstående</option>
                                                    <option value="text_block" <?= ($slide['module'] ?? '') === 'text_block' ? 'selected' : ''; ?>>Textblock</option>
                                                </select>
                                            </div>

                                            <!-- Content for text_block module -->
                                            <div class="text-block-content" style="display: <?= ($slide['module'] ?? '') === 'text_block' ? 'block' : 'none'; ?>;">
                                                <div class="mb-3">
                                                    <label class="form-label">Rubrik (H1)</label>
                                                    <input type="text" name="slides[<?= $index; ?>][heading_content]" class="form-control" placeholder="Ange rubrik som ska visas" value="<?= isset($slide['content']['heading']) ? htmlspecialchars($slide['content']['heading']) : ''; ?>">
                                                    <small class="text-muted">Detta blir den stora rubriken som visas på sliden.</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Textinnehåll</label>
                                                    <textarea name="slides[<?= $index; ?>][text_content]" class="form-control" rows="3" placeholder="Ange textinnehåll som ska visas under rubriken"><?= isset($slide['content']['text']) ? htmlspecialchars($slide['content']['text']) : ''; ?></textarea>
                                                    <small class="text-muted">Detta visas under rubriken som brödtext.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" name="slides[<?= $index; ?>][excludable]" class="form-check-input" <?= ($slide['excludable'] ?? true) ? 'checked' : ''; ?>>
                                                <label class="form-check-label">Kan exkluderas</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" name="slides[<?= $index; ?>][is_active]" class="form-check-input" <?= ($slide['is_active'] ?? true) ? 'checked' : ''; ?>>
                                                <label class="form-check-label">Aktiv</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-outline-primary" id="add-slide">
                                    <i class="ti ti-plus"></i> Lägg till slide
                                </button>
                            </div>
                        </div>

                        <!-- Spara-knapp för hela formuläret -->
                        <div class="d-grid">
                            <?php if ($is_edit_mode) { ?>
                            <button type="submit" name="submit_update" class="btn btn-custom-secondary">
                                <i class="ti ti-device-floppy"></i> Uppdatera kurs och slides
                            </button>
                            <?php } else { ?>
                            <button type="submit" name="submit_create" class="btn btn-custom-primary">
                                <i class="ti ti-plus"></i> Skapa kurs med slides
                            </button>
                            <?php } ?>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="bg-light border rounded-3 p-4 mb-5">
                        <h5>Kursinformation</h5>
                        
                        <?php if ($is_edit_mode) { ?>
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8"><?= $course['id']; ?></dd>
                            
                            <dt class="col-sm-4">Hash:</dt>
                            <dd class="col-sm-8"><code><?= $course['hash']; ?></code></dd>
                            
                            <dt class="col-sm-4">Skapad:</dt>
                            <dd class="col-sm-8"><?= date('Y-m-d H:i', strtotime($course['created_at'])); ?></dd>
                            
                            <dt class="col-sm-4">Slides:</dt>
                            <dd class="col-sm-8"><?= count($course['content']); ?> st</dd>
                        </dl>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <a href="<?= CONFIG['siteURL']; ?>/course?id=<?= $course['id']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-eye"></i> Förhandsgranska
                            </a>
                        </div>
                        <?php } else { ?>
                        <p class="text-muted">Kursinformation kommer att visas här efter att kursen har skapats.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let slideIndex = <?= count($slides ?? []) - 1; ?>;

    // Add new slide
    document.getElementById('add-slide').addEventListener('click', function() {
        slideIndex++;
        const container = document.getElementById('slides-container');
        const newSlide = createSlideHTML(slideIndex);
        container.insertAdjacentHTML('beforeend', newSlide);
        updateSlideNumbers();
    });

    // Remove slide
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-slide')) {
            if (document.querySelectorAll('.slide-item').length > 1) {
                e.target.closest('.slide-item').remove();
                updateSlideNumbers();
            } else {
                alert('Du måste ha minst en slide.');
            }
        }
    });

    // Move slides up/down
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('move-up')) {
            const slide = e.target.closest('.slide-item');
            const prev = slide.previousElementSibling;
            if (prev) {
                slide.parentNode.insertBefore(slide, prev);
                updateSlideNumbers();
            }
        }

        if (e.target.classList.contains('move-down')) {
            const slide = e.target.closest('.slide-item');
            const next = slide.nextElementSibling;
            if (next) {
                slide.parentNode.insertBefore(next, slide);
                updateSlideNumbers();
            }
        }
    });

    // Handle slide type changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('slide-type-select')) {
            const slideItem = e.target.closest('.slide-item');
            const type = e.target.value;

            // Hide all content types
            slideItem.querySelectorAll('.image-content, .video-content, .placeholder-content').forEach(el => {
                el.style.display = 'none';
            });

            // Show selected content type
            const targetContent = slideItem.querySelector('.' + type + '-content');
            if (targetContent) {
                targetContent.style.display = 'block';
            }
        }

        // Handle module changes for placeholder content
        if (e.target.classList.contains('slide-module-select')) {
            const slideItem = e.target.closest('.slide-item');
            const module = e.target.value;

            // Hide all module-specific content
            slideItem.querySelectorAll('.text-block-content').forEach(el => {
                el.style.display = 'none';
            });

            // Show text content for text_block module
            if (module === 'text_block') {
                const textBlockContent = slideItem.querySelector('.text-block-content');
                if (textBlockContent) {
                    textBlockContent.style.display = 'block';
                }
            }
        }
    });

    function createSlideHTML(index) {
        return `
            <div class="slide-item border rounded p-3 mb-3" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Slide ${index + 1}</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary move-up">↑</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary move-down">↓</button>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-slide">×</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Titel</label>
                            <input type="text" name="slides[${index}][title]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Typ</label>
                            <select name="slides[${index}][type]" class="form-control slide-type-select">
                                <option value="image" selected>Bild</option>
                                <option value="video">Video</option>
                                <option value="placeholder">Platshållare</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="slide-type-content">
                    <div class="image-content">
                        <div class="mb-3">
                            <label class="form-label">Ladda upp bild</label>
                            <input type="file" name="slide_files[${index}]" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="video-content" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Video URL (YouTube embed)</label>
                            <input type="url" name="slides[${index}][link]" class="form-control" placeholder="https://www.youtube.com/embed/...">
                        </div>
                    </div>

                    <div class="placeholder-content" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Modul</label>
                            <select name="slides[${index}][module]" class="form-control slide-module-select">
                                <option value="">Välj modul</option>
                                <option value="educator">Utbildare</option>
                                <option value="co_educator">Medutbildare</option>
                                <option value="statement">Påstående</option>
                                <option value="text_block">Textblock</option>
                            </select>
                        </div>

                        <div class="text-block-content" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Rubrik (H1)</label>
                                <input type="text" name="slides[${index}][heading_content]" class="form-control" placeholder="Ange rubrik som ska visas">
                                <small class="text-muted">Detta blir den stora rubriken som visas på sliden.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Textinnehåll</label>
                                <textarea name="slides[${index}][text_content]" class="form-control" rows="3" placeholder="Ange textinnehåll som ska visas under rubriken"></textarea>
                                <small class="text-muted">Detta visas under rubriken som brödtext.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="slides[${index}][excludable]" class="form-check-input" checked>
                            <label class="form-check-label">Kan exkluderas</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="slides[${index}][is_active]" class="form-check-input" checked>
                            <label class="form-check-label">Aktiv</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function updateSlideNumbers() {
        document.querySelectorAll('.slide-item').forEach((slide, index) => {
            slide.setAttribute('data-index', index);
            slide.querySelector('h6').textContent = `Slide ${index + 1}`;

            // Update form field names
            slide.querySelectorAll('input, select').forEach(field => {
                const name = field.getAttribute('name');
                if (name && name.includes('[')) {
                    const newName = name.replace(/\[\d+\]/, `[${index}]`);
                    field.setAttribute('name', newName);
                }
            });

            // Update move buttons
            const moveUp = slide.querySelector('.move-up');
            const moveDown = slide.querySelector('.move-down');
            const totalSlides = document.querySelectorAll('.slide-item').length;

            moveUp.disabled = index === 0;
            moveDown.disabled = index === totalSlides - 1;
        });
    }

    // Initial setup
    updateSlideNumbers();
});
</script>

<?php include 'templates/footer.php'; ?>
