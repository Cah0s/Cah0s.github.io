<?php
include '../system/database.php';
if(!isset($user_id) && !isset($usuario)){
	header("Location: " .HOME."auth");
}
else{
	header("Location: userhome.php");
}