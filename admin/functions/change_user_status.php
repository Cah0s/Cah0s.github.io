<?php
//ini_set("display_errors", 1);
include "../../system/database.php";
$pdo=conectar();

/*****************************
 * FUNÇÃO PARA DELETAR DADOS
 ****************************/
if(isset($_GET['change']) && !empty($_GET['change'])){
    $change  = $_GET['change'];
    // $numRows = $pdo->query("SELECT * FROM daten WHERE id='$change'")->fetchColumn();

    // if($numRows <= 0){ echo "..."; }
    // else{
        if(isset($_GET['action']) && $_GET['action'] == "bloquear"){
            $upd = $pdo->prepare("UPDATE users SET status='2', active='offline', offtime=NOW() WHERE idusr=:id");
            $upd->bindValue(':id', $change, PDO::PARAM_INT);
            $update = $upd->execute();

            // $status = $pdo->prepare("UPDATE newusers SET status='2' WHERE id=:id");
            // $status->bindValue(':id', $change, PDO::PARAM_INT);
            // $status->execute();

            if($change){
                // $message  = '<div class="message is-info" style="width:60%;margin:10px auto;">';
                // $message .= '  <p class="message-header">INFO</p>';
                // $message .= '  <p class="message-body">Status alterado com sucesso, usuário <font color="white">Bloqueado!</font></p>';
                // $message .= '</div>';
                $message = "<div class=\"stsmsg ok\">Usuário bloqueado com sucesso :) <i class=\"lnr lnr-cross\"></i></div>";
                echo $message;
            }
            else{
                // $message  = '<div class="message is-danger" style="width:60%;margin:10px auto;">';
                // $message .= '  <p class="message-header">ERROR</p>';
                // $message .= '  <p class="message-body">Erro ar mudar o status do usuário!</p>';
                // $message .= '</div>';
                $message = "<div class=\"stsmsg er\">Erro ao mudar o status!<i class=\"lnr lnr-cross\"></i></div>";
                echo $message;
            }
        }
        elseif(isset($_GET['action']) && $_GET['action'] == "desbloquear"){
            $upd = $pdo->prepare("UPDATE users SET status='1', active='offline' WHERE idusr=:id");
            $upd->bindValue(':id', $change, PDO::PARAM_INT);
            $update = $upd->execute();

            // $status = $pdo->prepare("UPDATE newusers SET status='1' WHERE id=:id");
            // $status->bindValue(':id', $change, PDO::PARAM_INT);
            // $status->execute();

            if($change){
                // $message  = '<div class="message is-info" style="width:60%;margin:10px auto;">';
                // $message .= '  <p class="message-header">INFO</p>';
                // $message .= '  <p class="message-body">Status alterado com sucesso, usuário <font color="white">Desbloqueado!</font></p>';
                // $message .= '</div>';
                $message = "<div class=\"stsmsg ok\">Usuário desbloqueado com sucesso :) <i class=\"lnr lnr-cross\"></i></div>";
                echo $message;
            }
            else{
                // $message  = '<div class="message is-danger" style="width:60%;margin:10px auto;">';
                // $message .= '  <p class="message-header">ERROR</p>';
                // $message .= '  <p class="message-body">Erro ar mudar o status do usuário!</p>';
                // $message .= '</div>';
                $message = "<div class=\"stsmsg er\">Erro ao mudar o status!<i class=\"lnr lnr-cross\"></i></div>";
                echo $message;
            }
        }
    //}
}