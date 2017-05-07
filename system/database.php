<?php
require_once 'iniconf.php';
require HOME.'vendor/autoload.php';
require 'anti_injection.php';

/*
<a href="intent://send/+558186848722#Intent;scheme=smsto;package=com.whatsapp;action=android.intent.action.SENDTO;end">Open WhatsApp chat window</a>
*/

/* CONEXÃO COM O BANCO */
function conectar(){
	try{
		$pdo=new PDO("mysql:host=".HOST.";dbname=".BANCO, USER, PASS);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	}
	catch(PDOExeption $e){
		echo $e->getMessage();
	}
	return $pdo;
}
$pdo=conectar();

/* PEGAR A URL COMPLETA ATUAL */
function getUrl() {
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  return $protocol.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
}

/* GERAR LOGS DE ACESSOS, UMA LINHA POR IP APENAS */
function injlogs($type, $injlog, $addr, $injusuario){
	$pdo=conectar();
	$injected = $pdo->query("INSERT INTO `injectionlogs` (`type`,`addr`,`user`,`url`,`injected`,`data`) VALUES ('$type','$addr','$injusuario','".getUrl()."','$injlog',NOW())");
	//if($injected){return TRUE;}
}

/* GERAR LOGS DE ACESSOS, UMA LINHA POR IP APENAS */
function accesslogs($addr, $tabela, $usrag, $url){
	$pdo=conectar();
	// if(anti_injection($url)){
	// 	echo anti_injection($url);
	// }else{
		$check = $pdo->query("SELECT * FROM logs WHERE addr='$addr'")->fetchColumn();
	    if($check > 0) {
	    	$atualizar = $pdo->query("UPDATE $tabela SET url='$url', data=NOW() WHERE addr='$addr'");
	    	if($atualizar){ return TRUE; }
	    }
		else{
			$novo = $pdo->query("INSERT INTO $tabela (`addr`,`useragent`,`url`,`data`) VALUES ('$addr','$usrag','$url',NOW())");
			if($novo){return TRUE;}
		}
	//}
}

/* CHECAR SESSION PARA USERMENU */
function is_checked_in() {
	return isset($_SESSION['user_id']);
}

/* CRUD GERAL USUÁRIOS */
/* CRIAR NOVO CONTEÚDO */
function creat($tabela, $attributes){
	$pdo=conectar();
	$keys   = array_keys($attributes);
	$camp   = implode(',', $keys);
	$values = null;
	foreach ($keys as $key) {
		$values.=', :'.$key; //, ?, ?, ?
	}
	$values = (trim(ltrim($values, ',')));
	$creat = $pdo->prepare("INSERT INTO $tabela ( $camp ) VALUES ( $values )");
	$creat->execute($attributes);
	if($creat){ return TRUE; }
}
/* LER TABELA PADRÃO */
function read($tabela){
	$pdo=conectar();

	/* PAGINATION */
	$sql_count_emp = $pdo->query("SELECT * FROM empresas JOIN cidades ON cid = cid_id JOIN estados ON eid = esd_id");
	$count = $sql_count_emp->rowCount(PDO::FETCH_ASSOC);
	if(empty($_GET['pag'])){ $pag=1; }else{ $pag = "$_GET[pag]"; }
    if($pag >= '1'){ $pag = $pag; }else{ $pag = '1'; }
	$max=10;
	$ini = ($pag * $max) - $max;
	$paginas=ceil($count/$max);

	$read = $pdo->query("SELECT * FROM empresas JOIN cidades ON cid = cid_id JOIN estados ON eid = esd_id LIMIT $ini, $max");
	$read->execute();
	
	while ($rows = $read->fetch(PDO::FETCH_OBJ)) {
		$data = strftime("%d/%m/%Y",$rows->data);
		$status = ($rows->visivel == "sim") ? "yesview" : "notview" ;
		echo"
		<div id='firmenlist'>
            <div class='firmentitle'><small class='trigger $status status'></small> $rows->empresa <span> 
            <a href='?edit=$rows->empresa&uid=$rows->id'><i class='lnr lnr-pencil'></i></a>
            <a href='?del=$rows->empresa&uid=$rows->id'><i class='lnr lnr-cross'></i></a>
            </span></div>
            <br class='clear'>
        </div>";
	}

	echo "
	<table class='pag'>
	<tr>";
	if($pag!=1){ echo "<td class=num><a name='".($pag-1)."' href='?pag=".($pag-1)."'><i class='lnr lnr-arrow-left-circle'></i></a></td>"; }
    if($count<=$max){  }
    else{
        for($i=1;$i<=$paginas;$i++){
            if($pag==$i){ echo "<td class=num><a name='$i' href='?pag=".$i."'><font color=red>".$i."</font></a></td>"; }
            else{ echo "<td class=num><a name='$i' href='?pag=".$i."'>".$i."</a></td>"; }
        }
    }
    if($pag!=$paginas){ echo "<td class=num><a name='".($pag+1)."' href='?pag=".($pag+1)."'><i class='lnr lnr-arrow-right-circle'></i></a></td>"; }
    echo "</tr></table>";

	if($read) { return TRUE; }
}
/* ATUALIZAR CONTEÚDO */
function update($uid, $tabela, $attributes){
	$pdo=conectar();
	$values = null;
	foreach ($attributes as $key => $value) {
		$values.= $key.'=:'.$key.',';
	}
	$values = (rtrim($values, ','));
	$update = $pdo->prepare("UPDATE $tabela SET $values WHERE id=:uid");
	$attributes['uid'] = $uid;
	$update->execute($attributes);
	if($update) { return TRUE; }
}
/* DELETAR CONTEÚDO */
function del($uid, $tabela){
	$pdo=conectar();
	$delete = $pdo->prepare("DELETE FROM $tabela WHERE idusr=:uid");
	$delete->bindParam(':uid', $uid, PDO::PARAM_INT);
	$delete->execute();
	if($delete) { return TRUE; }
}

