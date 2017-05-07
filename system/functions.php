<?php
include 'database.php';
accesslogs($addr, 'logs', $_SERVER['HTTP_USER_AGENT'], getUrl());
blockcheck($user_id);
logout($user_id, $_GET['user']);
logout_admin($admin_id, $_GET['user']);
$match = "/(from|select|insert|delete|where|drop table|show tables|order by|group|union|all|#|--|0x|'|\"|´|`|0x27|%27|%00|%3C|%3E|&lt|&gt|marquee|scr|IMG|svg|onload|alert|uname|ls|dir|tree)/i";
//include 'anti_injection.php';
//$_SESSION['_token'] = (!isset($_SESSION['_token'])) ? hash('sha256', rand(100,1000)) : $_SESSION['_token'];

function setHome()  { echo HOME; }
function setHeader(){ require_once('tpl/header.inc.php'); }
function setFooter(){ require_once('tpl/footer.inc.php'); }
//function setModal() { echo BASE.'?fechar=true'; }

function getGet( $key ){
  return isset( $_GET[ $key ] ) ? $_GET[ $key ] : null;
}
  
$pg = getGet('page');
switch($pg){
	/* user */
  case 'auth':
		$title = 'User Login - ';
		break;
  case 'register':
    $title = 'New User - ';
    break;
  case 'recover':
    $title = 'Recover Password - ';
    break;
  case 'recover_passwd':
    $title = 'New Password - ';
    break;
  case 'activate_user':
    $title = 'User Activation - ';
    break;
  case 'data/advanced_search':
      $title = 'Advanced Search - ';
      break;
  case 'show':
      $table = preg_replace($match, "", $_GET['show']);
      $name  = preg_replace($match, "", $_GET['uid']);
      if(!isset($_GET['show']) && !isset($_GET['uid'])){ header("location: index.php"); }
      if(isset($table) && isset($name)):
        $titulo = $pdo->query("SELECT * FROM $table WHERE id='$name'");
        $dataemp = $titulo->fetch(PDO::FETCH_OBJ);
        $title = ($dataemp->visivel == "sim") ? "$dataemp->empresa - " : "Nothing Found - ";
          if($dataemp->categoria == "Mercado"){
            $keys = $dataemp->categoria. ", einkaufen, kaufen, produkte";
          }
          elseif($dataemp->categoria == "Immobiliária"){
            $keys = $dataemp->categoria. ", haus verkauf, vermietung";
          }
          elseif($dataemp->categoria == "ISP"){
            $keys = $dataemp->categoria. ", internet service provider, internet, web";
          }

        $keywords = "$dataemp->empresa, $keys";
        $description = $dataemp->descricao;
        if($title == "Nothing Found - "){
          header("location: index.php");
        }else{}
      else:
        $title = 'Listning - ';
      endif;
      break;
  case 'work':
      $table = preg_replace($match, "", $_GET['show']);
      $name  = preg_replace($match, "", $_GET['uid']);
      if(!isset($_GET['show']) && !isset($_GET['uid'])){ header("location: index.php"); }
      if(isset($table) && isset($name)):
        $titulo = $pdo->query("SELECT * FROM $table WHERE id='$name'");
        $dataemp = $titulo->fetch(PDO::FETCH_OBJ);
        $title = ($dataemp->visible == "sim") ? "$dataemp->work - " : "Nothing Found - ";
          if($dataemp->cat == "Mercado"){
            $keys = $dataemp->cat. ", einkaufen, kaufen, produkte";
          }
          elseif($dataemp->cat == "Immobiliária"){
            $keys = $dataemp->cat. ", haus verkauf, vermietung";
          }
          elseif($dataemp->cat == "ISP"){
            $keys = $dataemp->cat. ", internet service provider, internet, web";
          }
          elseif($dataemp->cat == "Hotel"){
            $keys = $dataemp->cat. ", Férias, Viajar, Conforto";
          }

        $keywords = "$dataemp->company, $dataemp->work, $keys";
        $description = $dataemp->descricao;
        if($title == "Nothing Found - "){
          header("location: index.php");
        }else{}
      else:
        $title = 'Listning - ';
      endif;
      break;
  case 'jobs':
    //if(!isset($_GET['show'])){ header("location: index.php"); }
    $title = 'jobsuche - ';
    break;
   default:
      $title = 'Hauptsite - ';
}

