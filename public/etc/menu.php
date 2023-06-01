<!DOCTYPE html>
<html>
<head>
	<title>Menú de cafetería</title>
	<style>
		body {
			font-family: 'Times New Roman', serif;

		}
body {
background: #ECE3D6;

}



		h1 {
			font-family: 'Pacifico', cursive;

			font-size: 36px;
			margin: 0 0 20px;
			 text-align:center;
position: relative;
  top: -430px;
			
		}
            
h2{ 
font-family: 'Pacifico', cursive;

			font-size: 30px;
text-align:left;
                  padding-left: 0px;
			margin-bottom: 10px;

}

		h3 {
			font-family: 'Pacifico', cursive;
			color: #363431;
			font-size: 18px;
                  text-align:left;
                  padding-left: 0px;
			margin-bottom: 8px;

		}
h4 {
    font-family: 'Pacifico', cursive;
    font-size: 40px;
    text-align: center;
    margin: 0 auto; /* Centra horizontalmente */
    margin-bottom: 500px; /* Valor ajustable */
position: relative;
  top: 0px;
}

		p {
			font-family: 'Poppins', serif;
			color: #9F5500;
			font-weight: 500;
			font-size: 18px;
                  text-align:left;
                  padding-left: 0px;
			margin-bottom: 4px;


		}

		select, input[type="text"], input[type="submit"] {
			font-family: 'Poppins', serif;
			color: #9F5500;
			font-weight: 500;
			font-size: 14px;
			padding: 8px 12px;
			border: 1px solid #9F5500;
			border-radius: 4px;
			margin-bottom: 16px;
                  position: relative;
  top: -400px;

		}

		input[type="submit"] {
			background-color: #9F5500;
			color: #FFFFFF;
			cursor: pointer;
			transition: all 0.3s ease;
                  position: relative;
  top: -400px;
 
		}
          label {
         color: black;
        font-weight:bold;
        font-size: 16px;
         
position: relative;
  top: -400px;

           }
      

        span{
         color: black;
        font-weight:bold;
        font-size: 16px;
         position: relative;
  top: -400px;

           }

		input[type="submit"]:hover {
			background-color: #FFFFFF;
			color: #9F5500;
			border-color: #9F5500;

		}

		.menu-item {
			background-color: #ECE3D6;
			padding: 16px;
			border: 1px solid #9F5500;
			border-radius: 4px;
			margin-bottom: 16px;

		}

		.menu-item h3 {
			margin-top: 10;


		}

		.menu-item p.price {
			font-weight: bold;


		}
#menu {
  background-color: #EFECE8;
}


.image-container {
  text-align: center; /* Alinea la imagen al centro horizontalmente */
  margin-top: 20px; /* Ajusta el margen superior de la imagen */
}

.menu-imagen {
  width: 200px; /* Ajusta el ancho de la imagen */
  height: auto; /* Permite que la altura de la imagen se ajuste proporcionalmente */
}





.container {
      display: flex;
      height: 100vh; /* Establece el alto total de la página */

    }
    
    .fixed-section {
      flex: 0 0 30%; /* Establece el ancho fijo del 50% para la sección fija */
      background-color: #b8a38c;
      padding: 20px;
      box-sizing: border-box;
    }
    
    .scrollable-section {
      flex: 1; /* Toma el resto del espacio disponible para la sección desplazable */
      overflow-y: scroll; /* Permite el desplazamiento vertical */
      padding: 20px;
      box-sizing: border-box;
    }


	</style>





</head>





<body onload="calcularPrecioTotal()">
<div class="container">
    <div class="fixed-section">
<h4>The Coffee Shop </h4>



<h1>Menú de cafetería</h1>