/* BUSCA AVANCADA ADVANCED_SEARCH.PHP */
function advanced_search($uid, $city, $tags){
	$pdo=conectar();
	if(isset($uid) && $uid != ""){
		if(anti_injection($uid)){ echo anti_injection($uid); }
		else {
			/* CONTAGEM DE EMPRESAS */
			$sql_count = "SELECT COUNT(*) AS total FROM empresas JOIN cidades ON cid=cid_id WHERE visivel = 'sim' AND empresa LIKE '%$uid%' OR categoria LIKE '%$uid%' OR cidade LIKE '%$uid%' ORDER BY id ASC";
			$stmt_count = $pdo->prepare($sql_count);
			$stmt_count->execute();
			$total_count = $stmt_count->fetchColumn();

			/* PAGINATION */
			$sql_count_emp = $pdo->query("SELECT * FROM empresas JOIN cidades ON cid=cid_id WHERE visivel = 'sim' AND empresa LIKE '%$uid%' OR categoria LIKE '%$uid%' OR cidade LIKE '%$uid%'");
			$count = $sql_count_emp->rowCount(PDO::FETCH_ASSOC);
			if(empty($_GET['pag'])){ $pag=1; }else{ $pag = "$_GET[pag]"; }
		    if($pag >= '1'){ $pag = $pag; }else{ $pag = '1'; }
			$max=10;
			$ini = ($pag * $max) - $max;
			$paginas=ceil($count/$max);

			/* QUERY PRINCIPAL */
			$sql = "SELECT * FROM empresas JOIN cidades ON cid=cid_id JOIN estados ON eid=esd_id WHERE visivel = 'sim' AND empresa LIKE '%$uid%' OR categoria LIKE '%$uid%' OR cidade LIKE '%$uid%' LIMIT $ini, $max";
			$total = $pdo->prepare($sql);
			$total->execute();
			
			echo "
			<span class='total'>Zeigt
		    <font color='seagreen'> "; echo ($max > $total_count) ? $max = $total_count : $max; echo "</font> von
		    <font color='seagreen'>$total_count</font> ergebnisse per seite für <b>'$uid'</b><br><br>";

			if($total):
				while($row = $total->fetch(PDO::FETCH_OBJ)){
					$site = str_replace("http:", "", str_replace("https:", "", str_replace("/", "", $row->website)));
					($row->logo == '0' || $row->logo == "" ) ? $logo = "semimagem.jpg" : $logo = "empresas/$row->logo";
					echo "
					<div class='detail_box'>
						<div class='detail dtl1 detail_image'>
							<img src='".HOME."assets/images/$logo'>
						</div>

						<div class='detail dtl2 detail_info'>
							<div class='info'>
								<h3>$row->empresa</h3>
								<li><i class='lnr lnr-map-marker'></i> $row->estado</li>
								<li><i class='lnr lnr-phone-handset'></i> $row->telefone</li>
								<li><i class='lnr lnr-envelope'></i> <a href='mailto:$row->email'>$row->email</a></li>
								<li><i class='lnr lnr-link'></i> <a href='$row->website' target='_blank'>$site</a></li>
							</div>
							<a href='".HOME."show&show=empresas&uid=$row->id' target='_blank' class='detail_button'>Ver Mais</a>
						</div>

						<div class='detail dtl3 detail_description'>
							<h2>Beschreibung</h2>
							".limitarTexto($row->descricao, 150)."
						</div>";

					$work = $pdo->query("SELECT * FROM works WHERE company = '$row->empresa' AND visible = 'sim'");
					$work->execute();
					$show = $work->fetch(PDO::FETCH_OBJ);
					if($show >= 1){
						echo "<div class='workshow'>";
						echo "Vaga de emprego disponível <span><a href='#'>SHOW</a></span>";
						echo "</div>";
					}
					echo "</div>";
				}
				echo "
				<table class='pag'>
        		<tr>";
				if($pag!=1){ echo "<td class=num><a name='".($pag-1)."' href='advanced_search&uid=$uid&pag=".($pag-1)."'><i class='lnr lnr-arrow-left-circle'></i></a></td>"; }
	            if($count<=$max){  }
	            else{
	                for($i=1;$i<=$paginas;$i++){
	                    if($pag==$i){ echo "<td class=num><a name='$i' href='advanced_search&uid=$uid&pag=".$i."'><font color=red>".$i."</font></a></td>"; }
	                    else{ echo "<td class=num><a name='$i' href='advanced_search&uid=$uid&pag=".$i."'>".$i."</a></td>"; }
	                }
	            }
	            if($pag!=$paginas){ echo "<td class=num><a name='".($pag+1)."' href='advanced_search&uid=$uid&pag=".($pag+1)."'><i class='lnr lnr-arrow-right-circle'></i></a></td>"; }
	            echo "</tr></table>";
			endif;
		}
	}
	elseif(isset($city) && $city != ""){
		if(isset($tags) && $tags != ""){
			if(anti_injection($city)){ echo anti_injection($city); }
			elseif(anti_injection($tags)){ echo anti_injection($tags); }
			else{
				$tagexp = (preg_match("#,#", $tags)) ? explode(",", $tags) : $tags ;
				if($tagexp > 0){
					$tagcount = count($tagexp);
					for ($i=0; $i < $tagcount; $i++) {
						$tagfor = "categoria='$tagexp[$i]'";
						$tag[] = $tagfor;
					}
				}
				else{ $tag = "categoria='$tagexp'"; }
				$tagfinal = (is_array($tag) ? implode(" OR ", $tag) : $tag);
				$sql = "SELECT * FROM cidades JOIN estados ON eid = estado_id JOIN empresas ON cid_id=cid AND visivel = 'sim' WHERE $tagfinal AND cidade LIKE '$city%'";
				$total = $pdo->query($sql);
				$total->execute();
				echo "<font color='#2c3e50'><b>".$total->rowCount(PDO::FETCH_ASSOC)."</b> Ergebnis für <b>'$city'</b> in Bereich von <b>'$tags'</b></font><br><br>";

				if($total):
					while($row = $total->fetch(PDO::FETCH_OBJ)){
						echo "
						<div id='cityresu'>
							<li><i class='lnr lnr-map-marker'></i> $row->cidade</li>
							<li><i class='lnr lnr-apartment'></i> <font color='#2c3e50'>$row->empresa</font> <a class='minbutton' href='".HOME."show&show=empresas&uid=$row->id' target='_blank'>acessar</a></li>
							<li><i class='lnr lnr-flag'></i> $row->categoria</li>
						</div>
						";
					}
				endif;
			}
		}
		else {
			if(anti_injection($city)){
				echo anti_injection($city);
			}
			else{
				$sql = "SELECT * FROM cidades JOIN estados ON eid = estado_id WHERE cidade LIKE '$city%'";
				$total = $pdo->query($sql);
				$total->execute();
				echo "<font color='#2c3e50'><b>".$total->rowCount(PDO::FETCH_ASSOC)."</b> Ergebnisse für <b>'$city'</b> gefunden<br><br></font>";

				if($total):
					while($row = $total->fetch(PDO::FETCH_OBJ)){
						$emp = "SELECT * FROM empresas JOIN cidades ON cid = cid_id WHERE cidade='$row->cidade'";
						$empresa = $pdo->query($emp);
						$totemp = $empresa->rowCount(PDO::FETCH_ASSOC);

						echo "
						<div id='stateresu'>
							<li><i class='lnr lnr-map-marker'></i> <a href='advanced_search&uid=$row->cidade'>$row->cidade</a></li>
							<li><i class='lnr lnr-earth'></i> $row->estado</li>
							<li><i class='lnr lnr-apartment'></i> <a href='advanced_search&uid=$row->cidade'>$totemp</a></li>
						</div>
						";
					}
				endif;
			}
		} // branche
	}
	else{
		echo "Nichts gesucht";
	}
}

