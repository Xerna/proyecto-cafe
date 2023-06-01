<?php
require('../private/database/conn.php');
session_start();
if (isset($_SESSION['usuario'])){
    $nombreUsuario = $_SESSION['usuario'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['nombre'])){
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $query = "INSERT INTO preorden (descripcion,total,numMesa) VALUES ('$nombre','$precio','$nombreUsuario')";
        $conn->query($query);
    }
}
header('Location: carrito.php')
?>