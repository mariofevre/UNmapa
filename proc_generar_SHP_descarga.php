<?php
// eliminar esta funcion solo se utiliza para crear categorías, pero es muy insegura
/**
* agrega.php
*
* aplicación para generar nuevos registros en una base de datos a partir de la informaicón enviada vía  POST
* 
* @package    	intraTReCC
* @subpackage 	Comun
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2010-2015 TReCC SA
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
// verificaciÃƒÂ³n de seguridad 
include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include_once('./includes/cadenas.php');
ini_set('display_errors',true);
$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){header('Location: ./login.php');}


$Log=array();
$Log['res']='';
$Log['tx']=array();
$Log['mg']=array();
$Log['data']=array();
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}

/*if($_POST['idactividad']<'1'){
	$Log['res']='err';
	terminar($Log);
}*/


include_once('./actividades_consulta.php');
$Actividades=actividadesconsulta('',null);//carga los datos de la aactividad mediante esta funcion en atctividades_consulta.php



$Log['tx'][]="cargando actividadesconsulta , null)";


// Register autoloader
require_once('./terceres/Shapefile/ShapefileAutoloader.php');

Shapefile\ShapefileAutoloader::register();

// Import classes
use Shapefile\Shapefile;
use Shapefile\ShapefileException;
use Shapefile\ShapefileWriter;
use Shapefile\Geometry\Point;




$path='./documentos/';
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}

$path .= 'temp/';
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}
$Nom="UNmapa_".date("Y").date("m").date("d").date("H").date("i").date("s");
$file=$Nom.".shp";
$dest=$path.$file;

$Log['data']['ruta']=$path.$Nom.".zip";


try {
	
	$prjaux="./auxiliar/prj_4326.prj";
	$prjdest=$path.$Nom.".prj";
	
	if(file_exists($prjaux)){		
		copy( $prjaux , $prjdest );
	}
	
	 
    // Open Shapefile
    $Shapefile = new ShapefileWriter($dest);
    
    // Set shape type
    $Shapefile->setShapeType(Shapefile::SHAPE_TYPE_POINT);
    
    // Create field structure
    $Shapefile->addNumericField('ID', 10);
	$Shapefile->addNumericField('IDact', 10);
	$Shapefile->addCharField('actividad', 200);
	$Shapefile->addCharField('consigna', 200);
    $Shapefile->addCharField('usu', 200);
	
	$Shapefile->addNumericField('z', 50);
	$Shapefile->addCharField('fecha', 50);
	
	$Shapefile->addCharField('txbre', 50);
	$Shapefile->addCharField('texto', 250);
	$Shapefile->addCharField('num', 25);
	$Shapefile->addCharField('categ', 50);	
	$Shapefile->addCharField('categTx', 125);
	$Shapefile->addCharField('link', 250);
	
    $regs=0;
	foreach($Actividades as $idact => $Actividad){
		if($idact < 246){
			//esta actividad en realizada fue tomada prestada de un servidor previo.
			$Log['tx'][]='salteando actividad id: '.$idact;
			continue;			
		}
		foreach($Actividad['GEO'] as $gid => $gdata ){
			if($gdata['zz_bloqueado']!='0'){continue;}
			
			
			/*
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
			}*/
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
			
			
			if(abs($gd['x'])<0.1||abs($gd['y'])<0.1){
				
				$Log['tx'][]="este punto no fue bien cargado: ".$gd['x']." / ".$gd['y'];
				continue;
			}
			
			$regs++;
			$Point = new Point($gd['x'], $gd['y']);
	        // Set its data
	        $Point->setData('ID', $gid);
			$Point->setData('IDact', $idact);
			$Point->setData('actividad', $Actividad['resumen']);
	        $Point->setData('consigna', $Actividad['consigna']);
			$Point->setData('usu', $gdata['Usuario']['nombre']." ".$gdata['Usuario']['apellido']." (".$gdata['Usuario']['organizacion'].")");
			
			$Point->setData('z', $gd['z']);
			$Point->setData('fecha', $gd['fecha']);
			
			$Point->setData('txbre', $gd['textobreve']);
			$Point->setData('texto', $gd['texto']);
			$Point->setData('num', $gd['valor']);
			$Point->setData('categ', $gd['categoria']);
			$Point->setData('categTx', $gd['categoriaTx']);
			
			$Point->setData('link', 'http://190.111.246.33/extranet/UNmapa/'.$gd['link']);
	        // Write the record to the Shapefile
	        $Shapefile->writeRecord($Point);
	     }
    }
    
    // Finalize and close files to use them
    $Shapefile = null;
	
	
	chdir($path);	
	$comando='zip -r -j '.$Nom.".zip".' '.$Nom.'.*';
		 $Log['tx'][]=  $comando ;
	exec($comando,$exec_res);
 $Log['tx'][]=  $exec_res ;
	
} catch (ShapefileException $e) {
    // Print detailed error information
    $Log['tx'][]= "Error Type: " . $e->getErrorType();
    $Log['tx'][]= "\nMessage: " . $e->getMessage();
    $Log['tx'][]= "\nDetails: " . $e->getDetails();
	terminar($Log);
}

$Log['data']['registros']=$regs;
$Log['res']='exito';
terminar($Log);

?>
