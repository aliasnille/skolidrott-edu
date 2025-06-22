<?php
// Required files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './lib/PHPMailer/src/Exception.php';
require './lib/PHPMailer/src/PHPMailer.php';
require './lib/PHPMailer/src/SMTP.php';

// Die & dump
function dd($value) {

    echo "<pre>";
    print_r($value);
    echo "</pre>";

    die();

}

// Get user IP Address
function get_ip_address() {

    switch(true) {

        case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
        case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
        case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
        default : return $_SERVER['REMOTE_ADDR'];

    }

}

// Generate random and unique string (activation code)
function generate_random_string($length = 40) {
    $str = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);

    return sha1($str);
}

// Send email
function send_mail($type, $data) {

    if ($type == 'invite') {

        $mail = new PHPMailer(true);
        $mail->CharSet      = 'UTF-8';
        $mail->Encoding     = 'base64';
    
        $mail->isSMTP();
        $mail->Host         = 'smtp.resend.com';
        $mail->SMTPAuth     = true;
        $mail->Username     = 'resend';
        $mail->Password     = 're_6fkFvhx7_CfcLLMPFv5TfgCMsJtK6xrTQ';    
        $mail->Port         = 587;        
    
        $mail->setFrom('noreply@skolidrottskane.se', 'Skolidrottsförbundet i Skåne');
        $mail->AddReplyTo('kennet.nilsson@skolidrott.se', 'Kennet Nilsson');
        //$mail->AddReplyTo('jakob.ericson@skolidrott.se', 'Jakob Ericson');
        $mail->addAddress($data['email']);
    
        $mail->isHTML(false);
        $mail->Subject  = 'Inbjudan till utbildningsplattform';
        $message        = "Inbjudan till utbildningsplattform\r\n\r\n";
        $message       .= "Du har blivit inbjuden av oss på Skolidrottsförbundet i Skåne att registrera dig på vår utbildningsplattform.\r\n\r\n";
        $message       .= "Klicka på länken nedan för att registrera dig.\r\n\r\n";
        $message       .= CONFIG['siteURL'] . "/register?code=" . $data['code'] . "\r\n\r\n";
        $message       .= "OBS! Inbjudan är personlig och går ENDAST att använda en gång.\r\n\r\n";
        $message       .= "Har du några frågor, kontakta Jakob Ericson, jakob.ericson@skolidrott.se.\r\n\r\n";
        $message       .= "Hälsningar,\r\n";
        $message       .= "Skolidrottsförbundet i Skåne\r\n\r\n\r\n\r\n";
    
        $mail->Body = $message;
    
        if ($mail->send()) {

            return true;

        } else {

            return false;

        }        

    } else if ($type == 'reset_password') {        

        $mail = new PHPMailer(true);
        $mail->CharSet      = 'UTF-8';
        $mail->Encoding     = 'base64';
    
        $mail->isSMTP();
        $mail->Host         = 'smtp.resend.com';
        $mail->SMTPAuth     = true;
        $mail->Username     = 'resend';
        $mail->Password     = 're_6fkFvhx7_CfcLLMPFv5TfgCMsJtK6xrTQ';    
        $mail->Port         = 587;        
    
        $mail->setFrom('noreply@skolidrottskane.se', 'Skolidrottsförbundet i Skåne');
        $mail->AddReplyTo('kennet.nilsson@skolidrott.se', 'Kennet Nilsson');
        //$mail->AddReplyTo('jakob.ericson@skolidrott.se', 'Jakob Ericson');
        $mail->addAddress($data['email']);
    
        $mail->isHTML(false);
        $mail->Subject  = 'Återställning av lösenord';
        $message        = "Återställning av lösenord\r\n\r\n";
        $message       .= "Du har påbörjat återställning av ditt lösenord till utbildningsplattformen hos Skolidrottsförbundet i Skåne.\r\n\r\n";
        $message       .= "För att återställa och skapa ett nytt lösenord, klicka på länken nedan.\r\n\r\n";
        $message       .= CONFIG['siteURL'] . "/reset-password?token=" . $data['token'] . "&checksum=" . md5($data['email']) . "\r\n\r\n";
        $message       .= "OBS! Återställningslänken är endast giltlig i 30 minuter.\r\n\r\n";
        //$message       .= "Har du några frågor, kontakta Jakob Ericson, jakob.ericson@skolidrott.se.\r\n\r\n";
        $message       .= "Hälsningar,\r\n";
        $message       .= "Skolidrottsförbundet i Skåne\r\n\r\n\r\n\r\n";
    
        $mail->Body = $message;
    
        if ($mail->send()) {

            return true;

        } else {

            return false;

        }  

    } else {


    }

}

