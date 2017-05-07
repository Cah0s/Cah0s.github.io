<?php
ob_start();
(!isset($_SESSION) ? session_start() : NULL);
ini_set("display_errors", 0);
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
$usuario = (isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : NULL );
$user_id = (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL );
$admin = (isset($_SESSION["admin"]) ? $_SESSION["admin"] : NULL );
$admin_id = (isset($_SESSION["admin_id"]) ? $_SESSION["admin_id"] : NULL );
$addr    = $_SERVER['REMOTE_ADDR'];
//date_default_timezone_set("Europe/Berlin");
//setlocale(LC_ALL, 'de_DE.uft8');
//if (setlocale(LC_ALL, 'de_DE.utf8') == false) { print "<h1>Fehler beim einstellen der Sprache!</h1>"; }

// DEFINE CONEXÃO COM O BANCO ################
define('HOST', 'localhost');
define('USER', 'root');
define('PASS', 'cahos');
define('BANCO', 'testepanel');

// DEFINE SERVIDOR DE E-MAIL ################
define('MAILUSER', 'sebastiankopp.design@gmail.com');
define('MAILPASS', 'S3ba78I1nK0pp25');
define('MAILPORT', '465');
//define('MAILPORT', '587');
define('MAILHOST', 'smtp.gmail.com');

// DEFINE CAMINHO RAÍZ DO SITE ################
define('HOME', ( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"].'/meineprojekte/teste'. DIRECTORY_SEPARATOR :  'https://'.$_SERVER["SERVER_NAME"].'/meineprojekte/teste'. DIRECTORY_SEPARATOR);
define('CONFIG', 'system');
define('INCLUDE_PATH', HOME. DIRECTORY_SEPARATOR .CONFIG. DIRECTORY_SEPARATOR);
define('LOGIN', HOME.'auth');
define('PAINEL', HOME.'painel'. DIRECTORY_SEPARATOR);