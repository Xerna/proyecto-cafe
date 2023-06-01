<?php 
require ('../private/database/conn.php');
require('templates/header.php');
$stmt = $conn->prepare("SELECT * from menu");
$stmt->execute();
$i = 0; 
while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    $menu[$i]['nombre'] = $result['nombre'];
    $menu[$i]['categoria'] = $result['categoria'];
    $menu[$i]['descripcion'] = $result['descripcion'];
    $menu[$i]['urlimg'] = $result['urlimg'];
    $menu[$i]['precio'] = $result['precio'];
    $i++;
}
require('views/menu-view.php');
require('templates/footer.php');  
?>
