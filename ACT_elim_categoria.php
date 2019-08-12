<?php
/**
*ACT_ed_config
*
*
* modifica los atributos de una actividad a partir de los datos enviados por un usuario con permisos de coordinador.
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

//include("./includes/BASEconsultas.php");
$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];
if($UsuarioI==""){
	echo "usuario no identificado";exit;
	header('Location: ./login.php');
}


//$MODO = $_GET['modo'];

$HOY=date("Y-m-d");

$requeridos=array(
'idact'=>'',
'idcat'=>''
);
foreach($requeridos as $k => $v){
	if(!isset($_POST[$k])){
		$Log['tx'][]='error, falta variable:'.$k;
		$Log['res']='err';
		terminar($Log);
	}
}
foreach($_POST as $k => $v){
	$_POST[$k] = utf8_decode($v);
	$Log['data'][$k]=$v;
}
$Log['data']['id']=$_POST['idcat'];



$query="
	SELECT 
		zz_AUTOUSUARIOCREAC
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
	WHERE
		`id`  = '".$_POST['idact']."'

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
		`ACTaccesos`.`id_actividades` = '".$_POST['idact']."'
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




$query="
	UPDATE
		`ACTcategorias`
		SET
		zz_borrada='1'
	WHERE
		`id_p_actividades_id` = '".$_POST['idact']."'
		AND
		id =  '".$_POST['idcat']."'

";
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){
	$Log['tx'][]=$Conec1->error;
	$Log['res']='err';
	terminar($Log);
}
			
			
$Log['res']='exito';
terminar($Log);
?>

