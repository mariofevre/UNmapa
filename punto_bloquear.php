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

if(!isset($_POST['aid'])){
	$Log['tx'][]='error, falta variable aid (actividad id)';
	$Log['res']='err';
	terminar($Log);
}

if(!isset($_POST['rid'])){
	$Log['tx'][]='error, falta variable rid (registro id)';
	$Log['res']='err';
	terminar($Log);
}

if(!isset($_POST['uid'])){
	$Log['tx'][]='error, falta variable uid (usuario id)';
	$Log['res']='err';
	terminar($Log);
}

if(!isset($_POST['zz_bloqueadoTx'])){
	$Log['tx'][]='error, falta variable zz_bloqueadoTx (texto justificatorio)';
	$Log['res']='err';
	terminar($Log);
}
if($_POST['zz_bloqueadoTx']==''){
	$Log['tx'][]='error, falta variable zz_bloqueadoTx (texto justificatorio)';
	$Log['res']='err';
	terminar($Log);
}

if($_POST['uid']!=$UsuarioI){
	$Log['tx'][]='error, falta variable uid (usuario id)';
	$Log['mg'][]='ha habido un error en la validació de su seción. vuelva a ingresar al sistema y vuelva a intentarle.';
	$Log['res']='err';
	terminar($Log);
}

foreach($_POST as $k =>$v){
	$_POST[$k]=utf8_decode($v);
}


$query="
	SELECT 
		`actividades`.`id`,
	    `actividades`.`abierta`,
	    `actividades`.`resumen`,
	    `actividades`.`consigna`,
	    `actividades`.`marco`,
	    `actividades`.`nivel`,
	    `actividades`.`x0`,
	    `actividades`.`y0`,
	    `actividades`.`xF`,
	    `actividades`.`yF`,
	    `actividades`.`imx0`,
	    `actividades`.`imy0`,
	    `actividades`.`imxF`,
	    `actividades`.`imyF`,
	    `actividades`.`geometria`,
	    `actividades`.`adjuntosAct`,
	    `actividades`.`adjuntosDat`,
	    `actividades`.`adjuntosExt`,
	    `actividades`.`valorAct`,
	    `actividades`.`valorDat`,
	    `actividades`.`valorUni`,
	    `actividades`.`textobreveAct`,
	    `actividades`.`textobreveDat`,
	    `actividades`.`categAct`,
	    `actividades`.`categDat`,
	    `actividades`.`categLib`,
	    `actividades`.`textoAct`,
	    `actividades`.`textoDat`,
	    `actividades`.`objeto`,
	    `actividades`.`desde`,
	    `actividades`.`hasta`,
	    `actividades`.`resultados`,
	    `actividades`.`zz_AUTOUSUARIOCREAC`,
	    `actividades`.`zz_borrada`,
	    `actividades`.`zz_AUTOFECHACREACION`,
	    `actividades`.`zz_PUBLICO`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`
	WHERE 
		id='".$_POST['aid']."'
";

$Consulta = $Conec1->query($query);

if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= utf8_encode($query);
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}
$Actividad= $Consulta->fetch_assoc();
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
		id_actividades='".$_POST['aid']."'
		AND
		id_usuarios='".$UsuarioI."'

";
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}

$Actividad['docente']='0';

while($fila=$Consulta->fetch_assoc()){
	$Log['tx'][]=$fila['nivel'];
	$Log['tx'][]=$Actividad['zz_AUTOUSUARIOCREAC'];
	$Log['tx'][]=$UsuarioI;
	$Log['tx'][]='.';
	
	if($fila['nivel']>=3){
		$Actividad['docente']='1';
	}
}		


if($Actividad['zz_AUTOUSUARIOCREAC']==$UsuarioI){
	$Actividad['docente']='1';
}

if(
	$Actividad['docente']==0
){
	$Log['tx'][]= 'error al validar usuario';
	terminar($Log);		
}
	

$query="
	UPDATE
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`
	SET
	    `geodatos`.`zz_bloqueado` = '1',
	    `geodatos`.`zz_bloqueadoUsu` = '".$UsuarioI."',
	    `geodatos`.`zz_bloqueadoTx` = '".$_POST['zz_bloqueadoTx']."',
	    `geodatos`.`zz_bloqueadoFecha` = '".$HOY."'
	    
	WHERE
	    	`geodatos`.`id_actividades`= '".$_POST['aid']."'
		AND
			`geodatos`.id='".$_POST['rid']."'
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