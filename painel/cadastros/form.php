<?php
require '../../system/functions.php';
if(!isset($user_id) && !isset($usuario)){
  header("Location: " .HOME."auth");
}
logout($user_id, $_GET['user']);
$work = $pdo->query("SELECT empresa FROM empresas WHERE user_cad='$usuario'");
$workempresa = $work->fetch(PDO::FETCH_OBJ)->empresa;
?>

<!DOCTYPE html>
<html lang="pt" class="no-js">
<head>
  <meta>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
  <title>Cadastrar Empresa</title>
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/user.css">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/iziToast.css">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/linear-icons.css">
  <!-- <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/pace-theme-flash.css"> -->
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/messages.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/font-awesome.css">
    <link href='https://fonts.googleapis.com/css?family=Architects+Daughter|Kelly+Slab|Lato|Monoton|Neucha|Nova+Mono|Orbitron|Oswald|Quicksand:300,700' rel='stylesheet' type='text/css'>

    <!-- remove this if you use Modernizr -->
  <script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script>

</head>
<body class="site_flex">

  <?php //if(is_checked_in()): ?>  
  <div class="site_item menu navbar">
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>painel"><i class="lnr lnr-home"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>painel/cadastros/form.php"><i class="lnr lnr-bullhorn"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>painel/cadastros/uploads.php"><i class="lnr lnr-picture"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="<?php echo HOME; ?>" title="Go to Page" target="_blank"><i class="lnr lnr-earth"></i></a></li>
    <li class="nav_item"><a class="hvr-shutter-out-horizontal" href="?user=logout" title="Exit painel"><i class="lnr lnr-exit"></i></a></li>
  </div><!--/.navbar-collapse -->
  <?php //endif; ?>
  
  <div class="wrapper_painel site-flex-content">
    <div class="site_item contentcp">
      
      <div id="ud_tab"> 
        <input type="radio" id="tab1" name="ud_tabs" checked>
        <label for="tab1">COMPANY</label>

        <input type="radio" id="tab2" name="ud_tabs">
        <label for="tab2">JOB</label>


        <div id="ud_tab-content1" class="ud_content">
          <h3>COMPANY FORM</h3>
          <div class="register_box">
            <?php include 'empform.php'; ?>
          </div>
        </div>
        
        <div id="ud_tab-content2" class="ud_content">
          <h3>JOB FORM</h3>
          
          <div class="register_box">
             <?php include 'vagaform.php'; ?>
          </div>
        </div>
      </div>

    </div>
  </div>

<footer>
    <span><font color="white">Copyright &copy 2016 - 0x{TheClone}</font></span>
</footer>

<script type="text/javascript" src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="<?php echo HOME;?>assets/js/iziToast.js"></script>
<!-- <script src="<?php echo HOME;?>assets/js/pace.min.js"></script> -->
<script type="text/javascript" src="<?php echo HOME;?>assets/js/jquery.nicescroll.min.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/scroll.js"></script>
<script type="text/javascript" src="http://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
<script src="<?php echo HOME; ?>assets/js/jquery.form.js"></script>
<script src="<?php echo HOME; ?>assets/js/formmask.js"></script>
<script src="<?php echo HOME;?>assets/js/usercheck.js"></script>
<script src="<?php echo HOME;?>assets/js/input-file.js"></script>
<script>
$(document).ready(function () {
  $.ajax({
    type: "POST",
    url: 'post_file.php',
    data: {acao: "imgcheck"},
    error: function(){
      $('#result').html('erro').show();
      console.log('error');
    },
    success:   function( resposta ){
      $('#result').html(resposta).show();
      console.log(resposta);
    }
  });
});

$('#myform').on('submit', function(e) {
  e.preventDefault();
  var $this = $(this);
  
  $(this).ajaxSubmit({
        url: 'post_file.php',
        data: {acao: "newimg"},
        beforeSubmit: function(){
          $('#result').html('loading').show();
          //console.log('loading');
        },
        error: function(){
          $('#result').html('erro').show();
          //console.log('error');
        },
        success:   function( resposta ){
          $('#result').html(resposta).show();
          //console.log(resposta);
        }
  });
});
</script>
<script>
$(function() {
    $('.j_loadstate').change(function() {
        var uf = $('.j_loadstate');
        var city = $('.j_loadcity');
        var patch = 'city.php';

        city.attr('disabled', 'true');
        uf.attr('disabled', 'true');

        city.html('<option value=""> Carregando cidades... </option>');

        $.post(patch, {estado: $(this).val()}, function(cityes) {
            city.html(cityes).removeAttr('disabled');
            uf.removeAttr('disabled');
        });
    });
});

