<?php

/*
 * Função formatar_tempo()
 * 
 * Está função retorna o tempo em que determinada ação ocorreu.
 * Exemplo:
 *  - Postagem em um blog:
 *    3 minutos atrás
 *    7 dias atrás
 *    2 meses atrás
 * 
 * e assim por diante.
 * 
 * COMO USAR
 * 
 * Insira no banco de dados o tempo em segundos usando a função time()
 * Quando quiser exibir o tempo passado é só chamar a função formatar_tempo()
 * e passar como parametro o valor que foi inserido no banco de dados.
 * 
 * Script feito por: Túlio Spuri <tulios@comp.ufla.br>
 * 
 * Qualquer dúvida é só entrar em contato <tulios@comp.ufla.br>
 * 
 */

function formatar_tempo($timeBD) {

	$timeNow = time();
	$timeRes = $timeNow - $timeBD;
	$nar = 0;
	
	// variável de retorno
	$r = "";

	// Agora
	if ($timeRes == 0){
		$r = "agora";
	} else
	// Segundos
	if ($timeRes > 0 and $timeRes < 60){
		$r = $timeRes. " segundos atr&aacute;s";
	} else
	// Minutos
	if (($timeRes > 59) and ($timeRes < 3599)){
		$timeRes = $timeRes / 60;	
		if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
			$r = round($timeRes,$nar). " minuto atr&aacute;s";
		} else {
			$r = round($timeRes,$nar). " minutos atr&aacute;s";
		}
	}
	 else
	// Horas
	// Usar expressao regular para fazer hora e MEIA
	if ($timeRes > 3559 and $timeRes < 85399){
		$timeRes = $timeRes / 3600;
		
		if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
			$r = round($timeRes,$nar). " hora atr&aacute;s";
		}
		else {
			$r = round($timeRes,$nar). " horas atr&aacute;s";		
		}
	} else
	// Dias
	// Usar expressao regular para fazer dia e MEIO
	if ($timeRes > 86400 and $timeRes < 2591999){
		
		$timeRes = $timeRes / 86400;
		if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
			$r = round($timeRes,$nar). " dia atr&aacute;s";
		} else {

			preg_match('/(\d*)\.(\d)/', $timeRes, $matches);
			
			if ($matches[2] >= 5) {
				$ext = round($timeRes,$nar) - 1;
				
				// Imprime o dia
				$r = $ext;
				
				// Formata o dia, singular ou plural
				if ($ext >= 1 and $ext < 2){ $r.= " dia "; } else { $r.= " dias ";}
				
				// Imprime o final da data
				$r.= "&frac12; atr&aacute;s";
				
				
			} else {
				$r = round($timeRes,0) . " dias atr&aacute;s";
			}
			
		}		
				
	} else
	// Meses
	if ($timeRes > 2592000 and $timeRes < 31103999){

		$timeRes = $timeRes / 2592000;
		if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
			$r = round($timeRes,$nar). " mes atr&aacute;s";
		} else {

			preg_match('/(\d*)\.(\d)/', $timeRes, $matches);
			
			if ($matches[2] >= 5){
				$ext = round($timeRes,$nar) - 1;
				
				// Imprime o mes
				$r.= $ext;
				
				// Formata o mes, singular ou plural
				if ($ext >= 1 and $ext < 2){ $r.= " mes "; } else { $r.= " meses ";}
				
				// Imprime o final da data
				$r.= "&frac12; atr&aacute;s";
			} else {
				$r = round($timeRes,0) . " meses atr&aacute;s";
			}
			
		}
	} else
	// Anos
	if ($timeRes > 31104000 and $timeRes < 155519999){
		
		$timeRes /= 31104000;
		if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
			$r = round($timeRes,$nar). " ano atr&aacute;s";
		} else {
			$r = round($timeRes,$nar). " anos atr&aacute;s";
		}
	} else
	// 5 anos, mostra data
	if ($timeRes > 155520000){
		
		$localTimeRes = localtime($timeRes);
		$localTimeNow = localtime(time());
				
		$timeRes /= 31104000;
		$gmt = array();
		$gmt['mes'] = $localTimeRes[4];
		$gmt['ano'] = round($localTimeNow[5] + 1900 - $timeRes,0);				
					
		$mon = array("Jan ","Fev ","Mar ","Abr ","Mai ","Jun ","Jul ","Ago ","Set ","Out ","Nov ","Dez "); 
		
		$r = $mon[$gmt['mes']] . $gmt['ano'];
	}
	
	return $r;

}

?>