<?php
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
?>
<header>
	
	<div class="item1 mobileitem">
		<div class="logo">
			<!-- <img src="http://materializecss.com/images/responsive.png" alt=""> -->
			<img src="<?php echo HOME; ?>assets/images/logo3.png">
		</div>
	</div>
	
	<div class="item2 mobileitem">
		<nav>
			<ul>
				<li><a href="<?php echo HOME; ?>">HOME</a></li>
				<li>
					<a href="">Exemple</a>
					<ul>
						<li><a href="">Empresarial</a></li>
						<li><a href="">Comercial</a></li>
						<li><a href="">Festas / Eventos</a></li>
					</ul>
				</li>
				<li><a href="<?php echo HOME; ?>jobs">Jobs</a></li>
				<li><a href="">über uns</a></li>
				<li><a href="">Kontakt</a></li>
			</ul>
		</nav>
	</div>
	
	<div class="item3 mobileitem">
		<?php if(!is_checked_in()): ?>
	    <div class="user_menu user_menu_before">
			<a class="log" href="<?php echo HOME; ?>auth"><span><i class="lnr lnr-enter"></i> Login</span></a>
			<a class="log" href="<?php echo HOME; ?>register"><span><i class="lnr lnr-enter"></i> Register</span></a>
		</div>
	    <?php else: ?>
	    <div class="user_menu">
			<!-- <img src="assets/user_img/<?php /*echo $data->img;*/ ?>"/> -->
			<img src="<?php echo HOME; ?>assets/images/semimagem.jpg"/>
			<span class="user">
				<i class="lnr lnr-user"></i>
				<?php echo $_SESSION['usuario']; ?>
			</span>

			<span class="country">
				<i class="lnr lnr-earth"></i>
				Erfurt
				<?php /*echo $data->country;*/ ?>
			</span>
			<ul class="user_sub">
				<li><a href="<?php echo PAINEL; ?>userhome.php" target="_blank"><i class="lnr lnr-eye"></i> Painel</a></li>
				<!-- <li><a href="<?php echo PAINEL; ?>edit.php?post=true&id=<?php echo $_SESSION["id_usr"]; ?>" target="_blank"><i class="lnr lnr-pencil"></i> Posts</a></li> -->
				<li><a href="&user=logout"><i class="lnr lnr-power-switch"></i> Logout</a></li>
			</ul>
		</div>
        <?php endif; ?>
	</div>

	<div class="item4 mobileitem">
		<div id="searching">
			<form action="<?php echo HOME; ?>data/advanced_search" method="POST">
			<input type="text" class="search" name="city" placeholder="searching" autocomplete="off">
			<i class="icon-search searchicon"></i>
			<select name="type" id="type" class="typesearch">
				<option value="stadt">stadt</option>
				<!-- <option value="estado">estado</option> -->
				<option value="unternehmen">unternehmen</option>
				<!-- <option value="evento">evento</option> -->
				<option value="branche">branche</option>
			</select>
			</form>
			<div id="display"></div>
		</div>
	</div>
</header>
<!-- http://pt.gravatar.com/avatar/81f5ccdadd1511d3ac73168e17502c77 -->

<div class="wrapper site-flex-content">
