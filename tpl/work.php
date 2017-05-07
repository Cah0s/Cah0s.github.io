<?php
$match = "/(from|select|insert|delete|where|drop table|show tables|order by|group|union|all|#|--|0x|'|\"|´|`|0x27|%27|%00|%3C|%3E|&lt|&gt|marquee|scr|IMG|svg|onload|alert|uname|ls|dir|tree)/i";
$uid = preg_replace($match, "", $_GET['uid']);
$tabela = preg_replace($match, "", $_GET['show']);

$display = "style='display:none;'";
$widthdiv = "style='width:100%;'";

$pdo=conectar();
$map = $pdo->query("SELECT * FROM $tabela WHERE id='$uid'");
$coord = $map->fetch(PDO::FETCH_OBJ);

//$map->execute();
?>
	<div class="content" <?php //echo $widthdiv; ?>>

		<div class="contentbanner" <?php //echo $display; ?>>
			<div class="adversiment bann--728x90_inn" >
				<div class="adv_content">
					<img src="http://www.amtekcompany.com/wp-content/uploads/2015/11/3d-scanning-banner1.jpg" alt="">
				</div>
				<!-- <div class="adv_footer"></div> -->
			</div>
		</div>
		<?php
		$lat = 0;
		$lng = 0;

		$url = "https://maps.google.com/maps/api/geocode/json?address=".str_replace(" ", "+", $coord->address);
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
		?>
		<?php joblist($uid, $tabela); ?>

	</div><!-- CONTENT -->

	<div class="adversing">
		
		<div class="adversiment bann--300x600" <?php //echo $display; ?>>
			<div class="adv_header">
				<span class="title">Adversiment</span>
			</div>
			
			<div class="adv_content">
				<center>
					<img src="<?php echo HOME;?>/assets/images/OauthLoginBanner.gif" alt="">
				</center>
			</div>
			<div class="adv_footer"></div>
		</div>
		
		<!-- <div class="sub_adversing">
			kkkk
		</div> -->

	</div>

	<div class="adversiment bann--1000x130 contentbannerbig" <?php //echo $display; ?>>
		<div class="adv_content">
			<img src="<?php echo HOME;?>/assets/images/wallbanner_white.gif" alt="">
		</div>
		<!-- <div class="adv_footer"></div> -->
	</div>