/* LISTAR EMPRESA/EVENTO SHOW.PHP */
function jobs($tabela, $city, $tags){
	$pdo=conectar();
	
	$tagexp = (preg_match("#,#", $tags)) ? explode(",", $tags) : $tags ;
	if($tagexp > 0){
		$tagcount = count($tagexp);
		for ($i=0; $i < $tagcount; $i++) {
			$tagfor = "cat='$tagexp[$i]'";
			$tag[] = $tagfor;
		}
	}
	else{ $tag = "cat='$tagexp'"; }
	$tagfinal = (is_array($tag) ? implode(" OR ", $tag) : $tag);
	$tagexist = ($tagfinal != "cat=''") ? "AND ".$tagfinal : str_replace("cat=''", "", $tagfinal);

	if(isset($city) && $city != ""){
		if(isset($tags) && $tags != ""){
			if(anti_injection($city)){ echo anti_injection($city); }
			elseif(anti_injection($tags)){ echo anti_injection($tags); }
			else{

				/* PAGINATION */
				$sql_count_emp = $pdo->query("SELECT * FROM $tabela WHERE visible = 'sim' $tagexist AND city = '$city'");
				//var_dump($sql_count_emp);
				$count = $sql_count_emp->rowCount(PDO::FETCH_ASSOC);
				if(empty($_GET['pag'])){ $pag=1; }else{ $pag = "$_GET[pag]"; }
			    if($pag >= '1'){ $pag = $pag; }else{ $pag = '1'; }
				$max=30;
				$ini = ($pag * $max) - $max;
				$paginas=ceil($count/$max);

				$read = $pdo->query("SELECT * FROM $tabela WHERE visible = 'sim' AND $tagfinal AND city = '$city' LIMIT $ini, $max");
				$read->execute();

				if($read){
					echo "<div id='jobres'>";
					while ($row = $read->fetch(PDO::FETCH_OBJ)) {
						echo "
						<div class='joblisting'>
							<div class='jobbody'>
								<h3>$row->company</h3>
								<span class='job'><strong><span><i class='lnr lnr-pushpin'></i> $row->work</strong></span>
								<span><i class='lnr lnr-apartment'></i> Erfurt &nbsp; <i class='lnr lnr-clock'></i> ".strftime("%d/%m/%Y",$row->data)." - 11/11/11</span>
								<span><a href='".HOME."work&show=works&uid=$row->id' target='_blank' class='detail_button_job'>Ver Mais</a></span>
							</div>
						</div>
						";
					}
					echo "</div>";

					echo "
					<table class='pag'>
	        		<tr>";
					if($pag!=1){ echo "<td class=num><a name='".($pag-1)."' href='jobs&city=$city&tags=$tags&pag=".($pag-1)."'><i class='lnr lnr-arrow-left-circle'></i></a></td>"; }
		            if($count<=$max){  }
		            else{
		                for($i=1;$i<=$paginas;$i++){
		                    if($pag==$i){ echo "<td class=num><a name='$i' href='jobs&city=$city&tags=$tags&pag=".$i."'><font color=lightgreen>".$i."</font></a></td>"; }
		                    else{ echo "<td class=num><a name='$i' href='jobs&city=$city&tags=$tags&pag=".$i."'>".$i."</a></td>"; }
		                }
		            }
		            if($pag!=$paginas){ echo "<td class=num><a name='".($pag+1)."' href='jobs&city=$city&tags=$tags&pag=".($pag+1)."'><i class='lnr lnr-arrow-right-circle'></i></a></td>"; }
		            echo "</tr></table>";
		        }
			}
		}
		else{
			if(anti_injection($city)){ echo anti_injection($city); }
			else{
				/* PAGINATION */
				$sql_count_emp = $pdo->query("SELECT * FROM $tabela WHERE visible = 'sim' $tagexist AND city = '$city'");
				//var_dump($sql_count_emp);
				$count = $sql_count_emp->rowCount(PDO::FETCH_ASSOC);
				if(empty($_GET['pag'])){ $pag=1; }else{ $pag = "$_GET[pag]"; }
			    if($pag >= '1'){ $pag = $pag; }else{ $pag = '1'; }
				$max=30;
				$ini = ($pag * $max) - $max;
				$paginas=ceil($count/$max);
				
				$read = $pdo->query("SELECT * FROM $tabela WHERE visible = 'sim' AND city = '$city' LIMIT $ini, $max");
				$read->execute();

				if($read){
					echo "<div id='jobres'>";
					while ($row = $read->fetch(PDO::FETCH_OBJ)) {
						echo "
						<div class='joblisting'>
							<div class='jobbody'>
								<h3>$row->company</h3>
								<span class='job'><strong><span><i class='lnr lnr-pushpin'></i> $row->work</strong></span>
								<span><i class='lnr lnr-apartment'></i> $row->city &nbsp; <i class='lnr lnr-calendar-full'></i> ".strftime("%d/%m/%Y",$row->data)." - 11/11/11</span>
								<span><a href='".HOME."work&show=works&uid=$row->id' target='_blank' class='detail_button_job'>Ver Mais</a></span>
							</div>
						</div>
						";
					}
					echo "</div>";

					echo "
					<table class='pag'>
	        		<tr>";
					if($pag!=1){ echo "<td class=num><a name='".($pag-1)."' href='jobs&city=$city&pag=".($pag-1)."'><i class='lnr lnr-arrow-left-circle'></i></a></td>"; }
		            if($count<=$max){  }
		            else{
		                for($i=1;$i<=$paginas;$i++){
		                    if($pag==$i){ echo "<td class=num><a name='$i' href='jobs&city=$city&pag=".$i."'><font color=lightgreen>".$i."</font></a></td>"; }
		                    else{ echo "<td class=num><a name='$i' href='jobs&city=$city&pag=".$i."'>".$i."</a></td>"; }
		                }
		            }
		            if($pag!=$paginas){ echo "<td class=num><a name='".($pag+1)."' href='jobs&city=$city&pag=".($pag+1)."'><i class='lnr lnr-arrow-right-circle'></i></a></td>"; }
		            echo "</tr></table>";
				}
			}
		}
	}
	else{
		echo "
		<center>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
  	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
  	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
  	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
  	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
  	proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
  	<br><br>
  	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
  	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
  	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
  	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
  	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
  	proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
		</center>";
	}

}

