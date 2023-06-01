<h1 class="fs-1 text-center mt-5">Listo para ordenar?</h1>
<div class="container w-75 mt-5">
    <table class="table">
        <thead>
          <tr>
            <th scope="col">Descripcion</th>
            <th scope="col">Precio</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($preOrder as $preOrder_item):?>
          <tr>
            <td><?php echo $preOrder_item['descripcion'];?></td>
            <td class="text-success fw-bold">$<?php echo $preOrder_item['total'];?></td>
          </tr>
          <?php endforeach?>
        </tbody>
      </table>
      <h5 class=" fs-4 me-4 fw-bold text-end">Total: <span class="text-success">$<?php echo $total;?></span></h5>
      <form action="insertar_orden.php" method="POST" class="mt-3 text-center mx-auto">
      <input type="hidden" name="total" value="<?php echo $total;?>">
      <input type="hidden" name="descripcion" value="<?php echo $orden;?>">  
      <button type="submit" name="submit" class="btn bg-test text-white fs-6">Ordenar</button>
      <a href="menu.php" class="btn bg-brown-secondary border-main text-m  fs-6" style="font-size: 10px;">Volver</a>
      </form>
</div>