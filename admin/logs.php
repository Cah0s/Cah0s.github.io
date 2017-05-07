<?php
(!isset($_SESSION) ? session_start() : FALSE);
require_once '../system/functions.php';
$pdo=conectar();
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
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/linear-icons.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/pace-theme-flash.css">
    <link rel="stylesheet" href="<?php echo HOME; ?>assets/css/font-awesome.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Lato|Neucha|Quicksand:300,700' rel='stylesheet' type='text/css'>
</head>
<body class="site_flex">

		<div class="site_item menu navbar">
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>admin" title="Home"><i class="lnr lnr-home"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="firmen.php" title="Firmen listen"><i class="lnr lnr-bullhorn"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>" title="Go to Page" target="_blank"><i class="lnr lnr-earth"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="logs.php" title="Logs"><i class="lnr lnr-database"></i></a></li>
	      <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="?user=logout" title="Exit painel"><i class="lnr lnr-exit"></i></a></li>
	    </div><!--/.navbar-collapse -->

	<div class="wrapper_painel site-flex-content">	
		<div class="in_wrapper">
			<center>
				<h1 style="color:#2c3e50;">Lista de empresas</h1>
			</center>

			<form name="form_pesquisa" id="form_pesquisa" method="post" action="">
				<!-- <fieldset>
					<legend>Digite o nome a pesquisar</legend> -->
				<div class="input-prepend">
					<!-- <span class="add-on"><i class="lnr lnr-magnifier"></i></span> -->
					<input type="text" name="pesquisaEmpresa" class="pesquisaCliente" value="" tabindex="1" placeholder="Pesquisar empresa..." />
				</div>
				<!-- </fieldset> -->
			</form>
			<div id="contentLoading">
				<div id="loading"></div><br>
			</div>
			<section>
				<div id="MostraPesq"></div>
			</section>

			<?php
			if(isset($_GET['logs'])){
			    $id = $_GET['logs'];
			    $sth = $pdo->query("SELECT * FROM injectionlogs");
			    $sth->execute();
			    //$data = $sth->fetch(PDO::FETCH_OBJ);

			    while ($data = $sth->fetch(PDO::FETCH_OBJ)) {
			    	echo "<b>";
			    	echo $data->addr."<br>";
			    	echo $data->url."<br>";
			    	echo $data->user."<br>";
			    	echo $data->injected."<br>";
			    	echo $data->data."<br><br>";
			    	echo "</b>";
			    }
			}
			?>

		</div>
	</div>

<footer>
	<font color="white">Copyright &copy 2016 - 0x{TheClone}</font>
</footer>

<script type="text/javascript" src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="http://nickersoft.github.io/push.js/push.min.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/jquery.nicescroll.min.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/scroll.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/pace.min.js"></script>

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
	        load_dados(valores, 'search_empresas.php', '#MostraPesq');
	    }
	    else{
	    	$("#MostraPesq").hide();
	        //load_dados(null, 'pesquisa2.php', '#MostraPesq');
	    }
	});

});
</script>
</body>
</html>
</body>
</html>