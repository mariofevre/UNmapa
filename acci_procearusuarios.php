<?php 
/**
* accio_procesarusuarios.php
*
* ejecuta acciones para generar usuarios de terceros y brindar accesos y validar accesos.
* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	common
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013 TReCC SA
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


/* verificación de seguridad */
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

include("./actividades_consulta.php");

/* funciones frecuentes */
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];
echo "procesando";
$Hoy=date("Y-m-d");

if($UsuarioI==""){header('Location: ./login.php');}

$usuarios=usuariosconsulta();//en actividades_consulta.php;

if($_GET['actividad']>0){
	$Id=$_GET['actividad'];
	if(strlen($_POST['tablausuarios'])>10){
		
		//$e=explode('	',$_POST['tablausuarios']);
		$e=explode('
',$_POST['tablausuarios']);
	}	
}

foreach($e as $ek => $ev){
	//echo $ev;
	$ev=str_replace("'", "´", $ev);
	$l=explode('	',$ev);
	foreach($l as $kl => $vl){
		$fila[$ek][$kl]=$vl;
	}
	$fila[$ek]['cadena']=$ev;
}
//echo "<pre>";print_r($fila);echo "</pre>";
foreach($fila[0] as $ck => $cv){
	if(strtoupper($cv)==strtoupper('pass')||strtoupper($cv)==strtoupper('pase')||strtoupper($cv)==strtoupper('contraseña')||strtoupper($cv)==strtoupper('password')){
		$campos['pass']=$ck;		
	}elseif(strtoupper($cv)==strtoupper('nombre')||strtoupper($cv)==strtoupper('name')){
		$campos['nombre']=$ck;	
	}elseif(strtoupper($cv)==strtoupper('apellido')){
		$campos['apellido']=$ck;	
	}elseif(strtoupper($cv)==strtoupper('log')||strtoupper($cv)==strtoupper('login')||strtoupper($cv)==strtoupper('usuario')){
		$campos['log']=$ck;	
	}elseif(strtoupper($cv)==strtoupper('nivel')||strtoupper($cv)==strtoupper('rol')||strtoupper($cv)==strtoupper('cargo')){
		$campos['accnivel']=$ck;
	}elseif(strtoupper($cv)==strtoupper('mail')||strtoupper($cv)==strtoupper('email')||strtoupper($cv)==strtoupper('e-mail')||strtoupper($cv)==strtoupper('correo')){
		$campos['mail']=$ck;	
	}	
}

unset($fila[0]);

foreach($fila as $kf => $vf){
	
	foreach ($usuarios as $uk => $uv){
		if($uv['log']==$vf[$campos['log']]){
			$resultados[$kf]['ob']['log']='existe';
			$resultados[$kf]['acc']['consultas']['log']='¿renombrar log?';
		}
	}
	
	foreach ($usuarios as $uk => $uv){
		if($uv['mail']!=''&&($uv['mail']==$vf[$campos['mail']])){
			$resultados[$kf]['ob']['mail']='existe';
			$resultados[$kf]['acc']['consultas']['mail']='¿usar registro existente?';
		}
	}
	
	if(strlen($vf[$campos['pass']])<'5'){
		$resultados[$kf]['ob']['pass']='corto';
		$Log['tx'][]="ref: ".$vf[$campos['pass']];
		$resultados[$kf]['acc']['consultas']['pass']='pass invalido';
	}	

	if(STRTOUPPER($vf[$campos['accnivel']])=='COORDINADOR'){
		$fila[$kf][$vf[$campos['accnivel']]]='2';
	}
	
	if(STRTOUPPER($vf[$campos['accnivel']])=='PARTICIPANTE'){
		$fila[$kf][$vf[$campos['accnivel']]]='1';
	}		
}

foreach($fila as $kf => $vf){
	if(
		$resultados[$kf]['ob']['log']=='existe'
		||	$resultados[$kf]['ob']['mail']=='existe'
		||	$resultados[$kf]['ob']['pass']=='corto'
	){
		if($resultados[$kf]['ob']['log']=='existe'){$Log['tx'][]='el log ya existe';}
		if($resultados[$kf]['ob']['mail']=='existe'){$Log['tx'][]='el mail existe';}
		if($resultados[$kf]['ob']['pass']=='corto'){$Log['tx'][]='el pass es muy corto';}
		$Log['tx'][]="accion interrumpida para . ".$vf['cadena'];
	}else{
		$query="
			INSERT INTO `UNmapa`.`usuarios`
			SET
			`nombre`='".$vf[$campos['nombre']]."',
			`apellido`='".$vf[$campos['apellido']]."',
			`pass`='".MD5($vf[$campos['pass']])."',
			`mail`='".$vf[$campos['mail']]."',
			`log`='".$vf[$campos['log']]."',
			`zz_AUTOFECHACREACION`='".$Hoy."',
			`zz_activo`='1'
		";
		$Conec1->query($query);
		
		//mysql_query($query,$Conec1);
		echo  $Conec1->error;
		$nid=$Conec1->insert_id;
		$Log['tx'][]="nuevo usuario $nid";
		
		if($nid>0){
			$query="
			INSERT INTO 
				`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTaccesos`
			SET
				`id_actividades`='$Id',
				`id_usuarios`='$nid',
				`nivel`='".$vf[$campos['accnivel']]."',
				`autorizado`='1'
			";
			$Conec1->query($query);
			//mysql_query($query,$Conec1);
			echo  $Conec1->error;
			$Log['tx'][]=" -- nuevo acceso para . ".$vf['cadena'];
		}else{
			$Log['tx'][]=" -- error en la creacion del usuario. ".$vf['cadena'];	
		}
		
	}
	
}
echo "<pre>";print_r($Log);echo "</pre>";
echo "<pre>";print_r($fila);echo "</pre>";

?>	



