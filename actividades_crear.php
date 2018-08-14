<?php 
/**
* actividades_crear.php
*
* crea una actividad nueva y devuelve un json con los resultados.
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

// el reingreso a esta dirección desde su propio formulario php crea o modifica un registro de actividad 

ini_set('display_errors',true);

include('./includes/conexion.php');
include('./includes/conexionusuario.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

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


if(!isset($_POST['accion'])){
	$Log['tx'][]='no se envío la variable accion';
	$Log['mg'][]='error de funcionamiento.';
	$Log['res']='err';
	terminar($Log);
}

if(
	$_POST['accion']!='crear'
	&&
	$_POST['accion']!='duplicar'
	){
	$Log['tx'][]='no se envío una accion valida';
	$Log['mg'][]='error de funcionamiento.';
	$Log['res']='err';
	terminar($Log);
}

if($_POST['accion']=='duplicar'){
	
	if(!isset($_POST['dupid'])){
		$Log['tx'][]='no se envío la id de la actividad a duplicar dupid';
		$Log['mg'][]='error de funcionamiento.';
		$Log['res']='err';	
	}
	
	if($_POST['dupid']<1){
		$Log['tx'][]='no se envío la id de la actividad a duplicar dupid';
		$Log['mg'][]='error de funcionamiento.';
		$Log['res']='err';	
	}
}

if($UsuarioI<1){
	$Log['tx'][]='sin id de usuario valido';
	$Log['mg'][]='no se identifica a su usuario para generar una nueva actividad';
	$Log['res']='err';
	terminar($Log);
}

if($_POST['accion']=='crear'){
	$query="
		INSERT INTO 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`
		SET
			`zz_AUTOUSUARIOCREAC`='".$UsuarioI."',
			`zz_AUTOFECHACREACION`='".$HOY."'
	";
		
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error en la consulta crear';
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=$query;
		$Log['res']='err';
		terminar($Log);
	}
		
	$Log['data']['nid']=$Conec1->insert_id;
	
	if($Log['data']['nid']==''){
		$Log['tx'][]='error en la consulta, no se generó un id válido';
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=$query;
		$Log['res']='err';
		terminar($Log);	
	}
	
	$Log['res']='exito';	
	terminar($Log);	
	
}elseif($_POST['accion']=='duplicar'){
	
	$query="
		SELECT 
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
		    `actividades`.`hasta`
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`
		WHERE
			id='".$_POST['dupid']."'
	";
	
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error en la consulta select actividades';
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=$query;
		$Log['res']='err';
		terminar($Log);
	}
	
	$fila=$Consulta->fetch_assoc();
	$set='';
	foreach($fila as $c=>$v){
		$set.=" $c = '$v',";
	}		
	
	$query="		
		INSERT INTO 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`
		SET
			$set
			`zz_AUTOUSUARIOCREAC`='".$UsuarioI."',
			`zz_AUTOFECHACREACION`='".$HOY."'
	";
		
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error en la consulta insertar';
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=$query;
		$Log['res']='err';
		terminar($Log);
	}
	
	$Log['data']['nid']=$Conec1->insert_id;
	
	if($Log['data']['nid']==''){
		$Log['tx'][]='error en la consulta, no se generó un id válido';
		$Log['res']='err';
		terminar($Log);		
	}
	
	$query="
		SELECT 
			`ACTcategorias`.`id`,
		    `ACTcategorias`.`id_p_actividades_id`,
		    `ACTcategorias`.`nombre`,
		    `ACTcategorias`.`descripcion`,
		    `ACTcategorias`.`orden`,
		    `ACTcategorias`.`CO_color`,
		    `ACTcategorias`.`zz_fusionadaa`,
		    `ACTcategorias`.`zz_AUTOUSUARIOCREAC`
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTcategorias`
		WHERE
			`id_p_actividades_id` = '".$_POST['dupid']."'		
	";
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]='error en la consulta';
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=$Consulta->fetch_assoc()){
		
		$set='';
		foreach($fila as $c=>$v){
			$set.=" $c = '$v',";
		}
		
		$query="
			INSERT INTO
				`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTcategorias`
			SET
				`id_p_actividades_id`='".$Log['data']['nid']."',
				`nombre`='".$fila['nombre']."',
				`descripcion`='".$fila['descripcion']."',
				`orden`='".$fila['orden']."',
				`CO_color`='".$fila['CO_color']."',
				`zz_AUTOUSUARIOCREAC`=$UsuarioI			
		";	
		$Consulta = $Conec1->query($query);
		if($Conec1->error!=''){
			$Log['tx'][]='error en la consulta';
			$Log['tx'][]=utf8_encode($Conec1->error);
			$Log['tx'][]=$query;
			$Log['res']='err';
			terminar($Log);
		}
		
		$naid=$Conec1->insert_id;
		if($naid==''){
			$Log['tx'][]='error en la consulta, no se generó un id válido';
			$Log['res']='err';
			terminar($Log);		
		}
		
	}
	
	$Log['res']='exito';
	
	terminar($Log);		
	
}



