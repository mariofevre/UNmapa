<?php 
/**
* actividades_descarga_ajax.php
*
* aplicación que genera un archivo de descarga para bajar el contenido de una actividad.
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


// verificaciÃƒÂ³n de seguridad 
include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include_once('./includes/cadenas.php');

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){header('Location: ./login.php');}


$Log=array();
function terminar($Log){
	echo json_encode($Log);
	exit;
}

if($_POST['idactividad']<'1'){
	$Log['res']='err';
	terminar($Log);
}

//ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);
include_once('./actividades_consulta.php');
$Actividad=reset(actividadesconsulta($_POST['idactividad'],null));//carga los datos de la aactividad mediante esta funcion en atctividades_consulta.php

$Log['tx'][]="cargando actividadesconsulta ".$_POST['idactividad']." , null)";

$encabezado="id;lon;lat;zoom;fecha;autor;catid;catnombre;texto;textobre;link;".PHP_EOL;
$Csv.=$encabezado;

//print_r($Actividad['GEO']);
foreach($Actividad['GEO'] as $gid => $gdata ){
	if($gdata['zz_bloqueado']!='0'){continue;}
	$fila="";
	unset($gd);
	$fn++;
	
	foreach($gdata as $k =>$v){		
		if(!is_array($v)){
			
			$csvtx=str_replace('"','´',strip_tags($v));
			$csvtx=str_replace("'",'´',strip_tags($csvtx));			
			$csvtx=eliminarCodigoAsciiAcentos($csvtx);
			
			$csvtx=str_replace(';',',',$csvtx);
			
			$csvtx=str_replace(PHP_EOL,' ',$csvtx);
			$csvtx = str_replace(array("\r", "\n"), '', $csvtx);
			
			$gd[$k]=$csvtx;
			
		}
	}
	
	$fila="";
	$fila.=$gid.";";
	$fila.=$gd['x'].";";
	$fila.=$gd['y'].";";
	$fila.=$gd['z'].";";
	$fila.=$gd['fecha'].";";
	$usu=$gdata['Usuario']['nombre']." ".$gdata['Usuario']['apellido']." (".$gdata['Usuario']['organizacion'].")";
	$fila.=eliminarCodigoAsciiAcentos($usu).';';
	
	$fila.=$gd['categoria'].";";
	$fila.=$gd['categoriaTx'].';';
	
	$fila.=$gd['texto'].';';
	
	$fila.=$gd['textobreve'].';';
	
	$fila.=$gd['link'].';';
	
	$fila.=PHP_EOL;
	$Csv.=$fila;
}



$Log['tx'][]='cargadas $fn filas';



$path='./documentos/';
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}
$path .= 'actividades/';
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}
$carpeta = str_pad($_POST['idactividad'], 8, '0', STR_PAD_LEFT)."/";
$Log['tx'][]= $carpeta;
$path .= $carpeta;
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}
$path .= 'temp/';
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}
$file=$padId."_salidatxt_".date("Y").date("m").date("d").date("H").date("i").date("s").".csv";
$des=$path.$file;
file_put_contents($des,$Csv);

$Log['res']='exito';
$Log['data']['url']=$des;

terminar($Log);
?>