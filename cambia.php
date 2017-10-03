<?php
/**
* cambia.php
*
* aplicaci�n para modificar cualquier registro de una base de datos  
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


	$Id_contrato = $_POST['contrato'];
	$Tabla = $_POST['tabla'];
	$Id = $_POST['id'];
	$Accion = $_POST['accion'];
	
	$Campo = $_POST['campo'];
	$Salida = $_POST['salida'];
	$Salidaid = $_POST['salidaid'];	
	$Salidatabla = $_POST['salidatabla'];	
	
	$PanelI = $_SESSION['panelcontrol']->PANELI;
	$Base = $_SESSION['panelcontrol']->DATABASE_NAME;
	$Index = $_SESSION['panelcontrol']->INDEX;
	
	$HOY = date(Y."-".m."-".d);
	
	$HOYa = date(Y);
	$HOYm = date(m);
	$HOYd = date(d);
	
	foreach($_POST as $k => $v){// estas variables son pasadas por als aplicaciones comunes manteniendose.
		if(substr($k,0,5)=='PASAR'){
			$PASAR[$k]=$v;
		}
	}
	
	?>
	<head>
	<style>
	img{
		width:150px;
	}
	
	div{
		display:inline;
	}
	</style>
	</head>
	<?php

	$CODIGOELIMINACION = '-[-BORRX-]-'; //esta es la codificaci�n con la que debe recibirse un campo que debe ser eliminado, a diferencia de un campo sobre el que no halla cambios requeridos.

	
	print_r($_POST);
	$Consulta = mysql_query("SELECT * FROM $Tabla WHERE id='$Id'",$Conec1);	
 	$result = mysql_query('SHOW FULL COLUMNS FROM `'.$Tabla.'`',$Conec1);
	
	$panelconsultado=mysql_result($Consulta,0,'zz_AUTOPANEL');
	
	if($panelconsultado!='-1'&&$panelconsultado!=''&&$panelconsultado!=$PanelI){
		header('Location: ./mensaje.php?msj=Error en el acceso al Panel Activo. ref:'.$panelconsultado.' tabla: '.$Tabla);
	}else{
		
		//print_r($_POST);
		
	    if (mysql_num_rows($result) > 0) {				    	
	        while ($row = mysql_fetch_assoc($result)) {
	        	$campo = $row['Field'];
	        	$datomas = $_POST[$campo];
				$Typo = substr($row['Field'],0,3);			
				$Typolink = substr($row['Field'],0,4);
				
				/* para tablas padre */
				if($Typolink == "id_p"){
					$_SESSION['DEBUG']['mensajes'][] = "padre en: ".$campo. "->".$datomas;
					if($datomas == "n"){
						$_SESSION['DEBUG']['mensajes'][] = "nuevo item";
						$extraset='';
						$Baselink = substr($row['Field'],0,7);
						if($Baselink != "id_p_B_"){
							$_SESSION['DEBUG']['mensajes'][] = "padre interno";
							$o = explode("_", $row['Field']);
							$basepadre = $Base;
							$tablapadre = $o[2];
							$_SESSION['DEBUG']['mensajes'][] = "padre: ".$tablapadre;
							$campopadre = $o[4];
							if($campopadre==''){
								$_SESSION['DEBUG']['mensajes'][] = "campo padre: indefinido, explorando...";
								$padre = mysql_query('SHOW FULL COLUMNS FROM `'.$tablapadre.'`',$Conec1);
								$seteado='no';
								While($rp= mysql_fetch_assoc($padre)){
									if($seteado=='no'&&$rp['Field']!='id'){
										$seteado='si';
										$campopadre = $rp['Field'];
									}
								}
								$_SESSION['DEBUG']['mensajes'][] = "campo asignado: $campopadre";
							}
							
							$extra = "";
							if($o[5]=='tipoa'){$extra = ", tipo = 'a'";}
							elseif($o[5]=='tipob'){$extra = ", tipo = 'b'";}
							elseif($o[5]=='tipo'){$extra.=", ".$o[5]."='".$o[6]."'";}
							$padre = $basepadre . "." . $tablapadre;
							$campocont = $campo."_n";
							$nuevocontenido=$_POST[$campocont];
							
							
							//Verifica no repetici�n en el nombre para tablas espe�cificas, ej: grupos
							$query="	
								SELECT 
									* 
								FROM
									 $tablapadre
									 WHERE $campopadre='$nuevocontenido'
							";
							$existe=mysql_query($query,$Conec1);
							$_SESSION['DEBUG']['mensajes'][] = mysql_error($Conec1);
							if(mysql_num_rows($existe)>0){
								$_SESSION['DEBUG']['mensajes'][] = "<br>nombre de item existente, creaci�n anulada";
								$_SESSION['DEBUG']['mensajes'][] = $query;
								$Idnuevo=mysql_result($existe,0,'id');
								$_SESSION['DEBUG']['mensajes'][] = "<br>id reciclado: ".$Idnuevo;
								$datomas = $Idnuevo;
							}else{						
								$query = "INSERT INTO $tablapadre SET $campopadre='$nuevocontenido'";
								mysql_query($query,$Conec1);
								$_SESSION['DEBUG']['mensajes'][] = mysql_error($Conec1);
								$_SESSION['DEBUG']['mensajes'][] = $query;
								$Idnuevo = mysql_insert_id($Conec1);
								$_SESSION['DEBUG']['mensajes'][] = "nuevo id: ".$Idnuevo;
								$datomas = $Idnuevo;
								$_SESSION['DEBUG']['mensajes'][] = "agregar�: ".$datomas;
							}
						}
					}
					$_SESSION['DEBUG']['mensajes'][] = "<br>otro;". " - " . $row['Field']. " - " . $datomas."<br>";
						if($datomas != ""){
						$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
				}elseif($Typo == 'FI_'){
					
					$_SESSION['DEBUG']['mensajes'][] = "Dectectado campo de fichero (FI_), se guardaran los archivos enviados:<br>";
					echo "<pre>";print_r($_FILES);echo "</pre>";

					$NombrePHParchivo='archivo_'.$campo;
					
					if(isset($_FILES[$NombrePHParchivo])){
						$imagenid = $_FILES[$NombrePHParchivo]['name'];	
						$_SESSION['DEBUG']['mensajes'][] = "<br>cargando: ".$imagenid."<br>";
						$b = explode(".",$imagenid);
						$ext = $b[(count($b)-1)];
	
						$path = $_POST[('archivo_'.$campo.'_path')];	
						
						/* verificar y crear directorio */
						$path;
							$Publicacion.="analizando ruta de guardado<br>";
							$carpetas= explode("/",$path);
							$rutaacumulada="./documentos/p_1/";
							foreach($carpetas as $valor){								
							$rutaacumulada.=$valor."/";
							$_SESSION['DEBUG']['mensajes'][] = "probando ruta: ".$rutaacumulada."<br>";
								if (!file_exists($rutaacumulada)&&$valor!=''){
									$Publicacion.="creando: $rutaacumulada<br>";
								    mkdir($rutaacumulada, 0777, true);
								    chmod($rutaacumulada, 0777);
								}
							}
						/* FIN verificar y crear directorio */	
												
						$nombretipo = $Tabla.$Id;
						$nombrerequerido = isset($_POST[$Campo]) ? $_POST[$Campo]:'';
						
												
																		
						if($nombrerequerido!=''&&!file_exists($nombrerequerido)){
							$nombre=$nombrerequerido;
						}else{
							$nombre=$nombretipo;						
						}				
						
						$c=explode('.',$nombre);
						
						$cod = cadenaArchivo(10); // define un c�digo que evita la predictivilidad de los documentos ante b�squedas maliciosas
						$nombre=$rutaacumulada.$c[0].$cod.".".$ext;
	
						
						if($ext=="JPG"||$ext=="jpg"||$ext=="png"||$ext=="PNG"||$ext=="tif"||$ext=="TIF"||$ext=="bmp"||$ext=="BMP"||$ext=="gif"||$ext=="GIF"||$ext=="pdf"||$ext=="PDF"){
							$_SESSION['DEBUG']['mensajes'][] = "guardado en: ".$nombre."<br>";
							
							if (!copy($_FILES[$NombrePHParchivo]['tmp_name'], $nombre)) {
							    $_SESSION['DEBUG']['mensajes'][] = "Error al copiar $pathI...\n";
							}else{
								$_SESSION['DEBUG']['mensajes'][] = "imagen guardada";
								$datomas = $nombre;	
								$Datos .= " `" . $campo . "`='" .  $datomas . "',";
								$_SESSION['DEBUG']['mensajes'][] = 'sentencia: '.$campo . "`='" .  $datomas . "',";
							}
						}else{
							$_SESSION['DEBUG']['mensajes'][] = "solo se aceptan los formatos: jpg, png, tif, gif, bmp, pdf";
							$imagenid='';
						}
						
						
					}elseif(isset($_POST[$campo])){
						
						$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						
					}
				
				}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOFECHAMODIF'){
					
					$Datos .= " `" . $campo . "`='" .  $HOY . "',";
				
				}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOFECHACREACION'){
					/* este campo nunca se debe modificar, debe ser una impresi�n del momento de creaci�n del registro */
				}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOPANEL'){
					/* este campo nunca se debe modificar, debe ser una impresi�n del panel activo al momento de creaci�n del registro */
				}else{
					$Type = substr($row['Type'],0,3);
								
					if ($Type == "tex"||$Type == "lon"){
						$datomas = str_replace("'",'"',$_POST[$campo]);
						$datomas = str_replace("<br />","",$datomas);
						
						$_SESSION['DEBUG']['mensajes'][] = "<br>text;". " - " . $row['Field']. " - " . $datomas;
							if($datomas != ""){
								if($datomas == $CODIGOELIMINACION){$datomas ='';}
								$Datos .= " `" . $campo . "`='" .  $datomas . "',";
							}
					}elseif($Type == "dat"){
						$_SESSION['DEBUG']['mensajes'][] = "<br>fecha;". " - " . $row['Field']. " - " . $datomas;
						$campo_a = $campo . "_a";
						$campo_m = $campo . "_m";
						$campo_d = $campo . "_d";
						
						$datomas = $_POST[$campo_a] . "-" . $_POST[$campo_m] . "-" . $_POST[$campo_d];
							
							
							if($datomas != "--"){
								if($datomas=='0000-00-00'){$datomas=" null ";}else{$datomas="'$datomas'";}
								$Datos .= " `" . $campo . "`=" .  $datomas . ",";
							}
					}else{
						$datomas = $_POST[$campo];
						$_SESSION['DEBUG']['mensajes'][] = "<br>campo:" . $row['Field']. ", ".$row['Comment']." - :" . $datomas."<br>";
							if($datomas != ""){
								if($datomas == $CODIGOELIMINACION){$datomas ='';}
								$Datos .= " `" . $campo . "`='" .  $datomas . "',";
							}
					}
	        	}
			
		    } 
		}	
		
		$Datos = substr($Datos,0,(strlen($Datos)-1));
		
		$_SESSION['DEBUG']['mensajes'][] = "id: ".$Id_contrato."<br>";
		$_SESSION['DEBUG']['mensajes'][] = "tabla".$Tabla."<br>";
		$_SESSION['DEBUG']['mensajes'][] = "id: ".$Id."<br>";		
		$_SESSION['DEBUG']['mensajes'][] = "accion: ".$Accion."<br>";	
		$_SESSION['DEBUG']['mensajes'][] = "<h2>datos enviados: </h2>";
		$_SESSION['DEBUG']['mensajes'][] = "<div>".$Datos."</div>";
		
			
		$query="UPDATE $Tabla SET $Datos WHERE id='$Id'";
		echo $query;
		
		mysql_query($query,$Conec1);
		$_SESSION['DEBUG']['mensajes'][] = mysql_error($Conec1);
		
	
		$_SESSION['DEBUG']['mensajes'][] = $Salida;
		$_SESSION['DEBUG']['mensajes'][] =".php?tabla=";
		$_SESSION['DEBUG']['mensajes'][] = $Tabla;
		$_SESSION['DEBUG']['mensajes'][] ="&id=";
		$_SESSION['DEBUG']['mensajes'][] = $Salidaid;
	
	
	
		if($Salida=='reload'){
			echo "<script type='text/javascript'>";
			echo "
				page_y = parent.window.scrollY;
				_loc=parent.location.href;
				var _res = _loc.split('&y=');
				";
			echo "parent.window.location.assign(_res[0]+'&y='+page_y);";
			echo "</script>";
		}elseif($Salida!=''&&$Salida!='cerrar'){
			$cadenapasar='';
			foreach($PASAR as $k => $v){
				$cadenapasar.='&'.substr($k,5).'='.$v;
			}	
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
	
	}