<?php
require('../private/database/conn.php');
session_start();
if (isset($_SESSION['usuario'])){
    $nombreUsuario = $_SESSION['usuario'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $_POST['descripcion'];
    $total = $_POST['total'];
}
$stmt = $conn->query("INSERT INTO ordenes (descripcion, total,numMesa) VALUES ('$descripcion','$total','$nombreUsuario')");
$stmt = $conn->query("DELETE FROM preorden WHERE numMesa = '$nombreUsuario'");
header('location: volvermenu.php')
?>