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

if(!isset($_GET['aid'])){
	$Log['tx'][]='error, falta variable aid (actividad id)';
	$Log['res']='err';
	terminar($Log);
}
	
$ID=$_GET['aid'];

//echo json_encode($Res);

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
		id_actividades='".$ID."'
		AND
		id_usuarios='".$UsuarioI."'
";
$Consulta = mysql_query($query,$Conec1);

if(mysql_error($Conec1)!=''){
	$Log['tx'][]=mysql_error($Conec1);
	$Log['tx'][]= $query;
	$Log['tx'][]= 'error al consultar base';
	terminar($log);
}

$Act['docente']='0';
while($fila=mysql_fetch_assoc($Consulta)){
	$Log['tx'][]=$fila['nivel'];
	$Log['tx'][]=$Actividad['zz_AUTOUSUARIOCREAC'];
	$Log['tx'][]=$UsuarioI;
	$Log['tx'][]='.';	
	if($fila['nivel']>=3){
		$Act['docente']='1';
	}
}	
	


$Res['type']="FeatureCollection";

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
	    `actividades`.`hasta`,
	    `actividades`.`resultados`,
	    `actividades`.`zz_AUTOUSUARIOCREAC`,
	    `actividades`.`zz_borrada`,
	    `actividades`.`zz_AUTOFECHACREACION`,
	    `actividades`.`zz_PUBLICO`
	FROM `UNmapa`.`actividades`
	WHERE
	id='".$ID."'		
	
";
$Consulta = mysql_query($query,$Conec1);
echo mysql_error($Conec1);	
	
	//echo $query;
	while($fila=mysql_fetch_assoc($Consulta)){
		if($fila['zz_AUTOUSUARIOCREAC']==$UsuarioI){
			$Act['docente']='1';
		}
		
		foreach($fila as $k => $v){
			$Act[$k]=utf8_encode($v);
		}
	}

	
	$query="
		SELECT
			`usuarios`.`id`,
		    `usuarios`.`nombre`,
		    `usuarios`.`apellido`
		FROM `UNmapa`.`usuarios`
	";
	
	$ConsultaU = mysql_query($query,$Conec1);
	echo mysql_error($Conec1);	
	//echo $query;
	while($fila=mysql_fetch_assoc($ConsultaU)){
		$Usu[$fila['id']]=$fila;
	}

	$query="
		SELECT
			`ACTcategorias`.`id`,
		    `ACTcategorias`.`id_p_actividades_id`,
		    `ACTcategorias`.`nombre`,
		    `ACTcategorias`.`descripcion`,
		    `ACTcategorias`.`orden`,
		    `ACTcategorias`.`zz_fusionadaa`,
		    CO_color
		FROM `UNmapa`.`ACTcategorias`
		WHERE
			id_p_actividades_id='".$ID."'		
		ORDER BY 
			`ACTcategorias`.`orden` ASC
	";
	$ConsultaACTclases = mysql_query($query,$Conec1);
	echo mysql_error($Conec1);	
	//echo $query;
	while($fila=mysql_fetch_assoc($ConsultaACTclases)){
		$ActCat[$fila['id_p_actividades_id']][$fila['id']]=$fila;
		// cambia de categoría aquellos puntos dentro de categorías que fueron colapsadas
		if($fila['zz_fusionadaa']>0){
			$dest=$fila['zz_fusionadaa'];
		}else{
			$dest=$fila['id'];
		}		
		$CatConversor[$fila['id']]=$dest;
	}

	$query="
		SELECT `atributos`.`id`,
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
		    `atributos`.`areaUsuario`
		FROM `UNmapa`.`atributos`
		WHERE
		id_actividades='".$ID."'
	";
	
	$ConsultaATT = mysql_query($query,$Conec1);
	echo mysql_error($Conec1);

	while($fila=mysql_fetch_assoc($ConsultaATT)){
		
		// cambia de categoría aquellos puntos dentro de categorías que fueron colapsadas
		if(!isset($CatConversor[$fila['categoria']])){$CatConversor[$fila['categoria']]='SD';}
		$cat=$CatConversor[$fila['categoria']];
		
		$f=$fila;
		$f['categoria']=$cat;
		$f['categoriaNom']=$ActCat[$ID][$cat]['nombre'];
		$f['categoriaDes']=$ActCat[$ID][$cat]['descripcion'];
		
		$ATT[$fila['id']]=$f;
	}

	if (isset($seleccion['zoom']))
		$zoom = $seleccion['zoom'];
	else
		$zoom = 0;
	
	$query="
		SELECT 
			`geodatos`.`id`,
		    `geodatos`.`x`,
		    `geodatos`.`y`,
		    `geodatos`.`z`,
		    `geodatos`.`zz_bloqueado`,
		    `geodatos`.`zz_bloqueadoUsu`,
		    `geodatos`.`zz_bloqueadoTx`,
		    `geodatos`.`geometria`,
		    `geodatos`.`id_usuarios`,
		    `geodatos`.`id_actividades`,
		    `geodatos`.`fecha`
			
		FROM `UNmapa`.`geodatos`
		where 
		zz_borrada='0'
		and 
		id_usuarios>0
		AND id_actividades='".$ID."'
	";	
	
	$ConsultaACT = mysql_query($query,$Conec1);
	echo mysql_error($Conec1);
		
	while($fila=mysql_fetch_assoc($ConsultaACT)){
		
		if(
			$fila['x']>180
			||$fila['y']>90
			||$fila['x']<-180
			||$fila['y']<-90
		){continue;}		
		if(!isset($ATT[$fila['id']])){continue;}
		
		if($Act['docente']!='1'&&$fila['zz_bloqueado']==1&&$fila['id_usuarios']!=$UsuarioI){continue;}
		
		
		unset($f);
		$f['type']="Feature";
		$f['geometry']['type']="Point";
		$f['geometry']['coordinates'][]=floatval($fila['x']);
		$f['geometry']['coordinates'][]=floatval($fila['y']);
		
		
		$f['properties']=$Act;
		foreach($ATT[$fila['id']] as $k => $v){
			$f['properties'][$k]=utf8_encode($v);
		}

			
		foreach($fila as $k => $v){
			$f['properties'][$k]=utf8_encode($v);
		}
		
		if(isset($ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['CO_color'])){			
			$f['properties']['style']["color"]=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['CO_color'];
		}else{			
			$f['properties']['style']["color"]="rgb(80,120,250)";
		}
		
		$f['properties']['style']['fill']='red';
		$f['properties']['style']['stroke-width']='3';
		$f['properties']['style']['fill-opacity']=0.6;
		
		if(isset($Usu[$fila['id_usuarios']])){
			$f['properties']['nombreUsuario']=utf8_encode($Usu[$fila['id_usuarios']]['nombre']." ".$Usu[$fila['id_usuarios']]['apellido']);
		}
    	$f['properties']['sel']='no';
		$Res['features'][]=$f;
		
		//$Res['features']=array_slice ( $Res['features'], 0 , 50);
		
	}

	terminar($Res);


?>