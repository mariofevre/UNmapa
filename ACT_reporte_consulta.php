<?php 
/**
* actividad.php
*
* aplicación para cargar nuevos puntos de relevamiento
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
* @copyright	esta aplicación se desarrollo sobre una publicación GNU (agpl) 2014 TReCC SA
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

$Log=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['acc']='';
$Log['res']='';
$Log['data']='';
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}


$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){
	$e=explode('/',__FILE__);
	$f=$e[(count($e)-1)];
	$dest="DEST=$f";
	foreach($_GET as $kg => $Vg){
		$dest.='&'.$kg.'='.$Vg;		
		$Log['acc']='./login.php?'.$dest;
	}
	
}

// función de consulta de actividades a la base de datos 
include("./actividades_consulta.php");

$ID = isset($_GET['actividad'])?$_GET['actividad'] : '';
$RID = isset($_GET['registro'])?$_GET['registro'] : '';

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

// define si se esta defininedo una argumentación, de no ser así esta aplicación solo devuelve un mensaje escrito
if(isset($_POST['actividad'])){
	$Actividad=$_POST['actividad'];
	$ACCION = $_POST['accion'];
}elseif(isset($_GET['actividad'])){
	$Actividad=$_GET['actividad'];
}else{
	$Actividad='';
}
if($Actividad==''){
	$Log['tx']="ERROR de Acceso 1";
	$Log['res']="err";
	terminar($Log);
}


$UsuariosList = usuariosconsulta($ID);

// medicion de rendimiento lamp 
$starttime = microtime(true);

$FILTROFECHAH='';

$seleccion['zoom'] = 0;

// función para obtener listado formateado html de actividades 
$Contenido =  reset(actividadesconsulta($ID,$seleccion));
//echo "<pre>";print_r($Contenido);echo "</pre>";


foreach($Contenido['Acc'][2] as $acc => $accdata){
	if($accdata['id_usuarios']==$UsuarioI){
		$Coordinacion='activa';
	}
}
foreach($Contenido['Acc'][3] as $acc => $accdata){
	if($accdata['id_usuarios']==$UsuarioI){
		$Administracion='activa';
		$Coordinacion='activa';
	}
}

$Actividad=reset(actividadesconsulta($ID,$seleccion));
//echo "<pre>";print_r($Actividad);echo "</pre>";
if($Actividad['zz_PUBLICO']!='1'&&$Actividad['zz_AUTOUSUARIOCREAC']!=$UsuarioI){
	echo "<h2>Error en el acceso, esta actividad no se encuentra aún publicada y usted no se encuentra registrado como autor de la misma.</h2>";
	break;
}

$Log['data']=$Actividad;
$Log['res']='exito';
terminar($Log);
