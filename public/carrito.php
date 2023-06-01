<?php 
require('../private/database/conn.php');
session_start();
if (isset($_SESSION['usuario'])){
    $nombreUsuario = $_SESSION['usuario'];
}
$stmt = $conn->query("SELECT * from preorden WHERE numMesa = '$nombreUsuario'");
$i = 0;
$total = 0;
$orden = ""; 
while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    $preOrder[$i]['descripcion'] = $result['descripcion'];
    $preOrder[$i]['total'] = $result['total'];
    $orden .= $preOrder[$i]['descripcion'] . '\n';
    $total += floatval($preOrder[$i]['total']);
    $i++;
}  
require ('templates/header.php');
require('views/carrito-view.php');
require ('templates/footer.php');
?>