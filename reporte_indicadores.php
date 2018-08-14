<?php 
/**
* reporte_indicadores.php
*
* aplicación que genera de forma dínamica los valores para los indicadores de éxito diseñados para el proyecto académico.
* 
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	BASE
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
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
ini_set('diasplay_errors,true');
?>
<!DOCTYPE html>
	<head>
		<title>UNmapa - Área de Trabajo</title>
		<?php include("./includes/meta.php");?>
		<link href="css/treccppu.css" rel="stylesheet" type="text/css">
		<link href="css/UNmapa.css" rel="stylesheet" type="text/css">	
		<style type='text/css'>
			table,th,td{
				border:1px solid #000;
			}
			table{
				border-collapse:collapse;
			}
					.resumen{
			font-size:15px;
			font-weight:normal;
		}
		</style>
		
		
	</head>


<body>
	
	<?php
	include('./includes/encabezado.php');
	?>
	
	<div id="pageborde"><div id="page">
		<h1>Reporte de Indicadores <span class='resumen'>Reporte autómático de indicadores de seguimeinto del proyecto</span></h1>


<?php
	// consulta las características de la actividad seleccionada	

	$query="
	 
		SELECT 
			`actividades`.`id`,
		    `actividades`.`zz_AUTOUSUARIOCREAC`,
		    `actividades`.`zz_AUTOFECHACREACION`,
		    `actividades`.`zz_PUBLICO`
		    
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`actividades`	
		
		WHERE
			`actividades`.`zz_borrada` !='1'
			AND `actividades`.`zz_publico` ='1'
		ORDER BY 
			`actividades`.`zz_AUTOFECHACREACION` ASC
	
	";	
	$Consulta = $Conec1->query($query);
	echo $Conec1->error;
	
	$fila=$Consulta->fetch_assoc();
	//echo "<pre>";print_r($fila);echo "</pre>";	
	$ini=primerdiadelmes(sumames($fila['zz_AUTOFECHACREACION'],-2));
	$hoy=primerdiadelmes(date("Y-m-d"));
	
	while($ini<=$hoy){
		$Fechas[$ini]=0;
		$ini=messiguiente($ini);
	}
	
	$Consulta->data_seek(0);	
	while($fila=$Consulta->fetch_assoc()){
		foreach($Fechas as $F => $Cont){
			if(primerdiadelmes($fila['zz_AUTOFECHACREACION'])<=$F){
				$Fechas[$F]++;
			}
		}	
	}
	//echo "<pre>";print_r($Fechas);echo "</pre>";
	echo "<h2>Actividades creadas (luego publicadas)</h2>";	
	echo "<table>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>".mesuno($F)." ".ano($F)."</td>";
	}
	echo "</tr>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>$Cont</td>";
	}
	echo "</tr>";
	echo "</table>";

	
	unset($Fechas);
	$query="
	 
		SELECT 
			`zz_AUTOFECHACREACION`
		    
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`	

		ORDER BY 
			`zz_AUTOFECHACREACION` ASC
	
	";	
	$Consulta = $Conec1->query($query);
	echo $Conec1->error;
	
	$fila=$Consulta->fetch_assoc();
	//echo "<pre>";print_r($fila);echo "</pre>";	
	$ini=primerdiadelmes(sumames($fila['zz_AUTOFECHACREACION'],-2));
	$hoy=primerdiadelmes(date("Y-m-d"));
	
	while($ini<=$hoy){
		$Fechas[$ini]=0;
		$ini=messiguiente($ini);
	}
	
	$Consulta->data_seek(0);		
	while($fila=$Consulta->fetch_assoc()){
		foreach($Fechas as $F => $Cont){
			if(primerdiadelmes($fila['zz_AUTOFECHACREACION'])<=$F){
				$Fechas[$F]++;
			}
		}	
	}
	//echo "<pre>";print_r($Fechas);echo "</pre>";
	echo "<h2>Usuarios generados (por participantes manualmente o por coordinadores masivamente)</h2>";
	echo "<table>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>".mesuno($F)." ".ano($F)."</td>";
	}
	echo "</tr>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>$Cont</td>";
	}
	echo "</tr>";
	echo "</table>";
	

	unset($Fechas);
	$query="
	 
		SELECT 
			`zz_AUTOFECHACREACION`
		    
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`usuarios`	
		WHERE 
			zz_idactivacion is not null
		ORDER BY 
			`zz_AUTOFECHACREACION` ASC
	
	";	
	$Consulta = $Conec1->query($query);
	echo $Conec1->error;
	
	$fila=$Consulta->fetch_assoc();
	//echo "<pre>";print_r($fila);echo "</pre>";	
	$ini=primerdiadelmes(sumames($fila['zz_AUTOFECHACREACION'],-2));
	$hoy=primerdiadelmes(date("Y-m-d"));
	
	while($ini<=$hoy){
		$Fechas[$ini]=0;
		$ini=messiguiente($ini);
	}
	
	$Consulta->data_seek(0);		
	while($fila=$Consulta->fetch_assoc()){
		foreach($Fechas as $F => $Cont){
			if(primerdiadelmes($fila['zz_AUTOFECHACREACION'])<=$F){
				$Fechas[$F]++;
			}
		}	
	}
	//echo "<pre>";print_r($Fechas);echo "</pre>";
	echo "<h2>Usuarios creados Manualmente (por participantes)</h2>";
	echo "<table>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>".mesuno($F)." ".ano($F)."</td>";
	}
	echo "</tr>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>$Cont</td>";
	}
	echo "</tr>";
	echo "</table>";	
	

	
	
	
	unset($Fechas);
	$query="
	 
		SELECT 
			fecha, id_usuarios
		    
		FROM
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`	
		WHERE
			zz_borrada = '0'
		ORDER BY 
			fecha ASC
	
	";	
	$Consulta = $Conec1->query($query);
	echo $Conec1->error;
	
	$fila=$Consulta->fetch_assoc();
	//echo "<pre>";print_r($fila);echo "</pre>";	
	$ini=primerdiadelmes(sumames($fila['fecha'],-2));
	echo "ini: $ini";
	
	$hoy=primerdiadelmes(date("Y-m-d"));
	echo "hoy: $hoy";
	while($ini<=$hoy){
		$Fechas[$ini]=0;
		$ini=messiguiente($ini);
	}
	
	$Consulta->data_seek(0);	
	while($fila=$Consulta->fetch_assoc()){
		foreach($Fechas as $F => $Cont){
			//echo "<br>".primerdiadelmes($fila['fecha']). " <= ". $F;
			if(primerdiadelmes($fila['fecha'])<=$F){
				//echo "   - OK";
				$Fechas[$F]++;
			}
		}	
	}
	//echo "<pre>";print_r($Fechas);echo "</pre>";
	echo "<h2>Datos cargados</h2>";
	echo "<table>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>".mesuno($F)." ".ano($F)."</td>";
	}
	echo "</tr>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>$Cont</td>";
	}
	echo "</tr>";
	echo "</table>";	

	
	
		
	unset($Fechas);
	$query="
	 
		SELECT 
			fecha, id_usuarios
		    
		FROM 
			`".$_SESSION['Unmapa'][$CU]->DATABASE_NAME."`.`geodatos`	
		WHERE 
			zz_borrada = '0'
		ORDER BY 
			fecha ASC
	
	";	
	$Consulta = $Conec1->query($query);
	echo $Conec1->error;
	
	$fila=$Consulta->fetch_assoc();
	//echo "<pre>";print_r($fila);echo "</pre>";	
	$ini=primerdiadelmes(sumames($fila['fecha'],-2));
	echo "ini: $ini";
	
	$hoy=primerdiadelmes(date("Y-m-d"));
	echo "hoy: $hoy";
	while($ini<=$hoy){
		$Fechas[$ini]=0;
		$ini=messiguiente($ini);
	}

	$Consulta->data_seek(0);
	while($fila=$Consulta->fetch_assoc()){
		if(!isset($Usu[$fila['id_usuarios']])){$Usu[$fila['id_usuarios']]['Fmin']=$fila['fecha'];}
		$Usu[$fila['id_usuarios']]['Fmin']=min($Usu[$fila['id_usuarios']]['Fmin'],$fila['fecha']);
	}
		

	//echo "<pre>";print_r($Usu);echo "</pre>";	
	foreach($Usu as $fila){
		foreach($Fechas as $F => $Cont){
			//echo "<br>".primerdiadelmes($fila['fecha']). " <= ". $F;
			if(primerdiadelmes($fila['Fmin'])<=$F){
				//echo "   - OK";
				$Fechas[$F]++;
			}
		}	
	}
	//echo "<pre>";print_r($Fechas);echo "</pre>";
	echo "<h2>Usuarios que cargaron algún dato</h2>";
	echo "<table>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>".mesuno($F)." ".ano($F)."</td>";
	}
	echo "</tr>";
	echo "<tr>";
	foreach($Fechas as $F => $Cont){
		echo "<td>$Cont</td>";
	}
	echo "</tr>";
	echo "</table>";
	
	echo "</div>";echo "</div>";		
?>