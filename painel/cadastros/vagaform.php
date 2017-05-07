           <!-- formulario -->
           <form id="formulario" method="POST" name="workform">

             <fieldset>
              <h2><i class="fa fa-address-card-o"></i> Dados da Vaga</h2>
              <input type="text" name="work" placeholder="job opportunity name">
              <input type="text" name="mail" placeholder="contact mail">          
              <input type="text" name="phone" class="phone_with_ddd" placeholder="contact phone">

              <br>
              
              <h2><i class="fa fa-info"></i> Infos</h2>
              <input type="text" name="address" placeholder="e.g R. Albatroz - Monterrey, Louveira - SP, Brasil">
              <textarea name="description" placeholder="description"></textarea>
              <input type="text" name="web" placeholder="website">
              <?php
              $pdo=conectar();
    		  $workuser = $pdo->query("SELECT * FROM empresas JOIN users ON login='$usuario' WHERE user_cad='$usuario'");
    		  $workverif = $workuser->rowCount();
    		  $workempresa = $workuser->fetch(PDO::FETCH_OBJ)->empresa;
              if( $workverif > 1 ):
		          echo "<select name='company' id=''>";
		              while ($row = $workuser->fetch(PDO::FETCH_OBJ)) {
		                echo "<option value='$row->empresa'>$row->empresa</option>";
		              }
		          echo "</select>";
		      else:
		          echo "<input type='hidden' name='company' value='".$workempresa."'>";
		      endif;
		      ?>
              <input type="submit" name="next" class="acao" value="Enviar">
             </fieldset>

             <br class="clear">

             <!-- Camada de mensagens -->
             <div class="respwork"></div>
             <!-- /mensagens -->
           </form>
          <!-- /formulario -->