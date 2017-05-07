<?php
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
$pdo=conectar();
$usuario = (!isset($_SESSION['userlog']) ? 'Guest' : $_SESSION['userlog']);
$addr    = $_SERVER['REMOTE_ADDR'];
// $lembrete = (isset($_COOKIE['CookieLembrete'])) ? base64_decode($_COOKIE['CookieLembrete']) : '';
// $checked = ($lembrete == 'SIM') ? 'checked' : '';

if(isset($_SESSION['user_id']) && isset($_SESSION["usuario"])):
	header("Location: painel/userhome.php");
else:
?>
	<div class="content">

		<div class="contentbanner">
			<div class="adversiment bann--728x90_inn" >
				<div class="adv_content">
					<img src="http://www.amtekcompany.com/wp-content/uploads/2015/11/3d-scanning-banner1.jpg" alt="">
				</div>
				<!-- <div class="adv_footer"></div> -->
			</div>
		</div>

		<div class="my_box my_box_form_login">
		<?php if(checkloginlog() == "block"): ?>
		
			<div class="message is-info">
              <p class="message-header"> AVISO</p>
              <p class="message-body"><?php echo "<b><font color='#2c3e50'>".$usuario."</font></b>"; ?> você foi bloqueado pois foi detectado um possivel ataque de Brute forçe, caso seja apenas erros de credenciais contate a administração o mais breve possivel ou auarde <font color="black"><b><?php echo $_SESSION['time']; ?> min</b></font> para auto desbloqueio de seu usuário.</p>
            </div>

		<?php else: ?>
			
		    <div class="boxx">
		      <form method="post">
		        <div class="campo">
		          <input type="text" name="login" placeholder="usuário" value="<?php  echo (isset($_REQUEST['login'])) ? $_REQUEST['login'] : NULL ; ?>" class="inputsl" autofocus>
		        </div>
		        
		        <div class="campo">
		          <input type="password" name="password" placeholder="password" class="inputsr">
		        </div>


		        <div class="check">
		            <!-- <span class="check_sep">
		              <input type="checkbox" name="lembrete" value="SIM" <?php $checked ?>> Lembre-me
		            </span> -->
		            <span class="check_sep links"><a href="<?php echo HOME;?>recover">lost password?</a> | <a href="<?php echo HOME;?>register">registrar</a></span>
		        </div>

		        <input type="hidden" name="hash" value="<?php echo $_SESSION['_token'] ?>">
		        <input type="submit" name="send" class="send" value="envia">
		      </form>
		    </div>

		    <div class="boxx box_msg">
		      <?php
		        $error = false;
		        if(isset($_POST['send'])){

		        	if($_POST['hash'] != $_SESSION['_token']){
						echo "Erro de Token<br>Hash: {$_POST['hash']}<br>Session: {$_SESSION['_token']}";
						$error = true;
					}

		        	$login = ($_POST["login"] != "") ? addslashes(trim($_POST["login"])) : NULL;
		        	$senha = ($_POST["password"] != "") ? hash("sha1", trim($_POST["password"])) : NULL;
		        	$_SESSION['userlog'] = $login;

			          if($login == ""){
			            $message  = '<div class="message is-warning">';
			            $message .= '  <p class="message-header">WARNING</p>';
			            $message .= '  <p class="message-body">Login feld muss ausgefüllt werden!</p>';
			            $message .= '</div>';
			            echo $message;
			            $error = true;
			          }
			          if($senha == ""){
			            $message  = '<div class="message is-warning">';
			            $message .= '  <p class="message-header">WARNING</p>';
			            $message .= '  <p class="message-body">Password feld muss ausgefüllt werden!</p>';
			            $message .= '</div>';
			            echo $message;
			            $error = true;
			          }

			          if(anti_injection($login)){
			            echo anti_injection($login);
			            $log = true;
			          }
			          elseif(anti_injection($senha)){
			            echo anti_injection($senha);
			            $log = true;
			          }

			          $sql = $pdo->prepare("SELECT idusr, login, password, status FROM users WHERE login=:login AND password=:password");
			          $sql->bindValue(':login', $login, PDO::PARAM_STR);
			          $sql->bindValue(':password', $senha, PDO::PARAM_STR);
			          $sql->execute();
			          $total = $sql->rowCount(PDO::FETCH_ASSOC);
			          $dados = $sql->fetch(PDO::FETCH_OBJ);

			          if($dados->status == '0'){
			              $message  = '<div class="message is-info">';
			              $message .= '  <p class="message-header">INFO</p>';
			              $message .= '  <p class="message-body">Benutzer noch nicht aktiviert, überprüfen sie ihre email oder beantragen sie einen neuen aktiviergungs link <a href="newkey.php">hier</a></p>';
			              $message .= '</div>';
			              echo $message;
			              $error = true;
			              $log = true;
			          }
			          elseif($dados->status == '2'){
			              $message  = '<div class="message is-danger">';
			              $message .= '  <p class="message-header">INFO</p>';
			              $message .= '  <p class="message-body">Blockierter Benutzer, bitte kontaktiere die Administration!</p>';
			              $message .= '</div>';
			              echo $message;
			              $error = true;
			              $log = true;
			          }
			          else {
			              if(!$error):
			                $usr = $pdo->prepare("SELECT * FROM users WHERE login=:login");
			                $usr->bindValue(':login', $login, PDO::PARAM_STR);
			                $usr->execute();
			                $user = $usr->fetch();

			                if($user === false) {
			                  $message  = '<div class="message is-danger">';
			                  $message .= '  <p class="message-header">INFO</p>';
			                  $message .= '  <p class="message-body">Dieser User existiert nicht!</p>';
			                  $message .= '</div>';
			                  echo $message;
			                  $error = true;
			                  $log = true;
			                }

			                if($senha != $dados->password){
			                  $message  = '<div class="message is-danger">';
			                  $message .= '  <p class="message-header">INFO</p>';
			                  $message .= '  <p class="message-body">Falsches passwort!</p>';
			                  $message .= '</div>';
			                  echo $message;
			                  $error = true;
			                  $log = true;
			                }

			                if($total){
			                  $upd = $pdo->query("UPDATE users SET active='online', ontime=NOW(), addr='$addr' WHERE login='$login'");

			                  // $lembrete = (isset($_POST['lembrete'])) ? $_POST['lembrete'] : '';
			                  // if($lembrete == 'SIM'):
			                  //    $expira = time() + 60*60*24*30; // 30 dias
			                  //    setCookie('CookieLembrete', base64_encode('SIM'), $expira);
			                  // endif;

			                  $_SESSION["user_id"]   = $dados->idusr;
			                  $_SESSION["usuario"]  = stripslashes($dados->login);
			                  $message  = '<div class="message is-success">';
			                  $message .= '  <p class="message-header">INFO</p>';
			                  $message .= '  <p class="message-body">User <b>'.$_SESSION["usuario"].'</b> eingeloggt, umleitung...</p>';
			                  $message .= '</div>';
			                  echo $message;
			                  header("refresh: 1; url=".HOME."painel/userhome.php");
			                }
			              endif;
			            }
			            if($log) { loginlog($usuario, $addr); }
		        }
		      ?>
		    </div>
		<?php endif; ?>
		</div>

	</div><!-- CONTENT -->

	<div class="adversing">
		
		<div class="adversiment bann--300x600">
			<div class="adv_header">
				<span class="title">Adversiment</span>
			</div>
			
			<div class="adv_content">
				<center>
					<img src="<?php echo HOME;?>assets/images/OauthLoginBanner.gif" alt="">
				</center>
			</div>
			<div class="adv_footer"></div>
		</div>
		
		<!-- <div class="sub_adversing">
			kkkk
		</div> -->

	</div>

	<div class="adversiment bann--1000x130 contentbannerbig">
		<div class="adv_content">
			<img src="<?php echo HOME;?>assets/images/wallbanner_white.gif" alt="">
		</div>
		<!-- <div class="adv_footer"></div> -->
	</div>
<?php endif; ?>