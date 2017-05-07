<?php
require_once '../../system/database.php';
$pdo=conectar();

$check_st = $_GET['check'];
$check_id = $_GET['id'];

if(isset($_GET['check']) && isset($_GET['id'])):
	$upd = $pdo->prepare("SELECT * FROM users WHERE idusr='$check_id'");
	$upd->execute();
	$online = $upd->fetch(PDO::FETCH_OBJ);

	echo $online->active;

	if($online->active == "online"){
		//echo '<audio autoplay> <source src="http://www.soundescapestudios.com/SESAudio/SES%20Site%20Sounds/Beeps/Beeps-short-01.wav" type="audio/mpeg"></audio>';
	}
	else{ NULL; /*echo " [ " .$check_st. " / " .$check_id. " ]";*/ }

endif;