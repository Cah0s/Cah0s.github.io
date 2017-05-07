
	<div class="content">

		<div class="contentbanner">
			<div class="adversiment bann--728x90_inn" >
				<div class="adv_content">
					<img src="http://www.amtekcompany.com/wp-content/uploads/2015/11/3d-scanning-banner1.jpg" alt="">
				</div>
				<!-- <div class="adv_footer"></div> -->
			</div>
		</div>

		<div class="advancedsearch">
			<div class="advitem">
				<form action="advanced_search" method="post">
					<input type="text" name="city" placeholder="e.g Erfurt">
					<input type="text" name="tags" id="tags" placeholder="e.g Isp, mercado">
					<input type="submit" name="advsearch">
				</form>
			</div>
		</div>

		<?php
		$city    = (isset($_GET['city']) ? $city = $_GET['city'] : $city = $_POST['city']);
		//$event    = (isset($_GET['event']) ? $city = $_GET['event'] : $city = $_POST['event']);
		//$branche = (isset($_GET['branche']) ? $branche = $_GET['branche'] : $branche = $_POST['branche']);
		$tags    = (isset($_GET['tags']) ? $tags = $_GET['tags'] : $tags = $_POST['tags']);
		advanced_search($_GET['uid'], $city, $tags);
		?>

	</div><!-- CONTENT -->

	<div class="adversing">
		
		<div class="adversiment bann--300x600">
			<div class="adv_header">
				<span class="title">Adversiment</span>
			</div>
			
			<div class="adv_content">
				<center>
					<img src="<?php echo HOME;?>/assets/images/OauthLoginBanner.gif" alt="">
					<!-- <img src="http://fisme.org.in/budget-2015/images/banner17_300_600_half_pagead.gif" alt=""> -->
				</center>
			</div>
			<div class="adv_footer"></div>
		</div>
		
		<!-- <div class="sub_adversing">
			kkkk
		</div> -->

	</div>

	<div class="adversiment bann--1000x130 contentbannerbig">
		<div class="adv_content">
			<img src="<?php echo HOME;?>/assets/images/wallbanner_white.gif" alt="">
		</div>
		<!-- <div class="adv_footer"></div> -->
	</div>

