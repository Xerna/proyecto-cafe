
// var btn = document.getElementById('cargar_usuarios');
// btn.addEventListener('click', function(){
//     var request = new XMLHttpRequest();
//     request.open('GET', 'datos.php');
//     request.onload = function(){
//         var datos = JSON.parse(request.responseText);
//         datos.forEach(orden => {
//             var elemento = document.createElement('tr');
//             elemento.innerHTML += ("<td>" + orden.order_number + "</td>");
//             elemento.innerHTML += ("<td>" + orden.order_table + "</td>");
//             elemento.innerHTML += ("<td>" + orden.order_details + "</td>");
//             elemento.innerHTML += ("<td>" + orden.order_total + "</td>");
//             document.getElementById('order_list').appendChild(elemento);
//         });
    
        
//     }
//     request.send();
// });

function loadXMLDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("lista-pedidos").innerHTML =
      this.responseText;
    }
  };
  xhttp.open("GET", "server.php", true);
  xhttp.send();
}
setInterval(function(){
	loadXMLDoc();
	// 1sec
},1000);

window.onload = loadXMLDoc;