<div class="container mt-5">
    <h1 class="fs-1 text-center">Menu</h1>
    <div class="row d-flex justify-content-evenly">
    <?php foreach ($menu as $menuitem):?>
        <div class="col-3 me-4 mb-4">
            <div class="card" style="width: 12rem;">
                <div class="" style="height: 30%">
                    <img src="<?php echo $menuitem['urlimg']?>" class="object-fit-cover " style="width: 100%; height: 200px;" alt="Card image">
                  </div>
                <div class="card-body" style="height: 12rem;">
                    <h5 class="card-title" style="height: 1rem;"><?php echo $menuitem['nombre']?></h5>
                    <h6 class="card-text" style="height: 4.25rem;"><?php echo $menuitem['descripcion']?></h6>
                    <h6 class="card-text text-success fw-bold" style="height: 1rem;">$<?php echo $menuitem['precio']?></h6>
                    <form action="insertar_preorden.php" method="POST">
                        <input type="hidden" name="nombre" value="<?php echo $menuitem['nombre']?>">
                        <input type="hidden" name="precio" value="<?php echo $menuitem['precio']?>">
                    <button type="submit" href="#" class="btn bg-test text-white fs-7" style="font-size: 10px;">Añadir al carrito</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>