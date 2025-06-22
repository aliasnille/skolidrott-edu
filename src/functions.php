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
    $sql[]  = "SELECT id, `hash`, title, byline, `description`, created_at, `image`";
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
    $stmt->bind_result($id, $hash, $title, $byline, $description, $created_at, $image);

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
    $sql[]  = "SELECT id, `hash`, title, byline, `description`, created_at, `image`, content";
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
    $stmt->bind_result($id, $hash, $title, $byline, $description, $created_at, $image, $content);
    $result = $stmt->fetch();
    $stmt->close();

    if ($result) {

        return [
            'id'                => $id,
            'hash'              => $hash,
            'title'             => $title,
            'description'       => $description,
            'created_at'        => $created_at,
            'image'             => $image,
            'content'           => json_decode($content, true)
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
        $sql[] = "(hash, title, byline, description, image, content, updated_at, is_copy, uid)";
        $sql[] = "SELECT `hash`, ?, byline, `description`, `image`, content, NOW(), ?, ?";
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

        $sql = "DELETE FROM courses WHERE id = ? AND uid = ?";
        
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ii', $id, $uid);
        
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