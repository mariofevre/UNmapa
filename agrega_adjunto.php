<?php
/**
* agrega_adjunto.php
*
* aplicación para guradar y registrar en la base correspondiente un archivo subido por el usuario
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



ini_set('display_errors', 1);
include('./includes/conexion.php');

include('./includes/conexionusuario.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");
ini_set('display_errors', 1);
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
if($UsuarioI<1){
	$Log['tx'][]='error, usuario';
	$Log['mg'][]=utf8_encode('ha caducado su sesión');
	$Log['res']='err';
	terminar($Log);
}


$query="
	 
	SELECT 
		`actividades`.`id`,
		`actividades`.`abierta`,
	    `actividades`.`desde`,
	    `actividades`.`hasta`,
	    `actividades`.`zz_PUBLICO`
	    
	FROM `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
	WHERE
	id='".$_POST['actividad']."'
";	

$Consulta  = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]=$query;
	$Log['res']='err';
	terminar($Log);
}

if($Consulta->num_rows<1){
	$Log['tx'][]= "error en la identificacion de la actividad: ".$query;
	$Log['mg'][]= "error en la identificacion de la actividad: ";
	$Log['res']='err';
	terminar($Log);
}


$nombre = $_FILES['upload']['name'];
$b = explode(".",$nombre);

$Log['tx'][]="ingresando: $nombre";

$ext = strtolower($b[(count($b)-1)]);
$carpeta = str_pad($_POST['actividad'], 8, '0', STR_PAD_LEFT)."/";
if(!is_numeric($_POST['actividad'])){
	$Log['tx'][]= "error en el nombre de carpeta de guardado: ".$carpeta;
	$Log['mg'][]= "error en el nombre de carpeta de guardado: ".$carpeta;
	$Log['res']='err';
	terminar($Log);
}


$Log['tx'][]= $carpeta;
$path='./documentos/';
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}
$path .= 'actividades/';
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}
$path .= $carpeta;
if(!file_exists($path)){
	$Log['tx'][]= "creando carpeta $path";mkdir($path, 0777, true);chmod($path, 0777);	
}



$nuevonombre= $path."u".str_pad($UsuarioI, 6, '0', STR_PAD_LEFT)."f".date("Y-m-d-H-i-s").".".$ext;

$Log['tx'][]= "ingresando: $nuevonombre ( $ext )";

$extVal['jpg']='1';
$extVal['png']='1';
$extVal['tif']='1';
$extVal['bmp']='1';
$extVal['gif']='1';
$extVal['pdf']='1';
$extVal['zip']='1';
$extVal['mp3']='1';
$extVal['mp4']='1';
$extVal['pdf']='1';

if(!isset($extVal[strtolower($ext)])){
	$srt='';
	foreach($extVal as $k => $v){$srt.="$k, ";}
	$Log['tx'][]="solo se aceptan los formatos:".$srt;
	$Log['mg'][]="solo se aceptan los formatos:".$srt;
	$Log['res']='err';
	terminar($Log);
}

if (!copy($_FILES['upload']['tmp_name'], $nuevonombre)) {
	$Log['tx'][]="Error al copiar $nuevonombre";
	$Log['res']='err';
	terminar($Log);	    
}else{
	$Log['tx'][]= "archivo guardado. ";			
	$query="
	INSERT INTO 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`FILEadjuntos`
		SET
		`nombre`='$nombre',
		`ruta`='$nuevonombre',
		`fecha`='".date("Y-m-d")."',
		`hora`='".date("H-i-s")."',
		`usuario`='$UsuarioI',
		`actividad`=".$_POST['actividad']."				
	";
	
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error: '.$Conec1->error;
		$Log['res']='err';
		terminar($Log);
	}		

	$NID=$Conec1->insert_id;
	if($NID>0){
		$Log['tx'][]= "registro guardado. $nuevonombre ($ext)";
		
		$imgtVal['jpg']='1';
		$imgtVal['png']='1';
		$imgtVal['tif']='1';
		$imgtVal['bmp']='1';
		$imgtVal['gif']='1';
		
		$Log['data']['nuevonombre']=$nuevonombre;
		
		if(isset($imgtVal[strtolower($ext)])){
			
			$Log['data']['tipo']='imagen';

			$i = getimagesize($nuevonombre);
			$ladomayor=1000;
			if($i[0]>$ladomayor*1.3&&$i[1]>$ladomayor*1.3){
				escalarImagen($nuevonombre, $ladomayor, '_hd');
			}
														
			
			$i = getimagesize($nuevonombre);
			$ladomayor=100;
			if($i[0]>$ladomayor*1.3&&$i[1]>$ladomayor*1.3){
				escalarImagen($nuevonombre, $ladomayor, '_th');
			}				

		}else{
			
			$Log['data']['tipo']='archivo';

		}


		$Log['res']='exito';
		terminar($Log);
		
	}else{
		$Log['tx'][]= "no pudo guardare el registro, puede que el documento permanezca inaccesible o sea eliminado. ";
		$Log['tx'][]=print_r($_FILES['upload'],true);	
		$Log['res']='err';
		terminar($Log);	
	}
}


$Log['tx'][]= "no pudo guardare el registro, puede que el documento permanezca inaccesible o sea eliminado. ";
$Log['tx'][]=print_r($_FILES['upload'],true);	
$Log['res']='err';
terminar($Log);




										
function escalarImagen($localizacion, $ladomayor, $ident) {

	$filterType=imagick::FILTER_CUBIC;
	$blur=1;
	$bestFit=0;
	$cropZoom=0;
	
	$p=strrpos($localizacion,'.');
	
	$e = explode(".",$localizacion);
	$e = end($e);
	
	$nn=substr($localizacion, 0, $p).$ident.'.'.$e;
	
	copy($localizacion, $nn);
	chmod($nn,0777);
				
    $imagick = new \Imagick(realpath($nn));
	
	$ancho = $imagick->getImageWidth();
	$alto = $imagick->getImageHeight();	
	if($ancho>($ladomayor*1.3)||$alto>($ladomayor*1.3)){
		if($ancho>$alto){			
			$e=$ladomayor/$ancho;
		}else{
			$e=$ladomayor/$alto;
		}
		$nalto=round($alto*$e);		
		$nancho=round($ancho*$e);

	    $imagick->resizeImage($nancho, $nalto, $filterType, $blur, $bestFit);
	    $imagick->writeImage ($nn);
		chmod($nn,0777);
		$Log['tx'][] ="archivo resamplead e:$e ($ancho x $alto > $nancho x $nalto)";
	}
}

?>
