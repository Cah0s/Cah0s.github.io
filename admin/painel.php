<?php
require_once '../system/functions.php';
if(!isset($_SESSION['admin_id']) && !isset($_SESSION["admin"])){
	header("Location: index.php");
}
$list = $_GET['pag'];
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
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/linear-icons.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/pace-theme-flash.css">
    <link rel="stylesheet" href="<?php echo HOME; ?>assets/css/font-awesome.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Lato|Nova Mono|Neucha|Quicksand:300,700' rel='stylesheet' type='text/css'>
</head>
<body class="site_flex">

	<div class="site_item menu navbar">
      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>admin" title="Home"><i class="lnr lnr-home"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="firmen.php" title="Firmen listen"><i class="lnr lnr-bullhorn"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>" title="Go to Page" target="_blank"><i class="lnr lnr-earth"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="logs.php" title="Logs"><i class="lnr lnr-database"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="?user=logout" title="Exit painel"><i class="lnr lnr-exit"></i></a></li>    </div><!--/.navbar-collapse -->

	<div id="status"></div>

	<div class="wrapper_painel site-flex-content">	
		<div class="in_wrapper">
			<center>
				<br><h1 style="color:#2c3e50;">Lista de usuários</h1>
			</center>

			<form name="form_pesquisa" id="form_pesquisa" method="post" action="">
				<!-- <fieldset>
					<legend>Digite o nome a pesquisar</legend> -->
				<div class="input-prepend">
					<!-- <span class="add-on"><i class="lnr lnr-magnifier"></i></span> -->
					<input type="text" name="pesquisaCliente" class="pesquisaCliente" value="" tabindex="1" placeholder="Pesquisar cliente..." />
				</div>
				<!-- </fieldset> -->
			</form>
			<div id="contentLoading">
				<div id="loading"></div><br>
			</div>
			<section>
				<div id="MostraPesq"></div>
			</section>

			<div id="lista"></div>

			<?php
			if(!empty($_GET['id'])){
			    $id = $_GET['id'];
			    $sth2 = $pdo->prepare("SELECT * FROM users WHERE idusr=:id");
			    $sth2->bindValue(':id', $id, PDO::PARAM_INT);
			    $sth2->execute();
			    $data = $sth2->fetch(PDO::FETCH_OBJ);
			    $pwdrec = ($data->pwdrec == NULL) ? "<font color='red'>nothing</font>" : $data->pwdrec ;

			    echo "<fieldset class='listing'>";
			    echo "<h1 class='rouge'>$data->forname $data->lastname</h1>";
			    echo "
			    	<p class=conteudo>
			    		Creat :: ".strftime('%m/%d/%Y %H:%M:%S', $data->creat)."<br>
			    		Email :: $data->emailusr<br>
			    		Recover :: $pwdrec | $data->pwdtime<br>
			    		Last Update :: $data->upd<br>
			    	</p>";
			    echo "<p class=mais><a href='?edit=$data->login&uid=$data->idusr'><i class='icon icon-link'></i> editar</a> > user</p>";
			    echo "</fieldset>";
			}
			if(isset($_GET['del']) && isset($_GET['uid'])){
				del($_GET['uid'], 'users');
			}
			if(isset($_GET['edit'])){
				$uid = $_GET['uid'];
				$info = $pdo->query("SELECT * FROM users WHERE idusr='$uid'");
				$info->execute();
				$edit = $info->fetch(PDO::FETCH_OBJ);
			?>
	  	    <div class="my_box my_box_login">
			    <div class="boxx box_form">
			      <form method="post">
			        <div class="campo">
			          <input type="text" name="login" value="<?php echo $edit->login; ?>" class="inputsl" autofocus>
			        </div>
			        
			        <div class="campo">
			          <input type="text" name="email" value="<?php echo $edit->emailusr; ?>" class="inputsr">
			        </div>

			        <input type="submit" name="send" class="send" value="envia">
			      </form>
			    </div>

			    <div class="boxx box_msg">
			      <?php
			        $error = false;
			        if(isset($_POST['send'])){

			        	$login = ($_POST["login"] != "") ? addslashes(trim($_POST["login"])) : NULL;
			        	$email = ($_POST["email"] != "") ? addslashes(trim($_POST["email"])) : NULL;

				          if($login == ""){
				            $message  = '<div class="message is-warning">';
				            $message .= '  <p class="message-header">WARNING</p>';
				            $message .= '  <p class="message-body">Campo login precisa ser preenchido!</p>';
				            $message .= '</div>';
				            echo $message;
				            $error = true;
				          }

				          if($email == ""){
				            $message  = '<div class="message is-warning">';
				            $message .= '  <p class="message-header">WARNING</p>';
				            $message .= '  <p class="message-body">Campo email precisa ser preenchido!</p>';
				            $message .= '</div>';
				            echo $message;
				            $error = true;
				          }

				          if(anti_injection($login)){
				            echo anti_injection($login);
				            $log = true;
				          }
				          elseif(anti_injection($email)){
				            echo anti_injection($email);
				            $log = true;
				          }

				          if(!$error){
					          $sql = $pdo->prepare("UPDATE users SET login=:login, emailusr=:email WHERE idusr='$uid'");
					          $sql->bindValue(':login', $login, PDO::PARAM_STR);
					          $sql->bindValue(':email', $email, PDO::PARAM_STR);
					          $newuser = $sql->execute();

					          if($newuser) { echo "user data updated"; }
					      }
			        }
			      ?>
			    </div>
			</div>
			<?php
		    }
			?>
		</div>
	</div><!-- WRAPPER -->