<form method="post" action="carrito.php">
  <label for="selectTipo">Selecciona una opción:</label>
  <select id="selectTipo" onchange="mostrarOpciones()">
    <option value="">Selecciona una opción</option>
    <option value="cafe">Café</option>
    <option value="salado">Salado</option>
    <option value="dulce">Dulce</option>
    <option value="bebidas">Bebidas</option>
  </select>

  <div id="opcionesCafe" style="display: none;">
    <label for="selectCafe">Selecciona un tipo de café:</label>
    <select id="selectCafe">
      <option value="">Selecciona una opción</option>
      <option value="Espresso" data-price="2.50">Espresso</option>
      <option value="Latte Macchiato" data-price="2.50">Latte Macchiato</option>
      <option value="Cappuccino" data-price="2.50">Cappuccino</option>
      <option value="Mocha" data-price="2.50">Mocha</option>
      <option value="Frappuccino Mocha" data-price="3.50">Frappuccino Mocha</option>
      <option value="Frappuccino Café" data-price="3.50">Frappuccino Café</option>
    </select>
    <span id="precio-unitario-cafe"></span>
  </div>

  <div id="opcionesSalado" style="display: none;">
    <label for="selectSalado">Selecciona una comida:</label>
    <select id="selectSalado">
      <option value="">Selecciona una opción</option>
      <option value="Croissant jamón y queso" data-price="3.50">Croissant jamón y queso</option>
      <option value="Pollo y Mozzarella" data-price="3.50">Pollo y Mozzarella</option>
      <option value="Bocadillo Jamón, Huevo y Queso" data-price="3.50">Bocadillo Jamón, Huevo y Queso</option>
      <option value="Bocadillo tocino, frijoles y aguacate" data-price="2.50">Bocadillo tocino, frijoles y aguacate</option>
      <option value="Bocadillo Carne y Queso" data-price="4.50">Bocadillo Carne y Queso</option>
      <option value="Ensalada de pollo" data-price="5.50">Ensalada de pollo</option>
      <option value="Ensalada de jamón" data-price="5.50">Ensalada de jamón</option>
    </select>
    <span id="precio-unitario-salado"></span>
  </div>

  <div id="opcionesDulce" style="display: none;">
    <label for="selectDulce">Selecciona un postre:</label>
    <select id="selectDulce">
      <option value="">Selecciona una opción</option>
      <option value="Alfajores" data-price="1.50">Alfajores</option>
      <option value="Brownie Cake" data-price="1.50">Brownie</option>
<option value="Cachito simple " data-price="1.00">Cachito simple </option>
<option value="Pan tostado " data-price="1.50">Pan tostado</option>
<option value="Pastel de frutas" data-price="2.50">Pastel de frutas</option>


		</select>
		<span id="precio-unitario-dulce"></span>
  </div>


  <div id="opcionesBebidas" style="display: none;">
    <label for="selectBebidas">Selecciona una bebida:</label>
    <select id="selectBebidas">
      <option value="">Selecciona una opción</option>
      <option value="Limonada con Fresa" data-price="2.50">Limonada con Fresa</option>
      <option value="Horchata" data-price="2.50">Horchata</option>
<option value="Rosa Jamaica " data-price="2.50">Rosa Jamaica </option>
<option value="Jugo de naranja " data-price="2.50">Jugo de naranja </option>
<option value="Soda" data-price="1.00">Soda</option>
<option value="Botella de agua" data-price="1.00">Botella de agua</option>

		</select>
		<span id="precio-unitario-bebidas"></span>

  </div>
<label for="cantidad">Cantidad:</label>
<input type="text" name="cantidad" id="cantidad">
<label for="precio-total">Precio total:</label>
<span id="precio-total-seccion"></span>

  <script>
		  function mostrarOpciones() {
      var select = document.getElementById("selectTipo");
      var opcionesCafe = document.getElementById("opcionesCafe");
      var opcionesSalado = document.getElementById("opcionesSalado");
      var opcionesDulce = document.getElementById("opcionesDulce");
      var opcionesBebidas = document.getElementById("opcionesBebidas");

      if (select.value === "cafe") {
        opcionesCafe.style.display = "block";
        opcionesSalado.style.display = "none";
        opcionesDulce.style.display = "none";
        opcionesBebidas.style.display = "none";
      } else if (select.value === "salado") {
        opcionesCafe.style.display = "none";
        opcionesSalado.style.display = "block";
        opcionesDulce.style.display = "none";
        opcionesBebidas.style.display = "none";
      } else if (select.value === "dulce") {
        opcionesCafe.style.display = "none";
        opcionesSalado.style.display = "none";
        opcionesDulce.style.display = "block";
        opcionesBebidas.style.display = "none";
      } else if (select.value === "bebidas"){
        opcionesCafe.style.display = "none";
        opcionesSalado.style.display = "none";
        opcionesDulce.style.display = "none";
        opcionesBebidas.style.display = "block";
      } else {
        opcionesCafe.style.display = "none";
        opcionesSalado.style.display = "none";
        opcionesDulce.style.display = "none";
        opcionesBebidas.style.display = "none";}
}
    
  </script>





