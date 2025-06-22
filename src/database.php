<?php
@$mysqli = new mysqli(CONFIG['db']['host'], CONFIG['db']['user'], CONFIG['db']['password'], CONFIG['db']['database']);

// Connection false
if ($mysqli->connect_errno) {
    die('ERROR: ' . $mysqli->connect_error);
}

// Set charset for connection
$mysqli->set_charset(CONFIG['db']['charset']);