/* LISTAR EMPREGO WORK.PHP */
function joblist($uid, $tabela){
	$pdo=conectar();
	
	if(anti_injection($uid)){ echo anti_injection($uid); }
	elseif(anti_injection($tabela)){ echo anti_injection($tabela); }
	
	$read = $pdo->query("SELECT * FROM $tabela WHERE id='$uid'");
	$read->execute();

	$view = $pdo->prepare("UPDATE $tabela SET viewsworks = viewsworks+1 WHERE id=:id");
    $view->bindValue(':id', $uid, PDO::PARAM_INT);
    $view->execute();

	if($read){
		while ($row = $read->fetch(PDO::FETCH_OBJ)) {
			$plusfind = $row->city;
			$pluscat  = $row->cat;

			?>
			<div class='detail_box_expand bewerbung'>
				<div class="posted">
					<li><i class='lnr lnr-eye'></i> <?php echo ($row->viewsworks >= 1) ? ($row->viewsworks+1).' ansichten' : '1 angesehen'; ?></li>
					<li><i class='lnr lnr-calendar-full'></i> <?php echo strftime("%d/%m/%Y",$row->data); ?></li>
				</div>

				<h2><?php echo "$row->company <br><h3><font color='#333' style='font-weight:normal;'>$row->work</font></h3>"; ?></h2>
				<div class='detail_expand dtl2 detail_info_expand'>
					<div class='info'>
						<div class='infobox'>
							<li><i class='lnr lnr-map'></i> <?php echo $row->address; ?></li>
							<li><i class='lnr lnr-phone-handset'></i> <?php echo $row->phone; ?></li>
							<li><i class='lnr lnr-envelope'></i> <?php echo $row->mail; ?></li>
						</div>
					</div>
				</div>

				<div class='detail_expand dtl3 detail_description_expand'>
					<h2>Beschreibung</h2>
					<?php echo nl2br($row->description); ?>
				</div>

				<div class="workteste">
					<h2>Grundbeschreibung</h2>
					<div class="intern">
						<h4>Ihre Aufgaben</h4>

						<li>Mitarbeit in einem professionellen Support-Team</li>
						<li>Kundensupport (First- und Second-Level) per E-Mail und Telefon für unsere Software</li>
						<li>Eigenständige Erfassung, Bearbeitung und Auswertung von Anfragen in unserem Ticket-System</li>
						<li>Erarbeiten von effizienten Problemlösungen für unsere Kunden</li>
						<li>Dokumentation anfallender Anwenderprobleme</li>
					</div>


					<div class="intern">
						<h4>Anforderungen</h4>

						<li>Abgeschlossene Berufsausbildung im kaufmännischen / informationstechnischen Bereich</li>
						<li>Berufserfahrung im Support, idealerweise für IT-Produkte / IT-Dienstleistungen</li>
						<li>Vorteilhaft sind Kenntnisse im Bereich Immobilienwirtschaft / Immobilienbewertung</li>
						<li>Sicherer Umgang mit betrieblicher Standard Software (MS Office, Windows)</li>
						<li>Sehr gute Deutschkenntnisse sowie ausgeprägte kommunikative Fähigkeiten</li>
						<li>Spaß am professionellen Kontakt und Umgang mit Kunden</li>
						<li>Hohe Service- und Dienstleistungsorientierung</li>
					</div>


					<div class="intern">
						<h4>Unser Angebot</h4>

						<li>mittelständiges Betriebsklima mit kurzen Entscheidungswegen und offener Kommunikation</li>
						<li>vielfältiger und abwechslungsreicher Arbeitsplatz mit flexiblen Arbeitszeiten</li>
						<li>Aufnahme in ein engagiertes Team und fundierte Einarbeitung</li>
						<li>persönliche Weiterentwicklung und berufliche Perspektiven </li>
						<li>Mitwirken am Erfolg und Wachstum der on-geo GmbH</li>
						<li>betriebliche Gesundheitsförderung</li>
					</div>
				</div>

				<div class="social">
					<li class="but"><a class="ml" href='mailto:<?php echo $row->mail; ?>'><i class='lnr lnr-envelope'></i></a></li>
					<li class="but"><a class="wp whatsapp" href="intent://send/<?php echo $row->phone;?>#Intent;scheme=smsto;package=com.whatsapp;action=android.intent.action.SENDTO;end"><i class='fa fa-whatsapp'></i></a></li>
					<li class="but"><a class="ws" href='<?php echo $row->web; ?>' target='_blank'><i class='lnr lnr-link'></i></a></li>
				</div>

				<div id='map'></div>				
			</div>
		<?php
		}

		$read2 = $pdo->query("SELECT * FROM $tabela WHERE city ='".$plusfind."' AND id != '$uid' LIMIT 6");
		$read2->execute();

		if($read2){
			if($read2->rowCount(PDO::FETCH_ASSOC) > 1):
				echo "<h3 style='padding:10px; width:100%; background:royalblue; color:#f9f9f9; text-align:center; border-top-right-radius:.25em; border-top-left-radius:.25em;'>WEITERE JOBSANGEBOTE</h3>";
				echo "<div id='jobres'>";
				while ($row2 = $read2->fetch(PDO::FETCH_OBJ)) {
					echo "
					<div class='joblisting'>
						<div class='jobbody'>
							<h3>$row2->company</h3>
							<span class='job'><strong><span><i class='lnr lnr-pushpin'></i> $row2->work</strong></span>
							<span><i class='lnr lnr-apartment'></i> $row2->city &nbsp; <i class='lnr lnr-calendar-full'></i> ".strftime("%d/%m/%Y",$row2->data)." - 11/11/11</span>
							<span><a href='".HOME."work&show=works&uid=$row2->id' target='_blank' class='detail_button_job'>Ver Mais</a></span>
						</div>
					</div>";
				}
				echo "</div>";
			endif;
		}
	}
	else { echo "Nichts gesucht"; }
}

