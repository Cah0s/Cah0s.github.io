<?php
ini_set("display_errors", 1);
include '../system/functions.php';
logout($user_id, $_GET['user']);
if(!isset($user_id) && !isset($usuario)){
	header("Location: " .HOME."auth");
}
//$pdo=conectar();
$map = $pdo->query("SELECT * FROM empresas WHERE user_cad='$usuario'");
$coord = $map->fetch(PDO::FETCH_OBJ);

$latitude = $_SESSION['lat'];
$longitude = $_SESSION['lng'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
	<meta>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<title>Panel</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/style.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/user.css">
  <link rel="stylesheet" href="<?php echo HOME; ?>assets/css/iziToast.css">
  <link rel="stylesheet" href="<?php echo HOME; ?>assets/css/linear-icons.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/messages.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/font-awesome.css">
  	<link href='https://fonts.googleapis.com/css?family=Architects+Daughter|Kelly+Slab|Lato|Monoton|Neucha|Nova+Mono|Orbitron|Oswald|Quicksand:300,700' rel='stylesheet' type='text/css'>
</head>
<body class="site_flex">

  <div class="site_item menu navbar">
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>painel" title="Home"><i class="lnr lnr-home"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>painel/cadastros/form.php" title="Neue daten"><i class="lnr lnr-bullhorn"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>painel/cadastros/uploads.php" title="Firmen fotos upload"><i class="lnr lnr-picture"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>" title="Go to Page" target="_blank"><i class="lnr lnr-earth"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="?user=logout" title="Exit painel"><i class="lnr lnr-exit"></i></a></li>
  </div><!--/.navbar-collapse -->
  
  <div class="wrapper_painel site-flex-content">
    <div class="site_item contentcp">
    	<?php
        $lat = 0;
        $lng = 0;

        $url = "https://maps.google.com/maps/api/geocode/json?address=".str_replace(" ", "+", $coord->endereco);
        $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
          //curl_setopt($ch, CURLOPT_HEADER, 1);
          $check = curl_exec($ch);
          //$gethead = get_headers($url);

        $data = json_decode($check);
        //echo $data->status;
        if ($data->status=="OK") {
          $_SESSION['lat'] = $data->results[0]->geometry->location->lat;
          $_SESSION['lng'] = $data->results[0]->geometry->location->lng;
        }

        user_emp_read($usuario, 'empresas', 'users');

      ?>
    </div>
  </div>

<footer>
    <span><font color="white">Copyright &copy 2016 - 0x{TheClone}</font></span>
</footer>

<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="<?php echo HOME;?>assets/js/iziToast.js"></script>
<script type="text/javascript" src="http://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
<script src="<?php echo HOME; ?>assets/js/jquery.form.js"></script>
<script src="<?php echo HOME; ?>assets/js/formmask.js"></script>
<script src="<?php echo HOME; ?>assets/js/usercheck.js"></script>
<script src="<?php echo HOME; ?>assets/js/jquery.nicescroll.min.js"></script>
<script src="<?php echo HOME; ?>assets/js/scroll.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRYZkHMdA9XGNv_Xkc33YvTpJBPg6ry_s"></script>
<script>
function initMap() {
    var mapOptions = {
        zoom: 14,
        center: new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>), // New York
        styles: [{"elementType":"geometry","stylers":[{"hue":"#ff4400"},{"saturation":-68},{"lightness":-4},{"gamma":0.72}]},{"featureType":"road","elementType":"labels.icon"},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"hue":"#0077ff"},{"gamma":3.1}]},{"featureType":"water","stylers":[{"hue":"#00ccff"},{"gamma":0.44},{"saturation":-33}]},{"featureType":"poi.park","stylers":[{"hue":"#44ff00"},{"saturation":-23}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"hue":"#007fff"},{"gamma":0.77},{"saturation":65},{"lightness":99}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"gamma":0.11},{"weight":5.6},{"saturation":99},{"hue":"#0091ff"},{"lightness":-86}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"lightness":-48},{"hue":"#ff5e00"},{"gamma":1.2},{"saturation":-23}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"saturation":-64},{"hue":"#ff9100"},{"lightness":16},{"gamma":0.47},{"weight":2.7}]}],
        //mapTypeId: google.maps.MapTypeId.ROADMAP
        //mapTypeId: google.maps.MapTypeId.SATELLITE
    };

    var mapElement = document.getElementById('map');
    var map = new google.maps.Map(mapElement, mapOptions);

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>),
        map: map,
        title: ''
    });
}
google.maps.event.addDomListener(window, 'load', initMap);
</script>
<script>
//Abrindo função principal empresas
$(function(){

  var atual_fs, next_fs, prev_fs;
  var formulario = $('form[name=updateEmpresa]');

    // Funções para seguir Passos
    function next(elem){
      atual_fs = $(elem).parent();
      next_fs = $(elem).parent().next();

      $('#progress li').eq($('fieldset').index(next_fs)).addClass('ativo');
      atual_fs.hide(500);
      next_fs.show(500);
    } // PROXIMO

  // Funções para Voltar um Passo
  $('.prev').click(function(){
      atual_fs = $(this).parent();
      prev_fs = $(this).parent().prev();

      $('#progress li').eq($('fieldset').index(atual_fs)).removeClass('ativo');
      atual_fs.hide(500);
      prev_fs.show(500);
  }); // VOLTAR

  $('form[name=updateEmpresa] input[name=next1]').click(function(){
    var array = formulario.serializeArray();
    if(array[0].value === ''){
      $('.respemp').html('<div class="erros"><i class="fa fa-warning"></i>&nbsp; Informe o nome da Empresa</div>');
    }
    else if(array[1].value === ''){
      $('.respemp').html('<div class="erros"><i class="fa fa-envelope"></i>&nbsp;Precisamos de um Email!</div>');
    }
    else if(array[2].value === ''){
      $('.respemp').html('<div class="erros"><i class="fa fa-phone"></i>&nbsp;Preencha o campo Telefone!</div>');
    }
    else{
      $('.respemp').html('');
      next($(this));
    }
  }); // NEXT1

  $('form[name=updateEmpresa] input[name=next2]').click(function(){
    var array = formulario.serializeArray();
    if(array[3].value === ''){
      $('.respemp').html('<div class="erros"><i class="fa fa-map-marker"></i> Onde fica a Empresa?</div>');
    }
    else if(array[4].value === ''){
      $('.respemp').html('<div class="erros"><i class="fa fa-map-commenting-o"></i> Fale um pouco sobre a Empresa</div>');
    }
    else if(array[5].value === ''){
      $('.respemp').html('<div class="erros"><i class="fa fa-map-link"></i> Ha website?</div>');
    }
    else{
      $('.respemp').html('');
      next($(this));
    }
  }); // NEXT2

  $('form[name=updateEmpresa] input[type=submit]').click(function(evento){
    var array = formulario.serializeArray();
    $('.respemp').html('<div class="ok">Completo!</div>');
    
    $.ajax({
       method: 'post',
       url: 'updateemp.php',
       data: {update: 'sim', campos: array},
       dataType: 'json',
       beforeSend: function(){
         $('.respemp').html('<div class="verf"><p>Aguarde a Verificação ...</p></div>');

       },
       success: function(valor){
         if(valor.erro == 'sim'){
           $('.respemp').html('<div class="erros"><i class="fa fa-cross"></i> '+valor.getErro+'</div>');
         }else{
           $('.respemp').html('<div class="ok">'+valor.msg+'</div>');
           console.log(array);
         }
       }
    }); // FIM AJAX

    evento.preventDefault();
  }); // FIM FUNCTION SUBMIT

}); // FIM FUNCTION
</script>
</body>
</html>