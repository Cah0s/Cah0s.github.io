<?php
require '../../system/functions.php';
//$pdo=conectar();
$estado = (int) strip_tags(trim($_POST['estado']));
$cidad = $pdo->prepare("SELECT * FROM cidades WHERE estado_id = '$estado'");
$cidad->execute();
sleep(1);

echo "<option value=\"\" disabled selected> Selecione a cidade </option>";
while ($cidade = $cidad->fetch(PDO::FETCH_OBJ)) {
    echo "<option value=\"{$cidade->cid}\"> {$cidade->cidade} </option>";
}