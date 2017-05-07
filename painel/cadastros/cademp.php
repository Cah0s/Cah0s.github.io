<?php
sleep(2);
require '../restrict/configs.php';
include '../../system/functions.php';
//$user_cad = $_SESSION['usuario'];
$urlverif = "/^http[s]?:\/\/(www\.)?[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/";
$socverif = "~^(https?://)?(www\.)?[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.com/\w{5,}$~i";

if(isset($_POST['cadastrar']) && $_POST['cadastrar'] == 'sim'):

	$novos_campos = array();
	$campos_post = $_POST['campos'];

    $respostas = array();
	foreach($campos_post as $indice => $valor){
		$novos_campos[$valor['name']] = $valor['value'];
	}

    /* HORÁRIOS */
    $segunda = 'Segunda ' .$novos_campos['horasegini']. ' ás ' .$novos_campos['horasegfin'];
    $terca   = 'Terça '   .$novos_campos['horaterini']. ' ás ' .$novos_campos['horaterfin'];
    $quarta  = 'Quarta '  .$novos_campos['horaquaini']. ' ás ' .$novos_campos['horaquafin'];
    $quinta  = 'Quinta '  .$novos_campos['horaquiini']. ' ás ' .$novos_campos['horaquifin'];
    $sexta   = 'Sexta '   .$novos_campos['horasexini']. ' ás ' .$novos_campos['horasexfin'];
    $sabado  = 'Sábado '  .$novos_campos['horasabini']. ' ás ' .$novos_campos['horasabfin'];
    $domingo = 'Domingo ' .$novos_campos['horadomini']. ' ás ' .$novos_campos['horadomfin'];
    $horario = $segunda.",".$terca.",".$quarta.",".$quinta.",".$sexta.",".$sabado.",".$domingo;

    //if(!strstr($novos_campos['email'], '@')){
    if(empty($novos_campos['empresa'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Diga o nome da empresa';

    }
    elseif(!filter_var($novos_campos['email'], FILTER_VALIDATE_EMAIL)){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Email inválido, revise-o por favor!<br> verifique o @ e/ou .dominio';

    }
    elseif(empty($novos_campos['telefone'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Há um erro no Telefone, o reveja por favor!<br>formatos válidos: (xx) 1234-5678 ou (xx) 1234-56789';

    }
    elseif(empty($novos_campos['empresa_estado'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Selecione o estado onde a empresa fica';

    }
    elseif(empty($novos_campos['empresa_cidade'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Seleciona a cidade onde a empresa fica';

    }
    elseif(empty($novos_campos['endereco'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'onde fica a empresa?';

    }
    elseif(empty($novos_campos['descricao'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Descreva sobre a empresa';

    }
    elseif(empty($novos_campos['categoria'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Qual é o tipo de sua empresa?';

    }
    // elseif(empty($novos_campos['website'])){
    elseif(!preg_match("$urlverif", $novos_campos['website'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Verifique a URL do site [http|https]';

    }
    // elseif(!strstr($novos_campos['facebook'], 'https://')){
    elseif(!preg_match("$socverif", $novos_campos['facebook'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Houve um erro na Url do facebook, revise-a por favor!';

    }
    // elseif(!strstr($novos_campos['twitter'], 'https://')){
    elseif(!preg_match("$socverif", $novos_campos['twitter'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Houve um erro na Url do Twitter, revise-a por favor!';

    }
    // elseif(!strstr($novos_campos['google'], 'https://')){
    elseif(!preg_match("$socverif", $novos_campos['google'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Houve um erro na Url do Google+, revise-a por favor!';

    }
    elseif(empty($_FILES['emplogo'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Nenhuma imagem selecionada';
    }
    else{
        $respostas['erro'] = 'nao';

        $empresa = $pdo->prepare("SELECT * FROM empresas WHERE empresa=:empresa");
        $result  = $empresa->execute(array('empresa' => $novos_campos['empresa']));
        $verif   = $empresa->fetch();
        
        if($verif !== false) {
          $respostas['erro'] = 'sim';
          $respostas['getErro'] = 'Esta Empresa ja está cadastrada em nossos sistemas';
        }
        else{

        //if($respostas['erro'] = 'nao'){

            $insert_db = $pdo->prepare("INSERT INTO `empresas` SET cid_id = ?, esd_id = ?, user_cad = '$usuario', data=".time().", empresa = ?, endereco = ?, horarios = '$horario', telefone = ?, email = ?, descricao = ?, facebook = ?, twitter = ?, google = ?, website = ?, mapa = ?, fotos = '', logo = ?, categoria = ?,  visivel = 'sim', views = 0");

            $array_sql = array(
                $novos_campos['empresa_cidade'],
                $novos_campos['empresa_estado'],
                $novos_campos['empresa'],
                $novos_campos['endereco'],
                $novos_campos['telefone'],
                $novos_campos['email'],
                $novos_campos['descricao'],
                $novos_campos['facebook'],
                $novos_campos['twitter'],
                $novos_campos['google'],
                $novos_campos['website'],
                $novos_campos['endereco'],
                $novos_campos['emplogo'],
                $novos_campos['categoria']
            );
            if($insert_db->execute($array_sql)){
                $respostas['msg'] = 'Concluído com Sucesso :)';
            }else{
                $respostas['erro'] = 'sim';
                $respostas['getErro'] = 'Houve uma falha no Banco de dados<br>Desculpe tente novamente mais tarde :(';
            }
        }
    }

    echo json_encode($respostas);
endif;
?>