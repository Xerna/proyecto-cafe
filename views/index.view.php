<?php require('header.php');?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<div id="dashboard">
    <div class="container mt-2">
        <div class="row d-flex p-5 text-center justify-content-center">
            <div class="col-2">
                <div class="menu-item bg-danger shadow py-5 text-white">
                    <i class="fa-solid fa-utensils menu-icon fs-1"></i>
                    <h6 class="menu-desc mt-3">Menu</h6>
                </div>
            </div>
            <div class="col-2">
                <div class="menu-item bg-success shadow py-5 text-white">
                    <i class="fa-solid fa-cash-register fs-1"></i>
                    <h6 class="menu-desc mt-3">Ordenes</h6>
                </div>
            </div>
            <div class="col-2">
                <div class="menu-item bg-primary shadow py-5 text-white">
                    <i class="fa-solid fa-user-pen fs-1"></i>
                    <h6 class="menu-desc mt-3">Usuarios</h6>
                </div>
            </div>
            <div class="col-2">
                <div class="menu-item bg-dark shadow py-5 text-white">
                    <i class="fa-solid fa-right-from-bracket fs-1"></i>
                    <h6 class="menu-desc mt-3">Salir</h6>
                </div>
            </div>
        </div>
    </div>
</div>
	<div class="container">
		<div id="cargar_usuarios">

		</div>
	</div>
<script>
function loadXMLDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("cargar_usuarios").innerHTML =
      this.responseText;
    }
  };
  xhttp.open("GET", "order_list.php", true);
  xhttp.send();
}
setInterval(function(){
	loadXMLDoc();
	// 1sec
},3000);

window.onload = loadXMLDoc;
</script>
<?php require('footer.php');?>