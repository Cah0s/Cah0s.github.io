	$(document).ready(function(){
		comecar();
	});
	var timerI = null;
	var timerR = false;

	function parar(){
		if(timerR)
			clearTimeout(timerI);
		timerR = false;
	}
	function comecar(){
		parar();
		listar();
	}

	function listar(){
			$.ajax({
			url:"status.php",
				success: function (textStatus){
					$('#lista').html(textStatus); //mostrando resultado
				}
			});
			timerI = setTimeout("listar()", 1000);//tempo de espera
		    timerR = true;
	}