<script>
    const itemSelect = document.getElementById('selectTipo');
    const precioUnitarioCafe = document.getElementById('precio-unitario-cafe');
    const precioUnitarioSalado = document.getElementById('precio-unitario-salado');
    const precioUnitarioDulce = document.getElementById('precio-unitario-dulce');
    const precioUnitarioBebidas = document.getElementById('precio-unitario-bebidas');
    const cantidadInput = document.getElementById('cantidad');
    const precioTotal = document.getElementById('precio-total-seccion');

    itemSelect.addEventListener('change', () => {
      const itemSeleccionado = itemSelect.options[itemSelect.selectedIndex];
      const tipoSeleccionado = itemSelect.value;

      if (tipoSeleccionado === "cafe") {
        const precioCafe = parseFloat(itemSeleccionado.getAttribute('data-price'));
        precioUnitarioCafe.textContent = `$${precioCafe.toFixed(2)}`;
        precioUnitarioSalado.textContent = "";
        precioUnitarioDulce.textContent = "";
        precioUnitarioBebidas.textContent = "";
      } else if (tipoSeleccionado === "salado") {
        const precioSalado = parseFloat(itemSeleccionado.getAttribute('data-price'));
        precioUnitarioSalado.textContent = `$${precioSalado.toFixed(2)}`;
        precioUnitarioCafe.textContent = "";
        precioUnitarioDulce.textContent = "";
        precioUnitarioBebidas.textContent = "";
      } else if (tipoSeleccionado === "dulce") {
        const precioDulce = parseFloat(itemSeleccionado.getAttribute('data-price'));
        precioUnitarioDulce.textContent = `$${precioDulce.toFixed(2)}`;
        precioUnitarioCafe.textContent = "";
        precioUnitarioSalado.textContent = "";
        precioUnitarioBebidas.textContent = "";
      } else if (tipoSeleccionado === "bebidas") {
        const precioBebidas = parseFloat(itemSeleccionado.getAttribute('data-price'));
        precioUnitarioBebidas.textContent =`$${precioBebidas.toFixed(2)}`;
        precioUnitarioCafe.textContent = "";
        precioUnitarioSalado.textContent = "";
        precioUnitarioDulce.textContent = "";
      }
      else{
        precioUnitarioCafe.textContent = "";
        precioUnitarioSalado.textContent = "";
        precioUnitarioDulce.textContent = "";
        precioUnitarioBebidas.textContent = "";
      }

      calcularPrecioTotal();
    });

    cantidadInput.addEventListener("input", calcularPrecioTotal);


function calcularPrecioTotal() {
  var selectTipo = document.getElementById("selectTipo");
var selectCafe = document.getElementById("selectCafe");
var selectSalado = document.getElementById("selectSalado");
var selectDulce = document.getElementById("selectDulce");
var selectBebidas = document.getElementById("selectBebidas");
var cantidad = document.getElementById("cantidad").value;
var precioUnitario = 0;
var precioTotal = 0;

 if (selectTipo.value === "cafe") {
    precioUnitario = parseFloat(selectCafe.options[selectCafe.selectedIndex].getAttribute("data-price"));
  } else if (selectTipo.value === "salado") {
    precioUnitario = parseFloat(selectSalado.options[selectSalado.selectedIndex].getAttribute("data-price"));
  } else if (selectTipo.value === "dulce") {
    precioUnitario = parseFloat(selectDulce.options[selectDulce.selectedIndex].getAttribute("data-price"));
  }
else if (selectTipo.value === "bebidas") {
    precioUnitario = parseFloat(selectBebidas.options[selectBebidas.selectedIndex].getAttribute("data-price"));
}
 precioTotal = precioUnitario * cantidad;


 

  document.getElementById("precio-unitario-cafe").innerHTML = "Precio unitario: $" + precioUnitario.toFixed(2);
  document.getElementById("precio-unitario-salado").innerHTML = "Precio unitario: $" + precioUnitario.toFixed(2);
  document.getElementById("precio-unitario-dulce").innerHTML = "Precio unitario: $" + precioUnitario.toFixed(2);
document.getElementById("precio-unitario-bebidas").innerHTML = "Precio unitario: $" + precioUnitario.toFixed(2);
 document.getElementById("precio-total-seccion").textContent = "$" + precioTotal.toFixed(2);
}
</script>

	<input type="submit" value="Agregar al carrito">
</form>
</div>

