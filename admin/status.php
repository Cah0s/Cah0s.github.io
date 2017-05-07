<?php
require_once '../system/functions.php';
$sql = $pdo->prepare("SELECT * FROM users ORDER BY idusr LIMIT $inicio, $maximo");
$sql->execute();

/*****************************
 * CONTAR O TOTAL DE REGISTROS
 ****************************/
$sql_count = "SELECT COUNT(*) AS total FROM users ORDER BY idusr ASC";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute();
$total = $stmt_count->fetchColumn();
//date_default_timezone_set("Europe/Berlin");
//setlocale(LC_ALL, 'de_DE.uft8');
if (setlocale(LC_ALL, 'de_DE.utf8') == false) { print "<h1>Fehler beim einstellen der Sprache!</h1>"; } 

if($total < 0):
    echo '<div class="err msg" style="text-align:center;"><b>Erro:</b> Nenhum Registro encontrado</div>';
else:
    echo "
    <div id='listusers'>
        <div class='item-list item-1'>Nome</div>
        <div class='item-list item-2'>Login</div>
        <div class='item-list item-3'>Email</div>
        <div class='item-list item-4'>Criado</div>
        <div class='item-list item-5'>Sts</div>
        <div class='item-list item-6'>Ativo</div>
        <div class='item-list item-7'>Modify</div>
    </div>
    ";
    while ($user = $sql->fetch(PDO::FETCH_OBJ)){
        //$id_user      = $user->id;
        $nome         = $user->forname;
        $last         = $user->lastname;
        $login        = $user->login;
        $email        = $user->emailusr;
        $password     = $user->password;
        $key          = $user->pwdrec;
        $key_data     = $user->pwdtime;
        $last_update  = $user->upd;
        $create       = strtotime(strftime('%m/%d/%Y %H:%M:%S', $user->creat)); //$user->creat;
        $type         = $user->type;
        $status       = $user->status;
        $active       = $user->active;
        ?>
        <div id='listusers'>
            <div class='item-list item-sub-1'>
                <span id="user_id" name="<?php echo $user->idusr; ?>">
                    <a href="?id=<?php echo $user->idusr; ?>&pag=<?php echo $pag; ?>"><?php echo $nome; ?></a>
                </span>
            </div>
            <div class='item-list item-sub-2'><?php echo $login; ?></div>
            <div class='item-list item-sub-3'><?php echo $email ;?></div>
            <div class='item-list item-sub-4'><?php echo time_ago($create); ?></div>
            <div class='item-list item-sub-5'>
                <?php
                    if($status==0) echo"<span class='nocheck'><i class='lnr lnr-warning'></i></span>";
                    elseif($status==1)
                        echo"<span id='bloquear' name='$user->idusr' class='checked'><i class='lnr lnr-checkmark-circle'></i></span>";
                    else
                        echo"<span id='desbloquear' name='$user->idusr' class='block'><i class='lnr lnr-lock'></i></span>";
                ?>
            </div>
            <div class='item-list item-sub-6'>
                <span id="check_st" name="<?php echo atividade_user($active); ?>"><?php echo atividade_user($active); ?></span>
            </div>
            <div class='item-list item-sub-7'>
                <a href="?edit=<?php echo $login; ?>&uid=<?php echo $user->idusr; ?>"><i class="lnr lnr-pencil"></i></a>
                <a href="?del=<?php echo $login; ?>&uid=<?php echo $user->idusr; ?>"><i class="lnr lnr-cross"></i></a>
            </div>
            <div class='item-list item-sub-8'></div>
        </div>
    <?php
    }
    ?>
    <!-- <span class='total'>Mostrando
    <font color='seagreen'><?php echo ($maximo > $total) ? $maximo = $total : $maximo ; ?></font> de
    <font color='seagreen'><?php echo $total; ?></font> usuário(s) por página</span> -->

    <table class="pag">
        <tr>
        <?php
            if($pag!=1){ echo "<td class=num><a name='".($pag-1)."' href='?pag=".($pag-1)."'><i class='lnr lnr-arrow-left-circle'></i></a></td>"; }
            if($contador<=$maximo){  }
            else{
                for($i=1;$i<=$paginas;$i++){
                    if($pag==$i){ echo "<td class=num><a name='$i' href='?pag=".$i."'><font color=red>".$i."</font></a></td>"; }
                    else{ echo "<td class=num><a name='$i' href='?pag=".$i."'>".$i."</a></td>"; }
                }
            }
            if($pag!=$paginas){ echo "<td class=num><a name='".($pag+1)."' href='?pag=".($pag+1)."'><i class='lnr lnr-arrow-right-circle'></i></a></td>"; }
        ?>
        </tr>
    </table>
    <?php
endif;
?>

<script type="text/javascript" src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<script>
$("span#bloquear").click(function(){
    var block_id = $(this).attr("name");
    $.get('functions/change_user_status.php?action=bloquear&change=' + block_id, function(data){
        $('#status').html(data)
        .fadeIn(1500, function() { $('.stsmsg'); })
        .fadeOut(1500, function() { $('.stsmsg'); });
    });
});
$("span#desbloquear").click(function(){
    var block_id = $(this).attr("name");
    $.get('functions/change_user_status.php?action=desbloquear&change=' + block_id, function(data){
        $('#status').html(data)
        .fadeIn(1500, function() { $('.stsmsg'); })
        .fadeOut(1500, function() { $('.stsmsg'); });
    });
});

// $(document).ready(function(){
//     var check_id = $("span#user_id").attr("name");
//     var check_st = $("span#check_st").attr("name");
//         $.ajax({
//            type: "GET",
//            url:"functions/online.php",
//            data: "check=" + check_st + "&id=" + 3, //$( this ).text(),
//                 success: function (textStatus){
//                     $('#bipp').html(textStatus); //mostrando resultado
//                     // if(textStatus === "online"){
//                     //     Push.create('Usuário '+ check_id +' Online!', {
//                     //         body: 'Testando notificações no navegador',
//                     //         icon: 'http://nickersoft.github.io/push.js/icon.png',
//                     //         timeout: 4000,
//                     //         onClick: function () {
//                     //             this.close();
//                     //         },
//                     //         vibrate: [200, 100, 200, 100, 200, 100, 200]
//                     //     });
//                     // }
//                 }
//         });
// });
</script>