/* LISTAR EMPRESA/EVENTO SHOW.PHP */
function listar($uid, $tabela){
	$pdo=conectar();
	if(anti_injection($uid)){ echo anti_injection($uid); }
	elseif(anti_injection($tabela)){ echo anti_injection($tabela); }
	
	$read = $pdo->query("SELECT * FROM $tabela JOIN cidades ON cid = cid_id JOIN estados ON eid = esd_id WHERE visivel = 'sim' AND id='$uid'");
	$read->execute();

	$view = $pdo->prepare("UPDATE $tabela SET views = views+1 WHERE id=:id");
    $view->bindValue(':id', $uid, PDO::PARAM_INT);
    $view->execute();

	if($read){
		while ($row = $read->fetch(PDO::FETCH_OBJ)) {
			$galeria = ($row->fotos != "" && $row->fotos != '0') ? NULL : 'display:none;' ;
			$pic = explode(",", $row->fotos);
			($row->logo == '0' || $row->logo == "" ) ? $logo = "semimagem.jpg" : $logo = "$tabela/$row->logo";
			$horas = explode(",", $row->horarios);
			?>
			<div class='detail_box_expand'>
				<div class="posted">
					<li><i class='lnr lnr-eye'></i> <?php echo ($row->views >= 1) ? ($row->views+1).' ansichten' : '1 angesehen'; ?></li>
					<li><i class='lnr lnr-calendar-full'></i> <?php echo strftime("%d/%m/%Y",$row->data); ?></li>
				</div>

				<h2><?php echo $row->empresa; ?></h2>
				<div class='detail_expand dtl2 detail_info_expand'>
					<div class='info'>
						<div class='infobox'>
							<li><i class='lnr lnr-map'></i> <?php echo $row->endereco.", ". $row->cidade ."-". $row->uf; ?></li>
							<li><i class='lnr lnr-phone-handset'></i> <?php echo $row->telefone; ?></li>
						</div>

						<div class='infobox'>
							<li><?php echo $horas[0]; ?></li>
							<li><?php echo $horas[1]; ?></li>
							<li><?php echo $horas[2]; ?></li>
							<li><?php echo $horas[3]; ?></li>
							<li><?php echo $horas[4]; ?></li>
							<li><?php echo $horas[5]; ?></li>
						</div>
					</div>
				</div>

				<div class='detail_expand dtl3 detail_description_expand'>
					<h2>Beschreibung</h2>
					<?php echo nl2br($row->descricao); ?>
				</div>

				<div id="workphotos" class='detail_expand detail_fotos' style='<?php echo $galeria; ?>'>
					<?php
					foreach($pic as $picture){
						echo "
						<picture>
						  <a class='lightbox' href='#$picture'><img src='".HOME."painel/cadastros/arquivos/newimg/$row->empresa/$picture'></a> 
							<div class='lightbox-target' id='$picture'>
							   <img src='".HOME."painel/cadastros/arquivos/newimg/$row->empresa/$picture'>
							   <a class='lightbox-close' href='#workphotos'></a>
							</div>
						</picture>
						";
					}
					?>
				</div>

				<div class="social">
					<li class="but"><a class="fb" href='<?php echo $row->facebook; ?>' target='_blank'><i class='fa fa-facebook'></i></a></li>
					<li class="but"><a class="tt" href='<?php echo $row->twitter; ?>' target='_blank'><i class='fa fa-twitter'></i></a></li>
					<li class="but"><a class="gg" href='<?php echo $row->google; ?>' target='_blank'><i class='fa fa-google'></i></a></li>
					<li class="but"><a class="ml" href='mailto:<?php echo $row->email; ?>'><i class='lnr lnr-envelope'></i></a></li>
					<li class="but"><a class="wp whatsapp" data-text="Teste Whatsapp! Visite :: " data-link='<?php echo $row->telefone; ?>'><i class='fa fa-whatsapp'></i></a></li>
					<li class="but"><a class="ws" href='<?php echo $row->website; ?>' target='_blank'><i class='lnr lnr-link'></i></a></li>
				</div>

				<div id='map'></div>
				
			</div>
		<?php
		}
	}
	else { echo "Nichts gesucht"; }
}