// Login user
function login($email, $password) {
    
    global $mysqli;    

    $array = [];

    $sql    = [];
    $sql[]  = "SELECT id, first_name, last_name, email, `password`, `admin`";
    $sql[]  = "FROM users";
    $sql[]  = "WHERE email = ? AND is_active = 1";
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($id, $first_name, $last_name, $email, $hashed_password, $admin);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result && password_verify($password, $hashed_password)) {

        $date_time  = date('Y-m-d H:i:s');
        $ip_address = get_ip_address();

        $sql    = [];
        $sql[]  = "UPDATE users";
        $sql[]  = "SET signed_in_at = ?, last_ip_address = ?";
        $sql[]  = "WHERE id = ?";
        $sql    = implode(" ", $sql);             

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssi', $date_time, $ip_address, $id);
        $stmt->execute();
        $stmt->close();           

        $array['id']            = $id;
        $array['first_name']    = $first_name;
        $array['last_name']     = $last_name;
        $array['email']         = $email;
        $array['admin']         = $admin;
        $array['date_time']     = $date_time;
        $array['ip_address']    = $ip_address;

        return $array;

    } else {

        return false;

    }

}

// Log out user
function logout($id) {
    
    global $mysqli;    

    $date_time  = date('Y-m-d H:i:s');

    $sql    = [];
    $sql[]  = "UPDATE users";
    $sql[]  = "SET signed_out_at = ?";
    $sql[]  = "WHERE id = ?";
    $sql    = implode(" ", $sql);             

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('si', $date_time, $id);
    $stmt->execute();
    $stmt->close();       

}

// Register user
function register($first_name, $last_name, $phone_number, $email, $password, $invite_code) {

    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT id";
    $sql[]  = "FROM invites";
    $sql[]  = "WHERE email = ? AND code = ? AND is_active = 1";
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $email, $invite_code);
    $stmt->execute();
    $stmt->bind_result($id);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        $created_at  = date('Y-m-d H:i:s');    
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $is_active = 1;

        // Create user
        $sql    = [];
        $sql[]  = "INSERT INTO users";
        $sql[]  = "(first_name, last_name, phone_number, email, `password`, created_at, is_active)";
        $sql[]  = "VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sql    = implode(" ", $sql);

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssssssi', $first_name, $last_name, $phone_number, $email, $hashed_password, $created_at, $is_active);
        $user_created = $stmt->execute();
        $user_id = $stmt->insert_id;
        $stmt->close();

        // Create user details
        $sql    = [];
        $sql[]  = "INSERT INTO user_details";
        $sql[]  = "(user_id, created_at)";
        $sql[]  = "VALUES (?, ?)";
        $sql    = implode(" ", $sql);

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('is', $user_id, $created_at);
        $user_details_created = $stmt->execute();
        $stmt->close();
        
        if ($user_created && $user_details_created) {

            $updated_at = date('Y-m-d H:i:s');

            $sql    = [];
            $sql[]  = "UPDATE invites";
            $sql[]  = "SET updated_at = ?, is_active = 0";
            $sql[]  = "WHERE code = ?";
            $sql    = implode(" ", $sql);             
    
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ss', $updated_at, $invite_code);
            $stmt->execute();
            $stmt->close();               

            return true;

        } else {

            return false;
            
        }

    } else {

        return false;

    }

}

// Email lookup
function email_lookup($email) {

    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT id";
    $sql[]  = "FROM users";
    $sql[]  = "WHERE email = ? AND is_active = 1";
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($id);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        return $id;

    } else {

        return false;

    }

}

// Forgot password
function forgot_password($id, $email) {

    global $mysqli;

    $created_at  = date('Y-m-d H:i:s');
    $token = generate_random_string();

    // Create password reset token
    $sql    = [];
    $sql[]  = "INSERT INTO password_reset_tokens";
    $sql[]  = "(email, token, created_at)";
    $sql[]  = "VALUES (?, ?, ?)";
    $sql    = implode(" ", $sql);

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $email, $token, $created_at);
    $result = $stmt->execute();
    $stmt->close();    

    if ($result) {

        // Send email
        if (send_mail('reset_password', ['email' => $email, 'token' => $token])) {

            return true;

        } else {

            return false;

        }

    } else {

        return false;

    }

}

// Valid password reset
function valid_reset($token, $checksum) {

    global $mysqli;

    $sql    = [];
    $sql[]  = "SELECT id, email, created_at";
    $sql[]  = "FROM password_reset_tokens";
    $sql[]  = "WHERE token = ?";
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($id, $email, $created_at);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        if (md5($email) == $checksum && (time() - strtotime($created_at)) < 1800) {

            return true;

        } else {

            return false;

        }

    } else {

        return false;

    }

}

// Reset password
function reset_password($token, $checksum, $password) {

    global $mysqli;

    $sql    = [];
    $sql[]  = "SELECT id, email, created_at";
    $sql[]  = "FROM password_reset_tokens";
    $sql[]  = "WHERE token = ?";
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($id, $email, $created_at);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        if (md5($email) == $checksum && (time() - strtotime($created_at)) < 1800) {

            $updated_at = date('Y-m-d H:i:s');
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql    = [];
            $sql[]  = "UPDATE users";
            $sql[]  = "SET password = ?, updated_at = ?";
            $sql[]  = "WHERE email = ?";
            $sql    = implode(" ", $sql);             
    
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('sss', $hashed_password, $updated_at, $email);
            $success = $stmt->execute();
            $stmt->close();

            if ($success) {

                $sql    = [];
                $sql[]  = "DELETE FROM password_reset_tokens";
                $sql[]  = "WHERE token = ?";
                $sql    = implode(" ", $sql);     
        
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('s', $token);
                $stmt->execute();
                $stmt->close();     

                return true;
                
            } else {

                return false;

            }
            
        } else {

            return false;

        }            

    } else {

        return false;

    }

}

