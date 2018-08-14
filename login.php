<?php
/**
* login.php
*
* aplicación inicial de acceso al sistema, permite dirigirse ala generación de usuairos o a la validación del usuario ingresante.* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2010 2014 TReCC SA
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


ini_set('display_errors', '1');
error_reporting(E_ERROR );
//if($_SERVER['SERVER_ADDR']=='192.168.0.252')ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);
session_start();
session_destroy();
include('./includes/conexion.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");


if(!isset( $_POST['log'])){ $_POST['log']='';}

// $_GET['DEST'] define a donde se dirigi´ra luego de loguearse
$mensaje='';
if(isset($_POST['loguear'])){
	$pass = md5($_POST['pass']);
	
	$query ="	
		SELECT `usuarios`.`id`,
		    `usuarios`.`pass`,
		    `usuarios`.`log`,
		    `usuarios`.`zz_activo`	    
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
		WHERE 
			BINARY log='".$_POST['log']."'
	";
	
	//$Consulta = mysql_query($query,$Conec1);
	$Consulta = $Conec1->query($query);
	echo  $Conec1->error;
//	echo mysql_error($Conec1);

	
	if($Consulta->num_rows>0){
		$row = $Consulta->fetch_assoc();
		if($row['zz_activo']=='1'){
			$passbase = $row['pass'];
			if($passbase==$pass){
				session_start();
		   		session_regenerate_id (true);
				
				//print_r($_SESSION['Unmapa']);
				$_SESSION['Unmapa'][$CU]->USUARIO['uid'] = $row['id'];
				//print_r($_SESSION['Unmapa']);
				
				$l="./actividades.php"; //por defecto dirije al listado de actividades. luego de logear
				if($_POST['DDEST']!=''){
					$l="./".$_POST['DDEST']."?";
					unset($_POST['DDEST']);
					foreach($_POST as $kp => $vp){
						if(substr($kp,0,1)=='D'){
							$l.="&".substr($kp,1)."="."$vp";
						}
					}
				}		
				header("location: $l");
			}else{
				$mensaje="La contraseña no coincide con el usuario solicitado.";
			}
		}else{
			$mensaje="La cuenta solicitada no ha sido activada aún.";
		}
	}else{
		$mensaje="El usuario requerido no se encuentra registrado.";
	}
}
?>
<html>
<head>
	<title><?php echo $_SESSION['Unmapa'][$CU]->PRESENTACION_TITULO;?></title>
	<?php include("./includes/meta.php");?>
	<link href="./css/treccppu.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div id="pageborde">
		<div id="page">
			<h1><?php echo $_SESSION['Unmapa'][$CU]->PRESENTACION_NOMBRE;?></h1>
			<h2>Una implementación del proyecto UNmapa</h2>
			<p>Bienvenido a nuestra web de trabajo.<br>
		    </p>
			<p class='error'>
				<?php echo $mensaje;?><br>
		    </p>	
		    
		    <h2>Acceso de usuarios</h2>    
			<form action="login.php" method="post">
				<input name="loguear" type="hidden" value="loguear"/>
				<div>
				<label for="log">Usuario :</label>
				<input name="log" type="text" value="<?php echo $_POST['log'];?>"/>
				</div>	
				
				<div>
				<label for="pass">Contraseña :</label>
				<input name="pass" type="password" value=""/>
				</div>
				
				<?php
				
				foreach($_GET as $gk => $gv){
					echo "<input type='hidden' name='D".$gk."' value='$gv'>";
				}
				
				?>
					
				<div>
				<input type="submit" value="Ingresar"/>
				</div>			
				<div>
				<!--- <a class='boton' href="registro.php">Regístrese aquí para participar si no posee un usuario</a> --->	
				<a href='./registro.php?DEST=<?php echo $_GET['DEST'];?>&actividad=<?php echo $_GET['actividad'];?>' class='boton'>Regístrese aquí para participar si no posee un usuario</a>
				</div>
				<div>
				<!--- <a class='boton' href='./registrorecuperar.php'>Olvidé mi contraseña</a> --->
				<a href='./registrorecuperar.php' class='boton' >Olvidé mi contraseña</a>
				</div>
				<div>
				<img style='float:left;height:50px;' src='./img/lgofirefox.png'>
				Este sitio está optimizado para el navegador mozilla Firefox.<br>
				Recomendamos utilizar software libre.<br>
				Descargue mozilla firefox <a href='http://support.mozilla.org/es/products/firefox'>aqui</a>.
				</div>			
		</form>

			<h2>Documentos publicados</h2>
				
			
			<p>
				
				<?php
				$a=scandir('./publicaciones');
				unset($a[0]);
				unset($a[1]);
				foreach($a as $f){
					echo "<h3><a download href='./publicaciones/$f'>$f</a></h3>"; 
				}
				
				?>
		    </p>
			
				    	    
		</div>
	</div>
	<?php
	include('./_serverconfig/pie.php');
	?>
</body>
</html>