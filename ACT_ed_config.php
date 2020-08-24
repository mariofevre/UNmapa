<?php
/**
*ACT_ed_config
*
*
* modifica los atributos de una actividad a partir de los datos enviados por un usuario con permisos de coordinador.
* 
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	mapas
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
$Log['res']='';
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;
}


//include("./includes/BASEconsultas.php");
$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){
	echo "usuario no identificado";exit;
	header('Location: ./login.php');
}


//$MODO = $_GET['modo'];

$HOY=date("Y-m-d");

if(!isset($_POST['actividad'])){
	$Log['tx'][]='error, falta variable actividad';
	$Log['res']='err';
	terminar($Log);
}
	
$ID=$_POST['actividad'];

foreach($_POST as $k => $v){
	$_POST[$k]=utf8_decode($v);
}


$query="
	SELECT 
		zz_AUTOUSUARIOCREAC
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
	WHERE
		`id` = '".$ID."'

";
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['res']='err';
	terminar($Log);
}

$acc=0;
$fila=$Consulta->fetch_assoc();
if($fila['zz_AUTOUSUARIOCREAC']==$UsuarioI&&$UsuarioI>0){
	$acc=2;
}


$query="
	SELECT 
		`ACTaccesos`.`id`,
	    `ACTaccesos`.`id_actividades`,
	    `ACTaccesos`.`id_usuarios`,
	    `ACTaccesos`.`nivel`,
	    `ACTaccesos`.`autorizado`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTaccesos`
	WHERE
		`ACTaccesos`.`id_actividades` = '".$ID."'
		AND
		id_usuarios = '".$UsuarioI."'

";
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['res']='err';
	terminar($Log);
}

//echo $query;
if($Consulta->num_rows>1){
	
	while($fila=$Consulta->fetch_assoc()){
		$acc=max($acc,$fila['nivel']);
	}
}
$nivelmin=2;
if($acc<2){
	$Log['tx'][]=utf8_encode('el nivel de su usuario ('.$acc.') no alcanza el mínimo necesario para cambiar la configuraicón de una actividad ('.$nivelmin.')');
	$Log['res']='err';
	terminar($Log);		
}

if($_POST['accion']=='guardar'){
		
		$query="
	 
		UPDATE
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
		SET
					
			`actividades`.`resumen`='".$_POST['resumen']."',
		    `actividades`.`consigna`='".$_POST['consigna']."',
		    `actividades`.`x0`='".$_POST['x0']."',
		    `actividades`.`y0`='".$_POST['y0']."',
		    `actividades`.`xF`='".$_POST['xF']."',
		    `actividades`.`yF`='".$_POST['yF']."',
		    `actividades`.`imx0`='".$_POST['imx0']."',
		    `actividades`.`imy0`='".$_POST['imy0']."',
		    `actividades`.`imxF`='".$_POST['imxF']."',
		    `actividades`.`imyF` ='".$_POST['imyF']."',
		    `actividades`.`geometria`='".$_POST['geometria']."',
		    `actividades`.`adjuntosAct`='".$_POST['adjuntosAct']."',
		    `actividades`.`adjuntosDat`='".$_POST['adjuntosDat']."',
		    `actividades`.`adjuntosExt`='".$_POST['adjuntosExt']."',	    
		    `actividades`.`valorAct`='".$_POST['valorAct']."',
		    `actividades`.`valorDat`='".$_POST['valorDat']."',
		    `actividades`.`valorUni`='".$_POST['valorUni']."',
		    `actividades`.`textobreveAct`='".$_POST['textobreveAct']."',
		    `actividades`.`textobreveDat`='".$_POST['textobreveDat']."',    
		    `actividades`.`categAct`='".$_POST['categAct']."',
		    `actividades`.`categDat`='".$_POST['categDat']."',
		    `actividades`.`categLib`='".$_POST['categLib']."',
		    `actividades`.`textoAct`='".$_POST['textoAct']."',
		    `actividades`.`textoDat`='".$_POST['textoDat']."',
		    `actividades`.`objeto`='".$_POST['objeto']."',
		    `actividades`.`desde`='".$_POST['desde']."',
		    `actividades`.`hasta`='".$_POST['hasta']."',
		    `actividades`.`resultados`='".$_POST['resultados']."',
		    `actividades`.`marco`='".$_POST['marco']."',
		    `actividades`.`nivel`='".$_POST['nivel']."'

		WHERE
			id='$ID'
	";	
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]=utf8_encode($query);
		$Log['res']='err';
		terminar($Log);
	}
	
	$Log['tx'][]=utf8_encode($query);
		
	
}
	

if($_POST['accionpub']=='Publicar'){
	
	$query="
		UPDATE
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
		SET
		    `actividades`.`zz_PUBLICO`='1'	
		WHERE
			id='$ID'
	";
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]=utf8_encode($query);
		$Log['res']='err';
		terminar($Log);
	}
	
	$Log['tx'][]=utf8_encode($query);
}

if($_POST['accionelim']=='Confirmo Eliminar'){
			
	$query="
		UPDATE
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
		SET
		    `actividades`.`zz_borrada`='1'	
		WHERE
			id='$ID'
	";			
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=$Conec1->error;
		$Log['tx'][]=utf8_encode($query);
		$Log['res']='err';
		terminar($Log);
	}
	
	$Log['tx'][]=utf8_encode($query);
}		
		

			
			
$Log['res']='exito';
$Log['data']=$ACT;
terminar($Log);
?>

