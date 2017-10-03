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

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}

$HOY=date("Y-m-d");

if(!isset($_POST['aid'])){
	$Log['tx'][]='error, falta variable aid (actividad id)';
	$Log['res']='err';
	terminar($Log);
}

if(!isset($_POST['uid'])){
	$Log['tx'][]='error, falta variable uid (usuario id)';
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
	FROM `UNmapa`.`actividades`
	WHERE 
	id='".$_POST['aid']."'
";

$Consulta = mysql_query($query,$Conec1);

if(mysql_error($Conec1)!=''){
	$Log['tx'][]=mysql_error($Conec1);
	$Log['tx'][]= utf8_encode($query);
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}
$Actividad=mysql_fetch_assoc($Consulta);
$query="	
	SELECT 
		`ACTaccesos`.`id`,
	    `ACTaccesos`.`id_actividades`,
	    `ACTaccesos`.`id_usuarios`,
	    `ACTaccesos`.`nivel`,
	    `ACTaccesos`.`autorizado`
	FROM 
		`UNmapa`.`ACTaccesos`
	WHERE
		id_actividades='".$_POST['aid']."'
		AND
		id_usuarios='".$usuarioI."'
";
$Consulta = mysql_query($query,$Conec1);
if(mysql_error($Conec1)!=''){
	$Log['tx'][]=mysql_error($Conec1);
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}
$Actividad['docente']='0';
while($fila=mysql_fetch_assoc($Consulta)){
	$Log['tx'][]=$fila['nivel'];
	$Log['tx'][]=$Actividad['zz_AUTOUSUARIOCREAC'];
	$Log['tx'][]=$UsuarioI;
	$Log['tx'][]='.';
	
	if($fila['nivel']>=3){
		$Actividad['docente']='1';
	}
}		


if(
	$Actividad['zz_PUBLICO']==0
	&&
	$Actividad['docente']==0
){
	$Log['tx'][]= 'error al validar usuario';
	terminar($Log);		
}
	




if(
	$_POST['nuevacategoria']!="-escriba el nombre de la nueva categoría-"
	&&
	$_POST['nuevacategoria']!=''
	&&
	$Actividad['categLib']==1
){
	
	$query="
	
	INSERT INTO
		 `UNmapa`.`ACTcategorias`
		SET
		`id_p_actividades_id`= '".$_POST['aid']."',
		`nombre`= '".$_POST['nuevacategoria']."',
		zz_AUTOUSUARIOCREAC= '".$UsuarioI."'
	";
	$Consulta = mysql_query($query,$Conec1);
	if(mysql_error($Conec1)!=''){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['tx'][]= $query;
		$Log['tx'][]= 'error al crear categoria';
		terminar($Log);
	}
	$CnID=mysql_insert_id($Conec1);
	
	if($CnID<1){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['tx'][]= $query;
		$Log['tx'][]= 'error al crear categoria';
		terminar($Log);
	}	
	$_POST['categoria']=$CnID;
}
	
$query="
	INSERT INTO
		`UNmapa`.`geodatos`
	SET
		`x` = '".$_POST['x']."',
		`y` = '".$_POST['y']."',
		`z` = '".$_POST['z']."',
		`id_usuarios` = '".$_POST['uid']."',
		`id_actividades` = '".$_POST['aid']."',
		`fecha` = '".$HOY."'
";
$Consulta = mysql_query($query,$Conec1);
if(mysql_error($Conec1)!=''){
	$Log['tx'][]=mysql_error($Conec1);
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}
$NID=mysql_insert_id($Conec1);

if($NID<1){
	$Log['tx'][]=mysql_error($Conec1);
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}

$query="

INSERT INTO 
	`UNmapa`.`atributos`
SET
	`atributos`.`id` = '".$NID."',
    `atributos`.`valor` = '".$_POST['valor']."',
    `atributos`.`categoria`= '".$_POST['categoria']."',
    `atributos`.`texto`= '".$_POST['texto']."',
    `atributos`.`textobreve`= '".$_POST['textobreve']."',
    `atributos`.`link`= '".$_POST['link']."',
    `atributos`.`id_usuarios`= '".$_POST['uid']."',
    `atributos`.`id_actividades`= '".$_POST['aid']."',
    `atributos`.`fecha` = '".$_POST['valor']."',
    `atributos`.`escala` = '".$_POST['valor']."',
    `atributos`.`nivelUsuario` = '".$$USUARIO['nivel']."',
    `atributos`.`areaUsuario` = '".$$USUARIO['organizacion']."'
";
$Consulta = mysql_query($query,$Conec1);
if(mysql_error($Conec1)!=''){
	$Log['tx'][]=mysql_error($Conec1);
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($Log);
}

$Log['res']='exito';

terminar($Log);


?>