//Abrindo função principal empresas
$(function(){
  var empform  = $('form[name=empform]');
  var workform = $('form[name=workform]');

  $('form[name=empform] input[type=submit]').click(function(evento){
    var array = empform.serializeArray();
    $('.respemp').html('<div class="ok">Completo!</div>');
    
    $.ajax({
       method: 'post',
       url: 'cademp.php',
       data: {cadastrar: 'sim', campos: array},
       dataType: 'json',
       beforeSend: function(){
         $('.respemp').html('<div class="verf"><p>Aguarde a Verificação</p></div>');
         /* DEBUG */
         console.log(array);
       },
       success: function(valor){
         if(valor.erro == 'sim'){
           $('.respemp').html('<div class="erros"><i class="fa fa-cross"></i> '+valor.getErro+'</div>');
         }else{
           $('.respemp').html('<div class="ok">'+valor.msg+'</div>');
         }
       }
    }); // FIM AJAX
    evento.preventDefault();
  }); // FIM FUNCTION SUBMIT

  /* WORK FORM */
  $('form[name=workform] input[type=submit]').click(function(evento){
    var array = workform.serializeArray();
    $('.respwork').html('<div class="ok">Completo!</div>');
  
    $.ajax({
       method: 'post',
       url: 'cadwork.php',
       data: {newwork: 'sim', campos: array},
       dataType: 'json',
       beforeSend: function(){
         $('.respwork').html('<div class="verf"><p>Aguarde a Verificação</p></div>');
         console.log(array);

       },
       success: function(valor){
         if(valor.erro == 'sim'){
           $('.respwork').html('<div class="erros"><i class="fa fa-cross"></i> '+valor.getErro+'</div>');
         }else{
           $('.respwork').html('<div class="ok">'+valor.msg+'</div>');
           console.log(valor.msg);
         }
       }
    }); // FIM AJAX
    evento.preventDefault();
  //}
  }); // FIM FUNCTION SUBMIT
}); // FIM FUNCTION

idleTimer = null;
idleState = false;
//var time = 1 * 60; // 10 minutos para timeout
(function ($) {
    $(document).ready(function () {
        $('*').bind('mousemove keydown scroll', function () {
            var HOME = 'http://localhost/meineprojekte/teste/';
            clearTimeout(idleTimer);
            if (idleState === true) {
                // Reactivated event
                $.ajax({ url: HOME + 'inc/sesscheck.php?active' });
            }
            idleState = false;
            idleTimer = setTimeout(function () {
                // Idle Event
                idleState = true;
                $.ajax({ url: HOME + 'inc/sesscheck.php?inactive' });
            }, 5000);

            idleTimer = setTimeout(function () {
                // Idle Event
                idleState = true;
                $.ajax({ url: HOME + 'inc/sesscheck.php?outside' });
            }, 10000);
        });
        $("body").trigger("mousemove");
    });
}) (jQuery);
</script>
</body>
</html>
<!--
MODIS IT Outsourcing GmbH

info@modis.de

49 361 60660600

Parsevalstrasse 8-10, 99092 Erfurt



Die Modis IT Outsourcing GmbH ist ein international tätiges Unternehmen der Adecco Gruppe mit der Fähigkeit, personalintensive IT Prozesse für unsere Kunden aus über 180 Ländern umzusetzen. 

Seit 1996 erbringen wir erfolgreich Service Desk Dienstleistungen aus Deutschland. Die Modis IT Outsourcing ist zum 1. Oktober 2016 aus dem Service Desk Bereich eines der weltweit größten IT Unternehmen hervorgegangen. 

Die Modis IT Outsourcing GmbH bietet herstellerübergreifende Supportdienstleistungen für Kunden aus verschiedensten Branchen durch individuell abgestimmte Lösungen, in unterschiedlichen Supporttiefen, Sprachen und Zusatzmodulen an. Wir unterstützen Endanwender von mittelständischen Unternehmen in Deutschland bis hin zu multinationalen, weltweit operierenden Unternehmen. 

Besonderen Wert legen wir dabei auf den kontinuierlichen Dialog mit unseren Kunden, um unseren Service permanent auf deren Bedürfnisse auszurichten und somit die Kundenzufriedenheit auf hohem Niveau zu gewährleisten.



http://www.modis.de

https://www.facebook.com/ModisFrance


https://hastebin.com/voqiqulonu.xml php que processa
https://hastebin.com/otemowixog.xml o formulário
https://hastebin.com/bidofegeze.js a função ajax
-->