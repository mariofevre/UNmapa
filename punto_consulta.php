<?php 
/**
* actividades_consulta.php
*
* aplicación que consulta el listado de actividades presentadas.
* 
* @package    	UNmapa Herramienta pedágogica para la construccion colaborativa del territorio.  
* @subpackage 	actividad
* @author     	Universidad Nacional de Moreno
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on proyecto Plataforma Colectiva de Información Territorial: UBATIC2014
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicación deriba de publicaciones GNU AGPL : Universidad de Buenos Aires 2015 / TReCC SA 2014
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

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){header('Location: ./login.php');}

$HOY=date("Y-m-d");

if(!isset($_POST['aid'])){
	$Log['tx'][]='error, falta variable aid (actividad id)';
	$Log['res']='err';
	terminar($Log);
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
		id = '".$_POST['aid']."'
";
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($log);
}
$fila=$Consulta->fetch_assoc();
foreach($fila as $k => $v){
	$Actividad[$k]=utf8_encode($v);
}


$Log['tx'][]=$fila['desde']."-".$fila['hasta'];

if($fila['hasta']!='0000-00-00'&&$fila['hasta']<$HOY){
	$Actividad['estado']="terminada";
}elseif($fila['desde']!='0000-00-00'&&$fila['desde']>$HOY){
	$Actividad['estado']="noinicio";
}else{
	$Actividad['estado']="activa";	
}

if($Actividad['estado']=="activa"&&!isset($_POST['pid'])){
	$Actividad['editor']="1";	
}else{
	$Actividad['editor']="0";
}
//luego se ajusta este valor si el autor y el usuario no coinciden



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
	terminar($log);
}
$Actividad['docente']='0';
while($fila= $Consulta->fetch_assoc()){
	$Log['tx'][]=$fila['nivel'];
	$Log['tx'][]=$Actividad['zz_AUTOUSUARIOCREAC'];
	$Log['tx'][]=$UsuarioI;
	$Log['tx'][]='.';
	
	if($fila['nivel']>=3){
		$Actividad['docente']='1';
	}
}	
$Log['tx'][]='autoria: '.$Actividad['zz_AUTOUSUARIOCREAC'].' vs '.$UsuarioI;
if($Actividad['zz_AUTOUSUARIOCREAC']==$UsuarioI){
	$Actividad['docente']='1';
}

$Log['data']['actividad']=$Actividad;	

$query="	
	SELECT `ACTcategorias`.`id`,
    `ACTcategorias`.`id_p_actividades_id`,
    `ACTcategorias`.`nombre`,
    `ACTcategorias`.`descripcion`,
    `ACTcategorias`.`orden`,
    `ACTcategorias`.`CO_color`,
    `ACTcategorias`.`zz_fusionadaa`
FROM 
	`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTcategorias`
WHERE
	id_p_actividades_id = '".$_POST['aid']."'
	AND zz_fusionadaa = '0'
	AND zz_borrada='0'
ORDER BY orden
";

$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($log);
}
	
while($fila= $Consulta->fetch_assoc()){
	unset($c);
	
	foreach($fila as $k => $v){
		$c[$k]=utf8_encode($v);
	}	
	$Log['data']['actividad']['categorias'][$fila['id']]=$c;	
	$Log['data']['actividad']['catOrden'][]=$fila['id'];		
	// cambia de categoría aquellos puntos dentro de categorías que fueron colapsadas
	if($fila['zz_fusionadaa']>0){
		$dest=$fila['zz_fusionadaa'];
	}else{
		$dest=$fila['id'];
	}		
	$CatConversor[$fila['id']]=$dest;
}


if(!isset($_POST['pid'])){
	$Log['tx'][]='no solicita punto, solo se enviaran datos de la actividad';
	$Log['res']='exito';
	terminar($Log);
}

$query="	
	SELECT
		`usuarios`.`id` as uid,
	    `usuarios`.`nombre`,
	    `usuarios`.`apellido`,
	    
	    `atributos`.`id`,
	    `atributos`.`valor`,
	    `atributos`.`categoria`,
	    `atributos`.`texto`,
	    `atributos`.`textobreve`, 
	    `atributos`.`link`,
	    `atributos`.`id_usuarios`,
	    `atributos`.`id_actividades`,
	    `atributos`.`fecha`,
	    `atributos`.`escala`,
	    `atributos`.`nivelUsuario`,
	    `atributos`.`areaUsuario`,	    

	    `geodatos`.`x`,
	    `geodatos`.`y`,
	    `geodatos`.`z`,
	    `geodatos`.`zz_bloqueado`,
	    `geodatos`.`zz_bloqueadoUsu`,
	    `geodatos`.`zz_bloqueadoTx`,
	    `geodatos`.`geometria`,
	    `geodatos`.`id_usuarios`,
	    `geodatos`.`id_actividades`,
	    `geodatos`.`fecha`,
	    
	    `usuarioblock`.`nombre` as zz_bloqueadoN,
		`usuarioblock`.`apellido` as zz_bloqueadoA
	    
	FROM 
	
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`,
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios` as usuarioblock,
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`atributos`,
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`
		
	WHERE
			geodatos.`id`='".$_POST['pid']."'
		AND
			`atributos`.`id`='".$_POST['pid']."'
		AND	
			`atributos`.`id_actividades`='".$_POST['aid']."'
		AND
			`usuarios`.`id`=`atributos`.`id_usuarios`
		AND
			(`usuarioblock`.`id`=`geodatos`.`zz_bloqueadoUsu` 
			OR
			 `geodatos`.`zz_bloqueadoUsu` is null)
";


$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($log);
}
$fila=$Consulta->fetch_assoc();
foreach($fila as $k => $v){
	$Punto[$k]=utf8_encode($v);
}

$cat=$CatConversor[$fila['categoria']];
$Punto['categoria']=$cat;
$Punto['categoriaNom']=$Log['data']['actividad']['categorias'][$cat]['nombre'];

$Punto['categoriaCol']=$Log['data']['actividad']['categorias'][$cat]['CO_color'];
if($Punto['categoriaCol']==''){
	$Punto['categoriaCol']="rgb(80,120,250)";
}

if(substr($Punto['link'],0,4)=='www.'||substr($Punto['link'],0,4)=='http'){
	$Punto['linkTipo']='weblink';
}else{
	$Punto['linkTipo']='locallink';
}


$extValImg['jpg']='1';
$extValImg['png']='1';
$extValImg['tif']='1';
$extValImg['bmp']='1';
$extValImg['gif']='1';
$extValDown['pdf']='1';
$extValDown['zip']='1';		
	
	
	
if(substr($Punto['link'],-4,1)=='.'){
	$ext=substr($Punto['link'],-3);	
	
	if(isset($extValImg[strtolower($ext)])){
		$Punto['linkForm']='imagen';		
	}elseif(isset($extValDown[strtolower($ext)])){
		$Punto['linkForm']='archivo';										
	}else{
		$Punto['linkForm']='desconocido';
	}
}


if($Log['data']['actividad']['estado']=="activa"&&$Punto['id_usuarios']==$UsuarioI){
	$Log['data']['actividad']['editor']="1";	
}else{
	$Log['data']['actividad']['editor']="0";
}


$Log['data']['punto']=$Punto;
$Log['res']='exito';

terminar($Log);


?>