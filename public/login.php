<?php
session_start();
require('../private/database/conn.php');
// session_start();
require('templates/header.php');
$message = '';
if(isset($_POST['submit'])) {
if(!empty($_POST['nombreUsuarios']) && !empty($_POST['contraseña'])){
    echo $_POST['nombreUsuarios'];
    $records = $conn-> prepare('SELECT usuario,password,tipousuario from usuarios where usuario = :nombreUsuarios');
    $records ->bindParam(':nombreUsuarios', $_POST['nombreUsuarios']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    // if (count($results)> 0 && password_verify($_POST['contraseña'], $results['contraseña'])){
        if (count($results)> 0){

        $_SESSION['usuario'] = $results['usuario'];
        $tipousuario = $results['tipousuario'];
        $_SESSION['tipousuario'] = $tipousuario;
        if($tipousuario != "cliente"){
            header('Location: ../public/index.php');
        }else{
            header('Location: ../public/menu.php');
        }
        
    }else{
        $message = 'Lo sentimos pero al parecer tus credenciales no coinciden';
    }
}
}
require('views/login-view.php');
require('templates/footer.php');
?>
