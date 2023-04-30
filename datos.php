<?php 
// error_reporting(0);
// require('conn.php');
// header('Content-type: application/json; charset=utf-8');
// $stmt = $conn->prepare("SELECT * from orders");
// $stmt->execute();
// $ordenes = []; 
// while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
//     $orders = [ 
//         'order_num'     => $result['order_number'],
//         'order_table'   => $result['order_table'],
//         'order_details' => $result['order_details'],
//         'order_total'   => $result['order_total']
//     ];
//     array_push($ordenes,$orders);
// }
// /*print_r($ordenes);*/
// var_dump($ordenes);
// echo json_encode($ordenes);
error_reporting(0);
header('Content-type: application/json; charset=utf-8');
require('conn.php');
$stmt = $conn->prepare("SELECT * from ordenes");
$stmt->execute();
$ordenes = array();
while($fila = $stmt->fetch(PDO::FETCH_ASSOC)){
    $ordenes[] = $fila;
}
$json_resultados = json_encode($ordenes);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'Error al codificar JSON: ' . json_last_error_msg();
}

echo $json_resultados;

?>
