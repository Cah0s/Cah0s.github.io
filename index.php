<?php
require_once 'system/functions.php';
require 'mail/class.phpmailer.php';
$_SESSION['_token'] = (!isset($_SESSION['_token'])) ? hash('sha256', rand(100,1000)) : $_SESSION['_token'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
	<meta>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<title><?php echo $title ; ?>Beg24</title>
	<link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/user.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/style.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/messages.css">
	<!-- <link rel="stylesheet" href="<?php echo HOME; ?>assets/css/linear-icons.css"> -->
	<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
	<link rel="stylesheet" href="<?php echo HOME; ?>assets/css/font-awesome.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  	<link href='https://fonts.googleapis.com/css?family=Architects+Daughter|Kelly+Slab|Lato|Monoton|Neucha|Nova+Mono|Orbitron|Oswald|Quicksand:300,700' rel='stylesheet' type='text/css'>

  	<meta name="author" content="">
	<meta name="keywords" content="<?php echo $keywords; ?>">
	<meta name="description" content="<?php echo limitarTexto($description, 150); ?>">

</head>
<body class="site_flex">

<?php
setHeader();
getHome();
setFooter();
?>

</body>
</html>