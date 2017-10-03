<?php
/**
* borra.php
*
* borra.php se incorpora en la carpeta raiz en tanto una funci�n general aplicable a todos los m�dulos. 
* esta aplicaci�n permite eliminar o enviar a la papelera cualquier registro (solo verifica que el usuario tenga acceso al panel en cuesti�n)
* Para definir si el registro va a la papelera o es eliminado se consultan los nombres de los campos de las tablas, en aquellas tablas que contienen nombres de capo para pseudoeliminaci�n no se eliminan registros.  
* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2010 2014 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los t�rminos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU General Public License" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
*/



/* verificaci�n de seguridad */
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

/* funciones frecuentes */
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['USUARIOID'];

if($UsuarioI==""){header('Location: ./login.php');}


$Id = $_POST['id'];
$Contrato = $_POST['contrato'];
$Tabla = $_POST['tabla'];
$Accion = $_POST['accion'];
$Salida = $_POST['salida'];
$Salidaid = $_POST['salidaid'];	
$Salidatabla = $_POST['salidatabla'];	
$HOY = date("Y-m-d");


foreach($_POST as $k => $v){// estas variables son pasadas por als aplicaciones comunes manteniendose.
	$_SESSION['DEBUG']['mensajes'][]="$k => $v";
	if(substr($k,0,5)=='PASAR'){
		$PASAR[$k]=$v;
	}
}

$query="SELECT * FROM $Tabla WHERE id = $Id";
$Consulta = mysql_query($query,$Conec1);
$_SESSION['DEBUG']['mensajes'][]=$query . " : ". mysql_numrows($Consulta) ;
$_SESSION['DEBUG']['mensajes'][]=mysql_error($Conec1);

$query="SHOW COLUMNS FROM $Tabla";
$campos = mysql_query($query,$Conec1);
$_SESSION['DEBUG']['mensajes'][]=mysql_error($Conec1);
	
$accion = "DELETE FROM $Tabla "; //por defecto elimina el registro, excepto que la tabla presente campos de papelera

$sets = "SET ";

while($row=mysql_fetch_assoc($campos)){
	
	if(substr($row['Field'],0,17)=='zz_borradausuario'){
		$sets .= $row['Field']."='$UsuarioI', ";
	}	
	
	if(substr($row['Field'],0,15)=='zz_borradafecha'){
		$sets .= $row['Field']."='$HOY', ";
	}		


	if(substr($row['Field'],0,9)=='zz_borrad'){
		
		$accion = "UPDATE $Tabla ";	
				
		if(substr($row['Type'],0,4)=='tiny'){
			$sets .= $row['Field']."='1', ";
		}elseif(substr($row['Type'],0,4)=='enum'){
			$sets .= $row['Field']."='si', ";
		}
		
	}
	
}

if($sets=="SET "){$sets='';}else{$sets=substr($sets,0,-2);}

$query="$accion $sets WHERE id='$Id'";


if($Accion == "borra"){
	$Consulta_contrato = mysql_query($query,$Conec1);
	$_SESSION['DEBUG']['mensajes'][]=$query;		
	$_SESSION['DEBUG']['mensajes'][]=mysql_error($Conec1);
}else{
	$_SESSION['DEBUG']['mensajes'][]="esta acci�n no fue llamada correctamente, borra.php solo se activa enviando via POST la 'accion' 'borra'";
}




if($Salidatabla != ""){$Tabla = $Salidatabla;}
if($Salidaid == ""){$Salidaid = $Id;}

echo $Salida;
echo".php?tabla=";
echo $Tabla;
echo"&id=";
echo $Salidaid;
$cadenapasar='';
foreach($PASAR as $k => $v){
	$cadenapasar.='&'.substr($k,5).'='.$v;
}

$_SESSION['DEBUG']['mensajes'][]=$errorsalida;

if($Salida!=''){
	?>
	    <SCRIPT LANGUAGE="javascript">
	    location.href = "./<?php echo $Salida;?>.php?tabla=<?php echo $Tabla;?>&id=<?php echo $Salidaid.$cadenapasar;?>";
	    </SCRIPT> 
	<?php
	}else{
	?>
	    <SCRIPT LANGUAGE="javascript">
	    window.close();
	    </SCRIPT> 
	<?php	
		
}

