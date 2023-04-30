<?php require('header.php')?>

    <div class="container py-5 bg-white rounded-5 shadow-lg mx-auto mt-50 d-flex justify-content-evenly align-items-center "
        style="width: 50%;">
        <img src="img/undraw_conversation_re_c26v.svg" alt="" style="width: 20rem;" class="img-fluid">
        <form class="p-2  rounded-5 " style="width: 30%;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="mb-3">
                <label for="user" class="form-label">Nombre de usuario</label>
                <input type="text" class="form-control" id="user" aria-describedby="user" name="user">
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="pass" name="pass">
            </div>
            <button type="submit" class="btn bg-btn">Inicar Sesion</button>
        </form>
    </div>
<?php require('footer.php');?>