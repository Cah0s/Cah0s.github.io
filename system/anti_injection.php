<?php
/**********************************
* ANTI INJECTION PARA CAMPOS
* (SQL, XSS, LFI, RFI, RCE)
**********************************/
function anti_injection($val) {
	$addr = $_SERVER['REMOTE_ADDR'];
	$injusuario = (isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : 'Guest' );
	$injlog = addslashes($val);

	$msg = NULL;
	$match = "/(from|select|insert|delete|where|drop table|show tables|order by|group|union|all|#|--|0x|'|\"|´|`|0x27|%27|%00)/i";
	$sql = preg_match($match, $val);
	$val = preg_replace($match, "", $val);
	$val = urldecode(htmlspecialchars(addslashes(trim($val))));
	$rfi = preg_match("#http|ftp|https|www|wget|exe|system|exec|cat|php://filter|%00#i", $val);
	$xss = preg_match("#%3C|%3E|&lt|&gt|marquee|scr|IMG|svg|onload|alert#i", $val);
	$rce = preg_match("#uname|ls|dir|tree#i", $val);
	$val = (!empty($val) ? $val : FALSE);
	
	//$exp = explode("=", $val);
	//if($exp[0] == '.' || $exp[0] == '..' || $exp[0] == '../'):
	if($val{0} == '.' || $val{1} == '.' || $val{2} == '/'):
	  $msg  = '<div class="message is-dark">';
      $msg .= '  <p class="message-header">HIGH ATTENTION</p>';
      $msg .= '  <p class="message-body"><i><font color="maroon"> LFI </font></i>Detected';
      $msg .= '</div>';
      $type = "LFI INJECTION";
      injlogs($type, $injlog, $addr, $injusuario);
	elseif($rfi):
	  $msg  = '<div class="message is-dark">';
      $msg .= '  <p class="message-header">HIGH ATTENTION</p>';
      $msg .= '  <p class="message-body"><i><font color="maroon"> RFI </font></i>Detected';
      $msg .= '</div>';
      $type = "RFI INJECTION";
      injlogs($type, $injlog, $addr, $injusuario);
	elseif($xss):
	  $msg  = '<div class="message is-dark">';
      $msg .= '  <p class="message-header">HIGH ATTENTION</p>';
      $msg .= '  <p class="message-body"><i><font color="maroon"> XSS </font></i>Detected';
      $msg .= '</div>';
      $type = "XSS INJECTION";
      injlogs($type, $injlog, $addr, $injusuario);
	elseif($rce):
	  $msg  = '<div class="message is-dark">';
      $msg .= '  <p class="message-header">HIGH ATTENTION</p>';
      $msg .= '  <p class="message-body"><i><font color="maroon"> RCE </font></i>Detected';
      $msg .= '</div>';
      $type = "RCE INJECTION";
      injlogs($type, $injlog, $addr, $injusuario);
	elseif($sql):
	  $msg  = '<div class="message is-dark">';
      $msg .= '  <p class="message-header">HIGH ATTENTION</p>';
      $msg .= '  <p class="message-body"><i><font color="maroon"> SQL </font></i>Detected';
      $msg .= '</div>';
      $type = "SQL INJECTION";
      injlogs($type, $injlog, $addr, $injusuario);
	else:
		//$msg = $val;
	endif;
	
	return $msg;
}
?>