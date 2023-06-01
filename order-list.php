<?php require('private/database/conn.php');
$stmt = $conn->prepare("SELECT * from ordenes");
$stmt->execute();
$i = 0; 
while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    $orders[$i]['order_num'] = $result['orderNum'];
    $orders[$i]['order_table'] = $result['numMesa'];
    $orders[$i]['order_details'] = $result['descripcion'];
    $orders[$i]['order_total'] = $result['total'];
    $i++;
}
?>
<div class="container" id="order-table">
    <h2 class="fs-2 text-center mt-4">Ordenes</h2>
    <table class="table bg-main mt-4" id="order_list">
        <thead class="bg-test text-white">
          <tr>
            <th class="col-1" scope="col-2">#</th>
            <th class="col-1" scope="col-2">Mesa</th>
            <th class="col-7" scope="col-6">Detalles de la orden</th>
            <th class="col-1" scope="col-2">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($orders as $orden):?>
          <tr>
            <th scope="row"><?php echo $orden['order_num'];?></th>
            <td><?php echo $orden['order_table'];?></td>
            <td><?php echo $orden['order_details'];?></td>
            <td class="text-success fw-bold"><?php echo $orden['order_total'];?></td>

          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
</div>