// Current page
function current_page($request_uri, $item) {

    $array = explode('/', $request_uri);
    unset($array[0]);
    $array = array_values($array);

    if (isset($array[0]) && !empty($array[0]) && $array[0] == $item) {

        return ' active';

    } else {

        return false;

    }

}

// Get user details
function get_user_details($id) {

    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT u.first_name, u.last_name, u.phone_number, u.email, d.image, d.birthday_year, d.birthday_month, d.birthday_day, d.gender, d.about, d.statement_1, d.statement_1_answer, d.statement_2, d.statement_2_answer, d.statement_3, d.statement_3_answer";
    $sql[]  = "FROM users u";
    $sql[]  = "LEFT JOIN user_details d";
    $sql[]  = "ON u.id = d.user_id";
    $sql[]  = "WHERE u.id = ? AND u.is_active = 1";
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $phone_number, $email, $image, $birthday_year, $birthday_month, $birthday_day, $gender, $about, $statement_1, $statement_1_answer, $statement_2, $statement_2_answer, $statement_3, $statement_3_answer);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        return array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone_number' => $phone_number,
            'email' => $email,
            'image' => $image,
            'birthday_year' => $birthday_year,
            'birthday_month' => $birthday_month,
            'birthday_day' => $birthday_day,
            'gender' => $gender,
            'about' => $about,
            'statement_1' => $statement_1,
            'statement_1_answer' => $statement_1_answer,
            'statement_2' => $statement_2,
            'statement_2_answer' => $statement_2_answer,
            'statement_3' => $statement_3,
            'statement_3_answer' => $statement_3_answer
        );

    } else {

        return false;

    }

}

// Update profile
function update_profile($type, $post) {

    global $mysqli;    

    if ($type == 'basic') {

        $updated_at = date('Y-m-d H:i:s');

        $sql    = [];
        $sql[]  = "UPDATE users";
        $sql[]  = "SET first_name = ?, last_name = ?, phone_number = ?, email = ?, updated_at = ?";
        $sql[]  = "WHERE id = ?";
        $sql    = implode(" ", $sql);             

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sssssi', $post['first_name'], $post['last_name'], $post['phone_number'], $post['email'], $updated_at, $post['user_id']);
        $stmt->execute();
        $stmt->close();      
        
        $sql    = [];
        $sql[]  = "UPDATE user_details";
        $sql[]  = "SET birthday_year = ?, birthday_month = ?, birthday_day = ?, gender = ?, updated_at = ?";
        $sql[]  = "WHERE user_id = ?";
        $sql    = implode(" ", $sql);             

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sssssi', $post['birthday_year'], $post['birthday_month'], $post['birthday_day'], $post['gender'], $updated_at, $post['user_id']);
        $stmt->execute();
        $stmt->close();           

        return true;

    } else if ($type == 'password') {

        $sql    = [];
        $sql[]  = "SELECT `password`";
        $sql[]  = "FROM users";
        $sql[]  = "WHERE id = ?";
        $sql[]  = "LIMIT 1";
        $sql    = implode(" ", $sql);
        
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $post['user_id']);
        $stmt->execute();
        $result = $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if ($result && $post['password'] == $post['password_confirmation'] && password_verify($post['password_current'], $hashed_password)) {

            $updated_at = date('Y-m-d H:i:s');
            $password   = password_hash($post['password'], PASSWORD_DEFAULT);

            $sql    = [];
            $sql[]  = "UPDATE users";
            $sql[]  = "SET password = ?, updated_at = ?";
            $sql[]  = "WHERE id = ?";
            $sql    = implode(" ", $sql);             

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssi', $password, $updated_at, $post['user_id']);
            $stmt->execute();
            $stmt->close();           

            return true;

        } else {

            return false;

        }       

    } else if ($type == 'image') {

        $updated_at = date('Y-m-d H:i:s');        

        $dir            = '.' . CONFIG['paths']['uploads'] . '/';
        $filename       = basename($_FILES['image']['name']);
        $extension      = substr($filename, strrpos($filename, '.') + 1);
        $new_filename   = md5(str_replace('.', '', microtime(true)) . '_' . rtrim(basename($filename, '.' . $extension))) . '.' . $extension;
        $file           = $dir . $new_filename;

        move_uploaded_file($_FILES['image']['tmp_name'], $file);

        $sql    = [];
        $sql[]  = "UPDATE user_details";
        $sql[]  = "SET image = ?, updated_at = ?";
        $sql[]  = "WHERE user_id = ?";
        $sql    = implode(" ", $sql);             

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssi', $new_filename, $updated_at, $post['user_id']);
        $stmt->execute();
        $stmt->close();         

        return true;

    } else if ($type == 'remove_image') {

        $updated_at = date('Y-m-d H:i:s');        

        $sql    = [];
        $sql[]  = "UPDATE user_details";
        $sql[]  = "SET image = NULL, updated_at = ?";
        $sql[]  = "WHERE user_id = ?";
        $sql    = implode(" ", $sql);             

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('si', $updated_at, $post['user_id']);
        $stmt->execute();
        $stmt->close();         

        unlink('.' . CONFIG['paths']['uploads'] . '/' . $post['image']);

        return true;        
        
    } else if ($type == 'about') {    
        
        $updated_at = date('Y-m-d H:i:s');

        $sql    = [];
        $sql[]  = "UPDATE user_details";
        $sql[]  = "SET about = ?, updated_at = ?";
        $sql[]  = "WHERE user_id = ?";
        $sql    = implode(" ", $sql);             

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssi', $post['about'], $updated_at, $post['user_id']);
        $stmt->execute();
        $stmt->close();    
        
        return true;

    } else if ($type == 'statements') {    
        
        $updated_at = date('Y-m-d H:i:s');

        $sql    = [];
        $sql[]  = "UPDATE user_details";
        $sql[]  = "SET statement_1 = ?, statement_1_answer = ?, statement_2 = ?, statement_2_answer = ?, statement_3 = ?, statement_3_answer = ?, updated_at = ?";
        $sql[]  = "WHERE user_id = ?";
        $sql    = implode(" ", $sql);             

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sssssssi', $post['statement_1'], $post['statement_1_answer'], $post['statement_2'], $post['statement_2_answer'], $post['statement_3'], $post['statement_3_answer'], $updated_at, $post['user_id']);
        $stmt->execute();
        $stmt->close();    
        
        return true;

    } else {

        return false;

    }

}

