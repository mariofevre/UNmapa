<?php 
/**
* actividades_consulta.php
*
* aplicación que consulta el listado de actividades presentadas.
* 
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	BASE
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

ini_set('display_errors',true);

include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");

$Log=array();
$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['res']='';

function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){header('Location: ./login.php');}

$HOY=date("Y-m-d");

if(!isset($_POST['id'])){
	$Log['tx'][]='error, falta variable id (punto id)';
	$Log['res']='err';
	terminar($Log);
}


if(!isset($_POST['xPsMerc'])){
	$Log['tx'][]='error, falta variable xPsMerc';
	$Log['res']='err';
	terminar($Log);
}

if(!isset($_POST['yPsMerc'])){
	$Log['tx'][]='error, falta variable yPsMerc';
	$Log['res']='err';
	terminar($Log);
}

foreach($_POST as $k =>$v){
	$_POST[$k]=utf8_decode($v);
}

$query="
	UPDATE
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`
	SET
		`xPsMerc` = '".$_POST['xPsMerc']."',
		`yPsMerc` = '".$_POST['yPsMerc']."'
	WHERE 
		`id` = '".$_POST['id']."'
		AND
		(
		xPsMerc='' OR xPsMerc is null
		OR 
		 yPsMerc='' OR yPsMerc is null)
";
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}

$Log['res']='exito';

terminar($Log);


?>