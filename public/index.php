<?php
require ('../private/database/conn.php');
$stmt = $conn->prepare("SELECT * from ordenes");
$stmt->execute();
$i = 0; 
while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    $orders[$i]['order_num'] = $result['orderNum'];
    $orders[$i]['order_table'] = $result['numMesa'];
    $orders[$i]['order_details'] = $result['descripcion'];
    $orders[$i]['order_total'] = $result['total'];
    $i++;
}  
require ('templates/header.php');
require ('templates/navbar.php');
require('views/index-view.php');
require ('templates/footer.php');
?>