<?php
    require_once '../system/functions.php';

    //recebemos nosso parâmetro vindo do form
    $parametro = isset($_POST['pesquisaCliente']) ? $_POST['pesquisaCliente'] : null;
    $msg = "";
    

    //começamos a concatenar nossa tabela
    $msg .="<table>";
    $msg .="    <thead>";
    $msg .="        <tr>";
    $msg .="            <th>#</th>";
    $msg .="            <th>Nome</th>";
    $msg .="            <th>SobreNome</th>";
    $msg .="            <th>Login</th>";
    $msg .="            <th>E-mail</th>";
    $msg .="            <th>Create</th>";
    $msg .="            <th class='txt_cen'>Ativo</th>";
    $msg .="            <th class='txt_cen'>Modify</th>";
    $msg .="        </tr>";
    $msg .="    </thead>";
    $msg .="    <tbody>";

    //requerimos a classe de conexão
    require_once('class/Conexao.class.php');
        try {
            $pdo = new Conexao(); 
            $resultado = $pdo->select("SELECT * FROM users WHERE forname LIKE '%$parametro%' OR lastname LIKE '%$parametro%' OR emailusr LIKE '%$parametro%' OR active LIKE '%$parametro%' ORDER BY idusr ASC");
            $pdo->desconectar();
                    
        }catch (PDOException $e){
            echo $e->getMessage();
        }   
        //resgata os dados na tabela
        if(count($resultado)){
            foreach ($resultado as $res) {
            $create = strtotime(strftime('%m/%d/%Y %H:%M:%S', $res['creat']));

    $msg .="    <tr>";
    $msg .="       <td style='width:5%;'>".$res['idusr']."</td>";
    $msg .="       <td style='width:10%;'>".$res['forname']."</td>";
    $msg .="       <td style='width:20%;'>".$res['lastname']."</td>";
    $msg .="       <td style='width:10%;'>".$res['login']."</td>";
    $msg .="       <td style='width:25%;'>".$res['emailusr']."</td>";
    $msg .="       <td style='width:10%;'>".time_ago($create)."</td>";
    $msg .="       <td class='txt_rig' style='width:7%;'>".atividade_user($res['active'])."</td>";
    $msg .="       <td class='txt_cen' style='width:5%;'>";
    $msg .="       <a href='?edit=".$res['login']."&uid=".$res['idusr']."'><i class='lnr lnr-pencil'></i></a>";
    $msg .="       <a href='?del=".$res['login']."&uid=".$res['idusr']."'><i class='lnr lnr-cross'></i></a></td>";
    $msg .="                </tr>";
                         
            }   
        }else{
            $msg = "";
            $msg .="Nenhum resultado foi encontrado...";
        }

    $msg .="    </tbody>";
    $msg .="</table>";

    //retorna a msg concatenada
    echo $msg; 
?>