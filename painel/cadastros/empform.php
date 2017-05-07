           <!-- formulario -->
           <form id="formulario" method="POST" name="empform" enctype="multipart/form-data">

             <fieldset>
              <h2><i class="fa fa-address-card-o"></i> Dados da Empresa</h2>
              <input type="text" name="empresa" placeholder="nome da Empresa">
              <input type="text" name="email" placeholder="email da Empresa">          
              <input type="text" name="telefone" class="phone_with_ddd" placeholder="telefone">
              <select class="j_loadstate" name="empresa_estado">
                  <option value="" selected> Selecione o estado </option>
                  <?php
                  $est = $pdo->prepare("SELECT * FROM estados ORDER BY estado ASC");
                  $est->execute();
                  while($estado = $est->fetch(PDO::FETCH_OBJ)){
                      echo "<option value='$estado->eid'> {$estado->uf} | {$estado->estado} </option>";
                  }
                  ?>                        
              </select>
              <select class="j_loadcity" name="empresa_cidade">
                  <option value="" selected> Selecione antes um estado </option>
              </select>

              <h2><i class="fa fa-info"></i> Infos</h2>
              <input type="text" name="endereco" placeholder="e.g R. Albatroz - Monterrey, Louveira - SP, Brasil">
              <textarea name="descricao" placeholder="sobre a empresa"></textarea>
              <select name="categoria">
                <option value="">Seleciona o tipo</option>
                <option value="Immobiliária">Immobiliária</option>
                <option value="Mercado">Mercado</option>
                <option value="ISP">ISP - Internet Serviçe Provider</option>
                <option value="Oficina">Oficina</option>
                <option value="Telecom">Telecommunicações</option>
                <option value="Loja">Loja</option>
                <option value="Rede Social">Rede Social</option>
              </select>
              <input type="text" name="website" placeholder="website">

              <h2><i class="fa fa-clock-o"></i> Horários seg-dom</h2>
              <div class="times">
                <div class="timerow">De <input type="time" name="horasegini"></div>
                <div class="timerow">As <input type="time" name="horasegfin"></div>
              </div>
              <div class="times">
                <div class="timerow">De <input type="time" name="horaterini"></div>
                <div class="timerow">As <input type="time" name="horaterfin"></div>
              </div>
              <div class="times">
                <div class="timerow">De <input type="time" name="horaquaini"></div>
                <div class="timerow">As <input type="time" name="horaquafin"></div>
              </div>
              <div class="times">
                <div class="timerow">De <input type="time" name="horaquiini"></div>
                <div class="timerow">As <input type="time" name="horaquifin"></div>
              </div>
              <div class="times">
                <div class="timerow">De <input type="time" name="horasexini"></div>
                <div class="timerow">As <input type="time" name="horasexfin"></div>
              </div>
              <div class="times">
                <div class="timerow">De <input type="time" name="horasabini"></div>
                <div class="timerow">As <input type="time" name="horasabfin"></div>
              </div>
              <div class="times">
                <div class="timerow">De <input type="time" name="horadomini"></div>
                <div class="timerow">As <input type="time" name="horadomfin"></div>
              </div>

              <h2><i class="fa fa-share-alt"></i> Social</h2>
              <input type="text" name="facebook" placeholder="facebook">
              <input type="text" name="twitter" placeholder="twitter">
              <input type="text" name="google" placeholder="google plus">
              <input type="file" name="emplogo">
              <input type="submit" name="next" class="acao" value="Enviar">
             </fieldset>

             <br class="clear">

             <!-- Camada de mensagens -->
             <div class="respemp"></div>
             <!-- /mensagens -->
           </form>
          <!-- /formulario -->