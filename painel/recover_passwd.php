<?php
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
if(isset($_SESSION['user_id']) || isset($_SESSION["usuario"])){
    header("Location: ".LOGIN);
}

$msg = false;
$userid = $_GET['userid'];
$code = $_GET['code'];

if(anti_injection($userid)) { echo anti_injection($userid); return FALSE; }
elseif(anti_injection($code)) { echo anti_injection($code); return FALSE; }

$pdo=conectar();
$statement = $pdo->prepare("SELECT * FROM users WHERE idusr=:id");
//$statement->execute();
$result = $statement->execute(array('id' => $userid));
$user = $statement->fetch(PDO::FETCH_OBJ);

if(!isset($userid) || !isset($code)) {
	$message  = '<div class="message is-warning">';
    $message .= '  <p class="message-header">WARNING</p>';
    $message .= '  <p class="message-body">Você não tem permissão para visualizar está página!</p>';
    $message .= '</div>';
    echo $message;
    $msg = true;
    return FALSE;
}
if($user->idusr == NULL) {
	$message  = '<div class="message is-danger" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">ERRO</p>';
    $message .= '  <p class="message-body">O Usuário não foi encontrado ou o mesmo não pediu uma recuperação de senha!</p>';
    $message .= '</div>';
    echo $message;
    $msg = true;
    return FALSE;
}
if($user->pwdrec == "") {
	$message  = '<div class="message is-danger" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">ERRO</p>';
    $message .= '  <p class="message-body">Não existe Key ativa para este Usuário</p>';
    $message .= '</div>';
    echo $message;
    $msg = true;
}
if($user->pwdrec != "" && strtotime($user->pwdtime) < (time()-24*3600) ) {
	$message  = '<div class="message is-warning" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">WARNING</p>';
    $message .= '  <p class="message-body">A validade da sua Key expirou. Por favor peça um novo no formulário de recuperação!</p>';
    $message .= '</div>';
    echo $message;
    $msg = true;
}
//Überprüfe den Passwortcode
if($user->pwdrec != "" && sha1($code) != $user->pwdrec) {
	$message  = '<div class="message is-warning" style="width:60%;margin:10px auto;">';
    $message .= '  <p class="message-header">WARNING</p>';
    $message .= '  <p class="message-body">A Key solicitada está inválida. Garanta que você acessou o link certo como enviado no email [<b>'.$user->emailusr.'</b>] de recuperação.<br>Caso tenha utilizado multiplas vezes o formulário de recuperação fique atento no email mais recente para poder alterar a senha com sucesso.</p>';
    $message .= '</div>';
    echo $message;
    $msg = true;
}
if(!$msg):
?>

	<div class="my_box my_box_form">

	    <div class="boxx box_header">
	      <span class="box_separator user_form_icon">
	        <div class="recover_icon" > </div>
	      </span>
	      <span class="box_separator box_header_title">OnlineSys</span>
	    </div>

		<div class="boxx">
			<form method="POST">
				<div class="campo">
				  <input type="text" name="password" placeholder="Informe a nova senha" class="inputsl">
				</div>

				<div class="campo">
				  <input type="text" name="password2" placeholder="Confirme a senha" class="inputsr">
				</div>

				<input type="submit" name="send" class="send">
			</form>
		</div>

		<div class="boxx box_msg">
			<?php
			$error = false;
				if(isset($_POST['send'])) {
					$password = $_POST['password'];
					$password2 = $_POST['password2'];
					
					if($password == ""){
						$message  = '<div class="message is-warning">';
			            $message .= '  <p class="message-header">WARNING</p>';
			            $message .= '  <p class="message-body">Informe uma Senha!</p>';
			            $message .= '</div>';
			            echo $message;
						$error = true;
					}
					elseif($password2 == ""){
						$message  = '<div class="message is-warning">';
			            $message .= '  <p class="message-header">WARNING</p>';
			            $message .= '  <p class="message-body">Confirme a Senha!</p>';
			            $message .= '</div>';
			            echo $message;
						$error = true;
					}
					elseif(strlen($password) < 5){
						$message  = '<div class="message is-warning">';
			            $message .= '  <p class="message-header">WARNING</p>';
			            $message .= '  <p class="message-body">A Senha deve conter no Minimo 8 Caracteres!</p>';
			            $message .= '</div>';
			            echo $message;
						$error = true;
					}
					elseif(anti_injection($password)){
						echo anti_injection($password)." no campo Senha</p></div>";
						$error = true;
					}
					elseif(anti_injection($password2)){
						echo anti_injection($password2)." no campo Confirmar Senha</p></div>";
						$error = true;
					}
					elseif($password != $password2) {
						$message  = '<div class="message is-warning">';
			            $message .= '  <p class="message-header">WARNING</p>';
			            $message .= '  <p class="message-body">As Senhas não coincidem!</p>';
			            $message .= '</div>';
			            echo $message;
						$error = true;
					}elseif(hash("sha1", $password) == $user->password) {
						$message  = '<div class="message is-danger">';
			            $message .= '  <p class="message-header">ERROR</p>';
			            $message .= '  <p class="message-body">A senha ( <span style="color:rgb(0, 156, 88);">'.$password.'</span> ) que você está tentando definir como nova é a sua atual senha!!, você não pode altera-lá usando o recovery, por favor faça login no painel e edite sua senha por la!</p>';
			            $message .= '</div>';
			            echo $message;
						$error = true;
					}
					else {
						if(!$error){
							//$passworthash = password_hash($passwort, PASSWORD_DEFAULT);
							$password_enc = hash("sha1", $password);
							$addr = $_SERVER['REMOTE_ADDR'];
							$statement = $pdo->prepare("UPDATE users SET addr='$addr', password=:password, pwdrec=NULL, pwdtime=NULL, upd=NOW() WHERE idusr=:id");
							$result = $statement->execute(array('password' => $password_enc, 'id'=> $userid ));
							
							if($result) {
								$message  = '<div class="message is-success">';
					            $message .= '  <p class="message-header">SUCCESS</p>';
					            $message .= '  <p class="message-body">Sua nova senha foi inserida, ja pode logar!</p>';
					            $message .= '</div>';
					            echo $message;
					            echo '<br><a href="'.LOGIN.'">&nbsp;voltar para login</a>';

							}
							else{
								$message  = '<div class="message is-danger">';
					            $message .= '  <p class="message-header">ERROR</p>';
					            $message .= '  <p class="message-body">Aconteceu algo, má nun sei uk...</p>';
					            $message .= '</div>';
					            echo $message;
							}
						}
						else{
							$message  = '<div class="message is-danger">';
				            $message .= '  <p class="message-header">ERROR</p>';
				            $message .= '  <p class="message-body">Erro Fatal :x</p>';
				            $message .= '</div>';
				            echo $message;
						}
					}
				}
				?>
		</div>
	</div>
<?php endif; ?>