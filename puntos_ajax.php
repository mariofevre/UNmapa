<?php 
/**
* actividad.php
*
* aplicaciÓn para cargar nuevos puntos de relevamiento EN UNA ACTIVIDAD
*  
* 
* @package    	Plataforma Colectiva de InformaciÓn Territorial: UBATIC2014
* @subpackage 	actividad
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicaciÓn se desarrollo sobre una publicación GNU (agpl) 2014 TReCC SA
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


// verificación de seguridad 
include('./includes/conexion.php');
include('./includes/conexionusuario.php');

$UsuarioI = $_SESSION['USUARIOID'];
if($UsuarioI==""){header('Location: ./login.php');}


if(isset($_POST['tipo']))
	$tipo=$_POST['tipo'];
else
	return;
if(isset($_POST['actividad']))
	$actividad=$_POST['actividad'];
else
	return;
if(isset($_POST['zoom']))
	$zoom=$_POST['zoom'];
else
	$zoom=0;

if(isset($_POST['x0']))
	$x0=$_POST['x0'];
else
	return;
if(isset($_POST['xf']))
	$xf=$_POST['xf'];
else
	return;
if(isset($_POST['y0']))
	$y0=$_POST['y0'];
else
	return;
if(isset($_POST['yf']))
	$yf=$_POST['yf'];
else
	return;



if ($tipo == 1) {
	//Tipo de consulta de puntos de la misma actividad
	$query="SELECT distinct `geodatos`.id_actividades,
`geodatos`.`id`,	
`geodatos`.`x`,
`geodatos`.`y`,
`geodatos`.`z`,
z - $zoom + case when `geodatos`.id_usuarios = $UsuarioI then 0 else 2 end ranking,
`geodatos`.`zz_bloqueado`,
`geodatos`.`zz_bloqueadoTx`,
`geodatos`.`zz_bloqueadoUsu`,
atributos.valor,
atributos.valor,
atributos.textobreve,
atributos.link,
ACTcategorias.CO_color color,
ACTcategorias.nombre nombreCategoria,
atributos.texto,
concat(concat(usuarios.nombre, ' '), usuarios.apellido) as nombreUsuario,
actividades.valorAct valorAct,
actividades.categAct categAct 
FROM `UNmapa`.`geodatos`
left outer JOIN UNmapa.actividades on `geodatos`.id_actividades = actividades.id
left outer JOIN UNmapa.atributos on `geodatos`.id = atributos.id
left outer JOIN UNmapa.ACTcategorias on atributos.categoria = ACTcategorias.id
left outer JOIN UNmapa.usuarios on geodatos.id_usuarios = usuarios.id
where 
geodatos.zz_borrada='0' and actividades.zz_borrada='0'
and x >= $x0 and x <= $xf and y >= $y0 and y <= $yf 
/*and z >= $zoom*/ and `geodatos`.id_actividades = $actividad
order by ranking
/*limit 100*/";
	
	$maxPuntos = 1000;//Lo comentamos porque se solicito que de la actividad se muestren todos los puntos $_SESSION['sigsao']->MAX_VISIBLE_ACTIVITY_POINTS;
	
}
else {
	//Tipo de consulta de puntos de otras actividades
	$query="SELECT distinct `geodatos`.id_actividades,
		`geodatos`.`id`,	
		`geodatos`.`x`,
		`geodatos`.`y`,
		`geodatos`.`z`,
		`geodatos`.`zz_bloqueado`,
		`geodatos`.`zz_bloqueadoTx`,
		`geodatos`.`zz_bloqueadoUsu`,
		z - $zoom + case when `geodatos`.id_usuarios = $UsuarioI then 0 else 3 end - (select count(a.id_p_TAGs_id) from UNmapa.ACTtags a where a.id_p_actividades_id = $actividad
		and a.id_p_TAGs_id = ACTtags.id_p_TAGs_id) ranking,
		atributos.valor,
		atributos.categoria, 
		ACTcategorias.nombre nombreCategoria,
		atributos.texto,
		atributos.textobreve,
		atributos.link,
		concat(concat(usuarios.nombre, ' '), usuarios.apellido) as nombreUsuario,
		actividades.valorAct valorAct,
		actividades.categAct categAct
		FROM `UNmapa`.`geodatos` 
		left outer JOIN UNmapa.actividades on `geodatos`.id_actividades = actividades.id
		left outer JOIN UNmapa.atributos on `geodatos`.id = atributos.id
		left outer JOIN UNmapa.ACTcategorias on atributos.categoria = ACTcategorias.id
		left outer JOIN UNmapa.usuarios on geodatos.id_usuarios = usuarios.id
		left outer JOIN UNmapa.ACTtags on ACTtags.id_p_actividades_id = geodatos.id_actividades
		where 
		geodatos.zz_borrada='0' and actividades.zz_borrada='0'
		and atributos.id is not null  
		and usuarios.id > 0
		and zz_bloqueado='0'
		and x >= $x0 and x <= $xf and y >= $y0 and y <= $yf
		".
		// lo que sigue restringe la búsqueda solo dentro del área de la actividad and exists (select 1 from UNmapa.actividades where actividades.id = $actividad and x >= actividades.x0 and x <= actividades.xf and y <= actividades.y0 and y >= actividades.yf)
		"and z >= $zoom and `geodatos`.id_actividades != $actividad
		order by ranking
		limit 100";
	$maxPuntos = 15;//Lo comentamos porque se solicito que de la actividad se muestren todos los puntos $_SESSION['sigsao']->MAX_VISIBLE_ACTIVITY_POINTS;
}

$ConsultaGEO = mysql_query($query,$Conec1);
echo mysql_error($Conec1);


$codif=array('nombreCategoria','texto','nombreUsuario','textobreve');

$puntos = array();
if (mysql_num_rows($ConsultaGEO) > 0) {
	
	$index = 0;
	while($fila=mysql_fetch_assoc($ConsultaGEO)){
		if ($index < $maxPuntos) {
			foreach($codif as $c){
				$fila[$c]=utf8_encode($fila[$c]);
			}
			$puntos[] = $fila;			
			$index++;
		}
	}
	
	
	$salida=json_encode($puntos);
	
	if($salida==''){
		print_r($puntos);
	}else{
		echo $salida;
	}
	
}else{
	
	
	$puntos[] = 'No hay puntos en este sector';	
	$salida=json_encode($puntos);
	print_r($salida);
}
?>