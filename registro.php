<?php 
/**
* registro.php
*
* aplicación para registrar nuevos ursuarios en la base de datos
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


//header('Content-Type:text/html; charset=cp-1252');
ini_set('display_errors', 1);
include('./includes/conexion.php');
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/class.phpmailer.php");

$HOY = date("Y-m-d");
$AHORA = date("H:i");
$ERROR = array();

$mensaje='';	

if(!isset($_GET['DEST'])){$_GET['DEST']='';}
$posts['DEST']=$_GET['DEST'];

if(!isset($_GET['actividad'])){$_GET['actividad']='';}
$posts['actividad']=$_GET['actividad'];
			 
foreach($_POST as $key => $value) {
	$valor = trim(htmlentities(strip_tags($value)));
	$posts[$key]=$nombre=html_entity_decode($valor);
}

if(!isset($posts['nombre'])){$posts['nombre']='';}				
if(!isset($posts['apellido'])){$posts['apellido']='';}	
if(!isset($posts['nacimiento_d'])){$posts['nacimiento_d']='';}
if(!isset($posts['nacimiento_m'])){$posts['nacimiento_m']='';}
if(!isset($posts['nacimiento_a'])){$posts['nacimiento_a']='';}
if(!isset($posts['mail'])){$posts['mail']='';}
if(!isset($posts['telefono'])){$posts['telefono']='';}	
if(!isset($posts['log'])){$posts['log']='';}	
if(!isset($posts['pass'])){$posts['pass']='';}	
if(!isset($posts['acepto1'])){$posts['acepto1']='';}	
if(!isset($posts['acepto2'])){$posts['acepto2']='';}	
if(!isset($posts['organizacion '])){$posts['organizacion']='';}	
if(!isset($posts['nivel '])){$posts['nivel']='';}	
	
$ERROR['nombre']='';
$ERROR['apellido'] ='';
$ERROR['nacimiento']='';
$ERROR['mail'] = "";
$ERROR['telefono'] ='';
$ERROR['log'] = '';
$ERROR['acepto1'] = '';
$ERROR['acepto2'] = '';
$ERROR['pass'] ='';


if(isset($_POST['registrar'])){
	
	$ipusuario = $_SERVER['REMOTE_ADDR'];
	
	if($posts['nombre']=='' || strlen($posts['nombre']) < 3 || !nombrevalido($posts['nombre'])){
		$ERROR['nombre'] = "ERROR - Nombre invalido. Por favor ingrese su/sus Nombre/s real/es de al menos 3 letras.".$posts['nombre'];
	}

	if($posts['apellido']=='' || strlen($posts['apellido']) < 3 || !nombrevalido($posts['apellido']))
		{$ERROR['apellido'] = "ERROR - Apellido invalido. Por favor ingrese un Apellido real de al menos 3 letras.";}	

	if(
		$posts['nacimiento_d']=='' || $posts['nacimiento_d']=='0'|| 
		$posts['nacimiento_m']=='' || $posts['nacimiento_m']=='0'||  
		$posts['nacimiento_a']=='' ||$posts['nacimiento_a']=='0'
	){
		$ERROR['nacimiento'] = "ERROR - Su fecha de nacimeinto no fue cargada correctamente.";
	}	
	if(mailvalido($posts['mail']==''))
		{$ERROR['mail'] = "ERROR - Dirección de correo electrónico inválida.";}
	
		
	if($posts['telefono']=='' || !telvalido($posts['telefono']))
		{$ERROR['telefono'] = "ERROR - El número de teléfono cargado no es válido.";}			
		
	if(!logvalido($posts['log'])){
		{$ERROR['log'] = "ERROR - Nombre de Usuario inválido. Ingrese un Usuario con 5 caracteres o más de la A la Z, puede contaner números";}
	}else{
		$query="
			SELECT 
			* 
			FROM 
			`usuarios` 
			WHERE BINARY log = '".$posts['log']."'
		";
		//echo $query;
		$Consulta = $Conec1->query($query);	
		if($Consulta->num_rows>0){
			$ERROR['log'] = "ERROR - El nombre de usuario fué registrado previamente.";			
		}
	}

	if(!passvalido($posts['pass']))
		{$ERROR['pass'] = "ERROR - Contraseña inválida. Ingrese una contraseña con 5 caracteres o más";
	}	

	if($posts['acepto1']!='si')
		{$ERROR['acepto1'] = "ERROR - Debe aceptar este item para poder ingresar.";}	
		
	if($posts['acepto2']!='si')
		{$ERROR['acepto2'] = "ERROR - Debe aceptar este item para poder ingresar.";}	
	
	$ok='si';
	foreach($ERROR as $k => $v){
		if($v!=''){$ok='no';}
	}
	if($ok='si') {
		
		$query = "INSERT INTO
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
			SET
				nombre ='".$_POST['nombre']."',
				apellido ='".$_POST['apellido']."',
				organizacion ='".$_POST['organizacion']."',
				nivel ='".$_POST['nivel']."',				
				nacimiento ='".$_POST['nacimiento_a']."-".$_POST['nacimiento_m']."-".$_POST['nacimiento_d']."',
				mail ='".$_POST['mail']."',
				telefono ='".$_POST['telefono']."',
				log ='".$_POST['log']."',
				pass ='".md5($_POST['pass'])."',
				zz_AUTOFECHACREACION = '".$HOY."',
				zz_activo='1'	
			";
		$Consulta = $Conec1->query($query);	
		echo $Conec1->error;
		//echo $query;
		//print_r($Consulta);
		$Nid = $Conec1->insert_id;
		if($Nid!=''){
		$activacion=md5($Nid."id".rand(1,1000));
		
		$query = "UPDATE
				`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`
				SET
					zz_idactivacion ='".$activacion."'
				WHERE id='".$Nid."'
				";					
			$Consulta = $Conec1->query($query);

			$mensaje .= "
Hola ".$posts['nombre'].": \n
Gracias por registrarse con nosotros. Aqui tiene los detalles de su cuenta de acceso...\n
				
usuario: ".$posts['log']." \n
Contraseña: ".$posts['pass']." \n
			";
			
			$mensaje .= "
Código de activación\n
http://190.111.246.33/MAPAUBA/registroactivar.php?user=".$activacion."\n
				"; 
			
			$mensaje .= "
Gracias
			
Equipo Técnico
para el desarrollo del UNmapa
				
______________________________________________________
ESTA ES UNA RESPUESTA AUTOMATICA. 
***NO RESPONDA ESTE CORREO ELECTRONICO****
			";
					
			$mail = new phpmailer();
			$mail->PluginDir = "includes/";
			$mail->Mailer = "smtp";
			$mail->Host = "mail.alpha2000.com.ar";
			$mail->SMTPAuth = true;
			$mail->Username = "info@baseobra.com.ar"; 
			$mail->Password = "1960alpha.1";
			$mail->From = "info@baseobra.com.ar";
			$mail->FromName = "baseobra";		
			$mail->AddAddress($_POST['mail']);
			$mail->Timeout=10;
			$mail->Port = 25;
			$mail->Subject ="registro de cuenta en SIGSAO";
			$mail->Body = $mensaje;
			$intentos=0;
			
			/*
			while ((!$exito) && ($intentos < 1)){
			  //echo "enviando mail $fila/$Consulta_filas intento $intentos/5";
				sleep(2);
			   	//echo $mail->ErrorInfo;
			   	$exito = $mail->Send();
			   	$intentos=$intentos+1;		
			}
			*/
			
			/*
			if(!$exito){
			$exito = mail($_POST['mail'] , "reactivación de cuenta en SIGSAO" , $mensaje);
			//echo "fallo en envío primario, utilizando envío secundario.";
			}
			*/
		
		$exito=false;
		if(!$exito){
		// solucion provisoria, los mails son cargados a mysql y enviados luego desde servitrecc 		 
		$query = "INSERT INTO
						`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`SISreportesmailspendientes`
					SET
						`destinatario`='".$_POST['mail']."',
						`asunto`='registro de cuenta en SIGSAO',
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
			';
			
			echo '<h2>¡Gracias!</h2> Su usuario ha sido registrado con éxito. Ahora puede <a href="login.php?DEST='.$_POST['DEST'].'&actividad='.$_POST['actividad'].'">ingresar aqui</a>';
			
			/*
			echo '
						<h1>¡Gracias!</h1>
						<p>Su registracion se ha completado!</p>
						<p>Un correo electronico de activacion ha sido enviado a su casilla<br>
						(no se olvide de revisar su carpeta de correo no deseado).</p>
				        <p>Por favor revise su correo y acceda al link de activacion.</p>
			 ';
			 */
			echo '
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
						texto ='ha fallado el envío de mail para activación del usuario ".$Nid." desde registro.php',
						zz_AUTOFECHACREACION ='".$HOY."'
					";
				$Consulta = $Conec1->query($query);
				$NregID=$Consulta->insert_id;
				
				$query = "INSERT INTO
							`SISreportesmailspendientes`
						SET
							`destinatario`='".$_POST['mail']."',
							`asunto`='Reactivación de cuenta en SIGSAO',
							`cuerpo`='$mensaje',
							`fecha de envío`='$HOY'
						";
				$Consulta = $Conec1->query($query);
				
				echo "Problemas enviando correo electrónico a ".$_POST['mail'];
				echo "<br/>Enviando mensaje al administrador con número de reclamo: ".$NregID.". Vuelva a intentarlo más tarde<br>";				
				
			}else{
				echo"
					<html>
					<head>
					<title>SIGSAO - Registro de Actores para los procesos participativos</title>
					";
				include("./includes/meta.php");
				echo '
						<link href="css/treccppu.css" rel="stylesheet" type="text/css">
					</head>
					
					<body>
						<div id="pageborde"><div id="page">
							<h1>¡Gracias!</h1>
							<p>Su registracion se ha completado!</p>
							<p>Un correo electronico de activacion ha sido enviado a su casilla<br>
							(no se olvide de revisar su carpeta de correo no deseado).</p>
					        <p>Por favor revise su correo y acceda al link de activacion.</p>
						</div></div>
					</body>
					</html>				
					<h1></h1>
			    ';
		  		exit();
			}

		}else{
			$mensaje .= 
			"Se ha registrado un error no identificado durante el registro.\n
				Vuelva a intentar registrarse. Si el error persiste comuníquese con nuestro administrador.\n
				mario@trecc.com.ar.\n
			";
		}
	}
}

?><!DOCTYPE html>
<html>
<head>
	<title>UNmapa - Registro de Usuarios</title>
	<?php include("./includes/meta.php");?>
	<link href="css/treccppu.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div id="pageborde"><div id="page">
	<?php 
	
	 if(isset($_GET['registrado'])) { ?>
	  <h2>Gracias</h2> Su usuario ha sido registrado con éxito. Ahora puede <a href="login.php?DEST=<?php echo $_POST['DEST'];?>&actividad=<?php echo $_POST['actividad'];?>">ingresar aqui</a>
	 <?php exit();
	  }
	?>
		<h1>Formulario de ingreso</h1>
		<p>
			Por favor registrese como Participante intresado antes de empezar a usar el sitio. <br>
	        La registracion es rapida! <br>
	        Fijese que los campos marcados con <span class="required">*</span> 
	        son obligatorios.
	    </p>
	 
		<form action="registro.php" method="post">
			
			<input name="DEST" type="hidden" class="" value='<?php echo $posts['DEST'];?>'/>
			<input name="actividad" type="hidden" class="" value='<?php echo $posts['actividad'];?>'/>			
			
			<input name="registrar" type="hidden" class="registrar"/>
			<div>
			<label for="nombre">Nombre *<span>Ingrese su nombre</span></label>
			<input name="nombre" type="text" class="required" value="<?php echo $posts['nombre'];?>"/>
			<span class="error"><?php echo $ERROR['nombre'];?></span>
			</div>
			
			<div>
			<label for="apellido">Apellido *<span>Ingrese su apellido</span></label>
			<input name="apellido" type="text" class="required" value="<?php echo $posts['apellido'];?>"/>
			<span class="error"><?php echo $ERROR['apellido'];?></span>
			</div>	
			
			<div>
			<label for="organizacion">Organización<span>Ingrese la organización a la que representa</span></label>
			<input name="organizacion" type="text" value="<?php echo $posts['organizacion'];?>"/>
			</div>	

			<div>
			<label for="nivel">Nivel<span>Ingrese el nivel de formación alcanzado</span></label>
			<input name="nivel" type="text" value="<?php echo $posts['nivel'];?>"/>
			</div>	
			
			<div>
			<label for="edad">Fecha de Nacimiento *<span>Ingrese su fecha de Nacimientoedad</span></label>
			<?php
					echo "<div class='dia'>";
						echo "<select class='dia required'' name='nacimiento_d'>";
						$a=0;
						$dd=$posts['nacimiento_d'];
						while($a<=31){
							if($a==$dd){$selected='selected';}else{$selected='';}
							echo "<option $selected value='$a'>$a</option>";
							$a++;
						}
						echo "</select>";	
					echo "</div>";	
					
					echo "<div class='mes'>";
						echo "<select class='dia required' name='nacimiento_m'>";
						$a=0;
						$dm=$posts['nacimiento_m'];
						while($a<=12){
							if($a==$dm){$selected='selected';}else{$selected='';}
							echo "<option $selected value='$a'>$a</option>";
							$a++;
						}
						echo "</select>";
					echo "</div>";			
										
					echo "<div class='ano'>";
						echo "<select name='nacimiento_a' class='required'>";
						$a=1920;
						$b=ano($HOY);
						echo $HOY;
						$da=isset($posts['nacimiento_a'])?$posts['nacimiento_a']:1980;
						while($a<=$b){
							if($a==$da){$selected='selected';}else{$selected='';}
							echo "<option $selected value='$a'>$a</option>";
							$a++;
						}
						echo "</select>";
					echo "</div>";	
			?>		
			<span class="error"><?php echo $ERROR['nacimiento'];?></span>
			</div>	
			
			<div>
			<label for="mail">Correo *<span>Ingrese su dirección de correo electrónico</span></label>
			<input name="mail" type="text" class="required" value="<?php echo $posts['mail'];?>"/>
			<span class="error"><?php echo $ERROR['mail'];?></span>
			</div>	
			
			<div>
			<label for="telefono">Telefono *<span>Ingrese su telefono</span></label>
			<input name="telefono" type="text" class="required" value="<?php echo $posts['telefono'];?>"/>
			<span class="error"><?php echo $ERROR['telefono'];?></span>
			</div>	
			
			<div>
			<label for="log">Log *<span>Ingrese su usuario</span></label>
			<input name="log" type="text" class="required" value="<?php echo $posts['log'];?>"/>
			<span class="error"><?php echo $ERROR['log'];?></span>
			</div>	
			
			<div>
			<label for="pass">Pass *<span>Ingrese su contraseña</span></label>
			<input name="pass" type="password" class="required" value=""/>
			<span class="error"><?php echo $ERROR['pass'];?></span>
			</div>	

			<div>
			<label for="nivel"><span>Acepto que esta información sea utilizada para actividades participativas.</span></label>
			<input name="acepto1" type="checkbox" class="required" value="si"/>
			<span class="error"><?php echo $ERROR['acepto1'];?></span>
			</div>	

			<div>
			<label for="nivel"><span>Acepto la plena responsabilidad por todo el material que suba a esta plataforma.</span></label>
			<input name="acepto2" type="checkbox" class="required" value="si"/>
			<span class="error"><?php echo $ERROR['acepto2'];?></span>			
			</div>				
		  
			<div>
			<label for="nivel"></label>				
			<input type="submit" value="Registrarse"/>
			</div>
			
			<div class="aclaracionsubmit">
				Toda la información registrada será utilizada exclusivamente en el marco del proyefcto UNmapa
			</div>			
			
      </form>
	   

	</div></div>
	<?php
	include('./_serverconfig/pie.php');
	?>
</body>
</html>
