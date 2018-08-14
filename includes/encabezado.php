<?php
if(isset( $_SESSION['Unmapa'][$CU]->USUARIO['uid'])){
echo "<div class='recuadro' id='recuadro1'>";
echo "<p>hola: ".$_SESSION['Unmapa'][$CU]->USUARIO['nombre']." ".$_SESSION['Unmapa'][$CU]->USUARIO['apellido']."</p>";
echo "<a class='boton' href='./login.php'>cerrar sesión</a>";
echo "<a class='boton' href='./actividades.php'>salir al listado de actividades</a>";

echo "</div>";
}
?>