/* LISTAR CONTEÚDO DO ATUAL USUÁRIO ONLINE PAINEL ADMIN */
function user_emp_read($uid, $tabela, $join){
	$pdo=conectar();
	$user = $pdo->query("SELECT * FROM $tabela JOIN $join ON login='$uid' JOIN cidades ON cid=cid_id WHERE user_cad='$uid'");
	$user->execute();

	//if($user->fetch(PDO::FETCH_OBJ) < 1){ echo "nada cadastrado ainda"; }
	if(isset($_GET['edit'])){
		$empid = $_GET['edit'];
		$info = $pdo->query("SELECT * FROM $tabela JOIN $join ON login='$uid' JOIN cidades ON cid=cid_id WHERE user_cad='$uid' AND id='$empid'");
		$info->execute();
		$edit = $info->fetch(PDO::FETCH_OBJ);
	?>
  	    <div class="my_box my_box_login">
		    <div class="boxx box_form">
		      <h2><?php echo $edit->empresa; ?></h2>
		      
		      <form method="post">
		        <div class="campo">
		          <input type="text" name="email" value="<?php echo $edit->email; ?>" class="inputsr">
		        </div>

		        <div class="campo">
		          <input type="text" name="telefone" value="<?php echo $edit->telefone; ?>" class="inputsl">
		        </div>

		        <div class="campo">
		          <input type="text" name="endereco" value="<?php echo $edit->endereco; ?>" class="inputsr">
		        </div>

		        <input type="submit" name="send" class="send" value="envia">
		      </form>
		    </div>

		    <div class="boxx box_msg">
		      <?php
		        $error = false;
		        if(isset($_POST['send'])){
		        	$email = $_POST['email'];
		        	$phone = $_POST['telefone'];
		        	$end   = $_POST['endereco'];
		        	
		        	if(anti_injection($email)){
						echo anti_injection($email);
						$error = true;
					}
		        	if(anti_injection($telefone)){
						echo anti_injection($telefone);
						$error = true;
					}
		        	
		        	if($email == ""){
		        	  $message  = '<div class="message is-warning">';
			          $message .= '  <p class="message-header">WARNING</p>';
			          $message .= '  <p class="message-body">Email feld muss ausgefüllt werden!</p>';
			          $message .= '</div>';
			          echo $message;
			          $error = true;
			      	}
	              	elseif($phone == ""){
		        	  $message  = '<div class="message is-warning">';
			          $message .= '  <p class="message-header">WARNING</p>';
			          $message .= '  <p class="message-body">Telefon feld muss ausgefüllt werden!</p>';
			          $message .= '</div>';
			          echo $message;
			          $error = true;
	              	}
	              	elseif($end == ""){
		        	  $message  = '<div class="message is-warning">';
			          $message .= '  <p class="message-header">WARNING</p>';
			          $message .= '  <p class="message-body">Adresse feld muss ausgefüllt werden!</p>';
			          $message .= '</div>';
			          echo $message;
			          $error = true;
	              	}

	              	if(!$error){
		              	$attributes = [
						  	'email'  => $email,
						 	'telefone'  => $phone,
						 	'endereco' => $end
						];

						$log = update($_GET['edit'], 'empresas', $attributes);
				        if($log) { echo "Informações Atualizado com Sucesso"; }
				        else { echo "0ps!"; }
				    }
				    else{ echo "Houve um erro fatal!"; }
		        }
		      ?>
		    </div>
		</div>
	<?php
    }
    else{

		while ($row = $user->fetch(PDO::FETCH_OBJ)) {
			$galeria = ($row->fotos != "" && $row->fotos != '0') ? NULL : 'display:none;' ;
			$pic = explode(",", $row->fotos);
			$horas = explode(",", $row->horarios);
			?>
			<div class='detail_box_expand'>
				<div class="posted">
					<li><i class='lnr lnr-eye'></i> <?php echo ($row->views >= 1) ? ($row->views+1).' visualizações' : '1  visualizado'; ?></li>
					<li><i class='lnr lnr-calendar-full'></i> <?php echo strftime("%d/%m/%Y",$row->data); ?></li>
				</div>

				<h2><?php echo $row->empresa; ?></h2>
				<div class='detail_expand dtl2 detail_info_expand'>
					<div class='info'>
						<div class='infobox'>
							<li><i class='lnr lnr-map'></i> <?php echo $row->endereco.", ". $row->cidade ."-". $row->uf; ?></li>
							<li><i class='lnr lnr-phone-handset'></i> <?php echo $row->telefone; ?></li>
						</div>

						<div class='infobox'>
							<li><?php echo $horas[0]; ?></li>
							<li><?php echo $horas[1]; ?></li>
							<li><?php echo $horas[2]; ?></li>
							<li><?php echo $horas[3]; ?></li>
							<li><?php echo $horas[4]; ?></li>
							<li><?php echo $horas[5]; ?></li>
						</div>
					</div>
				</div>

				<div class='detail_expand dtl3 detail_description_expand'>
					<h2>Beschreibung</h2>
					<?php echo $row->descricao; ?>
				</div>

				<div id="workphotos" class='detail_expand detail_fotos' style='<?php echo $galeria; ?>'>
					<?php
					foreach($pic as $picture){
						echo "
						<picture>
						  <a class='lightbox' href='#$picture'><img src='".HOME."painel/cadastros/arquivos/newimg/$row->empresa/$picture'></a> 
							<div class='lightbox-target' id='$picture'>
							   <img src='".HOME."painel/cadastros/arquivos/newimg/$row->empresa/$picture'>
							   <a class='lightbox-close' href='#workphotos'></a>
							</div>
						</picture>
						";
					}
					?>
				</div>

				<?php
				$work = $pdo->query("SELECT * FROM works WHERE company = '$row->empresa'");
				$work->execute();
				$show = $work->fetch(PDO::FETCH_OBJ);
				if($show >= 1){
					echo "<div class='workshow'>";
					echo "Vaga de emprego disponível <span><a href='#'>SHOW</a></span>";
					echo "</div>";
				}
				//echo "</div>";
				?>

				<div class="social">
					<li class="but"><a class="fb" href='<?php echo $row->facebook; ?>' target='_blank'><i class='fa fa-facebook'></i></a></li>
					<li class="but"><a class="tt" href='<?php echo $row->twitter; ?>' target='_blank'><i class='fa fa-twitter'></i></a></li>
					<li class="but"><a class="gg" href='<?php echo $row->google; ?>' target='_blank'><i class='fa fa-google'></i></a></li>
					<li class="but"><a class="ml" href='mailto:<?php echo $row->email; ?>'><i class='lnr lnr-envelope'></i></a></li>
					<li class="but"><a class="ws" href='<?php echo $row->website; ?>' target='_blank'><i class='lnr lnr-link'></i></a></li>
				</div>

				<div id='map'></div>

				<a href="?edit=<?php echo $row->id ?>" class="send">editar</a>

			</div>
		<?php
		}
		if($user) { return TRUE; }
	}
}

