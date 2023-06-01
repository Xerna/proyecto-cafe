<div class="container w-50 h-75 mt-5 bg-brown-secondary shadow d-flex flex-column align-items-center py-7">
   <img src="img/undraw_Coffee_Time_45em.svg" alt="" class="img-fluid bg-main rounded-circle" width="200">
   <form action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mt-3" method="POST">
     <input type="text" class="form-control form-input mb-2 p-1 px-2" name="nombreUsuarios" placeholder="Usuario">
     <input type="password" class="form-control mb-2 p-1 px-2" name="contraseña" placeholder="Contraseña">
     <button type="submit" class="btn bg-test text-white w-100 py-1"  name="submit">Iniciar Sesión</button>
   </form>
   <?php echo$message;?>
 </div>
 <div class="brand border-bottom border-3 w-30 mt-5 mx-auto justify-content-center d-flex align-items-center border-main px-1">
   <img src="img/logo.png" alt="Bootstrap" width="70">
   <h3 class="mt-3">The Coffee Shop</h3>
 </div>
