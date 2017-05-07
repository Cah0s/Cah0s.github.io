<?php
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
$pdo=conectar();
if(isset($_SESSION['user_id']) || isset($_SESSION["usuario"])){
    header("Location: ".LOGIN);
}

?>

	<div class="my_box my_box_form">

	    <div class="boxx box_header">
	      <span class="box_separator user_form_icon">
	        <div class="recover_icon" > </div>
	      </span>
	      <span class="box_separator box_header_title">OnlineSys</span>
	    </div>

		<div class="boxx">
			<form  method="POST">
				<div class="campo">
				  <input type="text" name="recover" placeholder="Informe o seu email" class="inputsl">
				</div>

				<input type="submit" name="send" class="send">
			</form>
		</div>

		<div class="boxx box_msg">
			<?php
		    $error = false;
		    /**********************************
		    * CADASTRAR NOVO ARTIGO
		    **********************************/
		    if(isset($_POST['send'])){
		        $recover   = $_POST['recover'];

		        if(anti_injection($recover)){
		          echo anti_injection($recover);
		          $error = true;
		        }
		        elseif($recover == ""){
		        	$message  = '<div class="message is-warning">';
	                $message .= '  <p class="message-header">WARNING</p>';
	                $message .= '  <p class="message-body">Informe um Email!</p>';
	                $message .= '</div>';
	                echo $message;
		        	$error = true;
		        }
		        elseif(!filter_var($recover, FILTER_VALIDATE_EMAIL)) {
		            $message  = '<div class="message is-warning">';
	                $message .= '  <p class="message-header">WARNING</p>';
	                $message .= '  <p class="message-body">Utilize um formato de email valido, ex: <font color="white">email@mail.com</font>!</p>';
	                $message .= '</div>';
	                echo $message;
		            $error = true;
		        }
		        else{
		        	$mail = $pdo->prepare("SELECT * FROM users WHERE emailusr=:email");
		            $result = $mail->execute(array('email' => $recover));
		            $user = $mail->fetch(PDO::FETCH_OBJ);
		            
		            if($user === false) {
		            	$message  = '<div class="message is-danger">';
		                $message .= '  <p class="message-header">ERROR</p>';
		                $message .= '  <p class="message-body">Este Email não esta cadastrado no nosso Sistema! Verifique-o.</p>';
		                $message .= '</div>';
		                echo $message;
		              $error = true;
		            }

		            if(!$error) {
		              $passwortcode = random_string();
		              $url_passwortcode = getSiteURL().'recover_passwd&userid='.$user->idusr.'&code='.$passwortcode;
					  $statement = $pdo->prepare('UPDATE users SET pwdrec=:pwdrec, pwdtime = NOW() WHERE idusr=:idusr');
					  $result = $statement->execute(array('pwdrec' => sha1($passwortcode), 'idusr' => $user->idusr));

					  if(!$result){
					  	$message  = '<div class="message is-danger">';
		                $message .= '  <p class="message-header">ERROR</p>';
		                $message .= '  <p class="message-body">Ocorreu um erro no banco de dados, nao foi possível recuperar o usuário solicitatdo, tente novamente mais tarde.</font>!</p>';
		                $message .= '</div>';
		                echo $message;
					  }
					  else{

						  $Email = new PHPMailer();
						  $Email->SetLanguage("br");
						  $Email->CharSet = "UTF-8";
						  $Email->IsSMTP();
						  $Email->SMTPAuth = true;
						  //$Email->Mailer = 'smtp.gmail.com';
						  $Email->Host = MAILHOST; // 'smtp.gmail.com';
						  $Email->Port = MAILPORT; // '465';
						  $Email->Username = MAILUSER; // 'sebastiankopp.design@gmail.com';
						  $Email->Password = MAILPASS; // 'S3ba78I1nK0pp25';
						  $Email->SMTPSecure = 'ssl'; //tls
						  $Email->IsHTML(true);
						  $Email->From = 'Cah0s@mail.com';
						  $Email->FromName = 'Onlinesys';
						  $Email->Sender = "kkkkk";
						  $Email->AddReplyTo('contact@onlinesys.com', 'OnlineSys Security');
						  $Email->AddAddress($recover);
						  $Email->Subject = 'Recuperação de Senha do Sistema OnlineSys';
						  $Email->Body  = 'Olá '.$user->forname;
						  $Email->Body  = '<br><br>Foi pedido uma recuperação de senha para sua conta no OnlineSys.';
						  $Email->Body .= '<br>Para alterar a sua senha acesse dentro de no maximo 24 Horas o link abaixo ou copie e cole o mesmo no seu browser<br>';
						  $Email->Body .= '<a href='.$url_passwortcode.' target="_blank">'.$url_passwortcode.'</a>';
						  $Email->Body .= '<br><br>Caso não tenha pedido recuperação de senha apenas ignore este E-mail. Se por acaso não tenha feito Cadastro em nosso Sistema, contate o mais breve possivel nossa Administração para podermos averiguar o caso';
						  $Email->Body .= '<br><br>Com atenção, OnlineSys Team ';
						}
						  // verifica se está tudo ok com oa parametros acima, se nao, avisa do erro. Se sim, envia.
						  if(!$Email->Send()){
							$message  = '<div class="message is-danger">';
			                $message .= '  <p class="message-header">ERROR</p>';
			                $message .= '  <p class="message-body">Erro ao enviar! Houve um problema ao recuperar sua senha, contate o administrador.</p>';
			                $message .= '</div>';
			                echo $message;
			                echo '<span class="av msg"><b>Erro: </b>' . $Email->ErrorInfo.'</span>'; die();
						  }else{
							$message  = '<div class="message is-success">';
			                $message .= '  <p class="message-header">SUCCESS</p>';
			                $message .= '  <p class="message-body">Uma mensagem com as informações de acesso foi enviada para '.$recover.'.</p>';
			                $message .= '</div>';
			                echo $message;
						  }	
						
						
					  }
					 //  else{
						// echo 'Erro desconhecido';
					 //  }
		        }
		    }
		    ?>
		</div>
	</div>
