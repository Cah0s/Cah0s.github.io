<?php
ini_set("display_errors", 1);
//require '../../system/database.php';
(!isset($_SESSION)) ? session_start() : NULL;
if(!isset($_SESSION['user_id']) || !isset($_SESSION["usuario"])){
	header("Location: ".LOGIN);
}
?>