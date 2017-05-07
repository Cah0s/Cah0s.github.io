<?php
$match = "/(from|select|insert|delete|where|drop table|show tables|order by|group|union|all|#|--|0x|'|\"|´|`|0x27|%27|%00|%3C|%3E|&lt|&gt|marquee|scr|IMG|svg|onload|alert|uname|ls|dir|tree)/i";
$uid = preg_replace($match, "", $_GET['uid']);
$tabela = preg_replace($match, "", $_GET['show']);
$display = "style='display:none;'";
$widthdiv = "style='width:100%;'";
$pdo=conectar();
// $map = $pdo->query("SELECT * FROM $tabela WHERE id='$uid'");
// $coord = $map->fetch(PDO::FETCH_OBJ);
//$map->execute();
?>
	<div class="content" >

		<div class="contentbanner" >
			<div class="adversiment bann--728x90_inn" >
				<div class="adv_content">
					<img src="http://www.amtekcompany.com/wp-content/uploads/2015/11/3d-scanning-banner1.jpg" alt="">
				</div>
				<!-- <div class="adv_footer"></div> -->
			</div>
		</div>

		<div class="advancedsearch">
			<div class="advitem">
				<form action="jobs" method="post">
					<input type="text" name="city" placeholder="e.g Erfurt">
					<input type="text" name="tags" id="tags" placeholder="e.g Isp, mercado">
					<input type="submit" name="advsearch">
				</form>
			</div>
		</div>

		<?php
		$city    = (isset($_GET['city']) ? $city = $_GET['city'] : $city = $_POST['city']);
		$tags    = (isset($_GET['tags']) ? $tags = $_GET['tags'] : $tags = $_POST['tags']);
		jobs('works', $city, $tags);
		?>

	</div><!-- CONTENT -->

	<div class="adversing">
		
		<div class="adversiment bann--300x600" >
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

	<div class="adversiment bann--1000x130 contentbannerbig" >
		<div class="adv_content">
			<img src="<?php echo HOME;?>/assets/images/wallbanner_white.gif" alt="">
		</div>
		<!-- <div class="adv_footer"></div> -->
	</div>