// Get user details
function get_invites() {

    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT id, email, code, created_at, is_active";
    $sql[]  = "FROM invites";
    $sql[]  = "ORDER BY created_at DESC";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $stmt->store_result(); 
    $stmt->bind_result($id, $email, $code, $created_at, $is_active);

    if ($stmt->num_rows > 0) {
        
        $array = [];

        while ($stmt->fetch()) {
            $array[] = [
                'id'            => $id,
                'email'         => $email,
                'code'          => $code,
                'created_at'    => $created_at,
                'is_active'     => $is_active
            ];
        }
        $stmt->close();    

        return $array;

    } else {

        $stmt->close();
        return false;

    }

}

// Create invite
function create_invite($post) {

    global $mysqli;

    if (isset($post['send_mail'])) {
        $post['send_mail'] = '';
    }

    $created_at = date('Y-m-d H:i:s');
    $is_active  = 1;
    $code       = generate_random_string();

    $sql    = [];
    $sql[]  = "INSERT INTO invites";
    $sql[]  = "(email, code, created_at, is_active)";
    $sql[]  = "VALUES (?, ?, ?, ?)";
    $sql    = implode(" ", $sql);

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssss', $post['email'], $code, $created_at, $is_active);
    $result = $stmt->execute();
    $stmt->close();
    
    if ($result) {

        if ($post['send_mail'] == '1') {

            if (send_mail('invite', ['email' => $post['email'], 'code' => $code])) {

                return true;

            } else {

                return false;

            }

        } else {

            return true;

        }

    } else {

        return false;
        
    }

}

// Get courses
function get_courses($type = 'originals') {

    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT id, `hash`, title, byline, `description`, created_at, `image`, background";
    $sql[]  = "FROM courses";
    $sql[]  = "WHERE is_active = 1";
    if ($type == 'duplicates') {
        $sql[]  = "AND is_copy = 1";
        $sql[]  = "AND uid = ?";
    } else {
        $sql[]  = "AND (is_copy IS NULL OR is_copy = 0)";
    }
    $sql    = implode(" ", $sql);

    $stmt = $mysqli->prepare($sql);
    if ($type == 'duplicates') {
        $stmt->bind_param('i', $_SESSION['id']);
    }
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hash, $title, $byline, $description, $created_at, $image, $background);

    if ($stmt->num_rows > 0) {
        
        $array = [];

        while ($stmt->fetch()) {
            $array[] = [
                'id'            => $id,
                'hash'          => $hash,
                'title'         => $title,
                'byline'        => $byline,
                'description'   => $description,
                'created_at'    => $created_at,
                'image'         => $image,
                'background'    => $background,
            ];
        }
        $stmt->close();    

        return $array;

    } else {

        $stmt->close();
        return false;

    }

}

