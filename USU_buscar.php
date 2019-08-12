<?php
/**

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

if(!isset($_POST['aid'])){
	$Log['tx'][]='error, falta variable aid';
	$Log['res']='err';
	terminar($Log);
}
if(!isset($_POST['busqueda'])){
	$Log['tx'][]='error, falta variable busqueda';
	$Log['res']='err';
	terminar($Log);
}


$e=explode(' ',$_POST['busqueda']);
$where='';
foreach($e as $v){
	if(strlen($v)>2){	
		$where.="OR( nombre like '%".$v."' OR apellido like '%".$v."' OR mail like '%".$v."' OR log like '%".$v."') ";
	}
}
$where=substr($where,2);

$query="
	SELECT 
			id, nombre, apellido, mail, log
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.usuarios
	WHERE
		$where
	ORDER BY 
		id DESC
";		
$Consulta = $Conec1->query($query);
if($Conec1->error!=''){	
	$Log['tx'][]='error en la consulta';
	$Log['tx'][]=$Conec1->error;
	$Log['tx'][]=utf8_encode($query);
	$Log['res']='err';
	terminar($Log);
}			

while($fila= $Consulta->fetch_assoc()){
	$Log['data']['candidatos'][]=$fila['id'];
	foreach($fila as $k => $v){
		$Log['data']['usuarios'][$fila['id']][$k]=utf8_encode($v);
	}
}

$Log['res']='exito';
terminar($Log);
?>

