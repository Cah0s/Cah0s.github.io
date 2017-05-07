<?php
ini_set("display_errors", 0);
require 'system/database.php';

 $uid = (empty($_GET['uid'])) ? NULL : $_GET['uid'];
 $create = (empty($_GET['create'])) ? NULL : $_GET['create'];
 $read = (empty($_GET['read'])) ? NULL : $_GET['read'];
 $update = (empty($_GET['update'])) ? NULL : $_GET['update'];
 $delete = (empty($_GET['delete'])) ? NULL : $_GET['delete'];

 $attributes = [
  	'email'  => 'kopp.design@gmail.com',
 	'login'  => 'skopp',
 	'senha'  => hash('sha1', 'kopp'),
 	'tipo'   => 'mod',
 	'active' => '1'
 ];

 if(isset($create) && $create == 'true'):
 	$action = creat('painel', $attributes);
 	if($action){
 		echo "Criado com sucesso";
 	}
 	else{
 		echo "Erro ao criar!";
 	}
 endif;

 if(isset($read) && $read == 'true'):
 	$action = read($uid, 'painel', 'postagens');
 	//$action = user_read($uid, 'empresas', 'users');;
 	if(!$action){
 		echo "Erro ao Ler Dados!";
 	}
 endif;

 if(isset($update) && $update == 'true'):
 	$action = update($uid, 'painel', $attributes);
 	if($action){
 		echo "Atualizado com sucesso";
 	}
 	else{
 		echo "Erro ao Atualizar!";
 	}
 endif;

 if(isset($delete) && $delete == 'true'):
 	$action = del('id', $uid, 'painel');
 	if($action){
 		echo "Deletado com sucesso";
 	}
 	else{
 		echo "Erro ao Deletar!";
 	}
 endif;


?>