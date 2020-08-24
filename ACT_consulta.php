<?php
/**
*MAPAactividad.php
*
*
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

if(!isset($_POST['seleccion'])){
	$_POST['seleccion']='';
}

$seleccion=$_POST['seleccion'];

	
if(!isset($Freportedesde)){$Freportedesde = '9999-12-30';}
$starttimef = microtime(true);
if(!isset($Freportehasta)||$Freportehasta=='0000-00-00'){$Freportehasta = '9999-12-30';}

	
	//consulta categorias utilizadas para la actividad seleccionada
	$query="
		SELECT
			`ACTcategorias`.`id`,
		    `ACTcategorias`.`id_p_actividades_id`,
		    `ACTcategorias`.`nombre`,
		    `ACTcategorias`.`descripcion`,
		    `ACTcategorias`.`orden`,
		    `ACTcategorias`.`zz_fusionadaa`,
		    CO_color
		FROM 
		
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTcategorias`
		WHERE
			id_p_actividades_id='".$ID."'
			AND
			zz_borrada='0'
		ORDER BY 
			`ACTcategorias`.`orden` ASC
	";
	$Consulta = $Conec1->query($query);
	
	if($Conec1->error!=''){
		$Log['tx'][]='error en la consulta';
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=utf8_encode($query);
		$Log['res']='err';
		terminar($Log);
	}
		
	//echo $query;
	while($fila= $Consulta->fetch_assoc()){
		foreach($fila as $k => $v){
			$ACT[$fila['id_p_actividades_id']]['categorias'][$fila['id']][$k]=utf8_encode($v);
		}
		$ACT[$fila['id_p_actividades_id']]['categorias'][$fila['id']]['cant']=0;
		
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
	$query="
		SELECT 
			`SISroles`.`id`,
		    `SISroles`.`nombre`,
		    `SISroles`.`descripción`
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`SISroles`;
	";
	$Consulta = $Conec1->query($query);
	
	if($Conec1->error!=''){
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=utf8_encode($query);
		$Log['res']='err';
		terminar($Log);
	}
	//echo $query;
	while($row=$Consulta->fetch_assoc()){
		foreach($row as $k => $v){
			$Roles[$row['id']][$k]=utf8_encode($v);
		}
	}	
	
	//consulta categorias utilizadas para la actividad seleccionada
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

	";
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=utf8_encode($query);
		$Log['res']='err';
		terminar($Log);
	}

	$ActAcc=array();
	$ActAcc[$ID]=array();
	$ActAcc[$ID]['Acc']=array();
	$ActAcc[$ID]['acc']['editores']=array();
	$ActAcc[$ID]['acc']['participantes']=array();
	while($fila=$Consulta->fetch_assoc()){
		foreach($fila as $k => $v){
			$ActAcc[$fila['id_actividades']]['Acc'][$fila['nivel']][$fila['id']][$k]=utf8_encode($v);
		}
		
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
		    
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`
		
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
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['tx'][]=utf8_encode($query);
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
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
	";	
	$ConsultaU = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=$ConsultaU->fetch_assoc()){	
		foreach($fila as $k => $v){
			$Usuarios[$fila['id']][$k]=utf8_encode($v);
		}	
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
		
	echo $Conec1->error;
	while($fila=$Consulta->fetch_assoc()){
		
		foreach($fila as $k => $v){
			$ACT[$fila['id']][$k]=utf8_encode($v);
		}
			
		
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
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`atributos`
		LEFT JOIN geodatos
		ON geodatos.id=atributos.id
		WHERE
		geodatos.zz_borrada='0'
		AND
		atributos.id_actividades='".$ID."'
		;
	";
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}
	
	
	while($fila=$Consulta->fetch_assoc()){
		if(isset($CatConversor[$fila['categoria']])){
			$cat=$CatConversor[$fila['categoria']];
		}else{
			$cat='';
		}
		
		if(!isset($ACT[$fila['id_actividades']]['categorias'][$cat]['cant'])){$ACT[$fila['id_actividades']]['categorias'][$cat]['cant']=0;}
		$ACT[$fila['id_actividades']]['categorias'][$cat]['cant']++;
		
		foreach($fila as $k => $v){
			$ATT[$fila['id']][$k]=utf8_encode($v);
		}
				
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
		
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`
	where 
		zz_borrada='0'
	and 
		id_usuarios>0
	AND 
		geodatos.id_actividades ='".$ID."'
	";
	
	$Consulta = $Conec1->query($query);
	if($Conec1->error!=''){
		$Log['tx'][]=utf8_encode($Conec1->error);
		$Log['res']='err';
		terminar($Log);
	}


	if (isset($ConsultaGEO)) {
	if ($ConsultaGEO != null) {
		while($fila=$Consulta->fetch_assoc()){
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
	}
$Log['res']='exito';
$Log['data']=$ACT;
terminar($Log);
?>

