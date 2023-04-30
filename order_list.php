<?php require('conn.php');
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
<div class="container bg-white p-5  shadow">
    <h2 class="fs-2 text-center">Ordenes</h2>
        <table class="table" id="order_list">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Mesa</th>
                    <th scope="col">Detalles</th>
                    <th scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $orden):?>
                <tr>
                    <td><?php echo $orden['order_num'];?></td>
                    <td><?php echo $orden['order_table'];?></td>
                    <td><?php echo $orden['order_details'];?></td>
                    <td><?php echo $orden['order_total'];?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
<?php ?>