// Get course
function get_course($id, $uid = null) {

    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT id, `hash`, title, byline, `description`, created_at, `image`, background, content, is_copy";
    $sql[]  = "FROM courses";
    $sql[]  = "WHERE id = ? AND is_active = 1";
    if ($uid != null && is_numeric($uid)) {
        $sql[]  = "AND uid = ?";
    }
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);

    $stmt = $mysqli->prepare($sql);
    if ($uid != null && is_numeric($uid)) {
        $stmt->bind_param('ii', $id, $uid);
    } else {
        $stmt->bind_param('i', $id);
    }
    $stmt->execute();
    $stmt->bind_result($id, $hash, $title, $byline, $description, $created_at, $image, $background, $content, $is_copy);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        return [
            'id'                => $id,
            'hash'              => $hash,
            'title'             => $title,
            'byline'            => $byline,
            'description'       => $description,
            'created_at'        => $created_at,
            'image'             => $image,
            'background'        => $background,
            'content'           => json_decode($content, true),
            'is_copy'           => $is_copy
        ];

    } else {

        return false;

    }

}

// Get users
function get_users($id = null) {
    
    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT id, first_name, last_name";
    $sql[]  = "FROM users";
    if ($id) {
        $sql[]  = "WHERE id != ? AND is_active = 1";
    } else {
        $sql[]  = "WHERE is_active = 1";
    }
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    if ($id) {
        $stmt->bind_param('i', $id);
    }
    $stmt->execute();
    $stmt->store_result(); 
    $stmt->bind_result($id, $first_name, $last_name);

    if ($stmt->num_rows > 0) {
        
        $array = [];

        while ($stmt->fetch()) {
            $array[] = [
                'id'            => $id,
                'first_name'    => $first_name,
                'last_name'     => $last_name
            ];
        }
        $stmt->close();    

        return $array;

    } else {

        $stmt->close();
        return false;

    }

}

// Get educator
function get_educator($id) {

    global $mysqli;    

    $sql    = [];
    $sql[]  = "SELECT u.first_name, u.last_name, u.phone_number, u.email, d.image, d.birthday_year, d.birthday_month, d.birthday_day, d.gender, d.about, d.statement_1, d.statement_1_answer, d.statement_2, d.statement_2_answer, d.statement_3, d.statement_3_answer";
    $sql[]  = "FROM users u";
    $sql[]  = "LEFT JOIN user_details d";
    $sql[]  = "ON u.id = d.user_id";
    $sql[]  = "WHERE u.id = ? AND u.is_active = 1";
    $sql[]  = "LIMIT 1";
    $sql    = implode(" ", $sql);
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $phone_number, $email, $image, $birthday_year, $birthday_month, $birthday_day, $gender, $about, $statement_1, $statement_1_answer, $statement_2, $statement_2_answer, $statement_3, $statement_3_answer);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        return [
            'id'                => $id,
            'first_name'        => $first_name,
            'last_name'         => $last_name,
            'phone_number'      => $phone_number,
            'email'             => $email,
            'image'             => $image,
            'birthday'          => str_pad($birthday_year, 4, '0', STR_PAD_LEFT) . '-' . str_pad($birthday_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($birthday_day, 2, '0', STR_PAD_LEFT),
            'gender'            => $gender,
            'about'             => $about,
            'statement_1'       => $statement_1,
            'statement_1_answer' => $statement_1_answer,
            'statement_2'       => $statement_2,
            'statement_2_answer' => $statement_2_answer,
            'statement_3'       => $statement_3,
            'statement_3_answer' => $statement_3_answer
        ];

    } else {

        return false;

    }

}

// Duplicate course
function duplicate_course($post) {

    global $mysqli;    

    if (isset($post['course_id']) && !empty($post['course_id']) && is_numeric($post['course_id']) && isset($post['course_title']) && !empty($post['course_title'])) {

        //$hash = substr(sha1(uniqid(mt_rand(), true)), 0, 8);

        // Set course as copy
        $post['course_copy'] = 1;

        // Duplicate course
        $sql   = [];
        $sql[] = "INSERT INTO courses";
        $sql[] = "(hash, title, byline, description, image, background, content, updated_at, is_copy, uid)";
        $sql[] = "SELECT `hash`, ?, byline, `description`, `image`, background, content, NOW(), ?, ?";
        $sql[] = "FROM courses";
        $sql[] = "WHERE id = ?";
        $sql   = implode(" ", $sql);
        
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('siii', $post['course_title'], $post['course_copy'], $post['uid'], $post['course_id']);
        if ($stmt->execute()) {
            $stmt->close();

            return true;
        } else {
            $stmt->close();

            return false;
        }

    } else {

        return false;
    
    }

}

