<?php 
/**
* registroactivar.php
*
* aplicación para avtivar usuarios registrados (instancia de validación mail.) 
* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	registro
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2014 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*
*/




include('./includes/conexion.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/class.phpmailer.php");

foreach($_GET as $key => $value) {
	$valor = trim(htmlentities(strip_tags($value)));
	$gets[$key]=$valor;
}

if(isset($gets['user'])) {
	
	$query = "
		SELECT 
			* 
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios` 
		WHERE zz_idactivacion = '".$gets['user']."'
	";
	$Consulta = $Conec1->query($query);
	if($Consulta->num_rows == 0){ 
	$mensage = "Disculpe, no existe la cuenta, el codigo de activacion es invalido, o la cuenta ya fue activada.";
	}else{
	$query = "
		UPDATE
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios` 
		SET
			zz_activo='1'	
		WHERE 
			zz_idactivacion='".$gets['user']."'
	";		
	$Consulta = $Conec1->query($query);
	$mensage = "
		Gracias. Su cuenta ha sido activada.
		<p>Para continuar hacia su cuenta puede <a href='login.php'>presionar aqui</a>.</p>
	";
	
	}
}else{
	$mensage = "Disculpe, no ha ingresado correctamente a este sitio.";
}

?>
<html>
<head>
	<title>SIGSAO - Registro de Actores para los procesos participativos</title>
	<?php include("./includes/meta.php");?>
	<link href="css/treccppu.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div id="pageborde"><div id="page">
	<h1>Activacion de cuenta</h1>
    <?php echo $mensage;?>
	</div></div>
	<?php
	include('./includes/pie.php');
	?>
</body>
</html>
