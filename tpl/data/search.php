<?php
require_once '../../system/database.php';
if($_POST){
	$q = $_POST['searchword'];
	$t = $_POST['type'];

	echo '<div class="display_box" align="left">';
	
	if($t == "stadt"){
		$sql = "SELECT * FROM cidades JOIN estados ON eid = estado_id WHERE cidade LIKE '$q%' LIMIT 4";
		$data = $pdo->prepare($sql);
		$data->execute();

		while($row = $data->fetch(PDO::FETCH_OBJ)) {
			$region = $row->region;
			//$brasao = $row->bandeira;
			$cidade = $row->cidade;
			$estado = $row->estado;
			$populacao  = $row->populacao;
			$re_cidade  = '<b>'.$q.'</b>';
			$re_estado  = '<b>'.$q.'</b>';
			$final_cidade = str_ireplace($q, $re_cidade, $cidade);
			$final_estado = str_ireplace($q, $re_estado, $estado);
			($row->brasao == '0' || $row->brasao == "" ) ? $brasao = "semimagem.jpg" : $brasao = "cidades/$row->brasao";

		?>
		<div class="box">
		<img src="<?php echo HOME; ?>assets/images/<?php echo $brasao; ?>"/>
			<span class="city"> 
				<?php echo "<a href='".HOME."data/advanced_search&city=$cidade' target='_blank'>".$final_cidade. "</a>"; ?>
			</span>
			<br class="clear">

			<span class="country">
				<i class="lnr lnr-earth"></i> <?php echo $final_estado; ?>&nbsp;
				<i class="lnr lnr-map-marker"></i> <?php echo $region; ?>&nbsp;
			</span>
		</div>
	<?php
		}
	?>
	<div class='box_footer'>
		<a href='<?php echo HOME; ?>data/advanced_search&city=<?php echo $q; ?>' target='_blank' style='font-color:#f9f9f9;'>Pesquisa avançada para '<?php echo $q; ?>'.</a>
	</div>
	<?php
	}//CIDADE

	if($t == "branche"){
		$sql = "SELECT * FROM empresas WHERE categoria LIKE '$q%' AND visivel = 'sim' LIMIT 5";
		$data = $pdo->prepare($sql);
		$data->execute();
		
		echo '<div class="display_box" align="left">';

		while($row = $data->fetch(PDO::FETCH_OBJ)) {
			$empresa = $row->empresa;
			//$logo = $row->logo;
			$branche = $row->categoria;
			$re_branche  = '<b>'.$q.'</b>';
			$final_branche = str_ireplace($q, $re_branche, $branche);
			($row->logo == '0' || $row->logo == "" ) ? $logo = "semimagem.jpg" : $logo = "empresas/$row->logo";

		?>
		<div class="box">
		<img src="<?php echo HOME; ?>assets/images/<?php echo $logo; ?>"/>
			<span class="city"> 
				<?php echo "<a href='".HOME."data/advanced_search&uid=$empresa' target='_blank'>".$empresa. "</a>"; ?>
			</span>
			<br class="clear">

			<span class="country">
				<i class="lnr lnr-flag"></i> <?php echo $final_branche; ?>
			</span>
		</div>
	<?php
		}
	?>
	<div class='box_footer'>
		<a href='<?php echo HOME;?>data/advanced_search&uid=<?php echo $q; ?>' target='_blank' style='font-color:#f9f9f9;'>Pesquisa avançada para '<?php echo $q; ?>'.</a>
	</div>
	<?php
	}//ESTADO
	

	if($t == "unternehmen"){
		$sql = "SELECT * FROM empresas JOIN cidades ON cid = cid_id JOIN estados ON eid = esd_id WHERE visivel = 'sim' AND empresa LIKE '%$q%' LIMIT 5";
		$data = $pdo->prepare($sql);
		$data->execute();
		
		echo '<div class="display_box" align="left">';

		while($row = $data->fetch(PDO::FETCH_OBJ)) {
			$empresa = $row->empresa;
			$branche = $row->categoria;
			$cidade = $row->cidade;
			$estado = $row->estado;
			$re_empresa  = '<b>'.$q.'</b>';
			$final_empresa = str_ireplace($q, $re_empresa, $empresa);
			($row->logo == '0' || $row->logo == "" ) ? $logo = "semimagem.jpg" : $logo = "empresas/$row->logo";

		?>
		<div class="box">
		<img src="<?php echo HOME; ?>assets/images/<?php echo $logo; ?>"/>
			<span class="city"> 
				<?php echo "<a href='".HOME."show&show=empresas&uid=$row->id' target='_blank'>".$final_empresa. "</a>"; ?>&nbsp;
			</span>
			<br class="clear">

			<span class="country">
				<i class="lnr lnr-apartment"></i> <?php echo $cidade; ?>&nbsp;
				<i class="lnr lnr-earth"></i> <?php echo $estado; ?>&nbsp;
				<i class="lnr lnr-flag"></i> <?php echo $branche; ?>
			</span>
		</div>
	<?php
		}
	?>
	<div class='box_footer'>
		<a href='<?php echo HOME;?>data/advanced_search&uid=<?php echo $q; ?>' target='_blank' style='font-color:#f9f9f9;'>Pesquisa avançada para '<?php echo $q; ?>'.</a>
	</div>
	<?php
	}//EMPRESA

	if($t == "evento"){
		$sql = "SELECT * FROM eventos JOIN cidades ON cid = cid_id_ev JOIN estados ON eid = esd_id_ev WHERE visivel = 'sim' AND evento LIKE '%$q%' LIMIT 5";
		$data = $pdo->prepare($sql);
		$data->execute();
		
		echo '<div class="display_box" align="left">';

		while($row = $data->fetch(PDO::FETCH_OBJ)) {
			$evento = $row->evento;
			$cidade = $row->cidade;
			$estado = $row->estado;
			$re_evento  = '<b>'.$q.'</b>';
			$final_evento = str_ireplace($q, $re_evento, $evento);
			($row->logo == '0' || $row->logo == "" ) ? $logo = "semimagem.jpg" : $logo = "empresas/$row->logo";

		?>
		<div class="box">
		<img src="<?php echo HOME; ?>assets/images/<?php echo $logo; ?>"/>
			<span class="city"> 
				<?php echo "<a href='".HOME."show&show=eventos&uid=$row->id' target='_blank'>".$final_evento. "</a>"; ?>&nbsp;
			</span>
			<br class="clear">

			<span class="country">
				<i class="lnr lnr-apartment"></i> <?php echo $cidade; ?>&nbsp;
				<i class="lnr lnr-earth"></i> <?php echo $estado; ?>&nbsp;
				<i class="lnr lnr-flag"></i> <?php echo $branche; ?>
			</span>
		</div>
	<?php
		}
	?>
	<div class='box_footer'>
		<a href='<?php echo HOME;?>data/advanced_search&event=<?php echo $q; ?>' target='_blank' style='font-color:#f9f9f9;'>Pesquisa avançada para '<?php echo $q; ?>'.</a>
	</div>
	<?php
	}//EVENTO
echo "</div>";
}
?>