// Delete course
function delete_course($id, $uid) {

    global $mysqli;

    if (isset($id) && !empty($id) && is_numeric($id)) {

        // Get course hash to delete directory
        $sql = "SELECT hash FROM courses WHERE id = ? AND uid = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ii', $id, $uid);
        $stmt->execute();
        $stmt->bind_result($hash);
        $result = $stmt->fetch();
        $stmt->close();

        if ($result) {
            // Check if there are any other courses (original or copies) using this hash
            $sql_check = "SELECT COUNT(*) FROM courses WHERE hash = ? AND is_active = 1 AND NOT (id = ? AND uid = ?)";
            $stmt_check = $mysqli->prepare($sql_check);
            $stmt_check->bind_param('sii', $hash, $id, $uid);
            $stmt_check->execute();
            $stmt_check->bind_result($other_count);
            $stmt_check->fetch();
            $stmt_check->close();

            // Delete course from database
            $sql = "DELETE FROM courses WHERE id = ? AND uid = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ii', $id, $uid);

            if ($stmt->execute()) {
                $stmt->close();

                // Only delete course directory if no other courses use this hash
                if ($other_count == 0) {
                    $course_dir = 'courses/' . $hash;
                    if (is_dir($course_dir)) {
                        delete_directory_recursive($course_dir);
                    }
                }

                return true;
            } else {
                $stmt->close();

                return false;
            }
        }

    } else {

        return false;

    }

}

// Update course
function update_course($post, $uid) {

    global $mysqli;

    if (isset($post) && !empty($post) && isset($uid) && !empty($uid) && is_numeric($uid)) {

        $stmt = $mysqli->prepare("SELECT content FROM courses WHERE id = ? AND uid = ?");
        $stmt->bind_param("ii", $post['course_id'], $uid);
        $stmt->execute();
        $stmt->bind_result($content);
        $stmt->fetch();
        $stmt->close();
        
        $slides = json_decode($content, true);
        if (!is_array($slides)) {
            die("ERROR!");
        }        

        $includes = isset($post['include']) && is_array($post['include']) ? $post['include'] : [];
        $orders   = isset($post['order']) && is_array($post['order']) ? $post['order'] : [];
        
        foreach ($slides as &$slide) {
            $slideId = $slide['id'];

            if (isset($orders[$slideId])) {
                $slide['order'] = (int) $orders[$slideId];
            }

            $slide['is_active'] = in_array($slideId, $includes);
        }
        unset($slide);
        
        usort($slides, function($a, $b) {
            return $a['order'] - $b['order'];
        });
        
        $new_content = json_encode($slides, JSON_UNESCAPED_UNICODE);

        $stmt = $mysqli->prepare("UPDATE courses SET content = ? WHERE id = ? AND uid = ?");
        $stmt->bind_param("sii", $new_content, $post['course_id'], $uid);
        
        if ($stmt->execute()) {
            $stmt->close();
            
            return true;
        } else {
            $stmt->close();
           
            return false;
        }

    } else {

        return false;

    }

}

// Make content
function make_content($content, $educator = null, $co_educator = null) {

    global $mysqli;

    $array = [];

    foreach ($content as $slide) {

        if ($slide['is_active'] == 1) {

            if ($slide['type'] == 'placeholder') {

                if ($slide['module'] == 'educator' && isset($educator) && !empty($educator) && is_array($educator) && isset($educator['about']) && !empty($educator['about'])) {

                    $array[] = [
                        'id' => $slide['id'],
                        'order' => $slide['order'],
                        'title' => $slide['title'],
                        'excludable' => $slide['excludable'],
                        'is_active' => $slide['is_active'],
                        'type' => $slide['type'],
                        'module' => $slide['module'],
                        'link' => $slide['link'],
                        'thumb' => $slide['thumb'],
                        'content' => [
                            'image' => (!empty($educator['image'])) ? $educator['image'] : null,
                            'heading' => 'Vem är ' . $educator['first_name'] . '?',
                            'text' => $educator['about']
                        ]
                    ];

                }

                if ($slide['module'] == 'co_educator' && isset($co_educator) && !empty($co_educator) && is_array($co_educator) && isset($co_educator['about']) && !empty($co_educator['about'])) {

                    $array[] = [
                        'id' => $slide['id'],
                        'order' => $slide['order'],
                        'title' => $slide['title'],
                        'excludable' => $slide['excludable'],
                        'is_active' => $slide['is_active'],
                        'type' => $slide['type'],
                        'module' => $slide['module'],
                        'link' => $slide['link'],
                        'thumb' => $slide['thumb'],
                        'content' => [
                            'image' => (!empty($co_educator['image'])) ? $co_educator['image'] : null,                            
                            'heading' => 'Vem är ' . $co_educator['first_name'] . '?',
                            'text' => $co_educator['about']
                        ]
                    ];

                }

                if ($slide['module'] == 'statement' && (!empty($educator['statement_1']) || !empty($educator['statement_2']) || !empty($educator['statement_3']))) {

                    $statements = [];

                    if (!empty($educator['statement_1']) && !empty($educator['statement_1_answer'])) {
                        $statements[] = [
                            'statement' => $educator['statement_1'],
                            'statement_answer' => $educator['statement_1_answer']
                        ];
                    }

                    if (!empty($educator['statement_2']) && !empty($educator['statement_2_answer'])) {
                        $statements[] = [
                            'statement' => $educator['statement_2'],
                            'statement_answer' => $educator['statement_2_answer']
                        ];
                    }    
                    
                    if (!empty($educator['statement_3']) && !empty($educator['statement_3_answer'])) {
                        $statements[] = [
                            'statement' => $educator['statement_3'],
                            'statement_answer' => $educator['statement_3_answer']
                        ];
                    }                    
               
                    if (isset($co_educator) && !empty($co_educator) && is_array($co_educator) && (!empty($co_educator['statement_1']) || !empty($co_educator['statement_2']) || !empty($co_educator['statement_3']))) {

                        if (!empty($co_educator['statement_1']) && !empty($co_educator['statement_1_answer'])) {
                            $statements[] = [
                                'statement' => $co_educator['statement_1'],
                                'statement_answer' => $co_educator['statement_1_answer']
                            ];
                        }
    
                        if (!empty($co_educator['statement_2']) && !empty($co_educator['statement_2_answer'])) {
                            $statements[] = [
                                'statement' => $co_educator['statement_2'],
                                'statement_answer' => $co_educator['statement_2_answer']
                            ];
                        }    
                        
                        if (!empty($co_educator['statement_3']) && !empty($co_educator['statement_3_answer'])) {
                            $statements[] = [
                                'statement' => $co_educator['statement_3'],
                                'statement_answer' => $co_educator['statement_3_answer']
                            ];
                        }     
                        
                    }

                    $keys = array_keys($statements);

                    shuffle($keys);

                    $shuffled = [];
                    foreach ($keys as $key) {
                        $shuffled[$key] = $statements[$key];
                    }                    

                    $statements = $shuffled;

                    foreach ($statements as $statement) {
    
                        $array[] = [
                            'id' => null,
                            'order' => null,
                            'title' => 'Påstående',
                            'excludable' => false,
                            'is_active' => true,
                            'type' => 'placeholder',
                            'module' => 'statement',
                            'link' => $slide['link'],
                            'thumb' => null,
                            'content' => [
                                'heading' => $statement['statement'],
                            ]
                        ];
    
                        $array[] = [
                            'id' => null,
                            'order' => null,
                            'title' => 'Svar',
                            'excludable' => false,
                            'is_active' => true,
                            'type' => 'image',
                            'module' => '',
                            'link' => ($statement['statement_answer'] == 't') ? 'true.gif' : 'false.gif',
                            'thumb' => null
                        ];                   
                    
                    }                    

                }

                if ($slide['module'] == 'text_block') {

                    $array[] = $slide;

                }

            } else {

                $array[] = $slide;

            }

        }

    }

    return $array;

}

