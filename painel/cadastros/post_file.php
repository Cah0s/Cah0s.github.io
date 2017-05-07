<?php
require '../../system/database.php';

$error = false;
$upload_dir = 'arquivos';

switch($_POST['acao']){
  case 'newimg':
  	$mywork  = $_POST['mywork'];
	$imagens = $_FILES['pic'];
	
	if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){ exit_status('Error! Wrong HTTP method!');
		$error= true;
	}
	if($imagens == null){
		?>
        <script>
          iziToast.info({
              title: 'INFO',
              message: '<?php echo "Não ha imagens selecionadas!"; ?>',
              position: 'center',
          });
        </script>
        <?php
		$error = true;
	}

	/* TRATANDO AS IMAGENS DA EMPRESA */
	$total = count($imagens['name']);
	if($total > 5){
		?>
		<script>
			iziToast.error({
			    title: 'REVISE',
			    message: '<?php echo 'Você só pode enviar 5 imagens, acima disso não serão enviadas!'; ?>',
			    position: 'center',
			});
		</script>
		<?php
		$error = true;
	}
	for($i = 0; $i < $total; $i++){
	    $perm = array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'text/plain');
	    $ext  = ($imagens['type'][$i] == 'image/png' ? '.png' : '.jpg');
	    $size = 1024*1024*2; //2MB
	    
	    if($imagens['size'][$i] > $size){
	        ?>
			<script>
				iziToast.warning({
				    title: 'REVISE',
				    message: '<?php echo 'A imagem <b><i>'.$imagens['name'][$i].'</i></b> é maior que 2MB e não é permitido passar disso'; ?>',
				    position: 'center',
				});
			</script>
			<?php
	        $error= true;
	    }elseif(!in_array($imagens['type'][$i], $perm)){
	    	?>
			<script>
				iziToast.warning({
				    title: 'AVISO',
				    message: '<?php echo $imagens['name'][$i]." inválida. Apenas imagens tipo [jpg, png] são aceito!"; ?>',
				    position: 'center',
				});
			</script>
			<?php
	        $error= true;
	    }
	    else{
	        $pasta = ($imagens['type'][$i] == 'text/plain' ? 'doc' : 'newimg');
	        $pasta = $upload_dir.'/'.$pasta;
	        if(!file_exists($pasta)) mkdir($pasta, 0777);
	        $final_dir = $pasta.'/'.$mywork;
	        if(!file_exists($final_dir)) mkdir($final_dir, 0777);
	        $nome  = md5($imagens['name'][$i]).$ext;
	        /* ARMAZENANDO TODAS IMAGENS EM UM ARRAY */
	        $img[] = $nome;

	        if(file_exists($final_dir."/".$nome)){
	        	?>
				<script>
					iziToast.warning({
					    title: 'REVISE',
					    message: '<?php echo $imagens['name'][$i]. " já existe! não foi possivel upar arquivo selecionado."; ?>',
					    position: 'center',
					});
				</script>
				<?php
	        	$error= true;
	        }

			/* CHAMANDO FOTOS DO BANCO */
	    	$check = $pdo->query("SELECT fotos FROM empresas WHERE empresa='$mywork'");
	    	$dados = $check->fetch(PDO::FETCH_OBJ);

	        $foto_exp = (preg_match("/,/", $dados->fotos) ? explode(",", $dados->fotos) : $dados->fotos = NULL);
	        $foto_count = count($foto_exp);
	        if($foto_count == 5){
	        	?>
				<script>
					iziToast.info({
					    title: 'REVISE',
					    message: '<?php echo "Ja tem 5 fotos upadas, delete alguma para poder upar mais"; ?>',
					    position: 'center',
					});
				</script>
				<?php
	        	$error = true;
	        }
	    }
	}

	/* CONTANTO A QUANTIDADE TOTAL DE IMAGENS, BANCO+NOVAS */
	if((count($img) + $foto_count) > 5){
		$limit_count = (5 - $foto_count);
		$limit = ($limit_count == 1) ? $limit_count." nova" : $limit_count." novas" ;
		?>
		<script>
			iziToast.info({
			    title: 'REVISE',
			    message: '<?php echo "Você tem ".($foto_count)." imagens, seu limite são 5. Você pode upar apenas mais $limit imagens."; ?>',
			    position: 'topRight',
			});
		</script>
		<?php
    	$error = true;
    }

    if(!$error){
	    /* FORMATANDO UPLOAD E INSERIÇÃO NO BANCO */
	    $images = implode(",", $img);
	    /* SE HAVER IMAGENS NO BANCO ELE CONCATENA AS NOVAS COM AS JA EXISTENTES, SE NÃO VAI TUDO AS NOVAS */
	    $final_images = ($dados->fotos != "") ? $images.",".$dados->fotos : $images;
	    $count_final_images = explode(",", $final_images);

	    foreach ($count_final_images as $key => $insertimg):
		    move_uploaded_file($imagens['tmp_name'][$key], $final_dir.'/'.$insertimg);
		    ?>
		    <script>
				iziToast.success({
				    title: 'OK',
				    message: '<?php echo "File $insertimg was uploaded successfuly!"; ?>',
				});
			</script>
			<?php
			$update = $pdo->query("UPDATE empresas SET fotos='$final_images' WHERE empresa='$mywork'");
		endforeach;

		if($update) {
			?>
			<script>
				iziToast.info({
				    title: 'OK',
				    message: '<?php echo "atualizado as fotos para $mywork"; ?>',
				});
			</script>
			<?php
		} else {
			?>
			<script>
				iziToast.error({
				    title: 'FATAL ERROR',
				    message: '<?php echo "Houve um erro ao processar seu pedido"; ?>',
				    position: 'center',
				});
			</script>
			<?php
		}
	}

  case 'imgcheck':

  	$imgcheck = $pdo->query("SELECT * FROM empresas WHERE user_cad='$usuario' "); //empresa='Tecmundo'");
  	$dados = $imgcheck->fetch(PDO::FETCH_OBJ);

	if($dados->fotos != 0 || $dados->fotos != ""):
		$foto_exp = explode(",", $dados->fotos);
		echo "<div id='checkimg'>";
		foreach ($foto_exp as $foto) {
			echo "
			<div class='imgbox'>
				<span id='del'><a href='?imgdel=$foto'><i class='lnr lnr-cross'></i></a></span>
				<img src='".HOME."painel/cadastros/arquivos/newimg/$dados->empresa/$foto'>
			</div>
			";
		}
		echo "</div>";
	else:
		?>
	      <script>
	        iziToast.info({
	            title: 'AVISO',
	            message: '<?php echo "Nenhuma foto encontrada"; ?>',
	            position: 'topRight',
	        });
	      </script>
	    <?php
	endif;
}

// Helper functions
function exit_status($str){
	echo "<font color='#f9f9f9'>".json_encode(array('status'=>$str), JSON_UNESCAPED_UNICODE)."</font><br>";
	//exit;
}
?>