<div class="scrollable-section">
<h2>Menú</h2>
<?php
$menu = array(

	array("nombre" => "Espresso", "descripcion" => "Café espresso con un sabor complejo e intenso, con doble shot de café y hazlo más intenso
", "precio" => 2.50, "imagen"=>"imagenes1/espresso.png"),
	array("nombre" => "Capuchino", "descripcion" => "Nuestro intenso espresso con leche y una capa de espuma cremosa", "precio" => 2.50, "imagen" => "imagenes1/cappu.png"),
	array("nombre" => "Latte Macchiato", "descripcion" => "Leche vaporizada finalizada con dos shot de espresso", "precio" => 2.50, "imagen"=>"imagenes1/latte.png"),
	array("nombre" => "Mocha", "descripcion" => "Intenso espresso, chocolate y leche cremosa, recubierto con nata montada", "precio" => 2.50, "imagen"=>"imagenes1/mocha.png"),
	array("nombre" => "Frappuccino Mocha", "descripcion" => "Base de café arábiga, intenso chocolate, leche y hielo. Todo ello batido y bien frío", "precio" => 3.50, "imagen"=>"imagenes1/frapmocha.png"),
	array("nombre" => "Frappuccino Café", "descripcion" => "Base de café expreso y leche, mezclado con hielo. Todo ello batido y bien frío
", "precio" => 3.50, "imagen"=>"imagenes1/frapcafe.png"),

	array("nombre" => "Croissant jamón y queso", "descripcion" => "Croissant francés horneado y preparado en tienda cada día, con queso Eddam y jamón york", "precio" => 3.50, "imagen"=>"imagenes1/croissant.png"),
	array("nombre" => "Pollo y Mozzarella", "descripcion" => "Sándwich de pollo con queso mozzarella.", "precio" => 3.50, "imagen"=>"imagenes1/polloymozarella.png"),
	array("nombre" => "Bocadillo Jamón, Huevo y Queso", "descripcion" => "Disfrute de un sabroso jamón, queso derretido y claras de huevo con vegetales", "precio" => 3.50, "imagen"=>"imagenes1/jamonyhuevo.png"),
array("nombre" => "Bocadillo tocino, frijoles y aguacate", "descripcion" => "Disfrute de un sabroso tocino, frijoles frescos y aguacate", "precio" => 2.50,"imagen"=>"imagenes1/frijolyaguacate.png"),
	array("nombre" => "Bocadillo Carne y Queso", "descripcion" => "Rebanadas de filete de carne caliente, tierno y jugoso servidas en el pan. ", "precio" => 4.50, "imagen"=>"imagenes1/carneyqueso.png"),
	array("nombre" => "Ensalada de pollo ", "descripcion" => "Vegetales trozados, como lechuga, cebollas coloradas, tomates, pepinos y pollo", "precio" => 5.50, "imagen"=>"imagenes1/ensaladapollo.png"),
	array("nombre" => "Ensalada de jamón", "descripcion" => "Vegetales trozados, como lechuga, cebollas coloradas, tomates, pepinos y pollo", "precio" => 5.50,"imagen"=>"imagenes1/ensaladajamon.png"),

	array("nombre" => "Alfajores", "descripcion" => "Disfrute de galletas horneadas, separadas entre sí por relleno de tofi", "precio" => 1.50,"imagen"=>"imagenes1/alfajor.png"),
	array("nombre" => "Brownie Cake", "descripcion" => "Bizcocho de chocolate que contiene nueces y, mantequilla de cacahuate
", "precio" => 1.50, "imagen"=>"imagenes1/brownie.png"),
	array("nombre" => "Cachito simple ", "descripcion" => "Disfrute de un pancito simple de masa horneada ", "precio" => 1.00, "imagen"=>"imagenes1/cachito.png"),
	array("nombre" => "Pan tostado ", "descripcion" => "Variedad de pan tostado", "precio" => 1.50, "imagen"=>"imagenes1/pan.png"),
	array("nombre" => "Pastel de frutas", "descripcion" => "Disfrute de una tarta de frutas frescas es todo un clásico de la repostería", "precio" => 2.50, "imagen"=>"imagenes1/pastel.png"),

array("nombre" => "Limonada con fresa", "descripcion" => "Una refrescante combinación de limones recién exprimidos y jugosas fresas", "precio" => 2.50, "imagen"=>"imagenes1/limonada.png"),
array("nombre" => "Horchata", "descripcion" => "Una refrescante bebida hecha con chufa, agua y azúcar.", "precio" => 2.50, "imagen"=>"imagenes1/horchata.png"),
array("nombre" => "Rosa Jamaica", "descripcion" => "Disfrute de una tarta de frutas frescas es todo un clásico de la repostería", "precio" => 2.50, "imagen"=>"imagenes1/jamaica.png"),
array("nombre" => "Jugo de naranja", "descripcion" => "Hecho con naranjas naturales", "precio" => 2.50, "imagen"=>"imagenes1/naranja.png"),
array("nombre" => "Soda", "descripcion" => "Disfrute de una fresca soda en lata", "precio" => 1.00, "imagen"=>"imagenes1/soda.png"),
    array("nombre" => "Botella de agua", "descripcion" => "Agua fresca", "precio" => 1.00,"imagen"=>"imagenes1/agua.png"));

foreach ($menu as $item) {
    echo "<h3>" . $item['nombre'] . "</h3>";
    echo "<p>" . $item['descripcion'] . "</p>";
    echo "<div class='imagen-container'>";
    echo "<img class='menu-imagen' src='" . $item['imagen'] . "' alt='" . $item['nombre'] .    "'>";
    echo "</div>";
    echo "<p>Precio: $" . number_format($item['precio'], 2) . "</p>";
}
?>
</div>
</body>
</html>







