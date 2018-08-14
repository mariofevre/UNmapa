<?php 
/**
* registrorecuperar.php
*
* aplicación para recuperar acceso por parte de un usuario registrado
* 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	registro
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


include('./includes/conexion.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/class.phpmailer.php");


$HOY = date("Y-m-d");
$AHORA = date("H:i");
$ERROR = array();
			 
		
if(isset($_POST['recuperar'])){
	
	foreach($_POST as $key => $value) {
		$valor = trim(htmlentities(strip_tags($value)));
		$posts[$key]=$valor;
	}
	
		
	if($posts['mail']==''){
		$ERROR['mail'] = "ERROR - Dirección de correo electrónico inválida.";
	}else{
		$mailcargado=$posts['mail'];
	}

			
	if(($posts['log'])==''){
		$ERROR['log'] = "ERROR - Nombre de Usuario vacio";
	}else{
		$query="
			SELECT 
			* 
			FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios` 
			WHERE log = '".$posts['log']."'
		";
			 
		$Consulta = $Conec1->query($query);
		$devuelto=	$Consulta->num_rows;
		if($devuelto==0){		
			$ERROR['log'] = "ERROR - El nombre de usuario no se encuentra registrado.";
		}else{
			$row=$Consulta->fetch_assoc();
			$mailguardado=$row['mail'];
			$idguardado=$row['id'];
			if($mailcargado!=$mailguardado){
				$ERROR['mail'] = "ERROR - La dirección de correo cargada no se corresponde con el usuario en nuestras bases.";			
			}
		}	 
	}

	if($posts['acepto1']!='si'){
		$ERROR['acepto1'] = "ERROR - Debe aceptar este item para poder ingresar.";
	}	

		
	if(empty($ERROR)){
	
	$NPASS=cadenaArchivo(7);									
									
	$query = "
		UPDATE
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
		SET
			pass ='".md5($NPASS)."'
		WHERE id='".$idguardado."'	
	";	
	$Consulta = $Conec1->query($query);
				
		$mensaje = "
Hola ".$posts['nombre'].": \n
Se ha requerido para su usario registrado la reactivación de cuenta.

Se ha generado una nueva contraseña de acceso.
				
usuario: ".$posts['log']." \n
Contraseña: ".$NPASS." \n
			";
			
				
		$mensaje .= "
Gracias
			
Equipo Técnico de TReCC. S.A.
para el dearrollo de los Códigos de Planificación Territorial y Códigdo de edificación del Municipio de San Antonio Oeste
				
______________________________________________________
ESTA ES UNA RESPUESTA AUTOMATICA. 
***NO RESPONDA ESTE CORREO ELECTRONICO****
		";
		
		
						
		$mail = new phpmailer();
		$mail->PluginDir = "includes/";
		$mail->Mailer = "smtp";
		$mail->Host = "mail2.alpha2000.com.ar";
		$mail->SMTPAuth = true;
		$mail->Username = "info@baseobra.com.ar"; 
		$mail->Password = "1960alpha.1";
		$mail->From = "info@baseobra.com.ar";
		$mail->FromName = "baseobra";		
		$mail->AddAddress($_POST['mail']);
		$mail->Timeout=1;
		$mail->Port = 25;
		$mail->Subject ="reactivación de cuenta en SIGSAO";
		$mail->Body = $mensaje;
		
		$intentos=1;
		
		
		/* apagado hasta que funcione el envío de mails desde servitest*/
		/*while ((!$exito) && ($intentos < 2)){
		  //echo "enviando mail $fila/$Consulta_filas intento $intentos/5";
			sleep(2);
		   	//echo $mail->ErrorInfo;
		   	$exito = $mail->Send();
		   	$intentos=$intentos+1;		
		}
		 
		
		/* 
		if(!$exito){
		$exito = mail("mario@trecc.com.ar" , "reactivación de cuenta en SIGSAO" , $mensaje);
		echo $exito;
			//echo "fallo en envío primario, utilizando envío secundario.";
		}
		 * 
		 */

		if(!$exito){
			/* solucion provisoria, los mails son cargados a mysql y enviados luego desde servitrecc */		 
		$query = "INSERT INTO
						`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`SISreportesmailspendientes`
					SET
						`destinatario`='".$_POST['mail']."',
						`asunto`='Reactivación de cuenta en SIGSAO',
						`cuerpo`='$mensaje',
						`fechaenvio`='$HOY',
						horaenvio='$AHORA'
					";
			$Consulta = $Conec1->query($query);
			echo $Conec1->error;
						echo"
				<html>
				<head>
				<title>SIGSAO - Reactivación de Actores para los procesos participativos</title>
				";
			include("./includes/meta.php");
			echo '
					<link href="css/treccppu.css" rel="stylesheet" type="text/css">
				</head>
				
				<body>
					<div id="pageborde"><div id="page">
						<h1>¡Gracias!</h1>
						<p>Su reactivación se ha completado!</p>
						<p>Un correo electronico con su nueva información ha sido enviado a su casilla: '.$_POST['mail'].'<br>
						(no se olvide de revisar su carpeta de correo no deseado).</p>
						<a href="./login.php">ir al sitio de acceso</a>
					</div></div>
				</body>
				</html>
		    ';
			exit();
		}
		 
		if(!$exito){
			$query = "INSERT INTO
				`SISreportes`
				SET
					texto ='ha fallado el envío de mail para activación del usuario ".$Nid." desde registrorecuperar.php',
					zz_AUTOFECHACREACION ='".$HOY."'
				";
			$Consulta = $Conec1->query($query);
			$NregID=$Consulta->insert_id;	
			
			$query = "INSERT INTO
						`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`SISreportesmailspendientes`
					SET
						`destinatario`='".$_POST['mail']."',
						`asunto`='Reactivación de cuenta en SIGSAO',
						`cuerpo`='$mensaje',
						`fechaenvio`='$HOY',
						horaenvio='$AHORA'
					";
			$Consulta = $Conec1->query($query);	
			echo $Conec1->error;
			
			

			
			echo "Problemas enviando correo electrónico a ".$_POST['mail'];
			echo "<br/>Enviando mensaje al administrador con número de reclamo: ".$NregID.". Vuelva a intentarlo más tarde<br>";				
			
		}else{
			echo"
				<html>
				<head>
				<title>SIGSAO - Reactivación de Actores para los procesos participativos</title>
				";
			include("./includes/meta.php");
			echo '
					<link href="css/treccppu.css" rel="stylesheet" type="text/css">
				</head>
				
				<body>
					<div id="pageborde"><div id="page">
						<h1>¡Gracias!</h1>
						<p>Su reactivación se ha completado!</p>
						<p>Un correo electronico con su nueva información ha sido enviado a su casilla: '.$_POST['mail'].'<br>
						(no se olvide de revisar su carpeta de correo no deseado).</p>
						<a href="./login.php">ir al sitio de acceso</a>
					</div></div>
				</body>
				</html>				
				<h1></h1>
		    ';
	  		exit();
		}
	
	}else{
		$mensaje = 
		"Se ha registrado un error no identificado durante el reactivacion.\n
			Vuelva a intentar la operación. Si el error persiste comuníquese con nuestro administrador.\n
			mario@trecc.com.ar.\n
		";
	}
		
}		
				
					 
?>

