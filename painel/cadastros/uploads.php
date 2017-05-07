<?php
include '../../system/functions.php';
include '../../vendor/autoload.php';
logout($user_id, $_GET['user']);
if(!isset($user_id) && !isset($usuario)){
	header("Location: " .LOGIN);
}

?>
<!DOCTYPE html>
<html lang="pt" class="no-js">
<head>
	<meta>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<title>ImageUp - Panel</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/user.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/iziToast.css">
  <link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/linear-icons.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>/assets/css/pace-theme-flash.css">
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
    <?php
    $pdo=conectar();
    $user = $pdo->query("SELECT * FROM empresas JOIN users ON login='$usuario' WHERE user_cad='$usuario'");
    $verif = $user->rowCount();
    $empresa = $user->fetch(PDO::FETCH_OBJ)->empresa;
    ?>
      <form action="" id="myform" class="imgupform" method="post" enctype="multipart/form-data" >
        <?php
        if( $verif > 1 ):
          echo "<select name='mywork' id=''>";
              while ($row = $user->fetch(PDO::FETCH_OBJ)) {
                echo "<option value='$row->empresa'>$row->empresa</option>";
              }
          echo "</select>";
        else:
          echo "<input type='hidden' name='mywork' value='".$empresa."'>";
        endif;
        ?>
        <input type="file" name="pic[]" id="pic" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple>
        <label for="pic"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> <span>Choose a file&hellip;</span></label>

        <input type="submit" class="imgupbt" name="sendimg" value="sendimg">
      </form>

      <?php
      if(isset($_GET['imgdel']) && !empty($_GET['imgdel'])){
        $oldimg = $_GET['imgdel'];
        $imgdel = $pdo->query("SELECT fotos FROM empresas WHERE empresa='$empresa'");
        $dados = $imgdel->fetch(PDO::FETCH_OBJ);

        if($dados->fotos != 0 || $dados->fotos != ""):
          $foto_exp = explode(",", $dados->fotos);

          if(in_array($oldimg, $foto_exp)){
            $deleting = unlink("arquivos/newimg/$empresa/".$oldimg);
            ?>
            <script>
              iziToast.info({
                  title: 'DELETED',
                  message: '<?php echo "$oldimg deletada com sucesso"; ?>',
              });
            </script>
            <?php
            $replacing = str_replace($oldimg, "", $foto_exp);
            $new_fotos = implode(",", array_filter($replacing));
            $new_fotos = ltrim($new_fotos, ",");

            $upimg = $pdo->query("UPDATE empresas SET fotos='$new_fotos' WHERE empresa='$empresa'");
            if($upimg){
              ?>
              <script>
                iziToast.info({
                    title: 'SUCCESS',
                    message: '<?php echo "Imagens atualizadas"; ?>',
                });
              </script>
              <?php
            }
            else {
              ?>
              <script>
                iziToast.error({
                    title: 'ERROR',
                    message: '<?php echo "Houve um Erro"; ?>',
                });
              </script>
              <?php
            }
          }
          else{
            ?>
            <script>
              iziToast.warning({
                  title: 'OPS!',
                  message: '<?php echo "$oldimg deletada com sucesso"; ?>',
              });
            </script>
            <?php
          }
        else:
          echo "Nenhuma foto pôde ser tratada";
        endif;
      }
      ?>

      <div id="result"></div>
    </div>
  </div>

<footer>
    <span><font color="white">Copyright &copy 2016 - 0x{TheClone}</font></span>
</footer>

<script type="text/javascript" src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="<?php echo HOME;?>assets/js/iziToast.js"></script>
<script src="<?php echo HOME;?>assets/js/pace.min.js"></script>
<script src="<?php echo HOME;?>assets/js/jquery.form.js"></script>
<script src="<?php echo HOME;?>assets/js/usercheck.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/jquery.nicescroll.min.js"></script>
<script type="text/javascript" src="<?php echo HOME;?>assets/js/scroll.js"></script>
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
</body>
</html>