//FUNÇÃO LFI (LOCAL FILE INCLUSION[FILTERED])
//FUNÇÃO RFI (REMOTE FILE INCLUSION[FILTERED])
function getHome() {
		$pg = (isset($_GET['page']) ? $_GET['page'] : NULL);

		$match = "/(from|select|insert|delete|where|drop table|show tables|order by|group|union|all|#|--|0x|'|\"|´|`|0x27|%27|%00)/i";
		$sql = preg_match($match, $pg);
		$pg = preg_replace($match, "", $pg);
		$pg = urldecode(htmlspecialchars(addslashes(trim($pg))));
		$rfi = preg_match("#http|ftp|https|www|wget|exe|system|exec|cat|php://filter|%00#i", $pg);
		$xss = preg_match("#%3C|%3E|&lt|&gt|marquee|scr|IMG|svg|onload|alert#i", $pg);
		$rce = preg_match("#uname|ls|dir|tree#i", $pg);

		if($pg{0} == '.' || $pg{1} == '.' || $pg{2} == '/'):
			echo '<div class="error"><img src="'.HOME.'/assets/images/404.svg"><br><br>Wrong file! Ta tentando LFI né Safado, mas aqui não cola não  mané</div>';
		elseif($rfi):
			echo '<div class="error"><img src="'.HOME.'/assets/images/404.svg"><br><br>Sério que você Ta tentando RFI? Lammerzinho n00b. hahaha</div>';
		elseif($xss):
			echo '<div class="error"><img src="'.HOME.'/assets/images/404.svg"><br><br>Lammer, sai desse XSS cuzão</div>';
		elseif($rce):
			echo '<div class="error"><img src="'.HOME.'/assets/images/404.svg"><br><br>Que feio, tentando RCE cuzão</div>';
		elseif($sql):
			echo '<div class="error"><img src="'.HOME.'/assets/images/404.svg"><br><br>Hahaha SQL, aqui não cuzão</div>';

		else:
			$pg = explode('/', $pg);
			$pg[0] = ($pg[0] == NULL ? 'home' : $pg[0]);
			$pg[1] = ( empty($pg[1]) ? null : $pg[1]);
			$pg[2] = ( empty($pg[2]) ? null : $pg[2]);

			   if(file_exists('tpl/'.$pg[0].'.php')){
			   	 require_once('tpl/'.$pg[0].'.php');
			   }
         elseif(file_exists('painel/'.$pg[0].'.php')){
           require_once('painel/'.$pg[0].'.php');
         }
         elseif(file_exists('tpl/'.$pg[0].'/'.$pg[1].'.php')){
			   	 require_once('tpl/'.$pg[0].'/'.$pg[1].'.php');
			   }
         elseif(file_exists('tpl/'.$pg[0].'/'.$pg[1].'/'.$pg[2].'.php')){
			   	 $secure = require_once('tpl/'.$pg[0].'/'.$pg[1].'/'.$pg[2].'.php');
			   }
         else{
			   	 require_once('tpl/error.php');
			   }
		endif;
}

function time_ago($time) {
   $diff = time() - $time;
   $seconds = $diff;
   $minutes = (int)($diff / 60);
   $hours = (int)($diff / 3600);
   $days = (int)($diff / 86400);
   $weeks = (int)($diff / 604800);
   $months = (int)($diff / 2419200);
   $years = (int)($diff / 29030400);

   if ($seconds <= 60) $time = "há $seconds s";
   else if ($minutes <= 60) $time =  $minutes == 1 ? 'há 1 m' : 'há '.$minutes.' min';
   else if ($hours <= 24) $time = $hours == 1 ? 'há 1 h' : 'há '.$hours.' hrs';
   else if ($days <= 7) $time = $days == 1 ? 'há 1 d' : 'há '.$days.' d';
   else if ($weeks <= 4) $time = $weeks == 1 ? 'há 1 w' : 'há '.$weeks.' w';
   else if ($months <= 12) $time = $months == 1 ? 'há 1 m' : 'há '.$months.' m';
   else $time = $years == 1 ? 'há 1 y' : 'há '.$years.' y';
   return $time;
}