<html>
<head>
	<title>SIGSAO - Reactivación de cuentas</title>
	<?php include("./includes/meta.php");?>
	<link href="css/treccppu.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div id="pageborde"><div id="page">

		<h1>Formulario de reactivacióno</h1>
		<p>
			Por favor registre los siguientes datos para la reactivación de la cuenta.<br>
	    </p>
	 
		<form action="registrorecuperar.php" method="post">
			<input name="recuperar" type="hidden" class="recuperar"/>
						
			<div>
			<label for="log">Log *<span>Ingrese su usuario</span></label>
			<input name="log" type="text" class="required" value="<?php echo $posts['log'];?>"/>
			<span class="error"><?php echo $ERROR['log'];?></span>
			</div>	
			
			<div>
			<label for="mail">Correo *<span>Ingrese su dirección de correo electrónico</span></label>
			<input name="mail" type="text" class="required" value="<?php echo $posts['mail'];?>"/>
			<span class="error"><?php echo $ERROR['mail'];?></span>
			</div>	

			<div>
			<label for="cargo"><span>La información cargada pertenece a mi cuenta personal.</span></label>
			<input name="acepto1" type="checkbox" class="required" value="si"/>
			<span class="error"><?php echo $ERROR['acepto1'];?></span>
			</div>	
		  
			<div>
			<label for="cargo"></label>				
			<input type="submit" value="Recuperar"/>
			</div>
			
      </form>
	   

	</div></div>
	<?php
	include('./includes/pie.php');
	?>
</body>
</html>