// Generate course hash
function generate_course_hash() {
    return substr(sha1(uniqid(mt_rand(), true)), 0, 8);
}

// Create course directory
function create_course_directory($hash) {
    $course_dir = 'courses/' . $hash;

    if (!file_exists($course_dir)) {
        if (mkdir($course_dir, 0755, true)) {
            return true;
        }
    }

    return false;
}

// Get all courses for admin
function get_all_courses() {
    global $mysqli;

    $sql    = [];
    $sql[]  = "SELECT id, `hash`, title, byline, `description`, created_at, `image`, background, is_copy, uid";
    $sql[]  = "FROM courses";
    $sql[]  = "WHERE is_active = 1 AND (is_copy IS NULL OR is_copy = 0)";
    $sql[]  = "ORDER BY created_at DESC";
    $sql    = implode(" ", $sql);

    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hash, $title, $byline, $description, $created_at, $image, $background, $is_copy, $uid);

    if ($stmt->num_rows > 0) {

        $array = [];

        while ($stmt->fetch()) {
            $array[] = [
                'id'            => $id,
                'hash'          => $hash,
                'title'         => $title,
                'byline'        => $byline,
                'description'   => $description,
                'created_at'    => $created_at,
                'image'         => $image,
                'background'    => $background,
                'is_copy'       => $is_copy,
                'uid'           => $uid
            ];
        }
        $stmt->close();

        return $array;

    } else {

        $stmt->close();
        return false;

    }
}

// Create new course
function create_course($post, $image_filename = null, $background_filename = null) {
    global $mysqli;

    if (isset($post['title']) && !empty($post['title'])) {

        $hash = generate_course_hash();
        $created_at = date('Y-m-d H:i:s');

        // Create course directory
        if (!create_course_directory($hash)) {
            return false;
        }

        // Default empty content
        $content = json_encode([]);

        $sql = [];
        $sql[] = "INSERT INTO courses";
        $sql[] = "(hash, title, byline, description, image, background, content, created_at, is_copy, uid, is_active)";
        $sql[] = "VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, NULL, 1)";
        $sql = implode(" ", $sql);

        $byline = $post['byline'] ?? '';
        $description = isset($post['description']) ? substr($post['description'], 0, 255) : '';
        $image = $image_filename ?? 'thumb.png';
        $background = $background_filename ?? 'default.png';

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssssssss',
            $hash,
            $post['title'],
            $byline,
            $description,
            $image,
            $background,
            $content,
            $created_at
        );

        if ($stmt->execute()) {
            $course_id = $stmt->insert_id;
            $stmt->close();
            return $course_id;
        } else {
            $stmt->close();
            return false;
        }
    }

    return false;
}

