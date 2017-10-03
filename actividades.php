<?php 
/**
* actividades.php
*
* aplicación para listar y acceder a las distintas actividades desarrolladas sobre esta plataorma
 * 
 *  
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	actividad
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2014 TReCC SA
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
*/


//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}

// función de consulta de actividades a la base de datos 
include("./actividades_consulta.php");

$ID = isset($_GET['actividad'])?$_GET['actividad'] : '';

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

// el reingreso a esta dirección desde su propio formulario php crea o modifica un registro de actividad 
if(isset($_POST['accion'])){
	$accion =$_POST['accion'];
	
	if($accion=='crear'&&$UsuarioI>0){
		$query="
		INSERT INTO 
			`UNmapa`.`actividades`
			SET
			`zz_AUTOUSUARIOCREAC`='".$UsuarioI."',
			`zz_AUTOFECHACREACION`='".$HOY."'
		";
		mysql_query($query,$Conec1);
		$NID=mysql_insert_id($Conec1);
		if($NID!=''){
			$ID=$NID;
			header('location: ./actividad_config.php?actividad='.$ID);
		}else{
			$mensaje.="<div class='error'>no se ha podido crear el nuevo registro, por favor vuelva a intentar</div>";
			$mensaje.= mysql_error($Conec1);
		}
		
	}
}

	
// medicion de rendimiento lamp 
	$starttime = microtime(true);

// filtro de representación restringe documentos visulazados, no altera datos estadistitico y de agregación 
	$FILTRO=isset($_GET['filtro'])?$_GET['filtro']:'';	
	
// filtro temporal de representación restringe documentos visulazados, no altera datos estadistitico y de agregación 	
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

	// función para obtener listado formateado html de actividades 
	$Contenido =  actividadeslistado($ID, null);
?>

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
	
	<?php
	include('./includes/encabezado.php');
	
	if($ID!=''){
	?>	
		<iframe class='recuadro' id="recuadro2" src="./argumentacionimagen.php"></iframe>
		<iframe class='recuadro' id="recuadro3" src="./argumentacionlocalizacion.php"></iframe>
	<?php
	}
	?>
	
	
	<div id="pageborde"><div id="page">
		<h1>Actividades</h1>
		<p>Actividad, descripción de la actividad.</p>

		<iframe src='./MAPAgeneral.php'></iframe>
		
		<?php

			// formulario para agregar una nueva actividad		
		if($ID==''){
			echo "<form method='post' action='./actividades.php'>";
			echo "<input type='submit' value='Crear una nueva actividad'>";
			echo "<input type='hidden' name='accion' value='crear'>";
			echo "<input type='hidden' name='tabla' value='actividades'>";			
			echo "</form>";
			echo "<div class='contenido'>";
			echo $Contenido;
			echo "</div>";				
		}
		?>
	
	</div></div>
	

<?php
include('./includes/pie.php');
	/*medicion de rendimiento lamp*/
	$endtime = microtime(true);
	$duration = $endtime - $starttime;
	$duration = substr($duration,0,6);
	echo "<br>tiempo de respuesta : " .$duration. " segundos";
?>
</body>
