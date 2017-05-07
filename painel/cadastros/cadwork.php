<?php
sleep(2);
require '../restrict/configs.php';
include '../../system/functions.php';
//$user_cad = $_SESSION['usuario'];

if(isset($_POST['newwork']) && $_POST['newwork'] == 'sim'):

	$novos_campos = array();
	$campos_post = $_POST['campos'];

    $respostas = array();
	foreach($campos_post as $indice => $valor){
		$novos_campos[$valor['name']] = $valor['value'];
	}

    //if(!strstr($novos_campos['email'], '@')){
    if(empty($novos_campos['work'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Informe o nome da vaga';
    }
    elseif(!filter_var($novos_campos['mail'], FILTER_VALIDATE_EMAIL)){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Email inválido, revise-o por favor!<br> verifique o @ e/ou .dominio';
    }
    elseif(empty($novos_campos['phone'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Sie müssen ein Phone angeben';
    }
    elseif(empty($novos_campos['address'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Gebe eine Addresse an';
    }
    elseif(empty($novos_campos['description'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Descreva a vaga de emprego!';
    }
    elseif(empty($novos_campos['web']) && !strstr("http://", $novos_campos['web'])){
        $respostas['erro'] = 'sim';
        $respostas['getErro'] = 'Informe um Website';
    }
    else{
        $respostas['erro'] = 'nao';

        $work = $pdo->prepare("SELECT * FROM works WHERE work=:work");
        $result  = $work->execute(array('work' => $novos_campos['work']));
        $verif   = $work->fetch();
        
        if($verif !== false) {
          $respostas['erro'] = 'sim';
          $respostas['getErro'] = 'Esta Vaga ja está cadastrada em nossos sistemas';
        }
        else{

            $insert_db = $pdo->prepare("INSERT INTO `works` SET company = ?, work = ?, workowner = '$usuario', mail = ?, phone = ?, address = ?, description = ?, web = ?, data=".time().", visible = 'nao', viewsworks = 0");

            $array_sql = array(
                $novos_campos['company'],
                $novos_campos['work'],
                $novos_campos['mail'],
                $novos_campos['phone'],
                $novos_campos['address'],
                $novos_campos['description'],
                $novos_campos['web']
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

/*
Vaga teste de Pentester Profissional
secsystemmail.com
(00) 0000-0000
Eislebender Str. 5, 122, 99086 Erfurt - Thüringen
Nenhuma descrição para a vaga atual
secsystem.com
*/
?>