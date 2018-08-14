<?php 
/**
* actividades.php
*
* aplicaci�n para listar y acceder a las distintas actividades desarrolladas sobre esta plataorma
 * 
 *  
* @package    	Plataforma Colectiva de Informaci�n Territorial: UBATIC2014
* @subpackage 	actividad
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicaci�n se desarrollo sobre una publicaci�n GNU 2014 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los t�rminos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU General Public License" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
*/

ini_set('display_errors', '1');
//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificaci�n de seguridad 
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){header('Location: ./login.php');}

// funci�n de consulta de actividades a la base de datos
include("./actividades_consulta.php");

$ID = isset($_GET['actividad'])?$_GET['actividad'] : '';

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

	
// medicion de rendimiento lamp 
	$starttime = microtime(true);

// filtro de representaci�n restringe documentos visulazados, no altera datos estadistitico y de agregaci�n 
	$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';	
	
// filtro temporal de representaci�n restringe documentos visulazados, no altera datos estadistitico y de agregaci�n 	
	$fechadesde_a=isset($_GET['fechadesde_a'])?str_pad($_GET['fechadesde_a'], 4, "0", STR_PAD_LEFT):'0000';
	$fechadesde_m=isset($_GET['fechadesde_m'])?str_pad($_GET['fechadesde_m'], 2, "0", STR_PAD_LEFT):'00';
	$fechadesde_d=isset($_GET['fechadesde_d'])?str_pad($_GET['fechadesde_d'], 2, "0", STR_PAD_LEFT):'00';
	if($fechadesde_a!='0000'&&$fechadesde_m!='00'&&$fechadesde_d!='00'){
		$FILTROFECHAD=$fechadesde_a."-".$fechadesde_m."-".$fechadesde_d;
	}else{
		$FILTROFECHAD='';
	}
	$fechahasta_a=isset($_GET['fechahasta_a'])?str_pad($_GET['fechahasta_a'], 4, "0", STR_PAD_LEFT):'0000';
	$fechahasta_m=isset($_GET['fechahasta_m'])?str_pad($_GET['fechahasta_m'], 2, "0", STR_PAD_LEFT):'00';
	$fechahasta_d=isset($_GET['fechahasta_d'])?str_pad($_GET['fechahasta_d'], 2, "0", STR_PAD_LEFT):'00';
	if($fechahasta_a!='0000'&&$fechahasta_m!='00'&&$fechahasta_d!='00'){
		$FILTROFECHAH=$fechahasta_a."-".$fechahasta_m."-".$fechahasta_d;
	}else{	
		$FILTROFECHAH='';
	}

	// funci�n para obtener listado formateado html de actividades 
	$Contenido =  actividadeslistado($ID, null);
?>
<!DOCTYPE html>
<html>
<head>
	<title>UNmapa - Listado de actividades</title>
	<?php include("./includes/meta.php");?>
	<link href="./css/UNmapa.css" rel="stylesheet" type="text/css">
	
	
	<style type='text/css'>
		.dato.fecha{
		    width: 60px;
		}
		.dato.autor{
		    width: 50px;
		}
		.dato.carga{
		    width: 30px;
		}				
		.dato.descripcion{
		    width: 210px;
		}		
		
		.dato.localizaciones, .dato.imagenes{
			font-size: 11px;
		}
		
		.elemento {
		    background-color: #ADD8E6;
		    border: 2px solid #08AFD9;
		    cursor: pointer;
		    display: inline-block;
		    font-size: 10px;
		    height: 14px;
		    overflow: hidden;
		    padding: 2px 1px;
		    position: relative;
		    width: 16px;
		 }
		 
		 .preliminar{
		 	background-color:#ddd;
		 	color:#444;
		 }
		 
		 
		 .resultado[estado='pendiente']{
			  background-color: #8ae;
			  color: #000;
			  border: 1px solid #55a;				
		}
			
		.resultado[estado='cerrada']{
			  background-color: #abf;
			  color: #000;
			  border: 1px solid #55f;
		}
		
		.resultado[estado='activa']{
		    background-color: #ffb;
		    border: 1px solid #f55;
		}
	</style>
	
	
</head>

<body>
	<script src="./js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
	<?php
	include('./includes/encabezado.php');
	?>
	
	<div id="pageborde"><div id="page">
		<h1>Actividades</h1>
		<p>Actividad, descripci�n de la actividad.</p>

		<iframe src='./MAPAgeneral.php'></iframe>
		<form method='post' onsubmit='crearActividad(event)'>
			<input type='submit' value='Crear una nueva actividad'>			
		</form>
		<div class='contenido'>
			<?php echo $Contenido;?>
		</div>				
	</div></div>
	
<script type='text/javascript'>

function crearActividad(_event){
	
	_event.preventDefault();
	_datos={
		'accion':'crear'
		}
	$.ajax({
		data: _datos,
		url:   './actividades_crear.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			
			console.log(_res);
			
			if(_res.res='exito'){
				window.location.reload();
			}
			
		}
	})
}
	
	
	
</script>
<?php
include('./_serverconfig/pie.php');
?>

</body>
<html>