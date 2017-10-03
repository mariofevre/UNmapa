<?php
/**
*BASEconsultas.php
*
* Esta aplicación contiene las consultas a la base de datos del mapa colectivo.
* Los criterio de selección de puntos a vusualizar debe ser calibrado
* 
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	base
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



/**
 * @param $filtrose  filtro expluyente. array numreado de arrays de (key:)tipo y (valor:)valor.
 * @param $filtrosi  filtro expluyente. array de tipo y valor.
 */
 
function obtenerPuntos($Actividad,$Escala,$Nivel,$Area,$filtrose,$filtrosi){

	include_once('./actividades_consulta.php');//funciones de consulta básica de la base de datos
	
	$ActividadDta=reset(actividadesconsulta($Actividad,$seleccion));
	
	
	//echo "<pre>";print_r($ActividadDta);echo "</pre>";
	foreach($ActividadDta['GEO'] as $pId => $pData){
		
		foreach($filtrose as  $f){
			foreach($f as  $tipo => $valor){			
				if($pData[$tipo]==$valor){
					unset($ActividadDta['GEO'][$pId]);
				} 
			}
		}
		
		foreach($filtrosi as  $f){
			foreach($f as  $tipo => $valor){			
				if($pData[$tipo]!=$valor){
					unset($ActividadDta['GEO'][$pId]);
				} 
			}
		}		
	}
	
	
	foreach($ActividadDta['GEO'] as $pId => $pData){
			$row=$pData;		
			$row['lon']=$pData['x'];
			$row['lat']=$pData['y'];
			$row['z']=$pData['z'];
			$LocalizacionesAct[]=$row;
	}
	//echo "<pre>";print_r($LocalizacionesAct);echo "</pre>";	


	//falso dato

	$row['lon']='-58.87533569336';
	$row['lat']='-34.55833358768';
	$row['id']='2';
	$row['datos']='undato';
	$row['texto']='undtexto';
	$row['valor']='unvalor';		
	$LocalizacionesBase[]=$row;
	
	$row['lon']='-58.766992187501';
	$row['lat']='-34.573134613071';
	$row['datos']='undato';
	$row['id']='1';
	$LocalizacionesBase[]=$row;	
//print_r($LocalizacionesBase);	

// consulta puntos de la base colectiva de todas las actividades



	$Puntos['Activ']=$LocalizacionesAct;
	$Puntos['Base']=$LocalizacionesBase;
	//print_r($Puntos);	
	return $Puntos;
}
?>