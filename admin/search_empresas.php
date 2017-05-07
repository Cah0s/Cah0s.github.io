<?php
    require_once '../system/functions.php';

    //recebemos nosso parâmetro vindo do form
    $parametro = isset($_POST['pesquisaEmpresa']) ? $_POST['pesquisaEmpresa'] : null;
    $msg = "";

    //requerimos a classe de conexão
    require_once('class/Conexao.class.php');
        try {
            $pdo = new Conexao(); 
            $resultado = $pdo->select("SELECT * FROM empresas WHERE empresa LIKE '%$parametro%' OR categoria LIKE '%$parametro%' ORDER BY id ASC");
            $pdo->desconectar();
                    
        }catch (PDOException $e){
            echo $e->getMessage();
        }   
        //resgata os dados na tabela
        if(count($resultado)){
            foreach ($resultado as $res) {
            
            $data = strftime("%d/%m/%Y",$res['data']);
            $status = ($res['visivel'] == "sim") ? "yesview" : "notview" ;
            $msg .="<div id='firmenlist'>";
            $msg .="    <div class='firmentitle'><small class='trigger $status status'></small> ".$res['empresa'];
            $msg .="    <a href='?idEmpresa=".$res['empresa']."'><i class='lnr lnr-eye'></i></a> ";
            $msg .="    <span>$data ";
            $msg .="    <a href='?edit=".$res['empresa']."&uid=".$res['id']."'><i class='lnr lnr-pencil'></i></a> ";
            $msg .="    <a href='?del=".$res['empresa']."&uid=".$res['id']."'><i class='lnr lnr-cross'></i></a>";
            $msg .="    </span></div>";
            $msg .="    <br class='clear'>";
            $msg .="</div>";
            }
        }else{
            $msg = "";
            $msg .="Nenhum resultado foi encontrado...";
        }

    //retorna a msg concatenada
    echo $msg; 
?>