// Update course basic info
function update_course_info($post, $course_id, $image_filename = null, $background_filename = null) {
    global $mysqli;

    if (isset($post) && !empty($post) && isset($course_id) && !empty($course_id) && is_numeric($course_id)) {

        $updated_at = date('Y-m-d H:i:s');

        $sql = [];
        $sql[] = "UPDATE courses";
        $sql[] = "SET title = ?, byline = ?, description = ?, updated_at = ?";

        // Add image update if provided
        if ($image_filename !== null) {
            $sql[] = ", image = ?";
        }

        // Add background update if provided
        if ($background_filename !== null) {
            $sql[] = ", background = ?";
        }

        $sql[] = "WHERE id = ? AND (is_copy IS NULL OR is_copy = 0)";
        $sql = implode(" ", $sql);

        $byline = $post['byline'] ?? '';
        $description = isset($post['description']) ? substr($post['description'], 0, 255) : '';

        $stmt = $mysqli->prepare($sql);

        // Bind parameters dynamically based on what's being updated
        if ($image_filename !== null && $background_filename !== null) {
            $stmt->bind_param('ssssssi',
                $post['title'],
                $byline,
                $description,
                $updated_at,
                $image_filename,
                $background_filename,
                $course_id
            );
        } elseif ($image_filename !== null) {
            $stmt->bind_param('sssssi',
                $post['title'],
                $byline,
                $description,
                $updated_at,
                $image_filename,
                $course_id
            );
        } elseif ($background_filename !== null) {
            $stmt->bind_param('sssssi',
                $post['title'],
                $byline,
                $description,
                $updated_at,
                $background_filename,
                $course_id
            );
        } else {
            $stmt->bind_param('ssssi',
                $post['title'],
                $byline,
                $description,
                $updated_at,
                $course_id
            );
        }

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    return false;
}

// Delete course (admin version)
function delete_course_admin($id) {
    global $mysqli;

    if (isset($id) && !empty($id) && is_numeric($id)) {

        // Get course hash to delete directory
        $sql = "SELECT hash FROM courses WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($hash);
        $result = $stmt->fetch();
        $stmt->close();

        if ($result) {
            // Check if there are any copies of this course before deleting files
            $sql_check = "SELECT COUNT(*) FROM courses WHERE hash = ? AND is_copy = 1 AND is_active = 1";
            $stmt_check = $mysqli->prepare($sql_check);
            $stmt_check->bind_param('s', $hash);
            $stmt_check->execute();
            $stmt_check->bind_result($copy_count);
            $stmt_check->fetch();
            $stmt_check->close();

            // Delete course from database
            $sql = "DELETE FROM courses WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $id);

            if ($stmt->execute()) {
                $stmt->close();

                // Only delete course directory if no copies exist
                if ($copy_count == 0) {
                    $course_dir = 'courses/' . $hash;
                    if (is_dir($course_dir)) {
                        delete_directory_recursive($course_dir);
                    }
                }

                return true;
            } else {
                $stmt->close();
                return false;
            }
        }
    }

    return false;
}

// Update course content (slides)
function update_course_content($course_id, $content) {
    global $mysqli;

    if (isset($course_id) && !empty($course_id) && is_numeric($course_id)) {

        $updated_at = date('Y-m-d H:i:s');
        $content_json = json_encode($content);

        $sql = [];
        $sql[] = "UPDATE courses";
        $sql[] = "SET content = ?, updated_at = ?";
        $sql[] = "WHERE id = ? AND (is_copy IS NULL OR is_copy = 0)";
        $sql = implode(" ", $sql);

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssi', $content_json, $updated_at, $course_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    return false;
}

// Handle file upload for course slides
function upload_course_file($file, $course_hash, $allowed_types = ['image/jpeg', 'image/png', 'image/gif']) {

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Validate file type
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    $course_dir = 'courses/' . $course_hash;

    // Create directory if it doesn't exist
    if (!file_exists($course_dir)) {
        if (!mkdir($course_dir, 0755, true)) {
            return false;
        }
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $course_dir . '/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }

    return false;
}

// Recursively delete directory and all contents
function delete_directory_recursive($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    // Normalize path separators for Windows compatibility
    $dir = rtrim(str_replace('\\', '/', $dir), '/');

    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file) {
        $path = $dir . '/' . $file;

        if (is_dir($path)) {
            delete_directory_recursive($path);
        } else {
            if (file_exists($path)) {
                // On Windows, make sure file is not read-only
                if (PHP_OS_FAMILY === 'Windows') {
                    chmod($path, 0777);
                }
                unlink($path);
            }
        }
    }

    // Clear file status cache
    clearstatcache();

    // Double-check that directory is empty
    $remaining_files = array_diff(scandir($dir), ['.', '..']);

    if (empty($remaining_files)) {
        // On Windows, make sure directory is not read-only
        if (PHP_OS_FAMILY === 'Windows') {
            chmod($dir, 0777);
        }
        return rmdir($dir);
    } else {
        // Log remaining files for debugging
        error_log("Cannot delete directory $dir - remaining files: " . implode(', ', $remaining_files));
        return false;
    }
}