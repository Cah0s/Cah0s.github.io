<?php
include '../system/functions.php';
// if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
//   header("location: ../");
// }
$pdo=conectar();
// $lembrete = (isset($_COOKIE['CookieLembrete'])) ? base64_decode($_COOKIE['CookieLembrete']) : '';
// $checked = ($lembrete == 'SIM') ? 'checked' : '';
$_SESSION['_token'] = (!isset($_SESSION['_token'])) ? hash('sha256', rand(100,1000)) : $_SESSION['_token'];

if(isset($_SESSION['admin_id']) && isset($_SESSION["admin"])):
	header("Location: painel.php");
else:
?>
<!DOCTYPE html>
<html lang="pt">
<head>
	<meta>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<title>OnlineSys Panel</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/style.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/user.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/messages.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Lato|Neucha|Quicksand:300,700' rel='stylesheet' type='text/css'>
</head>
<body>

<div class="my_box my_box_form" style="margin-top:220px;">
    <div class="boxx">
      <form method="post">
        <div class="campo">
          <input type="text" name="login" placeholder="usuário" value="<?php  echo (isset($_REQUEST['login'])) ? $_REQUEST['login'] : NULL ; ?>" class="inputsl" autofocus>
        </div>
        
        <div class="campo">
          <input type="password" name="password" placeholder="password" class="inputsr">
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
        	$senha = ($_POST["password"] != "") ? hash("md5", trim($_POST["password"])) : NULL;

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
	          }
	          elseif(anti_injection($senha)){
	            echo anti_injection($senha);
	          }

	          $sql = $pdo->prepare("SELECT id, login, senha FROM painel WHERE login=:login AND senha=:password");
	          $sql->bindValue(':login', $login, PDO::PARAM_STR);
	          $sql->bindValue(':password', $senha, PDO::PARAM_STR);
	          $sql->execute();
	          $total = $sql->rowCount(PDO::FETCH_ASSOC);
	          $dados = $sql->fetch(PDO::FETCH_OBJ);

              if(!$error):
                $usr = $pdo->prepare("SELECT * FROM painel WHERE login=:login");
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

                if($senha != $dados->senha){
                  $message  = '<div class="message is-danger">';
                  $message .= '  <p class="message-header">INFO</p>';
                  $message .= '  <p class="message-body">Falsches passwort!</p>';
                  $message .= '</div>';
                  echo $message;
                  $error = true;
                  $log = true;
                }

                if($total){
                  $_SESSION["admin_id"]   = $dados->id;
                  $_SESSION["admin"]  = stripslashes($dados->login);
                  $message  = '<div class="message is-success">';
                  $message .= '  <p class="message-header">INFO</p>';
                  $message .= '  <p class="message-body">User <b>'.$_SESSION["admin"].'</b> eingeloggt, umleitung...</p>';
                  $message .= '</div>';
                  echo $message;
                  header("refresh: 1; url=painel.php");
                }
              endif;
        }
      ?>
    </div>
<?php endif; ?>
</div>

</body>
</html>