<footer>
	<font color="white">Copyright &copy 2016 - 0x{TheClone}</font>
</footer>

<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="http://nickersoft.github.io/push.js/push.min.js"></script>
<script src="<?php echo HOME;?>assets/js/jquery.nicescroll.min.js"></script>
<script src="<?php echo HOME;?>assets/js/scroll.js"></script>
<script src="<?php echo HOME;?>assets/js/pace.min.js"></script>
<!-- <script src="assets/js/listing.js"></script> -->

<script type="text/javascript">
$(document).ready(function(){
	//Aqui a ativa a imagem de load
	function loading_show(){
		$('#loading').html("<img src='<?php echo HOME."assets/images/loader.gif";?>'>").fadeIn('low');
	}
	//Aqui desativa a imagem de loading
	function loading_hide(){
	    $('#loading').fadeOut('low');
	}       
	// aqui a função ajax que busca os dados em outra pagina do tipo html, não é json
	function load_dados(valores, page, div){
	    $.ajax ({
	        type: 'POST',
	        dataType: 'html',
	        url: page,
	        beforeSend: function(){//Chama o loading antes do carregamento
	              loading_show();
			},
	        data: valores,
	        success: function(msg){
	            loading_hide();
	            var data = msg;
		        $(div).html(data).fadeIn();				
	        }
	    });
	}
	//Aqui eu chamo o metodo de load pela primeira vez sem parametros para pode exibir todos
	//load_dados(null, 'pesquisa2.php', '#MostraPesq');

	//Aqui uso o evento key up para começar a pesquisar, se valor for maior q 0 ele faz a pesquisa
	$('.pesquisaCliente').keyup(function(){
	    var valores = $('#form_pesquisa').serialize(); //o serialize retorna uma string pronta para ser enviada
	    //pegando o valor do campo #pesquisaCliente
	    var $parametro = $(this).val();
	    
	    if($parametro.length >= 1){
	        load_dados(valores, 'search_itens.php', '#MostraPesq');
	    }
	    else{
	    	$("#MostraPesq").hide();
	        //load_dados(null, 'pesquisa2.php', '#MostraPesq');
	    }
	});

});
</script>

<script>
$(document).ready(function(){
	comecar();
});
var timerI = null;
var timerR = false;

function parar(){
	if(timerR)
		clearTimeout(timerI);
	timerR = false;
}
function comecar(){
	parar();
	listar();
}

function listar(){
		$.ajax({
		url:"status.php?pag=<?php echo $list; ?>",
			success: function (textStatus){
				$('#lista').html(textStatus);
			}
		});
		timerI = setTimeout("listar()", 1000);
	    timerR = true;
}
</script>

<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
	if (Notification.permission !== "granted"){
		Notification.requestPermission();
	}
});
</script> -->

</body>
</html>