/*
-------------------------------------------
-- FUNÇÃO DE UMA STRING RANDÔMICA PARA CAD/ACTV
-----------------------------------------------
*/
function random_string() {
	if(function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes(16);
		$str = bin2hex($bytes); 
	} else if(function_exists('mcrypt_create_iv')) {
		$bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
		$str = bin2hex($bytes); 
	} else {
		//Replace your_secret_string with a string of your choice (>12 characters)
		//$str = md5(uniqid('Cah0s', true));
		$str = hash("sha512", uniqid('Cah0s', true));
	}
	//$str = hash("sha256", uniqid('Cah0s', true));	
	return $str;
}

/*
-------------------------------------------
-- FUNÇÃO PARA VERIFICAR O STATUS DO USUÁRIO
-----------------------------------------------
*/
function atividade_user($status){
	if($status == "online"){ $stat = "Online <span class='trigger online status'></span>"; }
  elseif($status == "ausente"){ $stat = "Ausente <span class='trigger inactive status'></span>"; }
	elseif($status == "outside"){ $stat = "Outside <span class='trigger outside status'></span>"; }
	else { $stat = "Offline <span class='trigger offline status'></span>"; }
	return $stat;
}

function limitarTexto($texto, $limite, $quebrar = true){
  //corta as tags do texto para evitar corte errado
  $contador = strlen(strip_tags($texto));
  if($contador <= $limite):
    //se o número do texto form menor ou igual o limite então retorna ele mesmo
    $newtext = $texto;
  else:
    if($quebrar == true): //se for maior e $quebrar for true
      //corta o texto no limite indicado e retira o ultimo espaço branco
      $newtext = trim(mb_substr($texto, 0, $limite))."...";
    else:
      //localiza ultimo espaço antes de $limite
      $ultimo_espaço = strrpos(mb_substr($texto, 0, $limite)," ");
      //corta o $texto até a posição lozalizada
      $newtext = trim(mb_substr($texto, 0, $ultimo_espaço))."...";
    endif;
  endif;
  return $newtext;
}






// TRATAMENTO DE ERROS #####################
//CSS constantes :: Mensagens de Erro
define('SYS_ACCEPT', 'accept');
define('SYS_INFOR', 'infor');
define('SYS_ALERT', 'alert');
define('SYS_ERROR', 'error');

//Pegar a URL atual
function getSiteURL() {
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  return $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
}

//WSErro :: Exibe erros lançados :: Front
function SYSError($ErrMsg, $ErrNo, $ErrDie = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? SYS_INFOR : ($ErrNo == E_USER_WARNING ? SYS_ALERT : ($ErrNo == E_USER_ERROR ? SYS_ERROR : $ErrNo)));
    echo "<p class=\"trigger {$CssClass}\">{$ErrMsg}<span class=\"ajax_close\"></span></p>";

    if ($ErrDie):
        die;
    endif;
}

//PHPErro :: personaliza o gatilho do PHP
function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? SYS_INFOR : ($ErrNo == E_USER_WARNING ? SYS_ALERT : ($ErrNo == E_USER_ERROR ? SYS_ERROR : $ErrNo)));
    echo "<p class=\"trigger {$CssClass}\">";
    echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
    echo "<small>{$ErrFile}</small>";
    echo "<span class=\"ajax_close\"></span></p>";

    if ($ErrNo == E_USER_ERROR):
        die;
    endif;
}

//set_error_handler('PHPErro');
?>