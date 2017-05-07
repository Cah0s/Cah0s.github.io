var HOME = 'http://localhost/meineprojekte/relacionamentobd/';
/* CARREGAMENTO E ALIMENTAÇÃO DOS SELECTS NO
FORMULÁRIO DE CADASTRO DE NOVAS EMPRESAS */
$(function() {
    $('.j_loadstate').change(function() {
        var uf = $('.j_loadstate');
        var city = $('.j_loadcity');
        //var HOME = 'http://localhost/meineprojekte/relacionamentobd/';
        var patch = HOME + 'tpl/users/panel/cadastros/city.php';

        city.attr('disabled', 'true');
        uf.attr('disabled', 'true');

        city.html('<option value=""> Carregando cidades... </option>');

        $.post(patch, {estado: $(this).val()}, function(cityes) {
            city.html(cityes).removeAttr('disabled');
            uf.removeAttr('disabled');
        });
    });
});

/* EFEITO E CADASTRO DE NOVAS EMPRESAS */
var working = false;
$('#myform').on('submit', function(e) {
  e.preventDefault();
  
  if (working) return;
  working = true;
  
  var $this = $(this),
  $state = $this.find('button > .state');
  $this.addClass('loading');
  $state.html('Authenticating');
  
  $(this).ajaxSubmit({
        url: HOME + 'tpl/users/panel/cadastros/register.php',
        data: {acao: "cadastro"},
        beforeSubmit: function(){
          $('#result').html('loading').show();
        },
        error: function(){
        	$('#result').html('erro').show();
        },
        success:   function( resposta ){
        	$this.addClass('ok');
    		$state.html('Empresa Registrada!');
    		$('#result').html(resposta).show();
        }
  });

  setTimeout(function() {
      $state.html('Register');
      $this.removeClass('ok loading');
      working = false;
    }, 4000);
  
  // setTimeout(function() {
  //   $this.addClass('ok');
  //   $state.html('Welcome back!');
  //   setTimeout(function() {
  //     $state.html('Log in');
  //     $this.removeClass('ok loading');
  //     working = false;
  //   }, 4000);
  // }, 3000);
});