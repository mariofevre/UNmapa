<?php 
/**
* cargaarchivo.php
*
* cargaarchivo.php se incorpora en la carpeta raiz en tanto resulta una aplicación común a varios módulos
* permite al archivo que lo llama definir ruta para la creación y copia de archivos sin restricción, más que extenciones de imagen y pdf
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




if(isset($_FILES['archivo_F'])){
	$imagenid = $_FILES['archivo_F']['name'];	
	$nombre = isset($_POST['archivo_FI_nombre'])? $_POST['archivo_FI_nombre'] : $Tabla."[NID]";	
	$path = $_POST['archivo_FI_path'];	
	
	
	/* verificar y crear directorio */
		$Publicacion.="analizando ruta<br>";
		$carpetas= explode("/",$path);
		$rutaacumulada="";
		foreach($carpetas as $valor){
		$Publicacion .= "instancia: $valor<br>";
			
		$rutaacumulada.=$valor."/";
		$publicacion.= $rutaacumulada."<br>";
			if (!file_exists($rutaacumulada)&&$valor!=''){
				$Publicacion.="creando: $rutaacumulada<br>";
			    mkdir($rutaacumulada, 0777, true);
			    chmod($rutaacumulada, 0777);
			}
		}
	/* FIN verificar y crear directorio */	
	
	/*echo "<br>".$imagenid."<br>";*/
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
		$Publicacion .= "archivo guardado";
		$query="
			UPDATE 
				$Tabla
			SET
				FI_documento = '$pathI',
				FI_nombreorig = '$imagenid'
			WHERE
				id = '$NID'
		";
		mysql_query($query,$Conec1);
		$Publicacion .= $query;	
		$Publicacion .= mysql_error($Conec1);
		/*print_r($_FILES);*/ 
		}
	}else{
		$Publicacion .= "solo se aceptan los formatos: jpg, png, tif, gif, bmp, pdf, xls, ods, doc, odt";
		$imagenid='';
		/*print_r($_FILES);*/ 
	}
	if($imagenid==''){$imagenid=$archivo;}
}


$Publicacion .= "error mysql: ". mysql_error($_SESSION['panelcontrol']->Conec1);

/*
echo "
	<html>
		<head>
		 <style type='text/css'>
		 	body{
		 		font-size:10px;
		 		font-family:arial;
		 	}
		 </style>
		<head>
		<body>
			$Publicacion
		</body>
	</html>		
";
*/
?>	



