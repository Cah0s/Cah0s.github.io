<?php
//ini_set("display_errors", 1);
(!isset($_SESSION) ? session_start() : NULL );
require_once '../system/database.php';

/************************************
 * FUNÇÃO DE POR ONLINE OU AUSENTE
 ***********************************/
if(isset($_GET['inactive'])):
	// if(!isset($user_id) && !isset($usuario)){
	// 	$upd = $pdo->prepare("UPDATE users SET active='offline' WHERE addr='$addr'");
	// 	$upd->execute();
	// }
	// else{
		$upd = $pdo->prepare("UPDATE users SET active='ausente' WHERE idusr='$user_id'");
		$upd->execute();
	//}
elseif(isset($_GET['outside'])):
	$upd = $pdo->prepare("UPDATE users SET active='outside' WHERE idusr='$user_id'");
	$upd->execute();
elseif(isset($_GET['active'])):
	$upd = $pdo->prepare("UPDATE users SET active='online', ontime=NOW() WHERE idusr='$user_id'");
	$upd->execute();
endif;