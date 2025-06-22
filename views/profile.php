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
$page_title = 'Profil';

// Check if basic form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_basic']) && is_csrf_valid()) {

    $update_basic = update_profile('basic', $_POST);

    if ($update_basic) {

        // Display an status message
        $status = "<div class=\"alert alert-success rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Uppdateringen lyckades!</h5>";
        $status .= "</div>";

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-danger rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen misslyckades!</h5>";
        $status .= "</div>";

    }

}

// Check if password form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_password']) && is_csrf_valid()) {

    $update_password = update_profile('password', $_POST);

    if ($update_password) {

        // Display an status message
        $status = "<div class=\"alert alert-success rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Uppdateringen lyckades!</h5>";
        $status .= "</div>";

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-danger rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen misslyckades!</h5>";
        $status .= "</div>";

    }

}

// Check if image form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_image']) && is_csrf_valid()) {

    $update_image = update_profile('image', $_POST);

    if ($update_image) {

        // Display an status message
        $status = "<div class=\"alert alert-success rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Uppdateringen lyckades!</h5>";
        $status .= "</div>";

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-danger rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen misslyckades!</h5>";
        $status .= "</div>";

    }

}

// Check if remove image form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_remove_image']) && is_csrf_valid()) {

    $update_remove_image = update_profile('remove_image', $_POST);

    if ($update_remove_image) {

        // Display an status message
        $status = "<div class=\"alert alert-success rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Uppdateringen lyckades!</h5>";
        $status .= "</div>";

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-danger rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen misslyckades!</h5>";
        $status .= "</div>";

    }

}

// Check if about form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_about']) && is_csrf_valid()) {

    $update_about = update_profile('about', $_POST);

    if ($update_about) {

        // Display an status message
        $status = "<div class=\"alert alert-success rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Uppdateringen lyckades!</h5>";
        $status .= "</div>";

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-danger rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen misslyckades!</h5>";
        $status .= "</div>";

    }

}

// Check if statements form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_statements']) && is_csrf_valid()) {

    $update_statements = update_profile('statements', $_POST);

    if ($update_statements) {

        // Display an status message
        $status = "<div class=\"alert alert-success rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-up\"></i> Uppdateringen lyckades!</h5>";
        $status .= "</div>";

    } else {

        // Display an status message
        $status = "<div class=\"alert alert-danger rounded-0 pb-2 text-center\" role=\"alert\">";
        $status .= "<h5 class=\"alert-heading mb-0\"><i class=\"ti ti-thumb-down\"></i> Uppdateringen misslyckades!</h5>";
        $status .= "</div>";

    }

}

$user_details = get_user_details($_SESSION['id']);

