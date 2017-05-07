<?php
if (strcmp(basename($_SERVER['SCRIPT_NAME']), basename(__FILE__)) === 0){
  header("location: ../");
}
$pdo=conectar();
if(isset($_SESSION['user_id']) || isset($_SESSION["usuario"])){
    header("Location: ".LOGIN);
}
?>

  <div class="my_box my_box_form">

    <!-- <div class="boxx box_header">
      <span class="box_separator user_form_icon">
        <div class="user_icon" > </div>
      </span>
      <span class="box_separator box_header_title">OnlineSys</span>
    </div> -->

    <div class="boxx">
      <form method="POST">
      
      <div class="campo">
        <input type="text" name="forname" placeholder="forname" value="<?php  echo (isset($_REQUEST['forname'])) ? $_REQUEST['forname'] : NULL ; ?>" class="inputsl">
      </div>

      <div class="campo">
        <input type="text" name="lastname" placeholder="lastname" value="<?php  echo (isset($_REQUEST['lastname'])) ? $_REQUEST['lastname'] : NULL ; ?>" class="inputsr">
      </div>

      <div class="campo">
        <input type="text" name="login" placeholder="login" value="<?php  echo (isset($_REQUEST['login'])) ? $_REQUEST['login'] : NULL ; ?>" class="inputsl">
      </div>

      <div class="campo">
        <input type="text" name="email" placeholder="email" value="<?php  echo (isset($_REQUEST['email'])) ? $_REQUEST['email'] : NULL ; ?>" class="inputsr">
      </div>
      
      <div class="campo">
        <input type="password" name="password" placeholder="password" class="inputsl">
      </div>

      <div class="campo">
        <input type="password" name="password2" placeholder="password confirm" class="inputsr">
      </div>

      <input type="submit" name="send" class="send">
      </form>
    </div>

    <div class="boxx box_msg">
      <?php
      $error = false;
      /**********************************
      * CADASTRAR NOVO ARTIGO
      **********************************/
      if(isset($_POST['send'])){
          $forname   = $_POST['forname'];
          $lastname  = $_POST['lastname'];
          $login     = $_POST['login'];
          $email     = $_POST['email'];
          $password  = $_POST['password'];
          $password2 = $_POST['password2'];

          if(anti_injection($forname)){
            echo anti_injection($forname)." no campo Nome</p></div>";
          }
          elseif(anti_injection($lastname)){
            echo anti_injection($lastname)." no campo Sobrenome</p></div>";
          }
          elseif(anti_injection($login)){
            echo anti_injection($login)." no campo Login</p></div>";
          }
          elseif(anti_injection($email)){
            echo anti_injection($email)." no campo E-mail</p></div>";
          }
          elseif(anti_injection($password)){
            echo anti_injection($password)." no campo Senha</p></div>";
          }
          elseif(anti_injection($password2)){
            echo anti_injection($password2)." no campo confirmar Senha</p></div>";
          }else{
          
            if(empty($forname)) {
              $message  = '<div class="message is-warning">';
              $message .= '  <p class="message-header">WARNING</p>';
              $message .= '  <p class="message-body">Preencha o campo Nome!</p>';
              $message .= '</div>';
              echo $message;
              $error = true;
            }
            elseif(empty($lastname)){
              $message  = '<div class="message is-warning">';
              $message .= '  <p class="message-header">WARNING</p>';
              $message .= '  <p class="message-body">Preencha o campo Sobrenome!</p>';
              $message .= '</div>';
              echo $message;
              $error = true;
            }
            elseif(empty($email)){
              $message  = '<div class="message is-warning">';
              $message .= '  <p class="message-header">WARNING</p>';
              $message .= '  <p class="message-body">Preencha o campo Email!</p>';
              $message .= '</div>';
              echo $message;
              $error = true;
            }

            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              $message  = '<div class="message is-warning">';
              $message .= '  <p class="message-header">WARNING</p>';
              $message .= '  <p class="message-body">Utilize um Email válido!</p>';
              $message .= '</div>';
              echo $message;
              $error = true;
            }   
            elseif(strlen($password) == 0) {
              $message  = '<div class="message is-warning">';
              $message .= '  <p class="message-header">WARNING</p>';
              $message .= '  <p class="message-body">Informe uma Senha!</p>';
              $message .= '</div>';
              echo $message;
              $error = true;
            }
            elseif(strlen($password2) == 0) {
              $message  = '<div class="message is-warning">';
              $message .= '  <p class="message-header">WARNING</p>';
              $message .= '  <p class="message-body">Confirme sua Senha!</p>';
              $message .= '</div>';
              echo $message;
              $error = true;
            }
            elseif($password != $password2) {
              $message  = '<div class="message is-warning">';
              $message .= '  <p class="message-header">WARNING</p>';
              $message .= '  <p class="message-body">As Senhas não coincidem!</p>';
              $message .= '</div>';
              echo $message;
              $error = true;
            }
            
              if(!$error) { 
                $mail = $pdo->prepare("SELECT * FROM users WHERE emailusr=:email");
                $result = $mail->execute(array('email' => $email));
                $e_mail = $mail->fetch();
                
                if($e_mail !== false) {
                  $message  = '<div class="message is-danger">';
                  $message .= '  <p class="message-header">ERROR</p>';
                  $message .= '  <p class="message-body">Este Email ja está cadastrado no Sistema!</p>';
                  $message .= '</div>';
                  echo $message;
                  $error = true;
                }

                $usr = $pdo->prepare("SELECT * FROM users WHERE login=:login");
                $result = $usr->execute(array('login' => $login));
                $user = $usr->fetch();

                if($user !== false) {
                  $message  = '<div class="message is-danger">';
                  $message .= '  <p class="message-header">ERROR</p>';
                  $message .= '  <p class="message-body">Ja há um Usuário com esté login cadastrado no Sistema!</p>';
                  $message .= '</div>';
                  echo $message;
                  $error = true;
                }
              }
            
            if(!$error) { 
              $password_enc = hash("sha1", $password);
              $userkey = random_string();

              $attributes = array(
                 'forname'  => $forname,
                 'lastname' => $lastname,
                 'login'    => $login,
                 'addr'     => $_SERVER['REMOTE_ADDR'],
                 'emailusr' => $email,
                 'password' => $password_enc,
                 'pwdrec'   => "",
                 'pwdtime'  => "",
                 'upd'      => "",
                 'creat'    => time(), //date("Y-m-d h:i:s"),
                 'userkey'  => $userkey,
                 'type'     => "user",
                 'status'   => 0,
                 'active'   => "offline",
                 'ontime'   => "",
                 'offtime'  => ""
              );

              $url_acitaveuser = getSiteURL().'activate_user&data='.hash("sha1" ,$login).'&uid='.$login.'&key='.$userkey;
              $Email = new PHPMailer();
              $Email->SetLanguage("br");
              $Email->CharSet = "UTF-8";
              $Email->IsSMTP();
              $Email->SMTPAuth = true;
              //$Email->Mailer = 'smtp.gmail.com';
              $Email->Host = MAILHOST; // 'smtp.gmail.com';
              //$Email->Port = MAILPORT; // '465';
              $Email->Port = MAILPORT; // '465';
              $Email->Username = MAILUSER; // 'sebastiankopp.design@gmail.com';
              $Email->Password = MAILPASS; // 'S3ba78I1nK0pp25';
              $Email->SMTPSecure = 'ssl'; //tls
              $Email->IsHTML(true);
              $Email->FromEmail = 'Cah0s@mail.com';
              $Email->FromName = 'Onlinesys';
              $Email->Sender = "kkkkk";
              $Email->AddReplyTo('contact@onlinesys.com', 'Onlinesys Security');
              $Email->AddAddress($email);
              $Email->Subject = 'Ativação do Usuário do Sistema Onlinesys';
              $Email->Body  = 'Hallo '.$forname;
              $Email->Body  = '<br><br>Seu Usuário foi criado com sucesso, porém ainda falta ativa-lo!';
              $Email->Body .= '<br>Para isto acesse o link abaixo ou copie e cole o mesmo em seu navegador<br>';
              $Email->Body .= '<a href='.$url_acitaveuser.' target="_blank">'.$url_acitaveuser.'</a>';
              $Email->Body .= '<br><br>Caso ja tenha ativado seu Usuário apenas ignore este email. Caso não tenha feito Cadastro em nosso Sistema, contate o mais breve possivel nossa Administração para podermos averiguar o caso.';
              $Email->Body .= '<br><br>Com Atenção, Onlinesys Team ';

                if(!$Email->Send()){
                  $message  = '<div class="message is-danger">';
                  $message .= '  <p class="message-header">ERROR</p>';
                  $message .= '  <p class="message-body">Erro ao enviar! Houve um problema ao enviar o email e por essas questões o seu usuário não foi criado. Tente de novo mais tarde ou contate o administrador.</p>';
                  $message .= '</div>';
                  echo $message;
                  echo '<span class="av msg"><b>Erro: </b>' . $Email->ErrorInfo.'</span>'; die();
                  //echo "<pre>"; var_dump($Email); 
                }else{
                  $message  = '<div class="message is-success">';
                  $message .= '  <p class="message-header">SUCCESS</p>';
                  $message .= '  <p class="message-body">Uma mensagem com as informações de acesso foi enviada p/ o e-mail informado.</p>';
                  $message .= '</div>';
                  echo $message;
                }// fim envio email

              $cad  = creat('users', $attributes);
              // $mens = ($cad) ? '<div class="ok msg"><b>Sucesso:</b> Usuário Criado</div>' : '<div class="err msg"><b>Error:</b> Falha ao Cadastrar novo usuário</div>';
              // echo $mens;
              if($cad){
                $message  = '<div class="message is-success">';
                $message .= '  <p class="message-header">SUCCESS</p>';
                $message .= '  <p class="message-body">Usuário Criado com Sucesso, verifique no seu email '.$email.' o link de ativação.</p>';
                $message .= '</div>';
                echo $message;
              }
              else{
                $message  = '<div class="message is-danger">';
                $message .= '  <p class="message-header">ERROR</p>';
                $message .= '  <p class="message-body">Falha ao Cadastrar novo Usuário!</p>';
                $message .= '</div>';
                echo $message;
              }
            }// fim !$error
          }
      }
      ?>
    </div>
  </div>
