<?php
// eliminar esta funcion solo se utiliza para crear categorías, pero es muy insegura
/**
* agrega.php
*
* aplicación para generar nuevos registros en una base de datos a partir de la informaicón enviada vía  POST
* 
* @package    	intraTReCC
* @subpackage 	Comun
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2010-2015 TReCC SA
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

/* funciones frecuentes */
include("./includes/fechas.php");
include("./includes/cadenas.php");

$UsuarioI = $_SESSION['Unmapa'][$CU]->USUARIO['uid'];

if($UsuarioI==""){header('Location: ./login.php');}

$query="
	SELECT `ACTaccesos`.`id`,
	    `ACTaccesos`.`id_actividades`,
	    `ACTaccesos`.`id_usuarios`,
	    `ACTaccesos`.`nivel`
	FROM 
		`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`ACTaccesos`
	WHERE id_usuarios='".$UsuarioI."'
";
$Consulta = $Conec1->query($query);
echo $Conec1->error;	

while($row=$Consulta->fetch_assoc()){
	$Accesos[$row['id_usuarios']]=$row['nivel'];
}

if($Accesos[$UsuarioI]<1){
	//header('location: ./actividades.php');	//este usuario deber definir una actividad habilitada
	echo "ERROR de Acceso 2";
	print_r($Accesos);
	echo "<br>".$UsuarioI;
	exit;
}



	foreach($_POST as $k => $v){// estas variables son pasadas por als aplicaciones comunes manteniendose.
		if(substr($k,0,5)=='PASAR'){
			$PASAR[$k]=$v;
		}
	}
	

	$Id_contrato = $_POST["contrato"];
	$Tabla = $_POST["tabla"];
	$Id = $_POST["id"];
	$Accion = $_POST["accion"];	
	$Origenid = $_POST["Origenid"];      /* variable para cuando la entrada agregada responde a otra que debe ser cerrada (comunicaciones)*/
	$Paraorigen = $_POST["Paraorigen"];	/* variable para incorporar al orgigenid */	
	$Fechaa = $_POST["fechaemisionref_a"];
	$Fecham = $_POST["fechaemisionref_m"];
	$Fechad = $_POST["fechaemisionref_d"];	
	$Fecha = $Fechaa."-".$Fecham."-".$Fechad;
	$Tablahermana = $_POST["tablahermana"];
	$Idhermana = $_POST["idhermana"];		
	$Salida = $_POST['salida'];
	$Salidaid = $_POST['salidaid'];	
	$Salidatabla = $_POST['salidatabla'];		
	$PanelI = $_SESSION['panelcontrol']->PANELI;	
	$Base = 'MAPAUBA';
	$Index = $_SESSION['panelcontrol']->INDEX;		
	$HOY = date("Y-m-d");
	$HOYd = date("d");
	$HOYm = date("m");
	$HOYa = date("Y");
	$Publicacion .= "<br><br>";
	$query='SHOW FULL COLUMNS FROM `'.$_SESSION['Unmapa'][$CU]->DATABASE_NAME.'`.`'.$Tabla.'`';
 	$Consulta = $Conec1->query($query);
	print_r($_POST);
    if ($Consulta->num_rows > 0) {
        while ($row = $Consulta->fetch_assoc()) {
        	
        	$campo = $row['Field'];
			$datomas = $_POST[$campo];
			$Type = substr($row['Type'],0,3);
			$Typolink = substr($row['Field'],0,4);
			$Typo = substr($row['Field'],0,3);			
			
			/* para tablas padre */
			if($Typolink == "id_p"){
				$Publicacion .= "<br>padre en: ".$campo. "->".$datomas;
				if($datomas == "n"){
					$Publicacion .= "<br>n solicita nuevo item";
					$Baselink = substr($row['Field'],0,6);
					if($Baselink != "id_p_B")
					{
						$Publicacion .= "<br>padre interno";
						$o = explode("_", $row['Field']);
						$basepadre = $Base;
						$tablapadre = $o[2];
						$campopadre = $o[4];
						if($campopadre==''){
								$_SESSION['DEBUG']['mensajes'][] = "campo padre: indefinido, explorando...";
								$query='SHOW FULL COLUMNS FROM `'.$_SESSION['Unmapa'][$CU]->DATABASE_NAME.'`.`'.$tablapadre.'`';
								$padre = $Conec1->query($query);
								$seteado='no';
								While($rp=$padre->fetch_assoc()){
									if($seteado=='no'&&$rp['Field']!='id'){
										$seteado='si';
										$campopadre = $rp['Field'];
									}
								}
								$_SESSION['DEBUG']['mensajes'][] = "campo asignado: $campopadre";
							}
						
						$extra = "";
						
						$padre = $basepadre . "." . $tablapadre;
						$campocont = $campo."_n";
						$nuevocontenido=$_POST[$campocont];
						
						
						//Verifica no repetición en el nombre para tablas espe´cificas, ej: grupos
						$query="	
							SELECT 
								* 
							FROM
								 `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.$tablapadre
								 WHERE $campopadre='$nuevocontenido' 
						";
						$existe=$Conec1->query($query);
						$Publicacion .= $Conec1->error;	
						if($existe->num_rows>0){
							$Publicacion .= "<br>nombre de item existente, creación anulada";
							$Publicacion .= $query;
							$row=$existe->fetch_assoc();
							$Idnuevo=$row['id'];
							$Publicacion .= "<br>id reciclado: ".$Idnuevo;
							$datomas = $Idnuevo;
						}else{						
							$query = "INSERT INTO `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.$tablapadre SET $campopadre='$nuevocontenido'";
							$ccc = $Conec1->query($query);
							$Publicacion .= $Conec1->error;	
							$Idnuevo = $ccc->insert_id;
							$Publicacion .= "nuevo id: ".$Idnuevo;
							$datomas = $Idnuevo;
							$Publicacion .= "agregará: ".$datomas;
						}
					}
				}
				$Publicacion .= "<br>otro;". " - " . $row['Field']. " - " . $datomas."<br>";
				if($datomas != ""){
					$Datos .= " `" . $campo . "`='" .  $datomas . "',";
				}
					
			}elseif($Typo == 'zz_' && $campo == 'zz_AUTOFECHAMODIF'){
				$Datos .= " `" . $campo . "`='" .  $HOY . "',";
			}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOFECHACREACION'){
				$Datos .= " `" . $campo . "`='" .  $HOY . "',"; /* este campo nunca se debe modificar, debe ser una impresión del momento de creación del registro */
			}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOPANEL'){
				$Datos .= " `" . $campo . "`='" .  $PanelI . "',"; /* este campo nunca se debe modificar, debe ser una impresión del momento de creación del registro */
			}elseif($Typo == 'zz_'&& $campo == 'zz_AUTOUSUARIOCREAC'){
				$Datos .= " `" . $campo . "`='" .  $UsuarioI . "',"; /* este campo nunca se debe modificar, debe ser una impresión del momento de creación del registro */
			}elseif($Typo == 'FI_'){
				if(isset($_FILES['archivo_F'])){
					$imagenid = $_FILES['archivo_F']['name'];	
					$nombre = isset($_POST['archivo_FI_nombre'])? $_POST['archivo_FI_nombre'] : $Tabla."[NID]"; /* el texto [NID] se reemplazará por la uneva id */
					$path = $_POST['archivo_FI_path'];	
					
					/* verificar y crear directorio */
						$Publicacion.="analizando ruta<br>";
						$carpetas= explode("/",$path);
						$rutaacumulada="";
						
						foreach($carpetas as $valor){
							$Publicacion.="instancia: $valor<br>";
								
							$rutaacumulada.=$valor."/";
							echo $rutaacumulada."<br>";
							if(!file_exists($rutaacumulada)){
								$Publicacion.="la carpeta no existe!<BR>";
								if($valor!=''){
									$Publicacion.="creando: $rutaacumulada<br>";
								    mkdir($rutaacumulada, 0777, true);
									chmod($rutaacumulada, 0777); 
								}
							
							}
						}
					/* FIN verificar y crear directorio */	
					echo "<br>".$imagenid."<br>";
						$b = explode(".",$imagenid);
						$ext = $b[(count($b)-1)];
					if(
						$ext=="JPG"||$ext=="jpg"||$ext=="png"||$ext=="PNG"||$ext=="tif"||$ext=="TIF"||
						$ext=="bmp"||$ext=="BMP"||$ext=="gif"||$ext=="GIF"||
						$ext=="pdf"||$ext=="PDF"||
						$ext=="xls"||$ext=="XLS"||
						$ext=="ods"||$ext=="ODS"||
						$ext=="doc"||$ext=="DOC"||
						$ext=="odt"||$ext=="ODT"
					){
						$nombre = str_replace('[NID]', $Id, $nombre);
						$cod = cadenaArchivo(10); /* define un código que evita la predictividad de los documentos ante búsquedas maliciosas */
						$pathI = $path.$nombre."_".$cod.".".$ext;
						$Publicacion .= "guardado en".$pathI."<br>";
						
						if (!copy($_FILES['archivo_F']['tmp_name'], $pathI)) {
						    $Publicacion .= "Error al copiar $pathI...\n";
						}else{
						$Publicacion .= "imagen guardada";
						$datomas = $pathI;
						$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
					}else{
						$Publicacion .= "solo se aceptan los formatos: jpg, png, tif, gif, bmp, pdf, xls, ods, doc, odt";
						$imagenid='';
						print_r($_FILES); 
					}
					
					
				}
			}elseif($row['Field'] != "id"){
				if ($Type == "tex"){
					$datomas = str_replace("<br />","",$_POST[$campo]);
						if($datomas != ""){
							$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
				}elseif($Type == "dat"){
				$Publicacion .= "<br>fecha;". " - " . $row['Field']. " - " . $datomas;
					$campo_a = $campo . "_a";
					$campo_m = $campo . "_m";
					$campo_d = $campo . "_d";
					
					$contenidoa = $_POST[$campo_a];
					$contenidom = $_POST[$campo_m];
					$contenidod = $_POST[$campo_d];
					
					/* ojo este comentario tal vez gener conflicto
					if($contenidoa == '' || $contenidom == '' ||$contenidod == ''){
						if($contenidod == ''){$contenidod =$HOYd;}
						if($contenidom == ''){$contenidom =$HOYm;}
						if($contenidoa == ''){$contenidoa =$HOYa;}
					}
					*/
					
					$datomas = $contenidoa . "-" . $contenidom . "-" . $contenidod;
			
						if($datomas != "--"){
							$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
				}else{
						if($datomas != ""){
							$Datos .= " `" . $campo . "`='" .  $datomas . "',";
						}
				}

			
        	}
		}
	}


$Datos = substr($Datos,0,(strlen($Datos)-1));


$Publicacion .= "<br>id base: ";
$Publicacion .= $Id_contrato;
$Publicacion .= "<br>tabla: ";
$Publicacion .= $Tabla;
$Publicacion .= "<br>id: ";
$Publicacion .= $Id;
$Publicacion .= "<br>accion: ";
$Publicacion .= $Accion;
$Publicacion .= "<br>";
$Publicacion .= "<br>datos: ";
$Publicacion .= $Datos;
$Publicacion .= "<br>";
$Publicacion .= "<br>";


$query="INSERT INTO `".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.$Tabla SET $Datos";
$Consulta = $Conec1->query($query);
$Publicacion .= $query;
$Id = $Consulta->insert_id;
$NID = $Id;
$Publicacion .= $Id . "<br>";

$Publicacion .= "error mysql: ". $Conec1->error;


if(isset($_POST['__NID_valor'])){
	echo "<script type='text/javascript'>";
	echo "parent.document.getElementById('".$_POST['__NID_valor']."').value = '".$NID."';";
	echo "parent.document.getElementById('".$_POST['__NID_valor']."').parentNode.submit();";	
	echo "</script>";
}

if($Salidatabla != ""){$Salidatabla = $Tabla;}
if($Salidaid == ""){$Salidaid = $Id;}

$Publicacion .= $Salida;
$Publicacion .=".php?tabla=";
$Publicacion .= $Tabla;
$Publicacion .="&id=";
$Publicacion .= $Salidaid;

$_SESSION['DEBUG']['mensajes'][]=$Publicacion;


if(isset($_POST['TablaHtml'])){

	?>
	<script type='text/javascript'>
		_idtabla='<?php echo $_POST['TablaHtml'];?>';
		_valor='<?php echo $_POST['nombre'];?>';
		_tabla=window.parent.document.getElementById(_idtabla);
		_tr = window.parent.document.createElement('tr');
		_td1 = window.parent.document.createElement('td');
		_td1.innerHTML=_valor;
		_tabla.appendChild(_tr);
		
		_td2 = window.parent.document.createElement('td');
		_aa="<a target='_blank' href='./agrega_f.php?accion=cambia&tabla=<?php echo $Tabla;?>&id=<?php echo $NID;?>'>editar</a>";
		_td2.innerHTML=_aa;
		
		_tr.appendChild(_td1);
		_tr.appendChild(_td2);

	</script>
	<?php
	
}

if($Salida!='__ALTO'){
	if($Salida!=''){
		$Publicacion .= "saliendo...";
		echo $Publicacion;
		$cadenapasar='';
		foreach($PASAR as $k => $v){
			$cadenapasar.='&'.substr($k,5).'='.$v;
		}	
		$location="./".$Salida.".php?tabla=".$Tabla."&id=".$Salidaid.$cadenapasar;
		
		if($Tabla=='comunicaciones'){$location="./agrega_fcom.php?tabla=".$Tabla."&accion=cambia&salida=comunicaciones&id=".$Id;}
		?><SCRIPT LANGUAGE="javascript">location.href = "<?php echo $location;?>";</SCRIPT><?php  	
		
	}else{
		?>
			<button onclick="window.close();">cerrar esta ventana</button>
		<?php  
	}
}


?>