/* LOGIN LOGS */
function loginlog($user, $addr){
	$pdo=conectar();
	$logcheck = $pdo->query("SELECT * FROM userlogs WHERE addr='$addr' OR session='$user'");
	$verif = $logcheck->fetchColumn();
	$log = $logcheck->fetch(PDO::FETCH_OBJ);
    if($verif > 0) {
    	if($log->count <= 4){
    		$log = $pdo->query("UPDATE userlogs SET session='$user',data=".time().",count=(count+1) WHERE addr='$addr'");
    	}
    	elseif($log->count == 4){
			$log = $pdo->query("UPDATE userlogs SET vezesblock=vezesblock+1 WHERE addr='$addr'");
		}
    }
	else{
		$pdo->query("INSERT INTO userlogs (`session`,`addr`,`data`,`count`, `vezesblock`) VALUES ('$user','$addr',".time().",1,0)");
	}
}

/* LOGIN CHECK USER STATUS */
function checkloginlog(){
	$pdo=conectar();
	$addr = $_SERVER['REMOTE_ADDR'];
	$user = $usuario;
	$check = $pdo->query("SELECT * FROM userlogs WHERE addr='$addr' OR session='$user'");
	$num = $check->fetch(PDO::FETCH_OBJ);
	$tempo = time() - $num->data;
	$_SESSION['time'] = "";
	if($num->count > 4){
		$_SESSION['time'] = 5 - strftime('%M', $tempo);
		$login = "block";
	}
	if($login == "block" && $_SESSION['time'] <= 0){
		 $zerar = $pdo->query("UPDATE userlogs SET data=0,count=0 WHERE addr='$addr' OR session='$user'");
		 $zerar->execute();
		 $login = "";
	}
	return $login;
}

