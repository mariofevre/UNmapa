<?php
/**
*MAPAactividad.php
*
*
* aplicación para generar mapas para el dearrollo de una actividad, permitiendo la visualización y carga de puntos)
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

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}

//$MODO = $_GET['modo'];

$HOY=date("Y-m-d");

if(!isset($_POST['actividad'])){
	$Log['tx'][]='error, falta variable actividad';
	$Log['res']='err';
	terminar($Log);
}
	
$ID=$_POST['actividad'];

if(!isset($_POST['seleccion'])){
	$_POST['seleccion']='';
}

$seleccion=$_POST['seleccion'];

	
if(!isset($Freportedesde)){$Freportedesde = '9999-12-30';}
$starttimef = microtime(true);
if(!isset($Freportehasta)||$Freportehasta=='0000-00-00'){$Freportehasta = '9999-12-30';}

	
	//consulta categorias utilizadas para la actividad seleccionada
	if($ID!=''){$andid = " AND `ACTcategorias`.`id_p_actividades_id` = '".$ID."'";}else{$andid='';}
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
			1=1
			$andid
		
		ORDER BY 
			`ACTcategorias`.`orden` ASC
	";
	$ConsultaACTclases = mysql_query($query,$Conec1);
	echo mysql_error($Conec1);	
	//echo $query;
	while($fila=mysql_fetch_assoc($ConsultaACTclases)){
		$ActCat[$fila['id_p_actividades_id']][$fila['id']]=$fila;
		
		if($fila['zz_fusionadaa']>0){
			$dest=$fila['zz_fusionadaa'];
		}else{
			$dest=$fila['id'];
		}
		
		$CatConversor[$fila['id']]=$dest;
	}
	//print_r($ActCat);	
	//echo "<pre>";print_r($CatConversor);echo "</pre>";
	
	// consulta la clasificación de roles según sistema
	$query="SELECT 
		`SISroles`.`id`,
	    `SISroles`.`nombre`,
	    `SISroles`.`descripción`
	FROM `UNmapa`.`SISroles`;
	";
	$ConsultaSISroles = mysql_query($query,$Conec1);
	
	if(mysql_error($Conec1)!=''){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['res']='err';
		terminar($Log);
	}
	//echo $query;
	while($row=mysql_fetch_assoc($ConsultaSISroles)){
		$Roles[$row['id']]=$row;
	}	
	
	//consulta categorias utilizadas para la actividad seleccionada
	if($ID!=''){$andid = " AND `ACTaccesos`.`id_actividades` = '".$ID."'";}else{$andid='';}
	$query="
		SELECT 
			`ACTaccesos`.`id`,
		    `ACTaccesos`.`id_actividades`,
		    `ACTaccesos`.`id_usuarios`,
		    `ACTaccesos`.`nivel`,
		    `ACTaccesos`.`autorizado`
		FROM `UNmapa`.`ACTaccesos`
		WHERE
			1=1
			$andid

	";
	$ConsultaACTaccesos = mysql_query($query,$Conec1);
	if(mysql_error($Conec1)!=''){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['res']='err';
		terminar($Log);
	}
	
	//echo $query;
	while($fila=mysql_fetch_assoc($ConsultaACTaccesos)){
		
		$ActAcc[$fila['id_actividades']]['Acc'][$fila['nivel']][$fila['id']]=$fila;
		
		if($fila['nivel']=='2'){
			$ActAcc[$fila['id_actividades']]['acc']['editores'][$fila['id']]=$fila;
		}elseif($fila['nivel']=='1'){
			$ActAcc[$fila['id_actividades']]['acc']['participantes'][$fila['id']]=$fila;
		}		
	}
	//echo "<pre>";print_r($ActAcc);echo "</pre>";	
	
		
	
	// consulta las características de la actividad seleccionada	
	if($ID!=''){$andid = " AND `actividades`.`id` = '".$ID."'";}else{$andid='';}
	$query="
	 
		SELECT 
		
			`actividades`.`id`,
			`actividades`.`abierta`,
			
			`actividades`.`resumen`,
		    `actividades`.`consigna`,
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
		    `actividades`.`marco`,
		    `actividades`.`nivel`,
		    `actividades`.`zz_AUTOUSUARIOCREAC`,
		    `actividades`.`zz_AUTOFECHACREACION`,
		    `actividades`.`zz_PUBLICO`,
		    usuarios.nombre as Unombre,
		    usuarios.apellido as Uapellido,
		    (select count(1) from `geodatos` WHERE `geodatos`.id_actividades = `actividades`.id and `geodatos`.zz_borrada='0') cantidadPuntos
		    
		FROM `UNmapa`.`actividades`	
		
		LEFT JOIN 
			usuarios
			ON usuarios.id = actividades.zz_AUTOUSUARIOCREAC
		
		WHERE
			(`zz_AUTOUSUARIOCREAC`='$UsuarioI'
			OR
			`actividades`.`zz_PUBLICO` ='1')
			AND
			`actividades`.`zz_borrada` !='1'
			
			$andid
		
		ORDER BY 
			`actividades`.`zz_AUTOFECHACREACION` DESC
	
	";	
	$ConsultaACT = mysql_query($query,$Conec1);
	if(mysql_error($Conec1)!=''){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['res']='err';
		terminar($Log);
	}
	
	$query="
	SELECT `usuarios`.`id`,
	    `usuarios`.`nombre`,
	    `usuarios`.`apellido`,
	    `usuarios`.`organizacion`,
	    `usuarios`.`area`,
	    `usuarios`.`nivel`,
	    `usuarios`.`nacimiento`,
	    `usuarios`.`mail`,
	    `usuarios`.`telefono`,
	    `usuarios`.`log`
	FROM `UNmapa`.`usuarios`
	";	
	$ConsultaUsu = mysql_query($query,$Conec1);
	if(mysql_error($Conec1)!=''){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=mysql_fetch_assoc($ConsultaUsu)){
		$Usuarios[$fila['id']]=$fila;		
	}
		
	foreach($ActAcc as $Kact => $Vact){
		foreach($Vact['Acc'] as $kacN => $vacN){
			foreach($vacN as $kacc => $vacc){
				$ActAcc[$Kact]['Acc'][$kacN][$kacc]['usuario']=$Usuarios[$vacc['id_usuarios']];			
			}
		}
	}	

				
	foreach($ActAcc as $Kact => $Vact){
		foreach($Vact['acc']['participantes'] as $kacc => $vacc){
			$ActAcc[$Kact]['acc']['participantes'][$kacc]['usuario']=$Usuarios[$vacc['id_usuarios']];			
		}
		foreach($Vact['acc']['editores'] as $kacc => $vacc){
			$ActAcc[$Kact]['acc']['editores'][$kacc]['usuario']=$Usuarios[$vacc['id_usuarios']];			
		}
	}	
		
	echo mysql_error($Conec1);
	while($fila=mysql_fetch_assoc($ConsultaACT)){
		
		$cat['ACTcategorias']=array();
		$cat['ACTcategorias']=$ActCat[$fila['id']];
		
		$ACT[$fila['id']]=$fila;
		
		$a[0]=array_slice($fila,0,20);//corta el array de datos e intercala el listado de categorías-
		$a[1]=array_slice($fila,20);
		
		$ACT[$fila['id']]=array_merge($a[0],$cat,$a[1]);	
		
		$ACT[$fila['id']]['acc']=$ActAcc[$fila['id']]['acc'];
		$ACT[$fila['id']]['acc']['editores']['n']['id']='n';
		$ACT[$fila['id']]['acc']['editores']['n']['id_actividades']=$ID;
		$ACT[$fila['id']]['acc']['editores']['n']['id_usuarios']=$fila['zz_AUTOUSUARIOCREAC'];
		$ACT[$fila['id']]['acc']['editores']['n']['usuario']=$Usuarios[$fila['zz_AUTOUSUARIOCREAC']];	
		
		$ACT[$fila['id']]['Acc']=$ActAcc[$fila['id']]['Acc'];
		$ACT[$fila['id']]['Acc']['3']['n']['id']='n';
		$ACT[$fila['id']]['Acc']['3']['n']['id_actividades']=$ID;
		$ACT[$fila['id']]['Acc']['3']['n']['id_usuarios']=$fila['zz_AUTOUSUARIOCREAC'];
		$ACT[$fila['id']]['Acc']['3']['n']['usuario']=$Usuarios[$fila['zz_AUTOUSUARIOCREAC']];
		
		//echo "<pre>";print_r($ACT[$fila['id']]);echo "</pre>";
	}
	//echo "<pre>";print_r($ACT);echo "</pre>";
	/*if($ID!=''&&mysql_num_rows($ConsultaARG)==0){
		$ACT[]['resumen']="error en la selección de la argumentación";
	}*/	
	
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
		LEFT JOIN geodatos
		ON geodatos.id=atributos.id
		WHERE
		geodatos.zz_borrada='0'
		;
	";
	$ConsultaATT = mysql_query($query,$Conec1);
	if(mysql_error($Conec1)!=''){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=mysql_fetch_assoc($ConsultaATT)){
		$cat=$CatConversor[$fila['categoria']];
		$ATT[$fila['id']]=$fila;
		$ATT[$fila['id']]['categoria']=$cat;
	}
	
	
	if (isset($seleccion['zoom']))
		$zoom = $seleccion['zoom'];
	else
		$zoom = 0;
	
	$query="SELECT 
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
	where zz_borrada='0'
	and id_usuarios>0
	";
	
	$ConsultaGEO = mysql_query($query,$Conec1);
	if(mysql_error($Conec1)!=''){
		$Log['tx'][]=mysql_error($Conec1);
		$Log['res']='err';
		terminar($Log);
	}
	
	if ($ConsultaGEO != null) {
		while($fila=mysql_fetch_assoc($ConsultaGEO)){
			if(isset($ACT[$fila['id_actividades']])){
				if(!isset($ATT[$fila['id']])){continue;}
				$f=array_merge($fila,$ATT[$fila['id']]);
				$ACT[$fila['id_actividades']]['GEO'][$fila['id']]=$f;
				$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['Usuario']=$Usuarios[$fila['id_usuarios']];								
				$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['categoriaTx']=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['nombre'];
				$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['categoriaDes']=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['descripcion'];
				$ACT[$fila['id_actividades']]['GEO'][$fila['id']]['categoriaCo']=$ActCat[$fila['id_actividades']][$ATT[$fila['id']]['categoria']]['CO_color'];

				$ACT[$fila['id_actividades']]['categoriaspuntos'][$ATT[$fila['id']]['categoria']]++;
			}		
		}	
	}


$Log['data']=$ACT;
terminar($Log);
?>

