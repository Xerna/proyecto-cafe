<?php
require('../private/database/conn.php');
if (isset($_POST['submit'])) {
    // Retrieve the form data
    $descripcion = $_POST['descripcion'];
    $precio = floatval($_POST['precio']);
    $numMesa = 1; // ESTE VALOR LO VAN A SACAR DEL USUARIO
    // Create the SQL query
    $sql = "INSERT INTO preorden (idPreorden,descripcion, total,numMesa) VALUES (4,'$descripcion', '$precio',$numMesa)";
    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully";
}
header('Location:'.'carrito-ejemplo.php');
}
?>