/* MIN-MAX LISTNING ADMIN CP */
$sql = "SELECT * FROM users";
$res = $pdo->prepare($sql);
$res->execute();
$contador = $res->rowCount(PDO::FETCH_ASSOC);

    if(empty($_GET['pag'])){ $pag=1; }else{ $pag = "$_GET[pag]"; }
    if($pag >= '1'){ $pag = $pag; }else{ $pag = '1'; }

$maximo=10;
$inicio = ($pag * $maximo) - $maximo;
$paginas=ceil($contador/$maximo);

/*****************************
 * FUNÇÃO VALIDAR STATUS
 * DESTRUÍR SESSÃO
 ****************************/
function blockcheck($user_id){
	$pdo=conectar();
    $stt = $pdo->query("SELECT status FROM users WHERE idusr='$user_id'");
    $dados = $stt->fetch(PDO::FETCH_OBJ);

    if($dados->status == 2){
    	$upd = $pdo->query("UPDATE users SET active='offline', offtime=NOW() WHERE idusr='$user_id'");
	    session_destroy();
	    (isset($_COOKIE['CookieLembrete']) ? setCookie('CookieLembrete', "", time() - 60*60*24*30) : NULL);
	    header("refresh: 0; url=".HOME);
	}
}

/* LOGOUT USUÁRIO */
function logout($uid, $logout){
	if($logout == 'logout'):
		$pdo=conectar();
	    $upd = $pdo->query("UPDATE users SET active='offline', offtime=NOW() WHERE idusr='$uid'");
		setCookie('CookieLembrete', "", time() - 60*60*24*30);
	    session_destroy();
	    header("Location: ".HOME);
	endif;
}
/* LOGOUT USUÁRIO */
function logout_admin($uid, $logout){
	if($logout == 'logout'):
	    session_destroy();
	    header("Location: ".HOME."admin");
	endif;
}