include 'templates/header.php';
?>
        <header class="profile py-5 mb-5 d-flex align-items-center">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <img src="<?= (isset($user_details['image']) && !empty($user_details['image'])) ? 'uploads/' . $user_details['image'] : 'img/dummy-avatar.jpg'; ?>" class="rounded-circle mb-4" width="150">
                    <h1 class="fw-bolder"><?= $user_details['first_name'] . " " . $user_details['last_name']; ?></h1>                    
                </div>
            </div>
        </header>    

        <main class="container mb-5">
            <?php if (isset($status) && !empty($status)) { echo $status; } ?>
            <div class="row">
                <div class="col">
                    <div class="bg-light border rounded-3 p-4 mb-5">
                        <form method="POST" action="<?= CONFIG['siteURL']; ?>/profile">
                            <?php set_csrf(); ?>
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id']; ?>">

                            <h4>Grundläggande</h4>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="first_name" class="form-label">Förnamn <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="text" name="first_name" minlength="2" maxlength="255" class="form-control" id="first_name" placeholder="Förnamn" value="<?= (isset($user_details['first_name']) && !empty($user_details['first_name'])) ? $user_details['first_name'] : ''; ?>" required>
                                </div>
                                <div class="col">
                                    <label for="last_name" class="form-label">Efternamn <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="text" name="last_name" minlength="2" maxlength="255" class="form-control" id="last_name" placeholder="Efternamn" value="<?= (isset($user_details['last_name']) && !empty($user_details['last_name'])) ? $user_details['last_name'] : ''; ?>" required>
                                </div>
                            </div>   
                            
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="birthday_year" class="form-label">År <i class="ti ti-asterisk small text-danger"></i></label>
                                    <select name="birthday_year" class="form-select" id="birthday_year" required>
                                        <option value="">Födelseår</option>
                                        <option value="2025"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2025') ? ' selected' : ''; ?>>2025</option>                                        
                                        <option value="2024"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2024') ? ' selected' : ''; ?>>2024</option>
                                        <option value="2023"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2023') ? ' selected' : ''; ?>>2023</option>
                                        <option value="2022"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2022') ? ' selected' : ''; ?>>2022</option>
                                        <option value="2021"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2021') ? ' selected' : ''; ?>>2021</option>
                                        <option value="2020"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2020') ? ' selected' : ''; ?>>2020</option>
                                        <option value="2019"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2019') ? ' selected' : ''; ?>>2019</option>
                                        <option value="2018"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2018') ? ' selected' : ''; ?>>2018</option>
                                        <option value="2017"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2017') ? ' selected' : ''; ?>>2017</option>
                                        <option value="2016"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2016') ? ' selected' : ''; ?>>2016</option>
                                        <option value="2015"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2015') ? ' selected' : ''; ?>>2015</option>
                                        <option value="2014"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2014') ? ' selected' : ''; ?>>2014</option>
                                        <option value="2013"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2013') ? ' selected' : ''; ?>>2013</option>
                                        <option value="2012"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2012') ? ' selected' : ''; ?>>2012</option>
                                        <option value="2011"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2011') ? ' selected' : ''; ?>>2011</option>
                                        <option value="2010"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2010') ? ' selected' : ''; ?>>2010</option>
                                        <option value="2009"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2009') ? ' selected' : ''; ?>>2009</option>
                                        <option value="2008"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2008') ? ' selected' : ''; ?>>2008</option>
                                        <option value="2007"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2007') ? ' selected' : ''; ?>>2007</option>
                                        <option value="2006"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2006') ? ' selected' : ''; ?>>2006</option>
                                        <option value="2005"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2005') ? ' selected' : ''; ?>>2005</option>
                                        <option value="2004"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2004') ? ' selected' : ''; ?>>2004</option>
                                        <option value="2003"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2003') ? ' selected' : ''; ?>>2003</option>
                                        <option value="2002"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2002') ? ' selected' : ''; ?>>2002</option>
                                        <option value="2001"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2001') ? ' selected' : ''; ?>>2001</option>
                                        <option value="2000"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '2000') ? ' selected' : ''; ?>>2000</option>
                                        <option value="1999"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1999') ? ' selected' : ''; ?>>1999</option>
                                        <option value="1998"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1998') ? ' selected' : ''; ?>>1998</option>
                                        <option value="1997"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1997') ? ' selected' : ''; ?>>1997</option>
                                        <option value="1996"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1996') ? ' selected' : ''; ?>>1996</option>
                                        <option value="1995"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1995') ? ' selected' : ''; ?>>1995</option>
                                        <option value="1994"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1994') ? ' selected' : ''; ?>>1994</option>
                                        <option value="1993"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1993') ? ' selected' : ''; ?>>1993</option>
                                        <option value="1992"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1992') ? ' selected' : ''; ?>>1992</option>
                                        <option value="1991"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1991') ? ' selected' : ''; ?>>1991</option>
                                        <option value="1990"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1990') ? ' selected' : ''; ?>>1990</option>
                                        <option value="1989"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1989') ? ' selected' : ''; ?>>1989</option>
                                        <option value="1988"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1988') ? ' selected' : ''; ?>>1988</option>
                                        <option value="1987"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1987') ? ' selected' : ''; ?>>1987</option>
                                        <option value="1986"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1986') ? ' selected' : ''; ?>>1986</option>
                                        <option value="1985"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1985') ? ' selected' : ''; ?>>1985</option>
                                        <option value="1984"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1984') ? ' selected' : ''; ?>>1984</option>
                                        <option value="1983"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1983') ? ' selected' : ''; ?>>1983</option>
                                        <option value="1982"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1982') ? ' selected' : ''; ?>>1982</option>
                                        <option value="1981"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1981') ? ' selected' : ''; ?>>1981</option>
                                        <option value="1980"<?= (isset($user_details['birthday_year']) && !empty($user_details['birthday_year']) && $user_details['birthday_year'] == '1980') ? ' selected' : ''; ?>>1980</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="birthday_month" class="form-label">Månad <i class="ti ti-asterisk small text-danger"></i></label>
                                    <select name="birthday_month" class="form-select" id="birthday_month" required>
                                        <option value="">Födelsemånad</option>
                                        <option value="1"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '1') ? ' selected' : ''; ?>>Januari</option>
                                        <option value="2"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '2') ? ' selected' : ''; ?>>Februari</option>
                                        <option value="3"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '3') ? ' selected' : ''; ?>>Mars</option>
                                        <option value="4"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '4') ? ' selected' : ''; ?>>April</option>
                                        <option value="5"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '5') ? ' selected' : ''; ?>>Maj</option>
                                        <option value="6"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '6') ? ' selected' : ''; ?>>Juni</option>
                                        <option value="7"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '7') ? ' selected' : ''; ?>>Juli</option>
                                        <option value="8"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '8') ? ' selected' : ''; ?>>Augusti</option>
                                        <option value="9"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '9') ? ' selected' : ''; ?>>September</option>
                                        <option value="10"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '10') ? ' selected' : ''; ?>>Oktober</option>
                                        <option value="11"<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '11') ? ' selected' : ''; ?>>November</option>
                                        <option value="12<?= (isset($user_details['birthday_month']) && !empty($user_details['birthday_month']) && $user_details['birthday_month'] == '12') ? ' selected' : ''; ?>">December</option>
                                    </select>   
                                </div>
                                <div class="col">
                                    <label for="birthday_day" class="form-label">Dag <i class="ti ti-asterisk small text-danger"></i></label>
                                    <select name="birthday_day" class="form-select" id="birthday_day" required>
                                        <option value="">Födelsedag</option>
                                        <option value="1"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '1') ? ' selected' : ''; ?>>1</option>
                                        <option value="2"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '2') ? ' selected' : ''; ?>>2</option>
                                        <option value="3"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '3') ? ' selected' : ''; ?>>3</option>
                                        <option value="4"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '4') ? ' selected' : ''; ?>>4</option>
                                        <option value="5"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '5') ? ' selected' : ''; ?>>5</option>
                                        <option value="6"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '6') ? ' selected' : ''; ?>>6</option>
                                        <option value="7"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '7') ? ' selected' : ''; ?>>7</option>
                                        <option value="8"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '8') ? ' selected' : ''; ?>>8</option>
                                        <option value="9"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '9') ? ' selected' : ''; ?>>9</option>
                                        <option value="10"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '10') ? ' selected' : ''; ?>>10</option>
                                        <option value="11"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '11') ? ' selected' : ''; ?>>11</option>
                                        <option value="12"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '12') ? ' selected' : ''; ?>>12</option>
                                        <option value="13"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '13') ? ' selected' : ''; ?>>13</option>
                                        <option value="14"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '14') ? ' selected' : ''; ?>>14</option>
                                        <option value="15"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '15') ? ' selected' : ''; ?>>15</option>
                                        <option value="16"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '16') ? ' selected' : ''; ?>>16</option>
                                        <option value="17"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '17') ? ' selected' : ''; ?>>17</option>
                                        <option value="18"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '18') ? ' selected' : ''; ?>>18</option>
                                        <option value="19"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '19') ? ' selected' : ''; ?>>19</option>
                                        <option value="20"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '20') ? ' selected' : ''; ?>>20</option>
                                        <option value="21"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '21') ? ' selected' : ''; ?>>21</option>
                                        <option value="22"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '22') ? ' selected' : ''; ?>>22</option>
                                        <option value="23"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '23') ? ' selected' : ''; ?>>23</option>
                                        <option value="24"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '24') ? ' selected' : ''; ?>>24</option>
                                        <option value="25"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '25') ? ' selected' : ''; ?>>25</option>
                                        <option value="26"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '26') ? ' selected' : ''; ?>>26</option>
                                        <option value="27"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '27') ? ' selected' : ''; ?>>27</option>
                                        <option value="28"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '28') ? ' selected' : ''; ?>>28</option>
                                        <option value="29"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '29') ? ' selected' : ''; ?>>29</option>
                                        <option value="30"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '30') ? ' selected' : ''; ?>>30</option>
                                        <option value="31"<?= (isset($user_details['birthday_day']) && !empty($user_details['birthday_day']) && $user_details['birthday_day'] == '31') ? ' selected' : ''; ?>>31</option>
                                    </select>   
                                </div>                            
                            </div>                           

                            <div class="mb-3">
                                <label for="gender" class="form-label">Kön <i class="ti ti-asterisk small text-danger"></i></label>
                                <select name="gender" class="form-select" id="gender" required>
                                    <option value="">Ange kön</option>
                                    <option value="female"<?= (isset($user_details['gender']) && !empty($user_details['gender']) && $user_details['gender'] == 'female') ? ' selected' : ''; ?>>Kvinna</option>
                                    <option value="male"<?= (isset($user_details['gender']) && !empty($user_details['gender']) && $user_details['gender'] == 'male') ? ' selected' : ''; ?>>Man</option>
                                    <option value="other"><?= (isset($user_details['gender']) && !empty($user_details['gender']) && $user_details['gender'] == 'other') ? ' selected' : ''; ?>Annat</option>
                                    <option value="unknown"<?= (isset($user_details['gender']) && !empty($user_details['gender']) && $user_details['gender'] == 'unknown') ? ' selected' : ''; ?>>Vill ej ange</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Telefon/mobilnummer <i class="ti ti-asterisk small text-danger"></i></label>
                                <input type="tel" name="phone_number" minlength="7" maxlength="20" class="form-control" id="phone_number" placeholder="Telefon/mobilnummer" value="<?= (isset($user_details['phone_number']) && !empty($user_details['phone_number'])) ? $user_details['phone_number'] : ''; ?>" required>
                            </div>    

                            <div class="mb-3">
                                <label for="email" class="form-label">E-postadress <i class="ti ti-asterisk small text-danger"></i></label>
                                <input type="email" name="email" minlength="3" maxlength="255" class="form-control" id="email" placeholder="E-postadress" value="<?= (isset($user_details['email']) && !empty($user_details['email'])) ? $user_details['email'] : ''; ?>" required>
                            </div>  
                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_basic" class="btn btn-custom-secondary w-100 align-middle"><i class="ti ti-device-floppy"></i> Spara</button>
                            </div>
                        </form>                           
                    </div>

                    <div class="bg-light border rounded-3 p-4 mb-5">
                        <form method="POST" action="<?= CONFIG['siteURL']; ?>/profile">
                            <?php set_csrf(); ?>
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id']; ?>">  

                            <h4>Säkerhet</h4>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nytt lösenord <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Nytt lösenord">
                                </div>
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Nytt lösenord igen <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Nytt lösenord igen">
                                </div>
                                <div class="mb-3">
                                    <label for="password_current" class="form-label">Nuvarande lösenord <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="password" name="password_current" class="form-control" id="password_current" placeholder="Nuvarande lösenord">
                                </div>
                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_password" class="btn btn-custom-secondary w-100 align-middle"><i class="ti ti-replace"></i> Ändra</button>
                            </div>
                        </form>                              
                    </div>
                </div>
                <div class="col">
                    <div class="bg-light border rounded-3 p-4 mb-5">
                        <form method="POST" action="<?= CONFIG['siteURL']; ?>/profile" enctype="multipart/form-data">
                            <?php set_csrf(); ?>
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id']; ?>">  

                            <h4>Bild <i class="ti ti-asterisk text-danger fs-6"></i></h4>
                            <div class="mb-3">
                                <input class="form-control" type="file" name="image" id="image" aria-describedby="imageHelp" required>                        
                                <div id="imageHelp" class="form-text ms-2 mt-2">Ersätter <u>alltid</u> befintlig bild.</div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_image" class="btn btn-custom-secondary w-100"><i class="ti ti-upload"></i> Ladda upp</button>
                            </div>                        
                        </form>
                        <?php if (!empty($user_details['image'])) { ?>
                        <form method="POST" action="<?= CONFIG['siteURL']; ?>/profile" enctype="multipart/form-data">
                            <?php set_csrf(); ?>
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id']; ?>">
                            <input type="hidden" name="image" value="<?= $user_details['image']; ?>">  
                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_remove_image" class="btn btn-danger w-100"><i class="ti ti-trash-x"></i> Ta bort bild</button>
                            </div>                        
                        </form>                        
                        <?php } ?>
                    </div>

                    <div class="bg-light border rounded-3 p-4 mb-5">
                        <form method="POST" action="<?= CONFIG['siteURL']; ?>/profile">
                            <?php set_csrf(); ?>
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id']; ?>">  

                            <h4>Berätta om dig <i class="ti ti-asterisk text-danger fs-6"></i></h4>
                            <div class="mb-3">
                                <textarea name="about" class="form-control" id="about" rows="5" placeholder="Vem är du? Berätta om dig själv." required><?= (isset($user_details['about']) && !empty($user_details['about'])) ? $user_details['about'] : ''; ?></textarea>
                            </div>                        
                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_about" class="btn btn-custom-secondary w-100 align-middle"><i class="ti ti-device-floppy"></i> Spara</button>
                            </div>                        
                        </form>
                    </div>
                    
                    <div class="bg-light border rounded-3 p-4 mb-5">
                        <form method="POST" action="<?= CONFIG['siteURL']; ?>/profile">
                            <?php set_csrf(); ?>
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id']; ?>"> 

                            <h4>Sant eller falskt</h4>

                            <div class="row mb-3">
                                <div class="col-8">
                                    <label for="Statement1" class="form-label">Påstående 1 <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="text" name="statement_1" class="form-control" id="Statement1" placeholder="Påstående 1" value="<?= (isset($user_details['statement_1']) && !empty($user_details['statement_1'])) ? $user_details['statement_1'] : ''; ?>">
                                </div>
                                <div class="col">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="form-check form-check-inline pt-2">
                                        <input class="form-check-input" type="radio" name="statement_1_answer" id="StatementAnswer11" value="t"<?= (isset($user_details['statement_1_answer']) && !empty($user_details['statement_1_answer']) && $user_details['statement_1_answer'] == 't') ? ' checked' : ''; ?>>
                                        <label class="form-check-label" for="StatementAnswer11">Sant</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="statement_1_answer" id="StatementAnswer12" value="f"<?= (isset($user_details['statement_1_answer']) && !empty($user_details['statement_1_answer']) && $user_details['statement_1_answer'] == 'f') ? ' checked' : ''; ?>>
                                        <label class="form-check-label" for="StatementAnswer12">Falskt</label>
                                    </div>
                                </div>
                            </div>   

                            <div class="row mb-3">
                                <div class="col-8">
                                    <label for="Statement2" class="form-label">Påstående 2 <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="text" name="statement_2" class="form-control" id="Statement2" placeholder="Påstående 2" value="<?= (isset($user_details['statement_2']) && !empty($user_details['statement_2'])) ? $user_details['statement_2'] : ''; ?>">
                                </div>
                                <div class="col">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="form-check form-check-inline pt-2">
                                        <input class="form-check-input" type="radio" name="statement_2_answer" id="StatementAnswer21" value="t"<?= (isset($user_details['statement_2_answer']) && !empty($user_details['statement_2_answer']) && $user_details['statement_2_answer'] == 't') ? ' checked' : ''; ?>>
                                        <label class="form-check-label" for="StatementAnswer21">Sant</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="statement_2_answer" id="StatementAnswer22" value="f"<?= (isset($user_details['statement_2_answer']) && !empty($user_details['statement_2_answer']) && $user_details['statement_2_answer'] == 'f') ? ' checked' : ''; ?>>
                                        <label class="form-check-label" for="StatementAnswer22">Falskt</label>
                                    </div>
                                </div>
                            </div>      
                            
                            <div class="row mb-3">
                                <div class="col-8">
                                    <label for="Statement3" class="form-label">Påstående 3 <i class="ti ti-asterisk small text-danger"></i></label>
                                    <input type="text" name="statement_3" class="form-control" id="Statement3" placeholder="Påstående 3" value="<?= (isset($user_details['statement_3']) && !empty($user_details['statement_3'])) ? $user_details['statement_3'] : ''; ?>">
                                </div>
                                <div class="col">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="form-check form-check-inline pt-2">
                                        <input class="form-check-input" type="radio" name="statement_3_answer" id="StatementAnswer31" value="t"<?= (isset($user_details['statement_3_answer']) && !empty($user_details['statement_3_answer']) && $user_details['statement_3_answer'] == 't') ? ' checked' : ''; ?>>
                                        <label class="form-check-label" for="StatementAnswer31">Sant</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="statement_3_answer" id="StatementAnswer32" value="f"<?= (isset($user_details['statement_3_answer']) && !empty($user_details['statement_3_answer']) && $user_details['statement_3_answer'] == 'f') ? ' checked' : ''; ?>>
                                        <label class="form-check-label" for="StatementAnswer32">Falskt</label>
                                    </div>
                                </div>
                            </div>                           

                            <div class="d-grid mt-4">
                                <button type="submit" name="submit_statements" class="btn btn-custom-secondary w-100 align-middle"><i class="ti ti-device-floppy"></i> Spara</button>
                            </div>                        
                        </form>                     
                    </div>
                </div>
            </div>

        </main>
<?php include 'templates/footer.php'; ?>