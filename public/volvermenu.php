<?php 
require ('templates/header.php');
?>
<div class="container text-center">
<?php
    echo"<h1 class='fs-1'>Su orden ha sido registrada con exito. Muchas Gracias por su preferencia en breve estara lista</h1>";
    sleep(3);
    header('location: menu.php');
    ?>
</div>
<?php 
require ('templates/footer.php');
?>