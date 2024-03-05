<?php
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'tools_database');
define('CHARSET', 'utf8mb4');
try {
    if (defined("INITIALIZING_DATABASE"))
        $data_source_name = "mysql:host=" . DB_HOST . "; charset=" . CHARSET;
    else
        $data_source_name = "mysql:host=" . DB_HOST . "; dbname=" . DB_NAME . "; charset=" . CHARSET;
    $pdo = new PDO($data_source_name, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connnection failed: " . $e->getMessage();
    header("Location: dbinit.php");
}


function logout()
{
    $_SESSION = [];
    session_destroy();
    setcookie("PHPSESSID", '', time() - 3600, "/", "", 0, 0);
}

function redirectIfLoggedIn()
{
    if (!empty($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }
}

function redirectIfNotLoggedIn()
{
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }
}
