<?php
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
if(isset($_SESSION['user_id']) || isset($_SESSION["usuario"])){
    header("Location: ".LOGIN);
}

$data = $_GET['data'];
$uid  = $_GET['uid'];
$ukey = $_GET['key'];

$pdo=conectar();
$statement = $pdo->prepare("SELECT * FROM users WHERE login=:login");
$result = $statement->execute(array('login' => $uid));
$user = $statement->fetch(PDO::FETCH_OBJ);

if(!isset($data) || !isset($uid) || !isset($ukey)) {
    $message  = '<div class="message is-warning">';
    $message .= '  <p class="message-header">WARNING</p>';
    $message .= '  <p class="message-body">Você não tem permissão para visualizar está página!</p>';
    $message .= '</div>';
    echo $message;
    return FALSE;
}
if($user->login === NULL){
	$message  = '<div class="message is-danger" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">ERRO</p>';
    $message .= '  <p class="message-body">O Usuário não foi encontrado</p>';
    $message .= '</div>';
    echo $message;
    return FALSE;
}
elseif($user->userkey === ""){
	$message  = '<div class="message is-info" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">INFO</p>';
    $message .= '  <p class="message-body">O Usuário ja foi ativado!</p>';
    $message .= '</div>';
    echo $message;
    return FALSE;
}
elseif($ukey != $user->userkey){
	$message  = '<div class="message is-warning" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">WARNING</p>';
    $message .= '  <p class="message-body">A Key solicitada não existe ou está inválida!</p>';
    $message .= '</div>';
    echo $message;
    return FALSE;
}
elseif($data != hash("sha1", $user->login)){
	$message  = '<div class="message is-danger" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">ERRO</p>';
    $message .= '  <p class="message-body">O Usuário não pode ser ativado com essa Key!</p>';
    $message .= '</div>';
    echo $message;
    return FALSE;
}
else{
    $addr = $_SERVER['REMOTE_ADDR'];
	$statement = $pdo->prepare("UPDATE users SET addr='$addr', userkey=NULL, status=:status WHERE userkey='$ukey' AND login='$uid'");
	$result = $statement->execute(array('status' => 1));

	if($result){
		$message  = '<div class="message is-success" style="width:60%;margin:10px auto;">';
	    $message .= '  <p class="message-header">SUCCESS</p>';
	    $message .= '  <p class="message-body">Seu usuário está ativado, use a página de login para poder usar o painel!<br><br><font color="white">Obrigado por usar o nosso Sistema</font>.</p>';
	    $message .= '</div>';
	    echo $message;
	}
	else{
		$message  = '<div class="message is-danger">';
        $message .= '  <p class="message-header">ERROR</p>';
        $message .= '  <p class="message-body">Seu usuárionão pôde ser ativado, tente novamente mais tarde, caso o erro persista, contate a Administração!</p>';
        $message .= '</div>';
        echo $message;
	}
}
