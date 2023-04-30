<?php
/*if(isset($_SESSION['usuario'])){
    header('Location: index.php');
}*/
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    //$pass = hash('sha512',$pass);
    require('conn.php');
    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE nombre_usuario = :user AND contraseña = :pass');
    $stmt->execute(array(
    ':user' => $user,
    ':pass' => $pass));
    $result = $stmt->fetch();
    /*print_r($result);*/
    if($result !== false){
        $_SESSION['user'] = $user;
        header('Location: index.php');
    }
}

require('views/login.view.php')
?>